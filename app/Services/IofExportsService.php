<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EntryStatus;
use App\Http\Components\Iofv3\Entities\Attributes;
use App\Http\Components\Iofv3\Entities\ClassEntry;
use App\Http\Components\Iofv3\Entities\EventEntry;
use App\Http\Components\Iofv3\Entities\Name;
use App\Http\Components\Iofv3\Entities\Person;
use App\Http\Components\Iofv3\Entities\PersonEntry;
use App\Http\Components\Iofv3\Entities\StartTime;
use App\Http\Components\Iofv3\EntryList;
use App\Http\Components\Iofv3\ResultList;
use App\Http\Components\Iofv3\StartList;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use App\Models\UserEntry;
use DOMElement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class IofExportsService
{
    public function getResultList(string $xmlContent): ResultList
    {
        return $this->getSerializer()->deserialize(
            $xmlContent,
            'App\Http\Components\Iofv3\ResultList',
            'xml'
        );
    }

    public function getStartList(string $xmlContent): StartList
    {
        return $this->getSerializer()->deserialize(
            $xmlContent,
            'App\Http\Components\Iofv3\StartList',
            'xml'
        );
    }

    public function getStartListAttributes(string $xmlContent): Attributes
    {
        $xml = simplexml_load_string($xmlContent);
        $json = json_encode($xml);

        $array = json_decode($json, true);
        $array['Attributes'] = $array['@attributes'];
        unset($array['@attributes']);

        return $this->getSerializer()->denormalize(
            $array['Attributes'],
            'App\Http\Components\Iofv3\Entities\Attributes'
        );
    }

    public function getResourceBySlug(?string $slug): ?string
    {
        /** @var ?SportEventExport $sportEventExport */
        $sportEventExport = DB::table('sport_event_exports')->where('slug', '=', $slug)->first();

        return $sportEventExport?->result_path;
    }

    /**
     * Generate an IOF EntryList XML document for the given sport event.
     *
     * Builds an EntryList containing event metadata and person entries derived from active user entries
     * (statuses Create or Edit), enriches class and id metadata (minAge, sex, id type/value), and inserts
     * IOF namespace and creator attributes before returning the final XML string.
     *
     * @param SportEvent $sportEvent The sport event to generate the EntryList for.
     * @return string The finalized IOF EntryList XML.
    public function generateEntryListXml(SportEvent $sportEvent): string
    {
        // Build EventEntry
        $startTime = null;
        if ($sportEvent->date !== null) {
            $dateStr = $sportEvent->date->format('Y-m-d');
            $timeStr = null;
            if ($sportEvent->start_time !== null) {
                // start_time is stored as time string (HH:MM:SS)
                $timeStr = $sportEvent->start_time . '+01:00'; // Default timezone, adjust as needed
            }
            $startTime = new StartTime($dateStr, $timeStr);
        }
        $eventEntry = new EventEntry($sportEvent->name, $startTime);

        // Build PersonEntry array from active entries
        /** @var Collection<int, UserEntry> $userEntries */
        $userEntries = UserEntry::query()
            ->where('sport_event_id', '=', $sportEvent->id)
            ->whereIn('entry_status', [EntryStatus::Create, EntryStatus::Edit])
            ->with(['userRaceProfile', 'sportClassDefinition'])
            ->get();

        $personEntries = [];
        $classAttributesData = []; // Store data for post-processing Class attributes

        foreach ($userEntries as $userEntry) {
            if ($userEntry->userRaceProfile === null || $userEntry->sportClassDefinition === null) {
                continue;
            }

            $profile = $userEntry->userRaceProfile;

            // Build Person
            $personId = (string) ($profile->oris_id ?? $profile->iof_id ?? $profile->id);
            $personName = new Name($profile->last_name, $profile->first_name);
            $person = new Person($personId, $personName);

            // Build Organization (optional - only if club information is available)
            // Note: Currently Organization is omitted if club_user_id is not set or club name cannot be determined
            // This can be enhanced later to look up club names from ORIS or Club model
            $organization = null;
            // Organization is optional per IOF spec, so we skip it if we don't have proper club data

            // ControlCard - only if not renting
            $controlCard = null;
            if (!$userEntry->rent_si && $userEntry->si !== null) {
                $controlCard = (string) $userEntry->si;
            }

            // Build ClassEntry
            $classDef = $userEntry->sportClassDefinition;
            $className = $userEntry->class_name ?? $classDef->name ?? 'Unknown';
            $classIdValue = $classDef->oris_id !== null ? (string) $classDef->oris_id : (string) $classDef->id;

            $classEntry = new ClassEntry($classIdValue, $className);

            // Store data for post-processing
            $sex = null;
            if ($profile->gender === 'H') {
                $sex = 'M';
            } elseif ($profile->gender === 'D') {
                $sex = 'F';
            }

            $classAttributesData[] = [
                'minAge' => $classDef->age_from ?? null,
                'sex' => $sex,
                'idValue' => $classIdValue,
                'idType' => 'DALIN',
            ];

            // EntryTime
            $entryTime = $userEntry->entry_created?->format(\DateTimeInterface::ATOM) ??
                         $userEntry->created_at?->format(\DateTimeInterface::ATOM) ??
                         Carbon::now()->format(\DateTimeInterface::ATOM);

            $personEntries[] = new PersonEntry(
                $person,
                $organization,
                $controlCard,
                $classEntry,
                $entryTime
            );
        }

        // Build EntryList
        $entryList = new EntryList($eventEntry, $personEntries);

        // Serialize to XML
        $serializer = $this->getSerializer();
        $context = [
            XmlEncoder::ROOT_NODE_NAME => 'EntryList',
            XmlEncoder::ENCODING => 'UTF-8',
            XmlEncoder::FORMAT_OUTPUT => true,
        ];

        $xmlContent = $serializer->serialize($entryList, 'xml', $context);

        // Add XML declaration and namespace attributes
        $xmlWithAttributes = $this->addXmlNamespacesAndAttributes($xmlContent);

        // Add attributes to Class and Id elements
        return $this->addClassAndIdAttributes($xmlWithAttributes, $classAttributesData);
    }

    private function addXmlNamespacesAndAttributes(string $xml): string
    {
        // Insert namespace declarations and attributes into the root EntryList element
        $xml = str_replace(
            '<EntryList>',
            '<EntryList xmlns="http://www.orienteering.org/datastandard/3.0"'
                . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
                . ' iofVersion="3.0"'
                . ' createTime="' . Carbon::now()->format(\DateTimeInterface::ATOM) . '"'
                . ' creator="Dalin v11">',
            $xml
        );

        return $xml;
    }

    /**
     * Injects class-level age and sex attributes and normalizes Id elements inside IOF XML Class nodes.
     *
     * Removes any existing <minAge>/<sex> child elements, sets Class attributes `minAge` and `sex` when provided,
     * and replaces Id element contents by removing nested `value`/`type` children, setting the Id element text
     * to the provided idValue and the Id element attribute `type` to the provided idType for each Class in document order.
     *
     * @param string $xml The XML content containing IOF `Class` elements (expected to use the IOF v3 namespace).
     * @param array[] $classAttributesData Ordered list of attribute data for each Class node. Each entry should be an
     *     associative array with keys `minAge` (int|null), `sex` (string|null), `idValue` (string), and `idType` (string).
     * @return string The modified XML as a string, or an empty string if serialization fails.
     */
    private function addClassAndIdAttributes(string $xml, array $classAttributesData): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        // Load XML with namespace handling
        @$dom->loadXML($xml);

        // Get all Class elements
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('ns', 'http://www.orienteering.org/datastandard/3.0');

        $classNodes = $xpath->query('//ns:Class');
        $classIndex = 0;

        if ($classNodes === false) {
            return $dom->saveXML() ?: '';
        }

        foreach ($classNodes as $classNode) {
            /** @var DOMElement $classNode */
            if ($classIndex < count($classAttributesData)) {
                $data = $classAttributesData[$classIndex];

                // Remove minAge and sex elements if they exist
                $minAgeElements = $xpath->query('./ns:minAge', $classNode);
                if ($minAgeElements !== false) {
                    foreach ($minAgeElements as $element) {
                        if ($element instanceof \DOMNode) {
                            $element->parentNode?->removeChild($element);
                        }
                    }
                }

                $sexElements = $xpath->query('./ns:sex', $classNode);
                if ($sexElements !== false) {
                    foreach ($sexElements as $element) {
                        if ($element instanceof \DOMNode) {
                            $element->parentNode?->removeChild($element);
                        }
                    }
                }

                // Add minAge and sex attributes to Class element
                if ($data['minAge'] !== null) {
                    $classNode->setAttribute('minAge', (string) $data['minAge']);
                }
                if ($data['sex'] !== null) {
                    $classNode->setAttribute('sex', $data['sex']);
                }

                // Find Id element within this Class element
                $idNodes = $xpath->query('./ns:Id', $classNode);
                if ($idNodes !== false) {
                    foreach ($idNodes as $idNode) {
                        /** @var DOMElement $idNode */
                        // Remove nested value and type elements if they exist
                        $valueElements = $xpath->query('./ns:value', $idNode);
                        if ($valueElements !== false) {
                            foreach ($valueElements as $element) {
                                if ($element instanceof \DOMNode) {
                                    $element->parentNode?->removeChild($element);
                                }
                            }
                        }

                        $typeElements = $xpath->query('./ns:type', $idNode);
                        if ($typeElements !== false) {
                            foreach ($typeElements as $element) {
                                if ($element instanceof \DOMNode) {
                                    $element->parentNode?->removeChild($element);
                                }
                            }
                        }

                        // Set text content of Id element
                        $idNode->textContent = $data['idValue'];

                        // Set type attribute on Id element
                        $idNode->setAttribute('type', $data['idType']);
                    }
                }
            }
            $classIndex++;
        }

        return $dom->saveXML() ?: '';
    }

    /**
     * Create a configured Symfony Serializer for XML and JSON handling.
     *
     * The serializer is initialized with array and object normalizers and a property
     * info extractor (PhpDoc + Reflection) to support denormalization and type
     * information when converting between XML/JSON and PHP objects.
     *
     * @return \Symfony\Component\Serializer\Serializer The configured serializer instance.
     */
    private function getSerializer(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer(null, null, null, $extractor)];

        return new Serializer($normalizers, $encoders);
    }
}

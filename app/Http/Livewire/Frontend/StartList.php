<?php

declare(strict_types=1);

namespace App\Http\Livewire\Frontend;

use App\Http\Components\Iofv3\Entities\ClassStart;
use App\Models\SportEventExport;
use App\Services\IofExportsService;
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;

class StartList extends Component
{
    public ?string $message;
    public ?string $search = null;
    public string|null $eventName = null;
    public ClassStart|array|null $classStart = null;
    public SportEventExport|null $sportEventExport = null;
    public array|null $eventAttributes = null;

    public function mount(string|null $slug): void
    {

        if (EmptyType::stringNotEmpty($slug)) {

            $xmlContent = null;
            $this->sportEventExport = SportEventExport::where('slug', '=', $slug)
                ->where('export_type', '=', SportEventExport::ENTRY_LIST_CATEGORY)
                ->first();

            // TODO $this->sportEventExport bude null tak by to melo skapat

            $iof = new IofExportsService();

            $resource = $iof->getResourceBySlug($slug);
            if (!is_null($resource)) {
                $xmlContent = Storage::disk('events')->get($resource);
            }

            if (!is_null($xmlContent)) {
                $startList = $iof->getStartList($xmlContent);
                $this->eventName = $startList->getEvent()->getName();

                $startListAttributes = $iof->getStartListAttributes($xmlContent);
                $this->eventAttributes[] = $startListAttributes;


//                $filterBy = 'D10';
//
//                $filteredArray = array_filter($startList->getClassStart(), function ($startList) use ($filterBy) {
//                    return ($startList->getClass()->getShortName() === $filterBy);
//                });
//
//                $this->classStart = $filteredArray;

                $this->classStart = $startList->getClassStart();
            }
        }
    }

    public function render(): View
    {
        return view('livewire.frontend.startlist', [
            'eventName' => $this->eventName,
            'eventAttributes' => $this->eventAttributes,
            'classStart' => $this->classStart,
            'sportEventExport' => $this->sportEventExport,
        ]);
    }
}

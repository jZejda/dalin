<?php

namespace App\Livewire\Frontend;

use App\Http\Components\Iofv3\Entities\ClassResult;
use App\Models\SportEventExport;
use App\Services\IofExportsService;
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;

class ResultList extends Component
{
    public ?string $message;
    public ?string $search = null;
    public string|null $eventName = null;
    public ClassResult|array|null $classResult = null;
    public SportEventExport|null $sportEventExport = null;
    public array|null $eventAttributes = null;

    public function mount(string|null $slug): void
    {

        if (EmptyType::stringNotEmpty($slug)) {

            $xmlContent = null;
            $this->sportEventExport = SportEventExport::where('slug', '=', $slug)
                ->where('export_type', '=', SportEventExport::RESULT_LIST_CATEGORY)
                ->first();

            $iof = new IofExportsService();

            $resource = $iof->getResourceBySlug($slug);
            if (!is_null($resource)) {
                $xmlContent = Storage::disk('events')->get($resource);
            }


            if (!is_null($xmlContent)) {
                $resultList = $iof->getResultList($xmlContent);
                $this->eventName = $resultList->getEvent()->getName();

                $resultListAttributes = $iof->getStartListAttributes($xmlContent);
                $this->eventAttributes[] = $resultListAttributes;

                $this->classResult = $resultList->getClassResult();
            }
        }
    }

    public function render(): View
    {
        return view('livewire.frontend.result-list', [
            'eventName' => $this->eventName,
            'eventAttributes' => $this->eventAttributes,
            'classResult' => $this->classResult,
            'sportEventExport' => $this->sportEventExport,
        ]);
    }
}

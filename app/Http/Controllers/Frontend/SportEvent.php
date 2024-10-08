<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SportEvent as ModelsSportEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class SportEvent extends Controller
{
    public function singleEvent(string|null $eventId): View
    {
        try {
            $event = ModelsSportEvent::query()->findOrFail($eventId);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }

        return view('pages.frontend.single-event', [
            'event' => $event,
        ]);
    }
}

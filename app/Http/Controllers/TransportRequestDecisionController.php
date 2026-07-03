<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TransportRequest;
use App\Services\TransportRequestService;
use Illuminate\Contracts\View\View;

class TransportRequestDecisionController extends Controller
{
    public function __construct(
        private readonly TransportRequestService $transportRequestService,
    ) {
    }

    public function __invoke(TransportRequest $transportRequest, string $decision): View
    {
        // Idempotence: opakované kliknutí na link už stav nemění.
        if (! $transportRequest->isPending()) {
            return $this->resultView($transportRequest, 'already_decided');
        }

        $result = $decision === 'approve'
            ? ($this->transportRequestService->approve($transportRequest) ? 'approved' : 'rejected_capacity')
            : ($this->transportRequestService->reject($transportRequest) ? 'rejected' : 'already_decided');

        return $this->resultView($transportRequest->refresh(), $result);
    }

    private function resultView(TransportRequest $transportRequest, string $result): View
    {
        return view('transport.decision-result', [
            'transportRequest' => $transportRequest,
            'result' => $result,
        ]);
    }
}

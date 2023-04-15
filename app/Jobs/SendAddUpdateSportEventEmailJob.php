<?php

namespace App\Jobs;

use App\Mail\AddUpdateSportEvent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAddUpdateSportEventEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $users = User::where('email', '=', 'zejda.jiri@gmail.com')->first();
        $email = new AddUpdateSportEvent();
        Mail::to([$users])->send($email);
    }
}

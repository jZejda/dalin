<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\DB;

class UsersInDebit extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Uživatele s nízkým kreditem',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.userInDebit',
            with: [
                'usersData' => $this->getContentData(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function getContentData(): array
    {
        $userData = [];
        $users = User::all();
        foreach ($users as $index => $user) {

            $usersAmountCount = DB::table('user_credits')
                ->where('user_id', '=', $user->id)
                ->select(['amount'])
                ->sum('amount');

            if($usersAmountCount < 0) {
                $userData[$index]['fullName'] = $user->name;
                $userData[$index]['email'] = $user->email;
                $userData[$index]['id'] = $user->id;
                $userData[$index]['debit'] = $usersAmountCount;
            }
        }

        return $userData;
    }
}

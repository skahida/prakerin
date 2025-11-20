<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class SaveUserIdToSession
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // Menyimpan user_id yang login ke dalam sesi
        // $event->user adalah objek user yang login
        Session::put('user_id', $event->user->id);
    }
}

<?php

namespace App\Listeners;

use App\Events\ApiSystemLogEvent;
use App\Models\Settings;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApiSystemLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserReferred  $event
     * @return void
     */
    public function handle(ApiSystemLogEvent $event)
    {
        //
        
        if (!is_null($event->user)) {
            $systemlog = new SystemLog;
            $systemlog->user_id = $event->user;
            $systemlog->message = $event->message;
            $systemlog->ip_address = user_ip();
            $systemlog->is_api = 1;
            $systemlog->save();
        }
    }
}

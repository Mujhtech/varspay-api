<?php

namespace App\Listeners;

use App\Events\SystemLogEvent;
use App\Models\Settings;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SystemLogEventListener
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
    public function handle(SystemLogEvent $event)
    {
        //
        
        if (!is_null($event->user)) {
            $systemlog = new SystemLog;
            $systemlog->user_id = $event->user;
            $systemlog->message = $event->message;
            $systemlog->ip_address = user_ip();
            $systemlog->user_browser = get_browsers();
            $systemlog->user_os_platform = get_os();
            $systemlog->user_device = get_device();
            $systemlog->save();
        }
    }
}

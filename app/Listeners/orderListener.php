<?php

namespace App\Listeners;
use App\Mail\orderEmail;
use Illuminate\Support\Facades\Mail;
use App\Events\orderEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class orderListener
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
     * @param  \App\Events\orderEvent  $event
     * @return void
     */
    public function handle(orderEvent $event)
    {
        
      Mail::send('orderView',['order'=>$event->order],function($massege) use($event) {
        $massege->to($event->order->user->email);
    });
    }
}

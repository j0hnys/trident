<?php 

namespace App\Td\Workflows\Events\Listeners;

use App\Td\Workflows\Events\Triggers\PrinterTrigger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrinterListener {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PrinterTrigger  $event
     * @return void
     */
    public function handle(PrinterTrigger $event)
    {
        echo "event fired???";
    }

    /**
     * Handle a job failure.
     *
     * @param  App\Td\Workflows\Events\Triggers\PrinterTrigger  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(PrinterTrigger $event, $exception)
    {
        echo "event failed";
    }
}
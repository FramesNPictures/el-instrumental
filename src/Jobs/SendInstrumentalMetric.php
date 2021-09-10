<?php

namespace Fnp\ElInstrumental\Jobs;

use Fnp\ElInstrumental\Connectors\InstrumentalConnector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInstrumentalMetric implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $command;

    /**
     * @param  string  $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function handle(InstrumentalConnector $connector)
    {
        $connector->send($this->command);
    }
}
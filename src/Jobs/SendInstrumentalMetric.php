<?php

namespace Fnp\ElInstrumental\Jobs;

use Fnp\ElInstrumental\Connectors\InstrumentalConnector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
        $lock = Cache::lock('foo', 5);

        try {
            $lock->block(2);
            $connector->send($this->command);
            $connector->disconnect();
            usleep(100000);
        } catch (LockTimeoutException $e) {
            $this->job->release(rand(1,5));
        } finally {
            optional($lock)->release();
        }
    }
}
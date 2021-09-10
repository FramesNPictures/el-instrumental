<?php

namespace Fnp\ElInstrumental;

use Carbon\Carbon;
use Fnp\ElInstrumental\Jobs\SendInstrumentalMetric;
use Illuminate\Foundation\Bus\PendingDispatch;

class Instrumental
{
    protected string $appName;
    protected bool   $enabled;

    /**
     * @param  string  $appName
     * @param  bool    $enabled
     */
    public function __construct(string $appName, bool $enabled = true)
    {
        $this->appName = $appName;
        $this->enabled = $enabled;
    }

    public function gauge(string $metric, mixed $value, int $timestamp = null, int $count = 1): PendingDispatch
    {
        if (!$timestamp) {
            $timestamp = Carbon::now()->timestamp;
        }

        $command = implode(' ', ['gauge', $metric, $value, (string) $timestamp, (string) $count]);
        $job     = new SendInstrumentalMetric($command);

        return dispatch($job);
    }

    public function notice(int $timestamp = null, int $duration = 0, ?string $message = null): PendingDispatch
    {
        if (!$timestamp) {
            $timestamp = Carbon::now()->timestamp;
        }

        $command = implode(' ', ['notice', (string) $timestamp, (string) $duration, $message]);
        $job     = new SendInstrumentalMetric($command);

        return dispatch($job);
    }
}
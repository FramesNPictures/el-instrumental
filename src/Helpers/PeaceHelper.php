<?php

namespace Fnp\ElInstrumental\Helpers;

use Fnp\ElInstrumental\Instrumental;

class PeaceHelper
{
    protected float      $start;
    protected float      $stop;
    private Instrumental $instrumental;
    private string       $metric;

    public function __construct(Instrumental $instrumental, string $metric)
    {
        $this->instrumental = $instrumental;
        $this->start        = microtime(true);
        $this->metric       = $metric;
    }

    public function done()
    {
        $this->stop = microtime(true);
        $duration   = $this->stop - $this->start;

        $this->instrumental->gauge($this->metric, $duration);
    }
}
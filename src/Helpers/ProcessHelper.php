<?php

namespace Fnp\ElInstrumental\Helpers;

use Carbon\Carbon;
use Fnp\ElInstrumental\Instrumental;

class ProcessHelper
{
    protected int        $start;
    protected int        $stop;
    private Instrumental $instrumental;
    private string       $name;

    public function __construct(Instrumental $instrumental, string $name)
    {
        $this->instrumental = $instrumental;
        $this->start        = Carbon::now()->timestamp;
        $this->name = $name;
    }

    public function done()
    {
        $this->stop = Carbon::now()->timestamp;
        $duration   = $this->stop - $this->start;

        $this->instrumental->notice($this->start, $duration, $this->name);
    }
}
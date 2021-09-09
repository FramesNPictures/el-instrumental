<?php

namespace Fnp\ElInstrumental;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleEventListeners;

class Module extends ElModule
{
    use ModuleEventListeners;

    public function defineEventListeners(): array
    {
        return [];
    }
}
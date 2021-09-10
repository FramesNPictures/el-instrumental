<?php

namespace Fnp\ElInstrumental;

use Fnp\ElInstrumental\Connectors\InstrumentalConnector;
use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfig;
use Fnp\ElModule\Features\ModuleSingletons;
use Illuminate\Support\Facades\Config;

class InstrumentalModule extends ElModule
{
    use ModuleConfig;
    use ModuleSingletons;

    public function defineConfigFiles(): array
    {
        return [
            'instrumental' => __DIR__.'/../config/instrumental.php',
        ];
    }

    public function defineSingletons(): array
    {
        $enabled = Config::get('instrumental.enabled');
        $apiKey  = Config::get('instrumental.api.key');
        $appName = Config::get('instrumental.app.name');

        return [
            InstrumentalConnector::class => fn() => new InstrumentalConnector($apiKey),
            Instrumental::class => fn() => new Instrumental($appName, $enabled),
        ];
    }
}
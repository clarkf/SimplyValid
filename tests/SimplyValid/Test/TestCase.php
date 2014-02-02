<?php
// @codingStandardsIgnoreFile
namespace SimplyValid\Test;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\Factory;
use Illuminate\Events\Dispatcher;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        parent::setup();

        $this->app = new Container();

        Facade::setFacadeApplication($this->app);

        $this->app['validator'] = $this->app->share(function ($app) {
            return new Factory($app['translator'], $app);
        });

        $this->app['events'] = $this->app->share(function ($app) {
            return new Dispatcher($app);
        });

        $this->app->instance('translator', $this->getTranslator());
    }

    protected function getTranslator()
    {
        return m::mock('Symfony\Component\Translation\TranslatorInterface')
            ->shouldIgnoreMissing();
    }
}

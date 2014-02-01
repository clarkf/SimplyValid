<?php
// @codingStandardsIgnoreFile
namespace SimplyValid\Test\Exception;

use SimplyValid\Test\TestCase;
use SimplyValid\Model;
use SimplyValid\Exception\InvalidModelException;

class InvalidModelExceptionTest extends TestCase
{
    public function testItIsARuntimeException()
    {
        $model = new ConcreteModel;
        $this->assertInstanceOf('RuntimeException', new InvalidModelException($model));
    }

    public function testItIsAMessageProvider()
    {
        $model = new ConcreteModel;
        $this->assertInstanceOf('Illuminate\Support\Contracts\MessageProviderInterface', new InvalidModelException($model));
    }

    public function testItProvidesModel()
    {
        $model = new ConcreteModel;
        $exception = new InvalidModelException($model);
        $this->assertSame($model, $exception->getModel());
    }

    public function testItProvidesErrors()
    {
        $model = new ConcreteModel;
        $exception = new InvalidModelException($model);
        $this->assertEquals($model->getMessageBag(), $exception->getMessageBag());
    }
}

class ConcreteModel extends Model {}

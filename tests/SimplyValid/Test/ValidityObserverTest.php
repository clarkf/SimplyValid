<?php
// @codingStandardsIgnoreFile
namespace SimplyValid\Test;

use SimplyValid\ValidityObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class ValidityObserverTest extends TestCase
{
    public function testItShouldNotAllowInvalidModelsToSave()
    {
        $observer = new ValidityObserver;
        $model = new ArrayModel;

        $this->assertFalse($observer->saving($model));
    }

    public function testItShouldAllowValidModelsToSave()
    {
        $observer = new ValidityObserver;
        $model = new ArrayModel;
        $model->name = "Hello World!";

        $this->assertTrue($observer->saving($model));
    }

    public function testItShouldGetRulesFromMethodIfProvided()
    {
        $observer = new ValidityObserver;
        $model = new MethodModel;

        $this->assertFalse($observer->saving($model));

        $model->name = "Hello World!";
        $this->assertTrue($observer->saving($model));
    }

    public function testItShouldTriggerErrorEvent()
    {
        $observer = new ValidityObserver;
        $model = new ArrayModel;
        $called = false;

        Event::listen("eloquent.errors: ".get_class($model), function () use (&$called) {
            $called = true;
        });

        $observer->saving($model);
        $this->assertTrue($called);
    }

    public function testErrorEventShouldPassMessageBag()
    {
        $observer = new ValidityObserver;
        $model = new ArrayModel;

        Event::listen(
            "eloquent.errors: ".get_class($model),
            function ($model, $errors) {
                $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
            }
        );

        $observer->saving($model);
    }

    public function testErrorEventShouldPassModel()
    {
        $observer = new ValidityObserver;
        $model = new ArrayModel;

        Event::listen(
            "eloquent.errors: ".get_class($model),
            function ($passedModel, $errors) use($model) {
                $this->assertSame($model, $passedModel);
            }
        );

        $observer->saving($model);
    }
}

class ArrayModel extends Model
{
    public $rules = array(
        'name' => 'required'
    );
}

class MethodModel extends Model
{
    public $rules = array();

    public function getValidationRules()
    {
        return array(
            'name' => 'required'
        );
    }
}

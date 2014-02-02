<?php
// @codingStandardsIgnoreFile

namespace SimplyValid\Test;

use PHPUnit_Framework_TestCase;
use SimplyValid\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\MessageBag;
use Mockery as m;

class ModelTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        ConcreteModel::flushEventListeners();
        ConcreteModel::boot();
    }

    public function testItShouldExtendEloquentModel()
    {
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", new ConcreteModel);
    }

    public function testItShouldImplementMessageProvider()
    {
        $this->assertInstanceOf("Illuminate\Support\Contracts\MessageProviderInterface", new ConcreteModel);
    }

    public function testItShouldAlwaysProvideMessageBag()
    {
        $model = new ConcreteModel;
        $this->assertInstanceOf("Illuminate\Support\MessageBag", $model->getMessageBag());
    }

    public function testErrorsShouldProxyToMessageBag()
    {
        $model = new ConcreteModel;
        $this->assertEquals($model->getMessageBag(), $model->errors());
    }

    public function testItShouldStoreErrors()
    {
        $model = new ConcreteModel;
        $bag = new MessageBag;


        $this->app['events']->fire('eloquent.errors: ' . get_class($model), array($model, $bag));

        $this->assertSame($bag, $model->getMessageBag());
    }

    public function testInvalidShouldNotSave()
    {
        $model = new ConcreteModel;

        $model->save();

        $this->assertEquals(0, $model->inserted);
    }

    public function testValidShouldSave()
    {
        $model = new ConcreteMOdel;
        $model->name = "Hello";

        $model->save();

        $this->assertEquals(1, $model->inserted);
    }
}

class ConcreteModel extends Model {
    protected $rules = array(
        'name' => 'required'
    );

    protected $fillable = array('name');

    public $inserted = 0;

    protected function updateTimestamps() {}

    protected function performInsert(Builder $query) {
        $this->inserted += 1;
        return true;
    }

    public function newQuery($excludeDeleted = true)
    {
        $mock = m::mock('Illuminate\Database\Eloquent\Builder');
        return $mock;
    }
}

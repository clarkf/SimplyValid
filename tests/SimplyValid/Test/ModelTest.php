<?php
// @codingStandardsIgnoreFile

namespace SimplyValid\Test;

use PHPUnit_Framework_TestCase;
use SimplyValid\Model;
use SimplyValid\Exception\InvalidModelException;
use Illuminate\Validation\Validator;
use Mockery as m;

class ModelTest extends TestCase
{
    public function testItShouldExtendEloquentModel()
    {
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", new ConcreteModel);
    }

    public function testItShouldImplementMessageProvider()
    {
        $this->assertInstanceOf("Illuminate\Support\Contracts\MessageProviderInterface", new ConcreteModel);
    }

    public function testItShouldBeAbleToGenerateAValidator()
    {
        $model = new ConcreteModel;
        $this->assertInstanceOf("Illuminate\Validation\Validator", $model->makeValidator());
    }

    public function testItShouldAllowCustomValidators()
    {
        $model = new CustomValidatorModel;
        $this->assertInstanceOf('SimplyValid\Test\CustomValidator', $model->makeValidator());
    }

    public function testValidatorShouldHaveProperAttributes()
    {
        $model = new ConcreteModel;
        $model->test = "hello";
        $validator = $model->makeValidator();
        $data = $validator->getData();

        $this->assertEquals('hello', $data['test']);
    }

    public function testValidatorShouldHaveRules()
    {
        $model = new ConcreteModel;
        $validator = $model->makeValidator();
        $rules = $validator->getRules();

        $this->assertEquals('required', $rules['name'][0]);
    }

    public function testValidatorShouldHaveDynamicRules()
    {
        $model = new DynamicModel;
        $validator = $model->makeValidator();
        $rules = $validator->getRules();

        $this->assertEquals('required', $rules['name'][0]);
    }

    public function testValidShouldReturnFalseIfInvalid()
    {
        $model = new ConcreteModel;
        $this->assertFalse($model->valid());
    }

    public function testInvalidShouldReturnTrueIfInvalid()
    {
        $model = new ConcreteModel;
        $this->assertTrue($model->invalid());
    }

    public function testValidShouldReturnTrueIfValid()
    {
        $model = new ConcreteModel(array('name' => 'hello'));
        $this->assertTrue($model->valid());
    }

    public function testInvalidShouldReturnFalseIfValid()
    {
        $model = new ConcreteModel(array('name' => 'hello'));
        $this->assertFalse($model->invalid());
    }

    public function testSavingWhenInvalidDoesntSave()
    {
        $model = new ConcreteModel;
        $this->assertTrue($model->invalid());
        $model->save();

        $this->assertEquals(0, $model->inserted);
    }

    public function testSavingWhenInvalidReturnsFalse()
    {
        $model = new ConcreteModel;
        $this->assertTrue($model->invalid());
        $this->assertFalse($model->save());
    }

    public function testSavingWhenValidWillSave()
    {
        $model = new ConcreteModel;
        $model->name = "Hello World";
        $this->assertTrue($model->valid());
        $model->save();

        $this->assertEquals(1, $model->inserted);
    }

    public function testSavingWhenValidReturnsTrue()
    {
        $model = new ConcreteModel;
        $model->name = "Hello World";
        $this->assertTrue($model->valid());
        $this->assertTrue($model->save());
    }

    /**
     * @expectedException SimplyValid\Exception\InvalidModelException
     */
    public function testSaveOrFailWhenInvalidThrows()
    {
        $model = new ConcreteModel;
        $model->saveOrFail();
    }

    public function testSaveOrFailWhenInvalidThrowsWithModel()
    {
        $model = new ConcreteModel;
        try {
            $model->saveOrFail();
        } catch (InvalidModelException $e) {
            $this->assertSame($model, $e->getModel());
        }
    }

    public function testSaveOrFailWhenValidDoesntThrow()
    {
        $model = new ConcreteModel(array('name' => 'hello'));
        $model->saveOrFail();
    }

    public function testGetMessageBagShouldBeAMessageBag()
    {
        $model = new ConcreteModel;

        $this->assertInstanceOf('Illuminate\Support\MessageBag', $model->getMessageBag());
    }

    public function testValidShouldProvideErrors()
    {
        $model = new ConcreteModel;
        $model->valid();
        $errors = $model->getMessageBag();

        $this->assertCount(1, $errors);
    }

    public function testErrorsShouldAlsoReturnErrors()
    {
        $model = new ConcreteMOdel;
        $model->valid();

        $this->assertSame($model->getMessageBag(), $model->errors());
    }
}

class ConcreteModel extends Model {
    protected $rules = array(
        'name' => 'required'
    );

    protected $fillable = array('name');

    public $inserted = 0;

    protected function updateTimestamps() {}

    protected function performInsert() {
        $this->inserted += 1;
        return true;
    }

    public function newQuery()
    {
        $mock = m::mock('Illuminate\Database\Eloquent\Builder');
        return $mock;
    }
}

class DynamicModel extends Model {
    protected function getValidationRules()
    {
        return array(
            'name' => 'required'
        );
    }
}

class CustomValidatorModel extends Model
{
    protected function buildValidator(array $attributes, array $rules)
    {
        return new CustomValidator();
    }
}

class CustomValidator extends Validator
{
    public function __construct() {}
}

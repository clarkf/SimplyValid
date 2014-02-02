<?php
namespace SimplyValid;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Validator;
use SimplyValid\Exception\InvalidModelException;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;

abstract class Model extends Eloquent implements MessageProviderInterface
{
    /** @var array The validation rules */
    protected $rules = array();

    /** @var MessageBag The error message bag */
    protected $errors;

    /**
     * Boot the model
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();


        static::registerModelEvent('errors', function ($model, $errors) {
            $model->setMessageBag($errors);
        });
        static::observe(new ValidityObserver);
    }

    /**
     * Get computed validation rules.  By default, we use $this->rules.
     *
     * @return array Validation rules
     */
    public function getValidationRules()
    {
        return $this->rules;
    }

    /**
     * Set the underlying error message bag
     *
     * @param MessageBag $bag The bag
     *
     * @return void
     */
    public function setMessageBag(MessageBag $bag)
    {
        $this->errors = $bag;
    }

    /**
     * Get the error message bag
     *
     * @return MessageBag the message bag
     */
    public function getMessageBag()
    {
        if (!isset($this->errors)) {
            return new MessageBag;
        }

        return $this->errors;
    }

    /**
     * Get the error message bag
     *
     * @return MessageBag the message bag
     *
     * @see getMessageBag()
     */
    public function errors()
    {
        return $this->getMessageBag();
    }
}

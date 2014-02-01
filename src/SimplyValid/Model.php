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
     * Get computed validation rules.  By default, we use $this->rules.
     *
     * @return array Validation rules
     */
    protected function getValidationRules()
    {
        return $this->rules;
    }

    /**
     * Build a validator
     *
     * @return Validator A validator instance
     *
     * @see buildValidator()
     */
    public function makeValidator()
    {
        return $this->buildValidator(
            $this->getAttributes(),
            $this->getValidationRules()
        );
    }

    /**
     * Build the validator.  Override this to use a custom class
     *
     * @param array $attributes The model's attributes
     * @param array $rules      The rules to enforce
     *
     * @return Validator A validator instance
     *
     * @see makeValidator()
     */
    protected function buildValidator(array $attributes, array $rules)
    {
        return Validator::make($attributes, $rules);
    }

    /**
     * Determine if the model is valid
     *
     * @return boolean true if the model is valid
     */
    public function valid()
    {
        $validator = $this->makeValidator();

        if ($validator->passes()) {
            return true;
        }

        $this->errors = $validator->errors();

        return false;
    }

    /**
     * Determine if the model is invalid
     *
     * @return boolean true if the model is invalid
     */
    public function invalid()
    {
        return !$this->valid();
    }

    /**
     * Save the model after validation
     *
     * @param array $options Options
     *
     * @return boolean true if the model was saved, false otherwise
     */
    public function save(array $options = array())
    {
        if ($this->valid()) {
            return parent::save($options);
        } else {
            return false;
        }
    }

    /**
     * Save the model, or throw an exception if it wasn't saved
     *
     * @param array $options Options
     *
     * @return void
     * @throws SimplyValid\Exception\InvalidModelException If the model is
     *     invalid
     */
    public function saveOrFail(array $options = array())
    {
        if (!$this->save($options)) {
            throw new InvalidModelException($this);
        }
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
}

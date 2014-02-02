<?php
namespace SimplyValid;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\MessageBag;

class ValidityObserver
{
    /**
     * Handle the before-save event on a model
     *
     * @param EloquentModel $model The model
     *
     * @return boolean `true` if the model should be saved.
     */
    public function saving(EloquentModel $model)
    {
        $validator = $this->getValidator($model);

        if ($validator->passes()) {
            return true;
        } else {
            $this->handleErrors($model, $validator->errors());
            return false;
        }
    }

    /**
     * Get the validator for a specific model
     *
     * @param EloquentModel $model The model
     *
     * @return Illuminate\Validation\Validator A validator
     */
    protected function getValidator(EloquentModel $model)
    {
        $attributes = $model->getAttributes();
        $rules = $this->getRules($model);

        return $this->makeValidator($attributes, $rules);
    }

    /**
     * Build a validator based on a set of attributes and rules
     *
     * @param array $attributes The model attributes
     * @param array $rules      The rules to enforce
     *
     * @return Illuminate\Validation\Validator The validator
     */
    protected function makeValidator(array $attributes, array $rules)
    {
        return Validator::make($attributes, $rules);
    }

    /**
     * Get the rules from a model
     *
     * @param EloquentModel $model The model
     *
     * @return array An array of rules
     */
    protected function getRules(EloquentModel $model)
    {
        if (method_exists($model, "getValidationRules")) {
            return $model->getValidationRules();
        }

        return $model->rules;
    }

    /**
     * Fire the appropriate events upon model failure
     *
     * @param EloquentModel $model  The model
     * @param MessageBag    $errors The error bag
     *
     * @return void
     */
    protected function handleErrors(EloquentModel $model, MessageBag $errors)
    {
        $event = 'eloquent.errors: ' . get_class($model);

        Event::fire($event, array($model, $errors));
    }
}

<?php
namespace SimplyValid\Exception;

use \RuntimeException;
use SimplyValid\Model;
use Illuminate\Support\Contracts\MessageProviderInterface;

class InvalidModelException extends RuntimeException implements MessageProviderInterface
{
    protected $model;

    /**
     * Construct a new InvalidModelException
     *
     * @param Model $model The model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the error Message Bag
     *
     * @return MessageBag The message bag
     *
     * @see getModel()
     * @see SimplyValid\Model::getMessageBag()
     */
    public function getMessageBag()
    {
        return $this->getModel()->getMessageBag();
    }
}

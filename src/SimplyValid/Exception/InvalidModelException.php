<?php
namespace SimplyValid\Exception;

use \RuntimeException;
use SimplyValid\Model;

class InvalidModelException extends RuntimeException
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }
}

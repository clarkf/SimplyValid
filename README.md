# SimplyValid

[![Build Status](https://travis-ci.org/clarkf/SimplyValid.png)](https://travis-ci.org/clarkf/SimplyValid)
[![Coverage Status](https://coveralls.io/repos/clarkf/SimplyValid/badge.png)](https://coveralls.io/r/clarkf/SimplyValid)

  Automagic validation for your [Eloquent](http://laravel.com/docs/eloquent)
models.


## Installation

Add `clarkf/simply-valid` to your `composer.json`:

```JSON
{
    "require": {
        "clarkf/simply-valid": "@dev-master"
    }
}
```

## Usage

Extend `SimplyValid\Model`:

```PHP
<?php

class MyModel extends SimplyValid\Model
{
    protected $rules = array(
        // validation rules here
    );
}
```

### save()

`save()` on your models will now return `false` if the model is invalid,
meaning that you can do cool things in your controllers like:

```PHP
public function store()
{
    $model = new MyModel(Input::get());

    if ($model->save()) {
        // Model is valid, and has been saved!
    } else {
        // Handle validation problems here
    }
}
```

### saveOrFail()

Want to throw an exception when saving a model?  Use `saveOrFail()`!

```PHP
public function store()
{
    $model = new MyModel(Input::get());
    $model->saveOrFail()
}
```

`saveOrFail` will raise a `SimplyValid\Exception\InvalidModelException`
if the model is not valid.

### errors()

Want to do something with your model's errors?  Get the error
`MessageBag` by calling `errors()`:

```PHP
<div class='field'>
    {{ Form::text('name') }}

    @if ($model->errors()->has('name'))
        <div class='field-error'>
            {{ $model->errors()->first('name') }}
        </div>
    @endif
</div>
```

## License

Released under the MIT License.

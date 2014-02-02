# SimplyValid

[![Build Status](https://travis-ci.org/clarkf/SimplyValid.png)](https://travis-ci.org/clarkf/SimplyValid)
[![Coverage Status](https://coveralls.io/repos/clarkf/SimplyValid/badge.png)](https://coveralls.io/r/clarkf/SimplyValid)
[![Latest Stable Version](https://poser.pugx.org/clarkf/simply-valid/version.png)](https://packagist.org/packages/clarkf/simply-valid)

  Automagic validation for your [Eloquent](http://laravel.com/docs/eloquent)
models.


## Installation

Add `clarkf/simply-valid` to your `composer.json`:

```JSON
{
    "require": {
        "clarkf/simply-valid": ">= 1.0"
    }
}
```

## Usage - Easy Mode

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

## Usage - Advanced Mode

At it's core, SimplyValid uses an observer to ensure that the model does
not save if it isn't valid.  If you don't want to, or can't extend
`SimplyValid\Model`, you can just use the observer!

```PHP
<?php

class MyModel extends Eloquent
{
    public $rules = array(
        // Validator rules here
    );

    public static function boot()
    {
        parent::boot();
        static::observe(new SimplyValid\ValidityObserver());
    }
}
```

### Getting the errors

If a model is determined to contain errors, an `errors` event is
emitted.  You can handle this event, and grab the errors by using
`registerModelEvent`:

```PHP
public static function boot()
{
    parent::boot();
    // ...
    static::registerModelEvent('errors', function (MyModel $model, MessageBag $errors) {
        // Store the errors somewhere useful, for example
        $model->errors = $errors;
    });
}
```

## Defining rules

SimplyValid doesn't know or care what your rules are-- it's up to
Laravel's Validator class.  It does allow for a bit of flexibility,
though:  a public method (`getValidationRules`) takes precedence over
the public attribute (`$rules`).  This is nice if you need to compute
rules:

```PHP
class User extends SimplyValid\Model
{
    public function getValidationRules()
    {
        $rules = array();

        if (!$this->exists) {
            // User has not yet been created, so a password is required
            $rules['password'] = array('required');
        }

        return $rules;
    }
}
```

## License

The MIT License (MIT)

Copyright (c) 2014 Clark Fischer

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"), to
deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
sell copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
IN THE SOFTWARE.

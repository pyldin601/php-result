# php-result
[![Build Status](https://travis-ci.org/pldin601/php-result.svg?branch=master)](https://travis-ci.org/pldin601/php-result)
[![Code Climate](https://codeclimate.com/github/pldin601/php-result/badges/gpa.svg)](https://codeclimate.com/github/pldin601/php-result)
[![Test Coverage](https://codeclimate.com/github/pldin601/php-result/badges/coverage.svg)](https://codeclimate.com/github/pldin601/php-result/coverage)
[![Issue Count](https://codeclimate.com/github/pldin601/php-result/badges/issue_count.svg)](https://codeclimate.com/github/pldin601/php-result)

Result is an abstraction used for returning and propagating errors.
It has two variants: `ok`, representing success and containing a value,
and `fail`, representing error and containing an error value.
Inspired by Rust's module `std::result`.

## Functions
```php
use Result as R;

// Create result by hands
$ok = R\ok($value);
$fail = R\fail($value);

// Create result from results of execution a callable

// If callable throws an exception
R\tryCatch($callable, $exceptionTransformCallable, $value);

// If callable returns NULL on fail
R\notNull($callable);

// In any other case
R\resultify($callable);

// Check whether result is ok or fail
R\isOk($result);
R\isFail($result);

// Invoke callable if result is ok or fail
R\ifOk($result, $callable);
R\ifFail($result, $callable);

// Raise value from result or throw exception on fail
R\getOrThrow($result, $exceptionClass);

// Work with pipelines
R\bind($result, $callable);
R\pipeline(...$callables);
```

## Pipeline example
```php
use Result as R;


$readFile = function($filename) {
    return R\with($filename, 'file_exists', 'file_get_contents', function () {
        return "Can't read the file.";
    });
}

$proceedFile = function($content) {
    $transform = function ($exception) {
        return $exception->getMessage();
    };

    return R\tryCatch('doSomethingWithContent', $transform, $content);
}

$saveFile = function($filename) {
    return function ($content) use ($filename) {
        $bytesWritten = file_put_contents($filename, $content);

        return $bytesWritten === false
            ? R\fail("Can't save the file!")
            : R\ok();
    }
}

$pipeline = R\pipeline($readFile, $proceedFile, $saveFile('/tmp/output_file'));

$result = $pipeline('/tmp/input_file');

R\ifOk($result, function () {
    echo 'File successfully saved.';
});


```
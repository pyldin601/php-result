# php-result
[![Build Status](https://travis-ci.org/pldin601/php-result.svg?branch=master)](https://travis-ci.org/pldin601/php-result)
[![Code Climate](https://codeclimate.com/github/pldin601/php-result/badges/gpa.svg)](https://codeclimate.com/github/pldin601/php-result)
[![Test Coverage](https://codeclimate.com/github/pldin601/php-result/badges/coverage.svg)](https://codeclimate.com/github/pldin601/php-result/coverage)
[![Issue Count](https://codeclimate.com/github/pldin601/php-result/badges/issue_count.svg)](https://codeclimate.com/github/pldin601/php-result)

Result is an abstraction that can be used for returning and propagating errors.
Result can be `ok`, representing success and containing a value,
or `fail`, representing error and containing an error value.

Inspired by Rust's module `std::result`.

## Functions
```php
use Result as R;

R\ok('foo');
R\fail($value);

R\resultify($callable, ...$args);
R\notNull($callable, ...$args);
R\tryCatch($callable, $exceptionTransformCallable, ...$args);

R\isOk($result);
R\isFail($result);

R\ifOk($result, $callable);
R\ifFail($result, $callable);

R\getOrThrow($result, $exceptionClass);

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

# php-result
[![Build Status](https://travis-ci.org/pldin601/php-result.svg?branch=master)](https://travis-ci.org/pldin601/php-result)
[![Code Climate](https://codeclimate.com/github/pldin601/php-result/badges/gpa.svg)](https://codeclimate.com/github/pldin601/php-result)
[![Test Coverage](https://codeclimate.com/github/pldin601/php-result/badges/coverage.svg)](https://codeclimate.com/github/pldin601/php-result/coverage)
[![Issue Count](https://codeclimate.com/github/pldin601/php-result/badges/issue_count.svg)](https://codeclimate.com/github/pldin601/php-result)

Result is an abstraction used for returning and propagating errors.
It has two variants: `ok`, representing success and containing a value,
and `fail`, representing error and containing an error value.
Inspired by Rust's module `std::result`.

## Functions:
```php
// Create result by hands
$ok = \Result\ok($value);
$fail = \Result\fail($value);

// Create result from results of execution a callable

// If callable throws an exception
\Result\tryCatch($callable, $exceptionTransformCallable, $value);

// If callable returns NULL on fail
\Result\notNull($callable);

// In any other case
\Result\resultify($callable);

// Check whether result is ok or fail
\Result\isOk($result);
\Result\isFail($result);

// Invoke callable if result is ok or fail
\Result\ifOk($result, $callable);
\Result\ifFail($result, $callable);

// Raise value from result or throw exception on fail
\Result\getOrThrow($result, $exceptionClass);

// Work with transformers
\Result\bind($result, $callable);
\Result\pipeline(...$callables);
```

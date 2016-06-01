# php-result
[![Build Status](https://travis-ci.org/pldin601/php-result.svg?branch=master)](https://travis-ci.org/pldin601/php-result)
[![Code Climate](https://codeclimate.com/github/pldin601/php-result/badges/gpa.svg)](https://codeclimate.com/github/pldin601/php-result)
[![Test Coverage](https://codeclimate.com/github/pldin601/php-result/badges/coverage.svg)](https://codeclimate.com/github/pldin601/php-result/coverage)
[![Issue Count](https://codeclimate.com/github/pldin601/php-result/badges/issue_count.svg)](https://codeclimate.com/github/pldin601/php-result)

Abstraction that represents ok/error result. Functional alternative to exceptions.
Fully built on functions.

## Functions:
```php
// Produce ok/fail result
$ok = \Result\ok($value);
$fail = \Result\fail($value);

// Wrap results of callable into result
\Result\tryCatch($callable, $exceptionTransformCallable, $value);
\Result\notNull($callable);
\Result\resultify($callable);

// Check wheither result is ok/fail
\Result\isOk($result);
\Result\isFail($result);

// Evaluate callable if result is ok/fail
\Result\ifOk($result, $callable);
\Result\ifFail($result, $callable);

// Work with transformers
\Result\bind($result, $callable);
\Result\pipeline(...$callables);
```

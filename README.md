# php-result
[![Build Status](https://travis-ci.org/pldin601/php-result.svg?branch=master)](https://travis-ci.org/pldin601/php-result)
[![Code Climate](https://codeclimate.com/github/pldin601/php-result/badges/gpa.svg)](https://codeclimate.com/github/pldin601/php-result)
[![Test Coverage](https://codeclimate.com/github/pldin601/php-result/badges/coverage.svg)](https://codeclimate.com/github/pldin601/php-result/coverage)
[![Issue Count](https://codeclimate.com/github/pldin601/php-result/badges/issue_count.svg)](https://codeclimate.com/github/pldin601/php-result)

Abstraction that represents ok/error result. Functional alternative to exceptions.
Fully built on functions.

## Examples:
```php
\Result\ok($value);
\Result\fail($value);
\Result\isOk($result);
\Result\isFail($result);
\Result\ifOk($result, $callable);
\Result\ifFail($result, $callable);
\Result\tryCatch($callable, $exceptionTransformCallable, $value);
\Result\bind($result, $callable);
\Result\map($result, $callable);
\Result\pipeline(...$callables);
```

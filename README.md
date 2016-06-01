# php-result
Abstraction that represents ok/error result. Functional alternative to exceptions.
Fully built on using functions.

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

<?php

namespace Result;

const RESULT_OK = 'ok';
const RESULT_FAIL = 'fail';

const REDUCE_FUNCTION = 'Result\bind';
const IDENTITY_FUNCTION = 'Result\Util\id';

/**
 * Internal function that creates Result with given type and value.
 *
 * @param string $type
 * @param mixed $value
 * @return \Closure
 */
function create($type, $value)
{
    return function ($callable) use ($type, $value) {
        return $callable($type, $value);
    };
}

/**
 * Creates `ok` Result with given value.
 *
 * @param mixed $value
 * @return \Closure
 */
function ok($value = null)
{
    return create(RESULT_OK, $value);
}

/**
 * Creates `fail` Result with given value.
 *
 * @param mixed $value
 * @return \Closure
 */
function fail($value = null)
{
    return create(RESULT_FAIL, $value);
}

/**
 * Returns Result's type.
 *
 * @param callable $result
 * @return mixed
 */
function typeOf(callable $result)
{
    return $result(function ($type, $value) {
        return $type;
    });
}

/**
 * Returns Result's value.
 *
 * @param callable $result
 * @return mixed
 */
function valueOf(callable $result)
{
    return $result(function ($type, $value) {
        return $value;
    });
}

/**
 * Returns string representation of Result.
 *
 * @param callable $result
 * @return string
 */
function toStr(callable $result)
{
    return $result(function ($type, $value) {
        return sprintf("%s(%s)", $type, $value);
    });
}

/**
 * Calls $callable and wraps return value into `ok` Result.
 *
 * @param callable $callable
 * @param array ...$args
 * @return \Closure
 */
function resultify(callable $callable, ...$args)
{
    return ok($callable(...$args));
}

/**
 * Calls $callable and wraps return value as `ok` Result.
 * If $callable threw an exception, it will be caught
 * and returned as `fail` Result.
 *
 * You can optionally provide an exception transform
 * function to cast the exception to the necessary form.
 *
 * @param callable $callable
 * @param callable|null $exceptionTransform
 * @param array ...$args
 * @return \Closure
 */
function tryCatch(callable $callable, callable $exceptionTransform = IDENTITY_FUNCTION, ...$args)
{
    try {
        return ok($callable(...$args));
    } catch (\Exception $exception) {
        return fail($exceptionTransform($exception));
    }
}

/**
 * Does the same as resultify function, but if your
 * $callable returns `null` value it will be interpreted
 * as `fail`.
 *
 * @param callable $callable
 * @param array ...$args
 * @return \Closure
 */
function notNull(callable $callable, ...$args)
{
    $result = $callable(...$args);

    return is_null($result) ? fail() : ok($result);
}

/**
 * Returns whether Result is `ok`.
 *
 * @param callable $result
 * @return bool
 */
function isOk(callable $result)
{
    return typeOf($result) == RESULT_OK;
}

/**
 * Returns whether Result is `fail`.
 *
 * @param callable $result
 * @return bool
 */
function isFail(callable $result)
{
    return typeOf($result) == RESULT_FAIL;
}

/**
 * Binds $callable to `ok` Result.
 *
 * @param callable $result
 * @param callable $callable
 * @return callable
 */
function bind(callable $result, callable $callable)
{
    if (isFail($result)) {
        return $result;
    }
    return $callable(valueOf($result));
}

/**
 * Creates pipeline with functions that returns Result.
 *
 * @param array ...$callables
 * @return \Closure
 */
function pipeline(...$callables)
{
    return function ($initialValue = null) use ($callables) {
        return array_reduce($callables, REDUCE_FUNCTION, ok($initialValue));
    };
}

/**
 * Calls $callable if Result is `ok`.
 *
 * @param callable $result
 * @param callable $callable
 */
function ifOk(callable $result, callable $callable)
{
    if (isOk($result)) {
        $callable(valueOf($result));
    }
}

/**
 * Calls $callable if Result is `fail`.
 *
 * @param callable $result
 * @param callable $callable
 */
function ifFail(callable $result, callable $callable)
{
    if (isFail($result)) {
        $callable(valueOf($result));
    }
}

/**
 * Raises value from `ok` Result or throws an exception on `fail`.
 *
 * @param callable $result
 * @param string $exceptionClass
 * @return mixed
 */
function getOrThrow(callable $result, $exceptionClass = \Exception::class)
{
    if (isOk($result)) {
        return valueOf($result);
    }
    throw new $exceptionClass(valueOf($result));
}

/**
 * Tests $value using $test callable.
 *
 * If the test returns true, it applies $ifTrue to the $value
 * and returns result wrapped into `ok` Result.
 *
 * If the test returns false, it applies $ifFalse to the $value
 * and returns result wrapped into `fail` result.
 *
 * @param mixed $value
 * @param callable $test
 * @param callable $ifTrue
 * @param callable $ifFalse
 * @return \Closure
 */
function with($value, callable $test, callable $ifTrue, callable $ifFalse)
{
    if ($test($value)) {
        return ok($ifTrue($value));
    }

    return fail($ifFalse($value));
}

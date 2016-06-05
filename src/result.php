<?php

namespace Result;

const RESULT_OK = 'ok';
const RESULT_FAIL = 'fail';

const VALUE_METHOD = 'value';
const TYPE_METHOD = 'type';

const REDUCE_FUNCTION = 'Result\bind';

/**
 * Creates `ok` result with wrapped value.
 *
 * @param $value
 * @return \Closure
 */
function ok($value = null)
{
    return function ($method) use ($value) {
        switch ($method) {
            case VALUE_METHOD:
                return $value;
            case TYPE_METHOD:
                return RESULT_OK;
            default:
                throw new \BadMethodCallException;
        }
    };
}

/**
 * Creates `fail` result with wrapped $value.
 *
 * @param $value
 * @return \Closure
 */
function fail($value = null)
{
    return function ($method) use ($value) {
        switch ($method) {
            case VALUE_METHOD:
                return $value;
            case TYPE_METHOD:
                return RESULT_FAIL;
            default:
                throw new \BadMethodCallException;
        }
    };
}

/**
 * If your $callable throws an exception on failure, it
 * wraps the exception into `fail` result. Otherwise it
 * returns `ok` with result of $callable.
 *
 * You can optionally provide an exception transformation
 * function to cast the exception to the necessary form.
 *
 * @param callable $callable
 * @param callable|null $exceptionTransform
 * @param array ...$value
 * @return \Closure
 */
function tryCatch(callable $callable, callable $exceptionTransform = null, ...$value)
{
    try {
        return ok($callable(...$value));
    } catch (\Exception $exception) {
        if (is_callable($exceptionTransform)) {
            $transformedException = $exceptionTransform($exception);
            return fail($transformedException);
        }
        return fail($exception);
    }
}

/**
 * It invokes a $callable and wraps it's returning value
 * into `ok` type of result.
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
 * If your function returns NULL in case of fail,
 * it returns a `fail` result with no value. In other
 * case it behaves like `resultify` or `tryCatch`.
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
 * Returns type of result.
 *
 * @param callable $result
 * @return mixed
 */
function typeOf(callable $result)
{
    return $result('type');
}

/**
 * Returns value of result.
 *
 * @param callable $result
 * @return mixed
 */
function valueOf(callable $result)
{
    return $result('value');
}

/**
 * Returns whether result is `ok`.
 *
 * @param callable $result
 * @return bool
 */
function isOk(callable $result)
{
    return typeOf($result) == RESULT_OK;
}

/**
 * Returns whether result is `fail`.
 *
 * @param callable $result
 * @return bool
 */
function isFail(callable $result)
{
    return typeOf($result) == RESULT_FAIL;
}

/**
 * Binds $callable to `ok` result.
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
 * Creates pipeline with functions.
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
 * Invokes $callable if result is `ok`.
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
 * Invokes $callable if result is `fail`.
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
 * Raises value from `ok` result or throws an exception on `fail`.
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

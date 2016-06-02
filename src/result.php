<?php

namespace Result;

const RESULT_OK = 'ok';
const RESULT_FAIL = 'fail';

/**
 * Creates ok result with $value.
 *
 * @param $value
 * @return \Closure
 */
function ok($value = null)
{
    return function ($method) use ($value) {
        switch ($method) {
            case 'value':
                return $value;
            case 'type':
                return RESULT_OK;
            default:
                throw new \BadMethodCallException;
        }
    };
}

/**
 * Creates fail result with $value.
 *
 * @param $value
 * @return \Closure
 */
function fail($value = null)
{
    return function ($method) use ($value) {
        switch ($method) {
            case 'value':
                return $value;
            case 'type':
                return RESULT_FAIL;
            default:
                throw new \BadMethodCallException;
        }
    };
}

/**
 * Evaluates $callable and wraps result into ok or
 * returns fail if exception was thrown.
 *
 * @param callable $callable
 * @param callable|null $exceptionTransform
 * @param null $value
 * @return \Closure
 */
function tryCatch(callable $callable, callable $exceptionTransform = null, $value = null)
{
    try {
        return ok($callable($value));
    } catch (\Exception $exception) {
        return is_null($exceptionTransform)
            ? fail($exception)
            : fail($exceptionTransform($exception));
    }
}

/**
 * Executes $callable and wraps result into ok.
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
 * Executes $callable and wraps result into ok
 * if result is not null. Otherwise returns fail.
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
 * Returns whether result is ok.
 *
 * @param callable $result
 * @return bool
 */
function isOk(callable $result)
{
    return typeOf($result) == RESULT_OK;
}

/**
 * Returns whether result is fail.
 *
 * @param callable $result
 * @return bool
 */
function isFail(callable $result)
{
    return typeOf($result) == RESULT_FAIL;
}

/**
 * Binds $callable to ok result.
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
 * Creates pipeline with transform functions.
 *
 * @param array ...$callables
 * @return \Closure
 */
function pipeline(...$callables)
{
    return function ($initialValue = null) use ($callables) {
        return array_reduce($callables, 'Result\bind', ok($initialValue));
    };
}

/**
 * Evaluates $callable if result is ok.
 *
 * @param $result
 * @param callable $callable
 */
function ifOk($result, callable $callable)
{
    if (isOk($result)) {
        $callable(valueOf($result));
    }
}

/**
 * Evaluates $callable if result is fail.
 *
 * @param $result
 * @param callable $callable
 */
function ifFail($result, callable $callable)
{
    if (isFail($result)) {
        $callable(valueOf($result));
    }
}

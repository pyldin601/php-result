<?php

namespace Result;

const RESULT_OK = 'ok';
const RESULT_ERROR = 'error';

/**
 * Creates success result with $value.
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
 * Creates error result with $value.
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
                return RESULT_ERROR;
            default:
                throw new \BadMethodCallException;
        }
    };
}

/**
 * Evaluates $callable and returns success with result or
 * returns error if $callable threw an exception.
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
 * Returns whether result is success.
 *
 * @param callable $result
 * @return bool
 */
function isOk(callable $result)
{
    return typeOf($result) == RESULT_OK;
}

/**
 * Returns whether result is error.
 *
 * @param callable $result
 * @return bool
 */
function isFail(callable $result)
{
    return typeOf($result) == RESULT_ERROR;
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
 * Maps $callable to ok result.
 *
 * @param callable $result
 * @param callable $callable
 * @return callable|\Closure
 */
function map(callable $result, callable $callable)
{
    if (isFail($result)) {
        return $result;
    }
    return ok($callable(valueOf($result)));
}

/**
 * Creates pipeline.
 *
 * @param array ...$callables
 * @return \Closure
 */
function pipeline(...$callables)
{
    return function ($initialValue) use ($callables) {
        return array_reduce($callables, 'Result\bind', ok($initialValue));
    };
}

/**
 * Evaluates $callable if result is success.
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
 * Evaluates $callable if result is error.
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

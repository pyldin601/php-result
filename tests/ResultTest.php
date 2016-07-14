<?php

namespace Tests;

use Result as R;

use const Result\RESULT_FAIL;
use const Result\RESULT_OK;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessResult()
    {
        $result = R\ok('foo');

        $this->assertEquals('foo', R\valueOf($result));
        $this->assertEquals(RESULT_OK, R\typeOf($result));
        $this->assertTrue(R\isOk($result));

        $flag = false;

        R\ifOk($result, function ($value) use (&$flag) {
            $this->assertEquals('foo', $value);
            $flag = true;
        });

        $this->assertTrue($flag);
    }

    public function testErrorResult()
    {
        $result = R\fail('foo');

        $this->assertEquals('foo', R\valueOf($result));
        $this->assertEquals(RESULT_FAIL, R\typeOf($result));
        $this->assertTrue(R\isFail($result));

        $flag = false;

        R\ifFail($result, function ($value) use (&$flag) {
            $this->assertEquals('foo', $value);
            $flag = true;
        });

        $this->assertTrue($flag);
    }

    public function testToString()
    {
        $ok = R\ok('good');
        $fail = R\fail('bad');

        $this->assertEquals('ok(good)', R\toStr($ok));
        $this->assertEquals('fail(bad)', R\toStr($fail));
    }

    public function testResultify()
    {
        $result = R\resultify(function () {
            return 'hello';
        });
        $this->assertTrue(R\isOk($result));
        $this->assertEquals('hello', R\valueOf($result));
    }

    public function testNotNull()
    {
        $result = R\notNull(function () {
            return 'foo';
        });
        $this->assertTrue(R\isOk($result));
        $this->assertEquals('foo', R\valueOf($result));

        $result = R\notNull(function () {
            return null;
        });
        $this->assertTrue(R\isFail($result));
        $this->assertNull(R\valueOf($result));
    }

    public function testTryCatch()
    {
        $result = R\tryCatch(function () {
            return 'foo';
        });


        $this->assertTrue(R\isOk($result));
        $this->assertEquals('foo', R\valueOf($result));

        $result = R\tryCatch(function () {
            throw new \Exception('bar');
        });

        $this->assertTrue(R\isFail($result));
        $this->assertInstanceOf(\Exception::class, R\valueOf($result));

        $result = R\tryCatch(function () {
            throw new \Exception('baz');
        }, function (\Exception $exception) {
            return $exception->getMessage();
        });

        $this->assertTrue(R\isFail($result));
        $this->assertEquals('baz', R\valueOf($result));
    }

    public function testBind()
    {
        $bindFunction = function ($value) {
            return $value != 0
                ? R\ok(100 / $value)
                : R\fail('Division by zero');
        };

        $result = R\bind(R\ok(5), $bindFunction);

        $this->assertTrue(R\isOk($result));
        $this->assertEquals(20, R\valueOf($result));

        $result = R\bind(R\fail('foo'), $bindFunction);

        $this->assertTrue(R\isFail($result));
        $this->assertEquals('foo', R\valueOf($result));

        $result = R\bind(R\ok(0), $bindFunction);

        $this->assertTrue(R\isFail($result));
        $this->assertEquals('Division by zero', R\valueOf($result));
    }

    public function testPipeline()
    {
        $f1 = function ($value) {
            return R\ok($value * 2);
        };

        $f2 = function () {
            return R\fail('Error');
        };

        $f3 = function ($value) {
            return R\ok($value + 10);
        };

        $pipeline = R\pipeline($f1, $f3);

        $result = $pipeline(5);

        $this->assertTrue(R\isOk($result));
        $this->assertEquals(20, R\valueOf($result));

        $pipeline = R\pipeline($f1, $f2, $f3);

        $result = $pipeline(5);

        $this->assertTrue(R\isFail($result));
        $this->assertEquals('Error', R\valueOf($result));
    }

    public function testGetOrThrow()
    {
        $ok = R\ok('foo');
        $fail = R\fail('bar');

        $value = R\getOrThrow($ok);

        $this->assertEquals('foo', $value);

        try {
            R\getOrThrow($fail);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('bar', $e->getMessage());
        }
    }
    
    public function testWithValue()
    {
        $filename = __DIR__."/fixtures/somefile";
        $ifFalse = function ($filename) {
            return "File $filename not exists.";
        };

        $one = R\with($filename, 'file_exists', 'file_get_contents', $ifFalse);
        $two = R\with('/some_non_existent_file', 'file_exists', 'file_get_contents', $ifFalse);

        $this->assertTrue(R\isOk($one));
        $this->assertEquals('some file content', R\valueOf($one));

        $this->assertTrue(R\isFail($two));
        $this->assertContains('not exists', R\valueOf($two));
    }
}

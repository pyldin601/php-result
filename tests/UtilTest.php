<?php

namespace Tests;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider identityDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testIdentity($value, $expected)
    {
        $this->assertEquals($expected, \Result\Util\id($value));
    }

    /**
     * @return array
     */
    public function identityDataProvider()
    {
        return [
            ['foo', 'foo'],
            ['bar', 'bar']
        ];
    }
}

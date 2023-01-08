<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\tests;

use Sura\Http\Request;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testInput()
    {
        $instance_1 = (new Request())->textFilter('qwerty');
        $instance_2 = (new Request())->textFilter('<div>qwerty' . PHP_EOL . 't`tt<div>');
        $instance_3 = (new Request())->textFilter('<div>t`tt<div>', 2500, true);
        self::assertTrue(true);
    }

    public function testInt()
    {
        $instance = (new Request())->int('qwerty');
        self::assertEquals(0, $instance);
    }

    public function testRequestFilter()
    {
        $instance = (new Request())->filter('qwerty');
        self::assertEquals('', $instance);
    }
}

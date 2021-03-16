<?php

namespace Sura\Libs;

use Sura\Libs\Registry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{

    protected string $res;

    public function testSet()
    {
        $this->res = 'Андрей';
        $res = Registry::set('Андрей', 'Андрей');
        $this->assertNotFalse($res);
    }

    public function testExists()
    {
        $res = Registry::exists('Андрей');
        $this->assertNotFalse($res);
    }

    public function testGet()
    {
        Registry::set('Андрей', 'Андрей');
        echo Registry::get('Андрей');
        $this->expectOutputString('Андрей');
    }


}

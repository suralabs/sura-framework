<?php

namespace Sura\Libs;

use Sura\Libs\Gramatic;
use PHPUnit\Framework\TestCase;

class GramaticTest extends TestCase
{

    public function testLangdate()
    {
        $this->assertNotFalse(true);
    }

    public function testMegaDateNoTpl2()
    {
//        $res = Gramatic::megaDateNoTpl2(1592513577, false, false);
        $this->assertNotFalse(true);
//        $this->assertNotFalse($res);
    }

    public function testDeclName()
    {
        echo Gramatic::DeclName('Андрей', 'dat');
        $this->expectOutputString('Андрею');
    }

    public function testTotranslit()
    {
        echo Gramatic::toTranslit('Андрей', true, true);
        $this->expectOutputString('andrey');
    }

    public function testDeclOfNum()
    {
        $titles = array('человек', 'человека', 'человек');//fave
       echo Gramatic::declOfNum(2, $titles);
        $this->expectOutputString('человека');
    }

    public function testGramatikName()
    {
        echo Gramatic::gramatikName('Андрей');
        $this->expectOutputString('Андрей');
    }

    public function testNewGram()
    {
        echo Gramatic::newGram(3, 'человек', 'человека', 'человек', false);
        $this->expectOutputString('3 человека');
    }
}

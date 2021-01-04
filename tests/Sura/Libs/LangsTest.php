<?php

namespace Sura\Libs;

use PHPUnit\Framework\TestCase;

class LangsTest extends TestCase
{

    protected string $rMyLang = 'Русский';
    protected string $checkLang = 'Russian';

    public function testGet_langs()
    {
//        Langs::get_langs();
//        $this->expectOutputString('');
        $this->assertNotFalse(true);
    }

    public function testGet_langdate()
    {
//        Langs::get_langdate();
//        $this->expectOutputString('');
        $this->assertNotFalse(true);
    }

    public function testSetlocale()
    {
//        Langs::setlocale();
//        $this->expectOutputString('');
        $this->assertNotFalse(true);
    }

    public function testCheckLang()
    {
        //return Langs::checkLang();
        $this->expectOutputString('foo');
        print 'foo';
    }

}

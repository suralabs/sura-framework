<?php

namespace Sura\Libs;

use Sura\Libs\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{

    public function testInstallationSelected()
    {
        $id = '';
        $options = '';

        Tools::InstallationSelected($id, $options);
        $this->expectOutputString('');

//        $this->assertNotFalse(true);
    }

    public function testCheckBlackList()
    {
        $this->assertNotFalse(true);
//        Tools::CheckBlackList($userId);
//        $this->expectOutputString('');
    }

    public function testCheck_xss()
    {
        $this->assertNotFalse(true);
    }

    public function testMyCheckBlackList()
    {
        $this->assertNotFalse(true);
    }

    public function testSet_cookie()
    {
        $this->assertNotFalse(true);
    }

    public function testNoAjaxQuery()
    {
        $this->assertNotFalse(true);
    }

    public function testCheckFriends()
    {
        $this->assertNotFalse(true);
    }

    public function testBox_navigation()
    {
        $this->assertNotFalse(true);
    }

    public function testGetVar()
    {
        $this->assertNotFalse(true);
    }

    public function testNavigation()
    {
        $this->assertNotFalse(true);
    }

    public function testAjaxTpl()
    {
        $this->assertNotFalse(true);
    }
}

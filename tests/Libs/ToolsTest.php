<?php

namespace Libs;

use Sura\Libs\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{

    public function testInstallationSelectedNew()
    {
        $id = '';
        $options = '';

        Tools::InstallationSelectedNew($id, $options);
        $this->expectOutputString('');
    }

    public function testCheckBlackList()
    {
        Tools::CheckBlackList($userId);
        $this->expectOutputString('');
    }

    public function testCheck_xss()
    {

    }

    public function testMyCheckBlackList()
    {

    }

    public function testSet_cookie()
    {

    }

    public function testNoAjaxQuery()
    {

    }

    public function testCheckFriends()
    {

    }

    public function testBox_navigation()
    {

    }

    public function testGetVar()
    {

    }

    public function testNavigation()
    {

    }

    public function testAjaxTpl()
    {

    }
}

<?php


namespace Sura\Contracts;


interface ModuleInterface
{
    public function user_info();

    public function logged();

    public function db();

    public function get_langs();

}
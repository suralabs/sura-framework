<?php

declare(strict_types=1);

namespace Sura\Contracts;


/**
 *
 */
interface ModuleInterface
{
    /**
     * @return mixed
     */
    public function userInfo() : mixed;

    /**
     * @return bool|null
     */
    public function logged() : bool|null;

    /**
     * @return array
     */
    public function getLangs() : array;

}
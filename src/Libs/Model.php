<?php

namespace Sura\Libs;

use Sura\Libs\Db;

abstract class Model
{
    /**
     * Model constructor.
     *
     * Получение экземпляра класса.
     * Если он уже существует, то возвращается, если его не было,
     * то создаётся и возвращается (паттерн Singleton)
     */
    public function __construct(
        protected Db $db
    )
    {
        $this->db = Db::getDB();
    }
}
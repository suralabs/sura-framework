<?php

declare(strict_types=1);

namespace Sura\Libs;


class Status
{
    public const  OK = 1;
    public const BAD = 0;

    const LOGGED = 2;
    const BAD_LOGGED = 3;
    const BAD_MAIL = 4;
    const BAD_PASSWORD = 5;
    const PASSWORD_DOESNT_MATCH = 6;
    const BAD_USER = 7;
    const NOT_USER = 8;

    const NOT_VALID = 10;
    const BAD_CODE = 11;
    const BAD_MOVE = 12;
    const FILE_NOT_EXIST = 12;
    const FILE_EXIST = 13;
    const BIG_SIZE = 14;
    const BAD_FORMAT = 15;

    const NOT_FOUND = 16;
    const FOUND = 17;
    const OWNER_FOUND = 18;
    const OWNER = 11;
    const NOT_DATA = 19;
    const LIMIT = 20;
    const MAX = 21;
    const BAD_RIGHTS = 11;
    const PERMISSION = 11;
    const PRIVACY = 11;

    const BAD_FRIEND = 11;
    const FRIEND = 11;

    const BAD_DEMAND = 11;
    const DEMAND = 11;
    const BAD_DEMAND_OWNER = 11;
    const DEMAND_OWNER = 11;

    const NOT_MONEY = 11;
    const BLACKLIST = 11;
    const ANTISPAM = 11;
    const SUBSCRIPTION = 11;

}
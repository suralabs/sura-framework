<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Libs\Model as Database;
use Sura\Time\Date;

/**
 *
 */
class Users
{

    /**
     * @return bool
     */
    public static function userOnline(): bool
    {
        $logged = Registry::get('logged');

        //Если юзер залогинен то обновляем последнюю дату посещения на личной стр
        if ($logged) {
            $user_info = Registry::get('user_info');

            //Начисления 1 убм.
            if (!$user_info['user_lastupdate']) {
                $user_info['user_lastupdate'] = 1;
            }

//            $server_time = intval($_SERVER['REQUEST_TIME']);
            $server_time = Date::time();

            if (date('Y-m-d', (int)$user_info['user_lastupdate']) < date('Y-m-d', $server_time)) {
                $sql_balance = ", user_balance = user_balance+1, user_lastupdate = '{$server_time}'";
            } else {
                $sql_balance = "";
            }

            //Определяем устройство
            $device_user = 0;
            if (($user_info['user_last_visit'] + 60) <= $server_time) {
                //FIXME
                $database = Database::getDB();
                $database->query("UPDATE LOW_PRIORITY `users` SET user_logged_mobile = '{$device_user}', user_last_visit = '{$server_time}' {$sql_balance} WHERE user_id = '{$user_info['user_id']}'");
            }
            return true;
        }
        return true;
    }
}
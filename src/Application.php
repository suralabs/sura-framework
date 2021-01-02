<?php

namespace Sura;

use Sura\Libs\Db;
use Sura\Libs\Settings;
use Sura\Libs\Templates;
use Sura\Libs\Registry;
use Sura\Libs\Router;

/**
 * Class Application
 * @package Sura
 */
class Application
{
    /**
     * VERSION
     */
    const VERSION = '1.0.0';

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version() : string
    {
        return static::VERSION;
    }

    /**
     *
     */
    function init(){

    }

    /**
     * @param $params
     */
    function routing($params){
        $router = Router::fromGlobals();
        $routers =  require __DIR__ . '/../../../../routes/web.php';
        $router->add($routers);
        if ($router->isFound()) {
            $router->executeHandler(
                $router->getRequestHandler(),
                $params
            );
        }else {
            echo 'error: page not found';
            http_response_code(404);
        }
    }

    /**
     * @param $params
     * @return bool
     */
    function user_online($params){

        $logged = $params['user']['logged'];

        //Елси юзер залогинен то обновляем последнюю дату посещения на личной стр
        if($logged){
            $user_info = $params['user']['user_info'];
            $db = Db::getDB();

            //Начисления 1 убм.
            if(!$user_info['user_lastupdate']) $user_info['user_lastupdate'] = 1;

            $server_time = intval($_SERVER['REQUEST_TIME']);

            if(date('Y-m-d', $user_info['user_lastupdate']) < date('Y-m-d', $server_time))
                $sql_balance = ", user_balance = user_balance+1, user_lastupdate = '{$server_time}'";
            else
                $sql_balance = "";

            //Определяем устройство
            if(check_smartphone()){
                if($_SESSION['mobile'] != 2)
                    $config['temp'] = "mobile";
                $check_smartphone = true;
            }else{
                $check_smartphone = false;
            }

            if($check_smartphone) {
                $device_user = 1;
            }
            else {
                $device_user = 0;
            }
            if(($user_info['user_last_visit'] + 60) <= $server_time){
                $db->query("UPDATE LOW_PRIORITY `users` SET user_logged_mobile = '{$device_user}', user_last_visit = '{$server_time}' {$sql_balance} WHERE user_id = '{$user_info['user_id']}'");
            }
            return true;
        }
        return true;
    }
}
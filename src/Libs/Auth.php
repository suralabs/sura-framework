<?php

declare(strict_types=1);

namespace Sura\Libs;

use JetBrains\PhpStorm\NoReturn;
use Sura\Contracts\AuthInterface;

/**
 * Авторизация пользователей
 */
class Auth implements AuthInterface
{
    /**
     * @return array
     */
    public static function index(): array
    {
        $db = Db::getDB();

        $requests = Request::getRequest();
        $request = ($requests->getGlobal());

        $server = $requests->server;
        $_IP = $requests->getClientIP();
        $_BROWSER = $requests->getClientAGENT();

        //Если есть данные сесии
        if (isset($_SESSION['user_id']) > 0) {

            $id = (int)$_SESSION['user_id'];

            $database = Model::getDB();
            $user_info = $database->fetch('SELECT user_id,user_name, user_lastname, time_zone, notifications_list, user_email, user_group, user_friends_demands, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, user_new_mark_photos, user_search_pref, user_status, user_last_visit, user_pm_num, invties_pub_num, user_balance, balance_rub FROM users WHERE user_id = ?', (array)$id);

            //Если есть данные о сесии, но нет инфы о юзере, то выкидываем его
            if (!$user_info['user_id']) {
                self::logout();
//				header('Location: https://' . $server['HTTP_HOST'] . '/logout/');
            }

            //ava
            if ($user_info['user_photo']) {
                $user_info['ava'] = '/uploads/users/' . $user_info['user_id'] . '/50_' . $user_info['user_photo'];
            } else {
                $user_info['ava'] = '/images/no_ava_50.png';
            }

            //Если юзер нажимает "Главная" и он зашел не с моб версии. то скидываем на его стр.
//			$host_site = $server['QUERY_STRING'];
            $logged = true;
            Registry::set('logged', true);

            Registry::set('user_info', $user_info);
        } //Если есть данные о COOKIE то проверяем
        elseif (isset($request['user_id']) > 0 and $request['hash']) {
            $id = (int)$request['user_id'];

            $database = Model::getDB(); // the same arguments as uses PDO
            $user_info = $database->fetch('SELECT user_id,user_name, user_lastname, time_zone, notifications_list, user_email, user_group, user_friends_demands, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, user_new_mark_photos, user_search_pref, user_status, user_last_visit, user_pm_num, invties_pub_num FROM users WHERE user_id = ?', (array)$id);

            //ava
            if ($user_info['user_photo']) {
                $user_info['ava'] = '/uploads/users/' . $user_info['user_id'] . '/50_' . $user_info['user_photo'];
            } else {
                $user_info['ava'] = '/images/no_ava_50.png';
            }

            //Если HASH совпадает то пропускаем
            if ($user_info['user_hash'] == $request['hash'] and $request['user_id'] == $user_info['user_id']) {
                $_SESSION['user_id'] = $user_info['user_id'];

                //Вставляем лог в бд
                $database->query('UPDATE log SET', ['browser' => $_BROWSER, 'ip' => $_IP,], 'WHERE uid = ?', $user_info['user_id']);

                //Удаляем все рание события
                $database->query('DELETE FROM updates WHERE for_user_id = ?', $user_info['user_id']);
                $logged = true;
            } else {
                $user_info = array();
                $logged = false;
                self::logout();
//                header('Location: https://'.$_SERVER['HTTP_HOST'].'/h/');
            }

            //Если юзер нажимает "Главная" и он зашел не с моб версии. то скидываем на его стр.
            $host_site = $server['QUERY_STRING'];
//            if($logged AND !$host_site AND $config['temp'] != 'mobile')
            if ($logged and !$host_site) header('Location: https://' . $server['HTTP_HOST'] . '/u' . $user_info['user_id']);

            Registry::set('logged', $logged);
            Registry::set('user_info', $user_info);
        } else {
            $user_info = array();
            $logged = false;
//			self::logout();
            // Registry::set('logged', $logged);
        }

        //Если данные поступили через пост и пользователь не авторизован
        if (isset($_POST['log_in']) and $logged == false) {

            //Приготавливаем данные
            $email = strip_tags($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            // if( _strlen( $name, $config['charset'] ) > 40 OR _strlen(trim($name), $config['charset']) < 3) $stop = 'error';
            //$lang = langs::get_langs();

            //Проверяем правильность e-mail
            if (Validation::check_email($email) == false and $_POST['token'] !== $_SESSION['_mytoken'] || empty($_POST['token'])) {
                return array('user_info' => $user_info, 'logged' => false);
                //msgbox('', $lang['not_loggin'].'<br /><a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?r</a>', 'info_red');
            }

            $database = Model::getDB();
            $user_info = $database->fetchAll('SELECT user_id  FROM users WHERE user_email = ? AND user_password = ?', (array)$email, (array)$password);
            $user_info = (array)$user_info[0];
//                $check_user = $db->super_query("SELECT user_id FROM `users` WHERE user_email = '".$email."' AND user_password = '".$password."'");

            //Если есть юзер то пропускаем
            if ($user_info) {
                //Hash ID
                $hid = $password . md5(md5($_IP));

                //Обновляем хэш входа
//                    $db->query("UPDATE `users` SET user_hash = '".$hid."' WHERE user_id = '".$check_user['user_id']."'");
                $database->query('UPDATE users SET', ['user_hash' => $hid,], 'WHERE user_id = ?', $user_info['user_id']);

                //Удаляем все рание события
//                    $db->query("DELETE FROM `updates` WHERE for_user_id = '{$check_user['user_id']}'");
                $database->query('DELETE FROM updates WHERE for_user_id = ?', $user_info['user_id']);

                //Устанавливаем в сессию ИД юзера
                $_SESSION['user_id'] = (int)$user_info['user_id'];

                //Записываем COOKIE
                Tools::set_cookie("user_id", (string)$user_info['user_id'], 365);
                Tools::set_cookie("password", $password, 365);
                Tools::set_cookie("hid", $hid, 365);

                //Вставляем лог в бд
                $db->query("UPDATE `log` SET browser = '" . $_BROWSER . "', ip = '" . $_IP . "' WHERE uid = '" . $user_info['user_id'] . "'");
                $database->query('UPDATE log SET', ['browser' => $_BROWSER, 'ip' => $_IP,], 'WHERE uid = ?', $user_info['user_id']);

//                    if($config['temp'] != 'mobile')
                header('Location: https://' . $server['HTTP_HOST'] . '/u' . $user_info['user_id']);
//                    else
//                        header('Location: https://'.$server['HTTP_HOST'].'/');
            } else {
                return array('user_info' => $user_info, 'logged' => false);
                //msgbox('', $lang['not_loggin'].'<br /><br /><a href="/restore/" onClick="Page.Go(this.href); return false">Забыли пароль?</a>', 'info_red');
            }
        }
        return array(
            'user_info' => $user_info,
            'logged' => $logged
        );
    }

    /**
     * logout site
     * @param bool $redirect
     */
    #[NoReturn]
    public static function logout($redirect = false): void
    {
        if (!empty($_SESSION['user_id'])) {
            $redirect = true;
        }
        Tools::set_cookie("user_id", "", 0);
        Tools::set_cookie("hash", "", 0);
        unset($_SESSION['user_id']);
        session_destroy();
        session_unset();
        if ($redirect) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . '/');
        }
    }
}


<?php

namespace Sura\Libs;

use App\Services\Cache;
use Sura\Libs\Db;
use Sura\Libs\Langs;
use Sura\Libs\Registry;
use Sura\Libs\Tools;
use Sura\Libs\Settings;

/**
 * Авторизация пользователей
 */
class Auth
{

    /**
     * @return array
     */
    public static function index()
	{
		$db = Db::getDB();
		$_IP = $_SERVER['REMOTE_ADDR'];
		$_BROWSER = $db->safesql($_SERVER['HTTP_USER_AGENT']);

        //Если юзер перешел по реф ссылке, то добавляем ид реферала в сессию
        //if($_GET['reg']) $_SESSION['ref_id'] = intval($_GET['reg']);

		//Если есть данные сесии
		if(isset($_SESSION['user_id']) > 0){
			$logged = true;
			$logged_user_id = $id = intval($_SESSION['user_id']);
/*                        $Cache = Cache::initialize();
                        try {
                            $value = $Cache->get("users/{$id}/profile_{$id}", $default = null);
                            $row = $user_info = unserialize($value);

                        }catch (Exception $e){
                            $dir = __DIR__.'/../cache/users/'.$id.'/';
                            if(!is_dir($dir)) {
                                mkdir($dir, 0777, true);
                            }

                            $row = $user_info = $db->super_query("SELECT
            user_id,
            user_name,
            user_lastname,
            time_zone,

            user_search_pref,
            user_country_city_name,
            user_birthday,
            user_xfields,
            user_xfields_all,
            user_city,
            user_country,

            user_friends_num,
            user_notes_num,
            user_subscriptions_num,
            user_wall_num,
            user_albums_num,
            user_videos_num,
            user_public_num,
            user_last_visit,
            user_status,
            user_privacy,
            user_sp,
            user_sex,
            user_gifts,
            user_audio,

            user_ban_date,
            xfields,
            user_logged_mobile ,
            user_cover,
            user_cover_pos,
            user_rating,

             FROM `users` WHERE user_id = '{$logged_user_id}'");
            //                $row = $user_info = $Profile->user_row($id);
                            $value = serialize($row);

                            $Cache->set("users/{$id}/profile_{$id}", $value);
                        }
*/



            $user_info = $db->super_query("SELECT user_id,user_name, user_lastname, time_zone, notifications_list, user_email, user_group, user_friends_demands, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, user_new_mark_photos, user_search_pref, user_status, user_last_visit, user_pm_num, invties_pub_num, user_balance, balance_rub FROM `users` WHERE user_id = '".$logged_user_id."'");
			//Если есть данные о сесии, но нет инфы о юзере, то выкидываем его
            if(!$user_info['user_id']){
                header('Location: https://'.$_SERVER['HTTP_HOST'].'/logout/');
            }

			//ava
            if($user_info['user_photo'])
                $user_info['ava'] = '/uploads/users/'.$user_info['user_id'].'/50_'.$user_info['user_photo'];
            else
                $user_info['ava'] = '/images/no_ava_50.png';

			//Если юзер нажимает "Главная" и он зашел не с моб версии. то скидываем на его стр.
			$host_site = $_SERVER['QUERY_STRING'];
			
			Registry::set('logged', $logged);

            //$config = Settings::loadsettings();

//			if($logged AND !$host_site AND $config['temp'] != 'mobile' AND $_SERVER['REQUEST_URI'] !== '/news/')
//				header('Location: /news/');

			Registry::set('user_info', $user_info);
        }
        //Если есть данные о COOKIE то проверяем
        elseif(isset($_COOKIE['user_id']) > 0  AND $_COOKIE['hash']){
			$cookie_user_id = intval($_COOKIE['user_id']);
			$user_info = $db->super_query("SELECT notifications_list, time_zone, user_id, user_email, user_group, user_password, user_hash, user_friends_demands, user_pm_num, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, user_new_mark_photos, user_search_pref, user_status, user_last_visit, invties_pub_num FROM `users` WHERE user_id = '".$cookie_user_id."'");

			//ava
            if($user_info['user_photo'])
                $user_info['ava'] = '/uploads/users/'.$user_info['user_id'].'/50_'.$user_info['user_photo'];
            else
                $user_info['ava'] = '/images/no_ava_50.png';

			//Если HASH совпадает то пропускаем
			if( $user_info['user_hash'] == $_COOKIE['hash'] AND $_COOKIE['user_id'] == $user_info['user_id']){
				$_SESSION['user_id'] = $user_info['user_id'];
				
				//Вставляем лог в бд
				$db->query("UPDATE `log` SET browser = '".$_BROWSER."', ip = '".$_IP."' WHERE uid = '".$user_info['user_id']."'");
				
				//Удаляем все рание события
				$db->query("DELETE FROM `updates` WHERE for_user_id = '{$user_info['user_id']}'");
						
				$logged = true;

			} else {
				$user_info = array();
				$logged = false;
//				echo 'e';
                self::logout();
//                header('Location: https://'.$_SERVER['HTTP_HOST'].'/h/');

            }

			$config = Settings::loadsettings();

			//Если юзер нажимает "Главная" и он зашел не с моб версии. то скидываем на его стр.
			$host_site = $_SERVER['QUERY_STRING'];
			if($logged AND !$host_site AND $config['temp'] != 'mobile')
				header('Location: https://'.$_SERVER['HTTP_HOST'].'/u'.$user_info['user_id']);
				
			Registry::set('logged', $logged);
			Registry::set('user_info', $user_info);
		}
		else {
			$user_info = array();
			$logged = false;

//			self::logout();

			// Registry::set('logged', $logged);
		}

		//Если данные поступили через пост и пользователь не авторизован
		if(isset($_POST['log_in']) AND $logged == false){

			//Приготавливаем данные
			$email = strip_tags($_POST['email']);

			$password = password_hash(GetVar($_POST['password']), PASSWORD_DEFAULT);

			// if( _strlen( $name, $config['charset'] ) > 40 OR _strlen(trim($name), $config['charset']) < 3) $stop = 'error';

            $lang = langs::get_langs();

			//Проверяем правильность e-mail
			if(Validation::check_email($email) == false AND $_POST['token'] !== $_SESSION['_mytoken'] || empty($_POST['token'])) {
				msgbox('', $lang['not_loggin'].'<br /><a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?r</a>', 'info_red');
			} else {

                $check_user = $db->super_query("SELECT user_id FROM `users` WHERE user_email = '".$email."' AND user_password = '".$password."'");

                //Если есть юзер то пропускаем
                if($check_user){
                    //Hash ID
                    $hid = $password.md5(md5($_IP));

                    //Обновляем хэш входа
                    $db->query("UPDATE `users` SET user_hid = '".$hid."' WHERE user_id = '".$check_user['user_id']."'");

                    //Удаляем все рание события
                    $db->query("DELETE FROM `updates` WHERE for_user_id = '{$check_user['user_id']}'");

                    //Устанавливаем в сессию ИД юзера
                    $_SESSION['user_id'] = intval($check_user['user_id']);

                    //Записываем COOKIE
                    Tools::set_cookie("user_id", intval($check_user['user_id']), 365);
                    Tools::set_cookie("password", $password, 365);
                    Tools::set_cookie("hid", $hid, 365);

                    //Вставляем лог в бд
                    $db->query("UPDATE `log` SET browser = '".$_BROWSER."', ip = '".$_IP."' WHERE uid = '".$check_user['user_id']."'");

                    $config = Settings::loadsettings();

                    if($config['temp'] != 'mobile')
                        header('Location: https://'.$_SERVER['HTTP_HOST'].'/u'.$check_user['user_id']);
                    else
                        header('Location: https://'.$_SERVER['HTTP_HOST'].'/');
                } else
                    msgbox('', $lang['not_loggin'].'<br /><br /><a href="/restore/" onClick="Page.Go(this.href); return false">Забыли пароль?</a>', 'info_red');
			}
		}
		return array('user_info' => $user_info, 'logged' => $logged);
	}

    /**
     * logout site
     */
    public static function logout($redirect = false)
    {
//        $redirect = false;
        if(!empty($_SESSION['user_id'])){
            $redirect = true;
        }
        Tools::set_cookie("user_id", "", 0);
//        Tools::set_cookie("password", "", 0);
        Tools::set_cookie("hash", "", 0);
        unset($_SESSION['user_id']);
        session_destroy();
        session_unset();
        if ($redirect == true){
            header('Location: https://'.$_SERVER['HTTP_HOST'].'/');
        }
    }
}


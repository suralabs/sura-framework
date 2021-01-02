<?php

namespace Sura\Libs;

use Sura\Cache\Adapter\FileAdapter;
use Sura\Cache\Cache;
use Sura\Libs\Templates;

/**
 * Class Tools
 * @package Sura\Libs
 */
class Tools
{

    /**
     * @param $url
     */
    public static function clean_url(string $url) {
        if( $url == '' ) return;

        $url = str_replace( "http://", "", strtolower( $url ) );
        $url = str_replace( "https://", "", $url );
        if( substr( $url, 0, 4 ) == 'www.' ) $url = substr( $url, 4 );
        $url = explode( '/', $url );
        $url = reset( $url );
        $url = explode( ':', $url );
        $url = reset( $url );
        return $url;
    }

    /**
     * @return string
     */
    public static function domain_cookie() : string
    {

        $domain_cookie = explode (".", self::clean_url( $_SERVER['HTTP_HOST'] ));
        $domain_cookie_count = count($domain_cookie);
        $domain_allow_count = -2;

        if($domain_cookie_count > 2){

            if(in_array($domain_cookie[$domain_cookie_count-2], array('com', 'net', 'org') ))
                $domain_allow_count = -3;

            if($domain_cookie[$domain_cookie_count-1] == 'ua' )
                $domain_allow_count = -3;

            $domain_cookie = array_slice($domain_cookie, $domain_allow_count);
        }

        $domain_cookie = ".".implode(".", $domain_cookie);
        return $domain_cookie;
    }
    /**
     * @param $name
     * @param $value
     * @param $expires
     */
    public static function set_cookie($name, $value, $expires) {
		if( $expires ) {
			$expires = time() + ($expires * 86400);
		} else {
			$expires = FALSE;
		}
        $domain = self::domain_cookie();
		setcookie($name, $value, $expires, "/", $domain, NULL, TRUE);
    }

    /**
     *
     */
    public static function NoAjaxQuery($url = NULL){
        return false;
		if(clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST']) AND $_SERVER['REQUEST_METHOD'] != 'POST'){
            if($url !== NULL){
                header('Location: '.$url);
            }else{
                header('Location: https://'.$_SERVER['HTTP_HOST'].'/none/');
            }
        }
	}

    /**
     * @param string $url
     */
    public static function NoAjaxRedirect(string $url = ''){
        $ajax = (isset($_POST['ajax'])) ? 'yes' : 'no';
        if($ajax == 'yes')
            if(clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST']) AND $_SERVER['REQUEST_METHOD'] != 'POST'){
                if($url !== ''){
                    header('Location: '.$url);
                }else{
                    header('Location: https://'.$_SERVER['HTTP_HOST'].'/none/');
                }
            }
    }


    /**
     * @param $v
     * @return string
     */
    public static function GetVar(string $v): string
    {
        if(ini_get('magic_quotes_gpc'))
            return stripslashes($v) ;
        return $v;
    }

    /**
     * @param $id
     * @param $options
     * @return string|string[]
     */
    public static function InstallationSelectedNew($id, $options): string
    {
        return str_replace('val="'.$id.'" class="', 'val="'.$id.'" class="active ', $options);
    }

    //deprecated

    /**
     * check xss
     */
    public static function check_xss(){
        $url = html_entity_decode(urldecode($_SERVER['QUERY_STRING']));

        if($url){
            if((strpos( $url, '<' ) !== false) || (strpos( $url, '>' ) !== false) || (strpos( $url, '"' ) !== false) || (strpos( $url, './' ) !== false) || (strpos( $url, '../' ) !== false) || (strpos( $url, '\'' ) !== false) || (strpos( $url, '.php' ) !== false)){
                if($_GET['go'] != "search" AND $_GET['go'] != "messages")
                    die('Hacking attempt!');
            }
        }

        $url = html_entity_decode( urldecode( $_SERVER['REQUEST_URI'] ) );
        if($url){
            if((strpos($url, '<') !== false) || (strpos($url, '>') !== false) || (strpos($url, '"') !== false) || (strpos($url, '\'') !== false)){
                if($_GET['go'] != "search" AND $_GET['go'] != "messages")
                    die('Hacking attempt!');
            }
        }
    }

    /**
     * @param $userId int not owner user
     * @return bool
     */
    public static function CheckBlackList(int $userId) : bool
    {
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];
        $bad_user_id = $userId;
        $db = Db::getDB();

        $row_blacklist = $db->super_query("SELECT id FROM `users_blacklist` WHERE users = '{$bad_user_id}|{$user_id}'");

        if ($row_blacklist){
            return true;
        }
        return false;
    }

    /**
     * check user to friends
     *
     * true - yes
     *
     * @param $friendId
     * @return bool
     */
    public static function CheckFriends(int $friendId) : bool
    {
        $user_info = Registry::get('user_info');
        $db = Db::getDB();
        $for_user_id = $friendId; // not owner

        $from_user_id = $user_info['user_id'];
        $check = $db->super_query("SELECT user_id FROM `friends` WHERE friend_id = '{$for_user_id}' AND user_id = '{$from_user_id}' AND subscriptions = 0");
        if ($check)
        {
            return true;
        }else{
            return false;
        }



//        $Cache = cache_init(array('type' => 'file'));
//        try {
//            $openMyList = $Cache->get("users/{$user_info['user_id']}/friends");
//            if(stripos($openMyList, "u{$friendId}|") !== false)
//                return true;
//            else
//                return false;
//        }catch (\Exception $e){
//            try {
//                $openMyList = $Cache->get("users/{$user_info['user_id']}/friends");
//            }catch (\Exception $e){
//                $Cache->set("users/{$user_info['user_id']}/friends", "");
//            }
//            $Cache->set("users/{$user_info['user_id']}/friends", $openMyList."u{$friendId}|");
//            if(stripos($openMyList, "u{$friendId}|") !== false)
//                return true;
//            else
//                return false;
//        }
    }

    public static function Online($time)
    {
        $config = Settings::loadsettings();
        $server_time = intval($_SERVER['REQUEST_TIME']);
        $online_time = $server_time - $config['online_time'];
        if ($time >= $online_time)
            return true;
        else
            return false;
    }

    /**
     * !Дубликат
     *
     * @param $limit колличество
     * @param $num
     * @param $type
     * @param $tpl
     * @return mixed
     */
    public static function navigation(int $items_per_page, int $items_count, string $type): string
    {
        if(isset($_GET['page']) AND $_GET['page'] > 0)
            $page = intval($_GET['page']);
        else
            $page = 1;

        $items_per_page = $items_per_page;
        $page_refers_per_page = 5;
        $pages = '';
        $pages_count = ( ( $items_count % $items_per_page != 0 ) ) ? floor( $items_count / $items_per_page ) + 1 : floor( $items_count / $items_per_page );
        $start_page = ( $page - $page_refers_per_page <= 0  ) ? 1 : $page - $page_refers_per_page + 1;
        $page_refers_per_page_count = ( ( $page - $page_refers_per_page < 0 ) ? $page : $page_refers_per_page ) + ( ( $page + $page_refers_per_page > $pages_count ) ? ( $pages_count - $page )  :  $page_refers_per_page - 1 );

        if($page > 1)
            $pages .= '<a href="'.$type.($page-1).'" onClick="Page.Go(this.href); return false">&laquo;</a>';
        else
            $pages .= '';

        if ( $start_page > 1 ) {
            $pages .= '<a href="'.$type.'1" onClick="Page.Go(this.href); return false">1</a>';
            $pages .= '<a href="'.$type.( $start_page - 1 ).'" onClick="Page.Go(this.href); return false">...</a>';
        }

        for ( $index = -1; ++$index <= $page_refers_per_page_count-1; ) {
            if ( $index + $start_page == $page )
                $pages .= '<span>' . ( $start_page + $index ) . '</span>';
            else
                $pages .= '<a href="'.$type.($start_page+$index).'" onClick="Page.Go(this.href); return false">'.($start_page+$index).'</a>';
        }

        if ( $page + $page_refers_per_page <= $pages_count ) {
            $pages .= '<a href="'.$type.( $start_page + $page_refers_per_page_count ).'" onClick="Page.Go(this.href); return false">...</a>';
            $pages .= '<a href="'.$type.$pages_count.'" onClick="Page.Go(this.href); return false">'.$pages_count.'</a>';
        }

        $resif = $items_count/$items_per_page;
        if(ceil($resif) == $page)
            $pages .= '';
        else
            $pages .= '<a href="'.$type.($page+1).'" onClick="Page.Go(this.href); return false">&raquo;</a>';

        if ( $pages_count <= 1 )
            $pages = '';
        $nav = '<div class="nav" id="nav">'.$pages.'</div>';
        return $nav;
    }

    /**
     *
     * @param $gc
     * @param $num
     * @param $id
     * @param $function
     * @param $act
     * @param $tpl
     * @return mixed
     */
    //TODO update code
    public static function  box_navigation($limit, $num, $id, $function, $act){
        if(isset($_GET['page']) AND $_GET['page'] > 0)
            $page = intval($_GET['page']);
        else
            $page = 1;


//        $limit = $limit;
        $cnt = $num;
        $items_count = $cnt;
        $items_per_page = $limit;
        $page_refers_per_page = 5;
        $pages = '';
        $pages_count = ( ( $items_count % $items_per_page != 0 ) ) ? floor( $items_count / $items_per_page ) + 1 : floor( $items_count / $items_per_page );
        $start_page = ( $page - $page_refers_per_page <= 0  ) ? 1 : $page - $page_refers_per_page + 1;
        $page_refers_per_page_count = ( ( $page - $page_refers_per_page < 0 ) ? $page : $page_refers_per_page ) + ( ( $page + $page_refers_per_page > $pages_count ) ? ( $pages_count - $page )  :  $page_refers_per_page - 1 );

        if(!$act)
            $act = "''";
        else
            $act = "'{$act}'";

        if($page > 1)
            $pages .= '<a href="" onClick="'.$function.'('.$id.', '.($page-1).', '.$act.'); return false">&laquo;</a>';
        else
            $pages .= '';

        if ( $start_page > 1 ) {
            $pages .= '<a href="" onClick="'.$function.'('.$id.', 1, '.$act.'); return false">1</a>';
            $pages .= '<a href="" onClick="'.$function.'('.$id.', '.($start_page-1).', '.$act.'); return false">...</a>';

        }

        for ( $index = -1; ++$index <= $page_refers_per_page_count-1; ) {
            if ( $index + $start_page == $page )
                $pages .= '<span>' . ( $start_page + $index ) . '</span>';
            else
                $pages .= '<a href="" onClick="'.$function.'('.$id.', '.($start_page+$index).', '.$act.'); return false">'.($start_page+$index).'</a>';
        }

        if ( $page + $page_refers_per_page <= $pages_count ) {
            $pages .= '<a href="" onClick="'.$function.'('.$id.', '.($start_page + $page_refers_per_page_count).', '.$act.'); return false">...</a>';
            $pages .= '<a href="" onClick="'.$function.'('.$id.', '.$pages_count.', '.$act.'); return false">'.$pages_count.'</a>';
        }

        $resif = $cnt/$limit;
        if(ceil($resif) == $page)
            $pages .= '';
        else
            $pages .= '<a href="/" onClick="'.$function.'('.$id.', '.($page+1).', '.$act.'); return false">&raquo;</a>';

        if ( $pages_count <= 1 )
            $pages = '';

        $config = Settings::loadsettings();

        return '<div class="nav" id="nav">'.$pages.'</div>';
    }

    /**
     * Server time
     */
    public static function time(): int
    {
        $requests = Request::getRequest();
        $server = $requests->server;

        return (int)$server['REQUEST_TIME'];
    }
}

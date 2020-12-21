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
    public static function clean_url($url) {
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
		if(clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST']) AND $_SERVER['REQUEST_METHOD'] != 'POST'){
            if($url !== NULL){
                header('Location: '.$url);
            }else{
                header('Location: https://'.$_SERVER['HTTP_HOST'].'/none/');
            }
        }
	}

    /**
     * @param null $url
     */
    public static function NoAjaxRedirect($url = NULL){
        $ajax = (isset($_POST['ajax'])) ? 'yes' : 'no';
        if($ajax == 'yes')
            if(clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST']) AND $_SERVER['REQUEST_METHOD'] != 'POST'){
                if($url !== NULL){
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
    public static function GetVar($v){
        if(ini_get('magic_quotes_gpc'))
            return stripslashes($v) ;
        return $v;
    }

    /**
     * @param $id
     * @param $options
     * @return string|string[]
     */
    public static function InstallationSelectedNew($id, $options){
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
     * @param $userId
     * @return bool
     */
    public static function CheckBlackList($userId){
        $user_info = Registry::get('user_info');

        $Cache = \App\Services\Cache::initialize();
        $key = "user_{$userId}/blacklist";

        try {
            $item = $Cache->get($key, $default = null);
            $item = unserialize($item);

            if(stripos($item, "|{$user_info['user_id']}|") !== false)
                return true;
            else
                return false;
        }catch (\InvalidArgumentException $e){
            return false;
        }

    }

    /**
     * @param $userId
     * @return bool
     */
    public static function MyCheckBlackList($userId){
        $user_info = Registry::get('user_info');
        $key = "user_{$user_info['user_id']}/blacklist";

        try {
            $Cache = \App\Services\Cache::initialize();
            $item = $Cache->get($key, $default = null);
            $item = unserialize($item);

            if(stripos($item, "|{$userId}|") !== false)
                return true;
            else
                return false;
        }catch (\InvalidArgumentException $e){
            return false;
        }

    }

    /**
     * check user to blacklist
     *
     * true - yes
     *
     * @param $friendId
     * @return bool
     */
    public static function CheckFriends($friendId){
        $user_info = Registry::get('user_info');

        //$openMyList = Cache::mozg_cache("user_{$user_info['user_id']}/friends");

        $key = "user_{$user_info['user_id']}/friends";

        try {
            $Cache = \App\Services\Cache::initialize();
            $item = $Cache->get($key, $default = null);
            $item = unserialize($item);

            if(stripos($item, "u{$friendId}|") !== false)
                return true;
            else
                return false;
        }catch (\InvalidArgumentException $e){
            return false;
        }

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
     * @param $gc
     * @param $num
     * @param $type
     * @param $tpl
     * @return mixed
     */
    public static function navigation($gcount, $num, $type){
        if(isset($_GET['page']) AND $_GET['page'] > 0)
            $page = intval($_GET['page']);
        else
            $page = 1;

        $cnt = $num;
        $items_count = $cnt;
        $items_per_page = $gcount;
        $page_refers_per_page = 5;
        $pages = '';
        $pages_count = ( ( $items_count % $gcount != 0 ) ) ? floor( $items_count / $gcount ) + 1 : floor( $items_count / $gcount );
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

        $resif = $cnt/$gcount;
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
    public static function  box_navigation($gc, $num, $id, $function, $act, $tpl){
        global $page;


        $gcount = $gc;
        $cnt = $num;
        $items_count = $cnt;
        $items_per_page = $gcount;
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

        $resif = $cnt/$gcount;
        if(ceil($resif) == $page)
            $pages .= '';
        else
            $pages .= '<a href="/" onClick="'.$function.'('.$id.', '.($page+1).', '.$act.'); return false">&raquo;</a>';

        if ( $pages_count <= 1 )
            $pages = '';

        $config = Settings::loadsettings();

        $tpl_2 = new Templates();
        $tpl_2->dir = __DIR__.'/../../../../../templates/'.$config['temp'];;
        $tpl_2->load_template('nav.tpl');
        $tpl_2->set('{pages}', $pages);
        $tpl_2->compile('content');
        $tpl_2->clear();
        $tpl->result['content'] .= $tpl_2->result['content'];
        return $tpl;
    }
}

<?php
declare(strict_types=1);

namespace Sura\Libs;

use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Pure;
use Sura\Utils\DateTime;

/**
 * Class Tools
 * @package Sura\Libs
 */
class Tools
{
	
	/**
	 * @param $url
	 * @return string
	 */
	public static function clean_url(string $url): string
	{
		$url = str_replace(array("http://", "https://"), "", strtolower($url));
		if (substr($url, 0, 4) == 'www.') {
			$url = substr($url, 4);
		}
		$url = explode('/', $url);
		$url = reset($url);
		$url = explode(':', $url);
		$url = reset($url);
		return $url;
	}
	
	/**
	 * @return string
	 */
	public static function domain_cookie(): string
	{
		
		$domain_cookie = explode(".", self::clean_url($_SERVER['HTTP_HOST']));
		$domain_cookie_count = count($domain_cookie);
		$domain_allow_count = -2;
		
		if ($domain_cookie_count > 2) {
			
			if (in_array($domain_cookie[$domain_cookie_count - 2], array('com', 'net', 'org'))) {
				$domain_allow_count = -3;
			}
			
			if ($domain_cookie[$domain_cookie_count - 1] == 'ua') {
				$domain_allow_count = -3;
			}
			
			$domain_cookie = array_slice($domain_cookie, $domain_allow_count);
		}
		
		$domain_cookie = "." . implode(".", $domain_cookie);
		return $domain_cookie;
	}
	
	/**
	 * @param $name
	 * @param $value
	 * @param $expires
	 */
	public static function set_cookie(string $name, string $value, int $expires): void
	{
		if ($expires) {
			$expires = time() + ($expires * 86400);
		} else {
			$expires = time() + ($expires * 86400);
//            $expires = FALSE;
		}
		$domain = self::domain_cookie();
		
		setcookie($name, $value, $expires, "/", $domain, true, true);
	}
	
	/**
	 * @param string $url
	 */
	public static function NoAjaxQuery(string $url = ''): void
	{
		if (self::clean_url($_SERVER['HTTP_REFERER']) !== self::clean_url($_SERVER['HTTP_HOST']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
			if ($url !== '') {
				header('Location: ' . $url);
			} else {
				header('Location: https://' . $_SERVER['HTTP_HOST'] . '/none/');
			}
		}
	}
	
	/**
	 * @param string $url
	 */
	public static function NoAjaxRedirect(string $url = ''): void
	{
		
		$ajax = (Request::getRequest()->checkAjax());
		$server = (Request::getRequest()->server);
		
		if (($ajax == false) && $server['REQUEST_METHOD'] !== 'POST') {
			if ($url !== '') {
				header('Location: ' . $url);
				echo 'redirect';
				exit();
			}
			
			header('Location: https://' . $server['HTTP_HOST'] . '/none/');
			echo 'redirect';
			exit();
		}
		
	}
	
	/**
	 * @param $id
	 * @param $options
	 * @return string
	 */
	public static function InstallationSelected($id, $options): string
	{
		return str_replace('val="' . $id . '" class="', 'val="' . $id . '" class="active ', $options);
	}
	
	//deprecated
	
	/**
	 * check xss
     * not used
     * @deprecated
	 */
	public static function check_xss(): void
	{
		$url = html_entity_decode(urldecode($_SERVER['QUERY_STRING']));
		
		if ($url) {
			if ((str_contains($url, '<')) || (str_contains($url, '>')) || (str_contains($url, '"')) || (str_contains($url, './')) || (str_contains($url, '../')) || (str_contains($url, '\'')) || (str_contains($url, '.php'))) {
				if ($_GET['go'] != "search" && $_GET['go'] != "messages") {
					throw \Sura\Exception\SuraException::Error('Hacking attempt!');
				}
			}
		}
		
		$url = html_entity_decode(urldecode($_SERVER['REQUEST_URI']));
		if ($url) {
			if ((str_contains($url, '<')) || (str_contains($url, '>')) || (str_contains($url, '"')) || (str_contains($url, '\''))) {
				if ($_GET['go'] != "search" && $_GET['go'] != "messages") {
                    die('Hacking attempt!');
                }
			}
		}
	}

	/**
	 * !Дубликат
	 *
	 * @param int $items_per_page
	 * @param int $items_count
	 * @param string $type
	 * @return mixed
	 */
	#[Pure] public static function navigation(int $items_per_page, int $items_count, string $type): string
	{
		if (isset($_GET['page']) and $_GET['page'] > 0) {
			$page = (int)$_GET['page'];
		} else {
			$page = 1;
		}
		
		$page_refers_per_page = 5;
		$pages = '';
		if (($items_count % $items_per_page !== 0)) {
			$pages_count = floor($items_count / $items_per_page) + 1;
		} else {
			$pages_count = floor($items_count / $items_per_page);
		}
//        $pages_count = ($items_count % $items_per_page !== 0) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
		
		$start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
//        if ($page - $page_refers_per_page <= 0) {
//            $start_page = 1;
//        } else {
//            $start_page = $page - $page_refers_per_page + 1;
//        }
		$page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
		
		if ($page > 1) {
			$pages .= '<a href="' . $type . ($page - 1) . '" onClick="Page.Go(this.href); return false">&laquo;</a>';
		} else {
			$pages .= '';
		}
		
		
		if ($start_page > 1) {
			$pages .= '<a href="' . $type . '1" onClick="Page.Go(this.href); return false">1</a>';
			$pages .= '<a href="' . $type . ($start_page - 1) . '" onClick="Page.Go(this.href); return false">...</a>';
		}
		
		for ($index = -1; ++$index <= $page_refers_per_page_count - 1;) {
			if ($index + $start_page == $page) $pages .= '<span>' . ($start_page + $index) . '</span>'; else
				$pages .= '<a href="' . $type . ($start_page + $index) . '" onClick="Page.Go(this.href); return false">' . ($start_page + $index) . '</a>';
		}
		
		if ($page + $page_refers_per_page <= $pages_count) {
			$pages .= '<a href="' . $type . ($start_page + $page_refers_per_page_count) . '" onClick="Page.Go(this.href); return false">...</a>';
			$pages .= '<a href="' . $type . $pages_count . '" onClick="Page.Go(this.href); return false">' . $pages_count . '</a>';
		}
		
		$resif = $items_count / $items_per_page;
		if (ceil($resif) == $page) {
			$pages .= '';
		} else {
			$pages .= '<a href="' . $type . ($page + 1) . '" onClick="Page.Go(this.href); return false">&raquo;</a>';
		}
		
		if ($pages_count <= 1) {
			$pages = '';
		}
		return '<div class="nav" id="nav">' . $pages . '</div>';
	}
	
	/**
	 *
	 * @param $limit
	 * @param $num
	 * @param $id
	 * @param $function
	 * @param $act
	 * @return mixed
	 */
	//TODO update code
	public static function box_navigation($limit, $num, $id, $function, $act)
	{
		if (isset($_GET['page']) and $_GET['page'] > 0) $page = (int)$_GET['page']; else
			$page = 1;


//        $limit = $limit;
		$cnt = $num;
		$items_count = $cnt;
		$items_per_page = $limit;
		$page_refers_per_page = 5;
		$pages = '';
		$pages_count = (($items_count % $items_per_page != 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
		$start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
		$page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
		
		if (!$act) $act = "''"; else
			$act = "'{$act}'";
		
		if ($page > 1) $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($page - 1) . ', ' . $act . '); return false">&laquo;</a>'; else
			$pages .= '';
		
		if ($start_page > 1) {
			$pages .= '<a href="" onClick="' . $function . '(' . $id . ', 1, ' . $act . '); return false">1</a>';
			$pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page - 1) . ', ' . $act . '); return false">...</a>';
			
		}
		
		for ($index = -1; ++$index <= $page_refers_per_page_count - 1;) {
			if ($index + $start_page == $page) $pages .= '<span>' . ($start_page + $index) . '</span>'; else
				$pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $index) . ', ' . $act . '); return false">' . ($start_page + $index) . '</a>';
		}
		
		if ($page + $page_refers_per_page <= $pages_count) {
			$pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $page_refers_per_page_count) . ', ' . $act . '); return false">...</a>';
			$pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . $pages_count . ', ' . $act . '); return false">' . $pages_count . '</a>';
		}
		
		$resif = $cnt / $limit;
		if (ceil($resif) == $page) $pages .= ''; else
			$pages .= '<a href="/" onClick="' . $function . '(' . $id . ', ' . ($page + 1) . ', ' . $act . '); return false">&raquo;</a>';
		
		if ($pages_count <= 1) $pages = '';
		
		$config = Settings::load();
		
		return '<div class="nav" id="nav">' . $pages . '</div>';
	}
}

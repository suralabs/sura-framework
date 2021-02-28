<?php
declare(strict_types=1);

namespace Sura\Libs;

use JetBrains\PhpStorm\Pure;

/**
 * Class Validation
 * @package System\Libs
 */
class Validation
{
	
	/**
	 * @var string[]
	 */
	private static array $regx = array(//邮箱
		'email' => '/^[\w-\.]+@[\w-]+(\.(\w)+)*(\.(\w){2,4})$/', //手机号码
		'mobile' => '/^\d{11}$/', //固定电话带分机号
		'tel' => '/^((0\d{2,3})-)(\d{7,8})(-(\d{1,4}))?$/', //固定电话不带分机号
		'phone' => '/^\d{3}-?\d{8}|\d{4}-?\d{7}$/', //域名
		'domain' => '/@([0-9a-z-_]+.)+[0-9a-z-_]+$/i', //日期
		'date' => '/^[1-9][0-9][0-9][0-9]-[0-9]{1,2}-[0-9]{1,2}$/', //日期时间
		'datetime' => '/^[1-9][0-9][0-9][0-9]-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}(:[0-9]{1,2}){1,2}$/', //时间
		'time' => '/^[0-9]{1,2}(:[0-9]{1,2}){1,2}$/', /*--------- 数字类型 --------------*/
		'int' => '/^\d{1,11}$/', //十进制整数
		'hex' => '/^0x[0-9a-f]+$/i', //16进制整数
		'bin' => '/^[01]+$/', //二进制
		'oct' => '/^0[1-7]*[0-7]+$/', //8进制
		'float' => '/^\d+\.[0-9]+$/', //浮点型
		/*---------字符串类型 --------------*/
		//utf-8中文字符串
		'chinese' => '/^[\x{4e00}-\x{9fa5}]+$/u', /*---------常用类型 --------------*/
		'english' => '/^[a-z0-9_\.]+$/i', //英文
		'nickname' => '/^[\x{4e00}-\x{9fa5}a-z_\.]+$/ui', //昵称，可以带英文字符和数字
		'realname' => '/^[\x{4e00}-\x{9fa5}]+$/u', //真实姓名
		'password' => '/^[a-z0-9]{6,32}$/i', //密码
		'area' => '/^0\d{2,3}$/', //区号
		'version' => '/^\d+\.\d+\.\d+$/',       //版本号
		'url' => '((https?)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)', //URL
	);
	
	/**
	 * @param $regx
	 * @param $input
	 * @return bool|string
	 */
	public static function regx(string $regx, string $input): bool|string
	{
		$n = preg_match($regx, $input, $match);
		if ($n === 0) {
			return false;
		}

        return $match[0];
    }
	
	/**
	 * @param $ctype
	 * @param $input
	 * @return mixed
	 */
	public static function check($input, $ctype): mixed
	{
		if (isset(self::$regx[$ctype])) {
			return self::regx(self::$regx[$ctype], $input);
		}
		
		return self::$ctype($input);
	}
	
	/**
	 * IPv4 or IPV6
	 * @param string $ip
	 * @return bool|string
	 */
	#[Pure] public static function check_ip(string $ip): bool|string
	{
		if (substr_count($ip, ".") + 1 == 4 || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || str_contains($ip, ":")) {
			return $ip;
		}
		return false;
	}
	
	/**
	 * @param $user_name
	 * @return bool
	 */
	public static function check_name(string $user_name): bool
	{
		if (empty($user_name)) {
			return false;
		}
		if (preg_match("/^[a-zA-Zа-яА-Я]+$/iu", $user_name) && strlen($user_name) >= 2) {
			return true;
		}
		return false;
	}

    /**
     * @param string $password_first
     * @param string $password_second
     * @return bool
     */
	#[Pure] public static function check_password(string $password_first, string $password_second): bool
	{
		if (empty($password_first) || empty($password_second)) {
			return false;
		}
		if (strlen($password_first) >= 6 && strlen($password_first) <= 72 && $password_first == $password_second) {
			return true;
		}
		
		return false;
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	#[Pure] public static function check_email(string $email): bool
	{
		if (empty($email)) {
            return false;
        }
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return !(empty($email) && strlen($email) > 40 && substr_count($email, "@") + 1 !== 2);
        }
		return false;
	}
	
	/**
	 * @param string $text
	 * @return string
	 */
	public static function strip_data(string $text): string
	{
		//$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", ";", ":", "@", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"' );
		$quotes = array("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&");
		$goodquotes = array("-", "+", "#");
		$repquotes = array("\-", "\+", "\#");
		$text = stripslashes($text);
		$text = trim(strip_tags($text));
		$text = str_replace(array($quotes, $goodquotes), array('', $repquotes), $text);
		return $text;
	}
	
	/**
	 * @param string $source
	 * @return string
	 */
	public static function replace_rn(string $source): string
	{
		
		$find[] = "'\r'";
		$replace[] = "";
		$find[] = "'\n'";
		$replace[] = "";
		
		$source = preg_replace($find, $replace, $source);
		
		return $source;
		
	}
	
	/**
	 * @param string $source
	 * @return string
	 */
	public static function myBr(string $source): string
	{
		
		$find[] = "'\r'";
		$replace[] = "<br />";
		
		$find[] = "'\n'";
		$replace[] = "<br />";
		
		$source = preg_replace($find, $replace, $source);
		
		return $source;
		
	}
	
	/**
	 * @param string $source
	 * @return string
	 */
	public static function myBrRn(string $source): string
	{
		
		$find[] = "<br />";
		$replace[] = "\r\n";
//		$find[] = "<br />";
//		$replace[] = "\n";
		
		$source = str_replace($find, $replace, $source);
		
		return $source;
	}
	
	/**
	 * @param string $source
	 * @return string
	 */
	public static function rn_replace(string $source): string
	{
		
		$find[] = "'\r'";
		$replace[] = "";
		$find[] = "'\n'";
		$replace[] = "";
		
		$source = preg_replace($find, $replace, $source);
		
		return $source;
		
	}
	
	/**
	 * @param string $source
	 * @param int $substr_num
	 * @param bool $strip_tags
	 * @return string
	 */
	public static function textFilter(string $source, int $substr_num = 25000, bool $strip_tags = false): string
	{
		// global $db;
		//$db = Db::getDB();
//		 if(function_exists("get_magic_quotes_gpc") AND get_magic_quotes_gpc())
		$source = stripslashes($source);
		
		$find = array('/data:/i', '/about:/i', '/vbscript:/i', '/onclick/i', '/onload/i', '/onunload/i', '/onabort/i', '/onerror/i', '/onblur/i', '/onchange/i', '/onfocus/i', '/onreset/i', '/onsubmit/i', '/ondblclick/i', '/onkeydown/i', '/onkeypress/i', '/onkeyup/i', '/onmousedown/i', '/onmouseup/i', '/onmouseover/i', '/onmouseout/i', '/onselect/i', '/javascript/i');
		
		$replace = array("d&#097;ta:", "&#097;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#097;vascript");
		
		$source = preg_replace("#<iframe#i", "&lt;iframe", $source);
		$source = preg_replace("#<script#i", "&lt;script", $source);
		
		$source = self::myBr(htmlspecialchars(substr(trim($source), 0, $substr_num), ENT_QUOTES | ENT_HTML5));

        $source = str_ireplace(array("{", "`", "{theme}"), array("&#123;", "&#96;", "&#123;theme}"), $source);
		
		$source = preg_replace($find, $replace, $source);
		
		if ($strip_tags) $source = strip_tags($source);
		
		return $source;
	}
	
	/**
	 * @param string $value
	 * @param string $charset
	 * @return int
	 */
	public static function _strlen(string $value, string $charset = "utf-8"): int
	{
		
		if (function_exists('mb_strlen')) {
			return mb_strlen($value, $charset);
		}
		
		if (function_exists('iconv_strlen')) {
			return iconv_strlen($value, $charset);
		}
		
		return strlen($value);
	}
	
	/**
	 * @param $source
	 * @param bool $encode
	 * @return string
	 */
	public static function word_filter($source, $encode = true): string
	{
		$safe_mode = false;
		
		if ($encode) {
			
			$all_words = file(__DIR__ . '/../../../../../config/wordfilter.db.php');
			$find = array();
			$replace = array();
			
			if (!$all_words or !count($all_words)) return $source;
			
			foreach ($all_words as $word_line) {
				$word_arr = explode("|", $word_line);
				
				if (function_exists("get_magic_quotes_gpc") and get_magic_quotes_gpc()) {
					
					$word_arr[1] = addslashes($word_arr[1]);
					
				}
				
				if ($word_arr[4]) {
					
					$register = "";
					
				} else $register = "i";
				
				//if($config['charset'] == "utf-8")
				$register .= "u";
				
				$allow_find = true;
				
				if ($word_arr[5] == 1 && $safe_mode) {
                    $allow_find = false;
                }
				if ($word_arr[5] == 2 && !$safe_mode) {
                    $allow_find = false;
                }
				
				if ($allow_find) {
					
					if ($word_arr[3]) {
						
						$find_text = "#(^|\b|\s|\<br \/\>)" . preg_quote($word_arr[1], "#") . "(\b|\s|!|\?|\.|,|$)#" . $register;
						
						if ($word_arr[2] == "") {
                            $replace_text = "\\1";
                        } else {
                            $replace_text = "\\1<!--filter:" . $word_arr[1] . "-->" . $word_arr[2] . "<!--/filter-->\\2";
                        }
						
					} else {
						
						$find_text = "#(" . preg_quote($word_arr[1], "#") . ")#" . $register;
						
						if ($word_arr[2] == "") {
                            $replace_text = "";
                        } else {
                            $replace_text = $word_arr[2];
                        }
						
					}
					
					if ($word_arr[6]) {
						
						if (preg_match($find_text, $source)) {
							
							return $source;
							
						}
						
					} else {
						
						$find[] = $find_text;
						$replace[] = $replace_text;
					}
					
				}
				
			}
			
			if (!count($find)) return $source;
			
			$source = preg_split('((>)|(<))', $source, -1, PREG_SPLIT_DELIM_CAPTURE);
			
			foreach ($source as $i => $iValue) {
				if ($iValue == "<" or $iValue == "[") {
					$i++;
					continue;
				}
				
				if ($iValue != "") {
                    $source[$i] = preg_replace($find, $replace, $source[$i]);
                }
			}
			
			$source = join("", $source);
			
		} else {
			
			$source = preg_replace("#<!--filter:(.+?)-->(.+?)<!--/filter-->#", "\\1", $source);
			
		}
		
		return $source;
	}
	
	/**
	 * @param $str
	 * @return mixed
	 */
	#[Pure] public static function string(string $str): string
	{
		return filter_var($str, FILTER_DEFAULT);
	}
	
	/**
	 * @param $binary_string
	 * @return false|int
	 */
	public static function strlen_8bit($binary_string): false|int
	{
		if (function_exists('mb_strlen')) {
			return mb_strlen($binary_string, '8bit');
		}
		return strlen($binary_string);
	}
	
	/**
	 * @param $binary_string
	 * @param $start
	 * @param $length
	 * @return false|string
	 */
	public static function substr_8bit($binary_string, $start, $length): false|string
	{
		if (function_exists('mb_substr')) {
			return mb_substr($binary_string, $start, $length, '8bit');
		}
		return substr($binary_string, $start, $length);
	}
	
}

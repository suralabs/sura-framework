<?php

declare(strict_types=1);

namespace Sura\Libs;

use JetBrains\PhpStorm\NoReturn;
use Sura\Contracts\LangsInterface;
use function Sura\resolve;

/**
 * Class Langs
 * @package System\Libs
 */
class Langs implements LangsInterface
{

    /**
     * @var \string[][]
     */
    private static array $langs = [
        0 => [
            'flag' => '<svg style="margin-right: 10px;width: 30px;height: 25px;" xmlns="http://www.w3.org/2000/svg" id="flag-icon-css-ru" viewBox="0 0 640 480"><g fill-rule="evenodd" stroke-width="1pt"><path fill="#fff" d="M0 h640v480H0z"/><path fill="#0039a6" d="M0 160h640v320H0z"/><path fill="#d52b1e" d="M0 320h640v160H0z"/></g></svg>',
            'name' => 'Русский',
            'iso1' => 'ru',
        ],
        1 => [
            'flag' => '<svg style="margin-right: 10px;width: 30px;height: 25px;" xmlns="http://www.w3.org/2000/svg" id="flag-icon-css-us" viewBox="0 0 640 480"><g fill-rule="evenodd"><g stroke-width="1pt"><path fill="#bd3d44" d="M0 0h972.8v39.4H0zm0 78.8h972.8v39.4H0zm0 78.7h972.8V197H0zm0 78.8h972.8v39.4H0zm0 78.8h972.8v39.4H0zm0 78.7h972.8v39.4H0zm0 78.8h972.8V512H0z" transform="scale(.9375)"/><path fill="#fff" d="M0 39.4h972.8v39.4H0zm0 78.8h972.8v39.3H0zm0 78.7h972.8v39.4H0zm0 78.8h972.8v39.4H0zm0 78.8h972.8v39.4H0zm0 78.7h972.8v39.4H0z" transform="scale(.9375)"/></g><path fill="#192f5d" d="M0 0h389.1v275.7H0z" transform="scale(.9375)"/><path fill="#fff" d="M32.4 11.8L36 22.7h11.4l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.3-6.7H29zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7h11.4zm64.8 0l3.6 10.9H177l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.3-6.7h11.5zm64.9 0l3.5 10.9H242l-9.3 6.7 3.6 11-9.3-6.8-9.3 6.7 3.6-10.9-9.3-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.2-6.7h11.4zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.6 11-9.3-6.8-9.3 6.7 3.6-10.9-9.3-6.7h11.5zM64.9 39.4l3.5 10.9h11.5L70.6 57 74 67.9l-9-6.7-9.3 6.7L59 57l-9-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.3 6.7 3.6 10.9-9.3-6.7-9.3 6.7L124 57l-9.3-6.7h11.5zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 10.9-9.2-6.7-9.3 6.7 3.5-10.9-9.2-6.7H191zm64.8 0l3.6 10.9h11.4l-9.3 6.7 3.6 10.9-9.3-6.7-9.2 6.7 3.5-10.9-9.3-6.7H256zm64.9 0l3.5 10.9h11.5L330 57l3.5 10.9-9.2-6.7-9.3 6.7 3.5-10.9-9.2-6.7h11.4zM32.4 66.9L36 78h11.4l-9.2 6.7 3.5 10.9-9.3-6.8-9.2 6.8 3.5-11-9.3-6.7H29zm64.9 0l3.5 11h11.5l-9.3 6.7 3.5 10.9-9.2-6.8-9.3 6.8 3.5-11-9.2-6.7h11.4zm64.8 0l3.6 11H177l-9.2 6.7 3.5 10.9-9.3-6.8-9.2 6.8 3.5-11-9.3-6.7h11.5zm64.9 0l3.5 11H242l-9.3 6.7 3.6 10.9-9.3-6.8-9.3 6.8 3.6-11-9.3-6.7h11.4zm64.8 0l3.6 11h11.4l-9.2 6.7 3.5 10.9-9.3-6.8-9.2 6.8 3.5-11-9.2-6.7h11.4zm64.9 0l3.5 11h11.5l-9.3 6.7 3.6 10.9-9.3-6.8-9.3 6.8 3.6-11-9.3-6.7h11.5zM64.9 94.5l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.3 6.7 3.6 11-9.3-6.8-9.3 6.7 3.6-10.9-9.3-6.7h11.5zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7H191zm64.8 0l3.6 10.9h11.4l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.3-6.7H256zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7h11.4zM32.4 122.1L36 133h11.4l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.3-6.7H29zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 10.9-9.2-6.7-9.3 6.7 3.5-10.9-9.2-6.7h11.4zm64.8 0l3.6 10.9H177l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.3-6.7h11.5zm64.9 0l3.5 10.9H242l-9.3 6.7 3.6 11-9.3-6.8-9.3 6.7 3.6-10.9-9.3-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.2-6.7h11.4zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.6 11-9.3-6.8-9.3 6.7 3.6-10.9-9.3-6.7h11.5zM64.9 149.7l3.5 10.9h11.5l-9.3 6.7 3.5 10.9-9.2-6.8-9.3 6.8 3.5-11-9.2-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.3 6.7 3.6 10.9-9.3-6.8-9.3 6.8 3.6-11-9.3-6.7h11.5zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 10.9-9.2-6.8-9.3 6.8 3.5-11-9.2-6.7H191zm64.8 0l3.6 10.9h11.4l-9.2 6.7 3.5 10.9-9.3-6.8-9.2 6.8 3.5-11-9.3-6.7H256zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 10.9-9.2-6.8-9.3 6.8 3.5-11-9.2-6.7h11.4zM32.4 177.2l3.6 11h11.4l-9.2 6.7 3.5 10.8-9.3-6.7-9.2 6.7 3.5-10.9-9.3-6.7H29zm64.9 0l3.5 11h11.5l-9.3 6.7 3.6 10.8-9.3-6.7-9.3 6.7 3.6-10.9-9.3-6.7h11.4zm64.8 0l3.6 11H177l-9.2 6.7 3.5 10.8-9.3-6.7-9.2 6.7 3.5-10.9-9.3-6.7h11.5zm64.9 0l3.5 11H242l-9.3 6.7 3.6 10.8-9.3-6.7-9.3 6.7 3.6-10.9-9.3-6.7h11.4zm64.8 0l3.6 11h11.4l-9.2 6.7 3.5 10.8-9.3-6.7-9.2 6.7 3.5-10.9-9.2-6.7h11.4zm64.9 0l3.5 11h11.5l-9.3 6.7 3.6 10.8-9.3-6.7-9.3 6.7 3.6-10.9-9.3-6.7h11.5zM64.9 204.8l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.3 6.7 3.6 11-9.3-6.8-9.3 6.7 3.6-10.9-9.3-6.7h11.5zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7H191zm64.8 0l3.6 10.9h11.4l-9.2 6.7 3.5 11-9.3-6.8-9.2 6.7 3.5-10.9-9.3-6.7H256zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.5 11-9.2-6.8-9.3 6.7 3.5-10.9-9.2-6.7h11.4zM32.4 232.4l3.6 10.9h11.4l-9.2 6.7 3.5 10.9-9.3-6.7-9.2 6.7 3.5-11-9.3-6.7H29zm64.9 0l3.5 10.9h11.5L103 250l3.6 10.9-9.3-6.7-9.3 6.7 3.6-11-9.3-6.7h11.4zm64.8 0l3.6 10.9H177l-9 6.7 3.5 10.9-9.3-6.7-9.2 6.7 3.5-11-9.3-6.7h11.5zm64.9 0l3.5 10.9H242l-9.3 6.7 3.6 10.9-9.3-6.7-9.3 6.7 3.6-11-9.3-6.7h11.4zm64.8 0l3.6 10.9h11.4l-9.2 6.7 3.5 10.9-9.3-6.7-9.2 6.7 3.5-11-9.2-6.7h11.4zm64.9 0l3.5 10.9h11.5l-9.3 6.7 3.6 10.9-9.3-6.7-9.3 6.7 3.6-11-9.3-6.7h11.5z" transform="scale(.9375)"/></g></svg>',
            'name' => 'English',
            'iso1' => 'en',
        ],
        2 => [
            'flag' => '<svg style="margin-right: 10px;width: 30px;height: 25px;" xmlns="http://www.w3.org/2000/svg" id="flag-icon-css-de" viewBox="0 0 640 480"><path fill="#ffce00" d="M0 320h640v160H0z"/><path d="M0 0h640v160H0z"/><path fill="#d00" d="M0 160h640v160H0z"/></svg>',
            'name' => 'Deutsch',
            'iso1' => 'de',
        ],
        3 => [
            'flag' => '<img style="margin-right: 10px;width: 30px;height: 25px;" src=\'/images/lang/es.svg\' alt=\'\'>',
            'name' => 'Español',
            'iso1' => 'es',
        ],
        4 => [
            'flag' => '<img style="margin-right: 10px;width: 30px;height: 25px;" src=\'/images/lang/fr.svg\' alt=\'\'>',
            'name' => 'Francesa',
            'iso1' => 'fr',
        ],
        5 => [
            'flag' => '<img style="margin-right: 10px;width: 30px;height: 25px;" src=\'/images/lang/pt.svg\' alt=\'\'>',
            'name' => 'Português',
            'iso1' => 'pt',
        ],
        6 => [
            'flag' => '<img style="margin-right: 10px;width: 30px;height: 25px;" src=\'/images/lang/tr.svg\' alt=\'\'>',
            'name' => 'Türk',
            'iso1' => 'tr',
        ],
        7 => [
            'flag' => '<img style="margin-right: 10px;width: 30px;height: 25px;" src=\'/images/lang/cn.svg\' alt=\'\'>',
            'name' => '中文',
            'iso1' => 'zh',
        ],

    ];

    /**
     * ser locale
     * @deprecated
     */
    #[NoReturn]
    public static function setLocale(): void
    {
        $check_lang = self::checkLang();
        setlocale(LC_ALL, $check_lang);
    }

    /**
     * get translate values
     * @return array
     */
    public static function getLangs(): array
    {
        $dir_app = resolve('app')->get('path');
        $check_lang = self::checkLang() ? self::checkLang() : 'ru';
        return require $dir_app . "/lang/{$check_lang}.php";
    }

    /**
     * get translate values
     * @param $lang
     * @return array
     */
    public static function getLangsList($lang): array
    {
        $dir_app = resolve('app')->get('path');
        return require $dir_app . "/lang/{$lang}.php";
    }

    /**
     * @param $name
     * @return string
     */
    public static function v($name): string
    {
        $lang = Langs::getLangs();
        if ($lang[$name])
            return $lang[$name];
        else
            return $name;
    }

    /**
     * @return array
     */
    public static function getLangDate(): array
    {
        $dir_app = resolve('app')->get('path');
        $check_lang = self::checkLang() ? self::checkLang() : 'ru';
        return require $dir_app . "/lang/date_{$check_lang}.lng";
    }

    /**
     * Check your language
     *
     * @return string
     */
    public static function checkLang(): string
    {
        $request = Request::getRequest()->getGlobal();
        $lang_list = self::langList();

        if(!isset($request['lang'])){
            $request['lang'] = 0;
        }else{
            if ($request['lang'] == 'ru'){
                $request['lang'] = 0;
            }
        }
//        $request['lang'] = ($request['lang'] == 'ru') ? 0 : $request['lang'];
        $use_lang = $request['lang'] ?? 0;
        if (!isset($_COOKIE['lang'])) {
            Tools::setCookie('lang', (string)$use_lang, 365);
        }
        return $lang_list[$use_lang]['iso1'];
    }

    /**
     * @return array Languages list
     */
    public static function langList(): array
    {
        /** @var array $expLangList all languages */
        return self::$langs;
    }

    /**
     * @param string $format
     * @param int $stamp
     * @return string
     */
    public static function langDate(string $format, int $stamp): string
    {
        $lang_date = Langs::getLangDate();
        return strtr(date($format, $stamp), $lang_date);
    }

    /**
     * @param $key_item
     * @param $value_item
     * @param $lang
     */
    public static function setItem($key_item, $value_item, $lang)
    {
        $langs = self::getLangsList($lang);

        $value_item = Validation::textFilter((string)$value_item);

        $langs[$key_item] = $value_item;



        $text = "<?php

    re" . "turn array ( ";
        foreach ($langs as $key => $value) {
            $text .= "'" . $key . "' => '" . $value . "',
            ";
        }
        $text .= ' );';

        $dir = resolve('app')->get('path');

        $file = $dir . "/lang/{$lang}.php";
        unlink($file);
        $file = $dir . "/lang/{$lang}.php";
        //если файла нету... тогда
        if (!file_exists($file)) {
            $handler = fopen($file, "w"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
            fwrite($handler, $text);
            fclose($handler);

        }

    }
}
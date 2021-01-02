<?php

namespace Sura\Libs;

use phpDocumentor\Reflection\Types\Integer;
use Sura\Libs\Settings;

/**
 * Class Langs
 * @package System\Libs
 */

class Langs {

    /**
     *
     */
    public static function setlocale()
    {
        $checkLang = self::check_lang();
        if($checkLang == 'ru'){
            setlocale(LC_ALL, "ru");
        }else{
            setlocale(LC_ALL, "ru");
        }
    }

    /**
     * @return array
     */
    public static function get_langs():array
    {
        $checkLang = self::check_lang() ? self::check_lang() :'ru';
        return include __DIR__.'/../../../../../app/lang/'.$checkLang.'.php';
//        return include __DIR__.'/../../../../../lang/'.$checkLang.'/langs.lng';
    }

    /**
     * @return array
     */
    public static function get_langdate():array
    {
        $checkLang = self::check_lang() ? self::check_lang() :'ru';
        return include __DIR__.'/../../../../../lang/'.$checkLang.'/date.lng';
    }

    /**
     * Check language
     *
     * @return string
     */
    public static function check_lang() : string
    {
        $requests = Request::getRequest();
        $request = ($requests->getGlobal());

        $expLangList = self::lang_list();
        $numLangs = count($expLangList);
        if ($request['lang'] > 0){
            $useLang = (int) $request['lang'];
            return $expLangList[$useLang];
        }else{
            if (!isset($request['lang']))
            {
                Tools::set_cookie("lang", 0, 365);
            }
            $useLang = 0;
            return $expLangList[$useLang];
        }
    }

    /**
     * @return array Languages list
     */
    public static function lang_list() : array
    {
        $config = Settings::loadsettings();
        $lang_list = nl2br($config['lang_list']);
        /** @var array $expLangList  all languages*/
        $expLangList = explode(' | ', $lang_list);
        return $expLangList;
    }
}
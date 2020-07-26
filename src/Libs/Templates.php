<?php
/*
	Appointment: Парсинг шаблонов
	File: templates.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
namespace Sura\Libs;
    
use Sura\Libs\Langs;
use Sura\Libs\Validation;

/**
 * Class Templates
 * @package Sura\Libs
 */
class Templates {
    /**
     * @var string
     */
    public $dir = '.';
    /**
     * @var null
     */
    var $template = null;
    /**
     * @var null
     */
    var $copy_template = null;
    /**
     * @var array
     */
    public $data = array();
    /**
     * @var array
     */
    public $block_data = array();
    /**
     * @var string[]
     */
    public $result = array('info' => '', 'vote' => '', 'speedbar' => '', 'content' => '');
    /**
     * @var bool
     */
    public $allow_php_include = true;
    /**
     * @var int
     */
    public $template_parse_time = 0;

    /**
     * @param $name
     * @param $var
     */
    function set($name, $var) {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set($key, $key_var);
            }
        } else $this->data[$name] = $var;
    }

    /**
     * @param $name
     * @param $var
     */
    function set_block($name, $var) {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set_block($key, $key_var);
            }
        } else $this->block_data[$name] = $var;
    }

    /**
     * @param $tpl_name
     * @return bool
     */
    function load_template($tpl_name) {
        $time_before = $this->get_real_time();
        if ($tpl_name == '' || !file_exists($this->dir . DIRECTORY_SEPARATOR . $tpl_name)) {
            die("Невозможно загрузить шаблон: ".$this->dir . $tpl_name);
            return false;
        }
        $this->template = file_get_contents($this->dir . DIRECTORY_SEPARATOR . $tpl_name);
        if (strpos($this->template, "[aviable=") !== false) {
            $this->template = preg_replace_callback("#\\[aviable=(.+?)\\](.*?)\\[/aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2]);
            }, $this->template);
        }
        if (strpos($this->template, "[not-aviable=") !== false) {
            $this->template = preg_replace_callback("#\\[not-aviable=(.+?)\\](.*?)\\[/not-aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2], false);
            }, $this->template);
        }
        if (strpos($this->template, "[not-group=") !== false) {
            $this->template = preg_replace_callback("#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2], false);
            }, $this->template);
        }
        if (strpos($this->template, "[group=") !== false) {
            $this->template = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2]);
            }, $this->template);
        }
        $this->copy_template = $this->template;
        $this->template_parse_time+= $this->get_real_time() - $time_before;
        return true;
    }

    /**
     * @param $aviable
     * @param $block
     * @param bool $action
     * @return string|string[]
     */
    function check_module($aviable, $block, $action = true) {
        global $mozg_module;
        $aviable = explode('|', $aviable);
        $block = str_replace('\"', '"', $block);
        if ($action) {
            if (!(in_array($mozg_module, $aviable)) and ($aviable[0] != "global")) return "";
            else return $block;
        } else {
            if ((in_array($mozg_module, $aviable))) return "";
            else return $block;
        }
    }

    /**
     * @param $groups
     * @param $block
     * @param bool $action
     * @return string|string[]
     */
    function check_group($groups, $block, $action = true) {
        global $user_info;
        $groups = explode(',', $groups);
        if ($action) {
            if (!array_key_exists($user_info['user_group'], $groups)) return "";
        } else {
            if (array_key_exists($user_info['user_group'], $groups)) return "";
        }
        $block = str_replace('\"', '"', $block);
        return $block;
    }

    /**
     *
     */
    function _clear() {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = $this->template;
    }

    /**
     *
     */
    function clear() {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = null;
        $this->template = null;
    }

    /**
     *
     */
    function global_clear() {
        $this->data = array();
        $this->block_data = array();
        $this->result = array();
        $this->copy_template = null;
        $this->template = null;
    }

    /**
     * @param $var
     * @return mixed
     */
    function load_lang($var) {
        $lang = langs::get_langs();

        return $lang[$var];
    }

    /**
     * @param $tpl
     */
    function compile($tpl) {
        $time_before = $this->get_real_time();
        if (count($this->block_data)) {
            foreach ($this->block_data as $key_find => $key_replace) {
                $find_preg[] = $key_find;
                $replace_preg[] = $key_replace;
            }
            $this->copy_template = preg_replace($find_preg, $replace_preg, $this->copy_template);
        }
        foreach ($this->data as $key_find => $key_replace) {
            $find[] = $key_find;
            $replace[] = $key_replace;
        }
        if (isset($find))
        $this->copy_template = str_replace($find, $replace, $this->copy_template);
        $this->copy_template = Validation::word_filter($this->copy_template);
        $this->copy_template = preg_replace_callback("#\\{translate=(.+?)\\}#is", function ($matches) {
            return $this->load_lang($match);
        }, $this->copy_template);
        if (isset($this->result[$tpl])) $this->result[$tpl].= $this->copy_template;
        else $this->result[$tpl] = $this->copy_template;
        $this->_clear();
        $this->template_parse_time+= $this->get_real_time() - $time_before;
    }

    /**
     * @return float
     */
    function get_real_time() {
        list($seconds, $microSeconds) = explode(' ', microtime());
        return (( float )$seconds + ( float )$microSeconds);
    }
}
?>
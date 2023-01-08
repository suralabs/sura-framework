<?php

/*
 * Copyright (c) 2023 Sura
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Sura\Http;

class Request
{
    /**
     * filtering input data
     * @param string $source
     * @param int $substr_num
     * @param bool $strip_tags
     * @return string
     */
    public function filter(string $source, int $substr_num = 25000, bool $strip_tags = false): string
    {
        if (empty($source)) {
            return '';
        }
        if (!empty($_POST[$source])) {
            $source = $_POST[$source];
        } elseif (!empty($_GET[$source])) {
            if (is_array($_GET[$source])) {
                return $_POST[$source];
            }
            $source = $_GET[$source];
        } else {
            return '';
        }
        return $this->textFilter($source, $substr_num, $strip_tags);
    }

    /**
     * string filtering
     * @param string $input_text
     * @param int $substr_num
     * @param bool $strip_tags
     * @return string
     */
    public function textFilter(string $input_text, int $substr_num = 25000, bool $strip_tags = false): string
    {
        $input_text = substr($input_text, 0, $substr_num);
        if (empty($input_text)) {
            return '';
        }
        if ($strip_tags) {
            $input_text = strip_tags($input_text);
        }
        $input_text = trim($input_text);
        $input_text = stripslashes($input_text);
        $input_text = str_replace(PHP_EOL, '<br>', $input_text);
        return htmlspecialchars($input_text, ENT_QUOTES);
    }

    /**
     * filtering the number
     * @param string $source
     * @param int $default
     * @return int
     */
    function int(string $source, int $default = 0): int
    {
        if (isset($_POST[$source])) {
            $source = $_POST[$source];
        } elseif (isset($_GET[$source])) {
            $source = $_GET[$source];
        } else {
            return $default;
        }
        return (int)$source;
    }

    /**
     * checking the ajax request
     * @return bool
     */
    public function checkAjax(): bool
    {
        return !empty($_POST['ajax']) && $_POST['ajax'] === 'yes';
    }
}

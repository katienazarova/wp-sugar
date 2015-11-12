<?php
namespace WPSugar;

class Localization {
    private static $_config = false;

	public static function load() {
        $lang = static::getLaguage();
        $filename = get_template_directory() . '/lang/' . $lang . '.json';
        if (!file_exists($filename) || !is_readable($filename)) {
            return;
        }

        $config = file_get_contents($filename);
        try {
            static::$_config = json_decode($config, true);
        } catch(Exception $e) {}

        return static::$_config;
	}
    
    public static function getMessage($message) {
        if (!static::$_config) {
            static::load();
        }

        if (static::$_config && isset(static::$_config[$message])) {
            return static::$_config[$message];
        }

        return $message;
    }
    
    public static function getLaguage() {
        if (isset($GLOBALS['q_config']['language'])) {
            return $GLOBALS['q_config']['language'];
        }

        return 'en';
    }
    
    public static function getTranslatedContent($content) {
        if (!isset($GLOBALS['q_config']['language'])
            || !isset($GLOBALS['q_config']['enabled_languages'])
            || !count($GLOBALS['q_config']['enabled_languages'])) {
            return $content;
        }

        $lang = static::getLaguage();
        $blocks = static::splitText($content);
        if (isset($blocks[$lang])) {
            return $blocks[$lang];
        }

        return $content;
    }
    
    private static function splitText($content) {
        $result = array();
        $split_regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        $blocks = preg_split($split_regex, $content, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        foreach($GLOBALS['q_config']['enabled_languages'] as $language) {
            $result[$language] = '';
        }
        
        $current_language = false;
        foreach($blocks as $block) {
            // detect c-tags
            if(preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            // detect b-tags
            }elseif(preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            // detect s-tags @since 3.3.6 swirly bracket encoding added
            }elseif(preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            }
            switch($block){
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_language = false;
                    break;
                default:
                    if ($current_language){
                        if (!isset($result[$current_language])) {
                            $result[$current_language] = '';
                        }
                        $result[$current_language] .= $block;
                        $found[$current_language] = true;
                        $current_language = false;
                    } else {
                        foreach ($GLOBALS['q_config']['enabled_languages'] as $language) {
                            $result[$language] .= $block;
                        }
                    }
                break;
            }
        }

        foreach ($result as $lang => $text){
            $result[$lang] = trim($text);
        }

        return $result;
    }
}
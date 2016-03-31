<?php
    /**
     * 参数校验类
     * @author luyuting
     *
     */
    class Comm_ArgsCheck {
        
        // 由空格、换行符构成的空字符串
        const EMPTY_STR = "/^(\\s)+$/";
        // 不包含特殊字符
        const BASE_STR = "/^[^<|>|;|\\?|\\||'|&]+$/";
        // 6-20位，字母、数字或下划线组成
        const USER_PASS = "/^[\\w]{6,20}$/";  
        // 9位，以201X开头
        const USER_STUDENT_ID = "/^(201)\\d{6}$/";
        
        public static function string($arg, $preg = self::BASE_STR) {
            if (!is_string($arg)) {
                return false;
            }
            return (bool) preg_match($preg, $arg);
        }
        
        public static function int(&$arg, $min = null, $max = null, $default = null) {
            is_string($arg) && is_numeric($arg) && $arg = (int) $arg;
            if (!is_int($arg) || (!is_null($min) && $arg < $min) || (!is_null($max) && $arg > $max)) {
                if (is_int($default)) {
                    $arg = $default;
                }
                return false;
            }
            return true;
        }
        
        public static function float(&$arg, $min = null, $max = null, $default = null) {
            is_numeric($arg) && $arg = floatval($arg);
            if (!is_float($arg) || (!is_null($min) && $arg < $min) || (!is_null($max) && $arg > $max)) {
                if (is_float($default)) {
                    $arg = $default;
                }
                return false;
            }
            return true;
        }
        
    }
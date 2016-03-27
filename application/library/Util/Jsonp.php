<?php
    class Util_Jsonp {
        
        public static function jsonp($callback, $data, $encoded = false) {
            $json = $encoded? $data: json_encode($data);
            $jsonp = $callback . '(' . $json . ')';
            return $jsonp;
        }
    }
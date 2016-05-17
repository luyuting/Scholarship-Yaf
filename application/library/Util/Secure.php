<?php
    class Util_Secure {
        
        // 构造随机数用于生成_token
        public static function generateRandom($len = 24) {
            return mb_substr(base64_encode(mt_rand() . time()), 0 , $len * 2);
        }
    }
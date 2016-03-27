<?php
    /**
     * curl 工具类
     * @author luyuting
     *
     */
    class Util_Curl {
        
        public function __construct() {
            
        }
        
        public function setOption() {
            
        }
        
        private static function connect($url) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER => 0
            ]);
            return $ch;
        }
        
        public static function exec($url) {
            $ch = self::connect($url);
            curl_exec($ch);
            curl_close($ch);
        }
        
        public static function multi(array $urls) {
            $mh = curl_multi_init();
            
            $chs = [];
            
            foreach ($urls as $url) {
                $ch = self::connect($url);
                curl_multi_add_handle($mh, $ch);
                $chs[] = $ch;
            }
            
            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            
            while ($active && $mrc == CURLM_OK) {
                // libcurl 7.24.0 + 异步处理，直接返回结果
                if (curl_multi_select($mh) == -1) {
                    usleep(100);
                }
                
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
 
            
            $res = [];
            foreach ($chs as $key => $ch) {
                $res[$key] = curl_multi_getcontent($ch);
                curl_multi_remove_handle($mh, $ch);
            }
            curl_multi_close($mh);
            return $res;
        }
        
        public function __destruct() {
            
        }
    }
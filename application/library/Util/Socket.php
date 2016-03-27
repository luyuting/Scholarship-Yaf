<?php
    /**
     * socket 工具类
     * @author luyuting
     *
     */
    class Util_Socket {
        
        private function __construct() {
            
        }

        /**
         * Create a socket connection and return. 
         * For example: ['scheme' => 'http', 'host' => 'www.a.com', 'port' => 80, 'path' => 'test']
         * @param string $url
         * @return mixed Connect resource or false
         */
        public static function connect($url) {
            $url_parts = parse_url($url);
            $port = isset($url_parts['port'])? $url_parts['port']: 80;
            $fp = fsockopen($url_parts['host'], $port, $errno, $errstr, 5);
            // 抛出异常，调用时增加异常处理
            return $fp;
        }
        
       /**
        * 循环写入数据
        * @param resource $fp
        * @param string $content
        * @return number 写入数据的长度
        */
        public static function write($fp, $content) {
            $total_len = strlen($content);
            $max = 10;
            while (null !== $content) {
                $len = fwrite($fp, $content);
                $content = substr($content, $len); 
                if (0 == -- $max) {
                    return $total_len - strlen($content);
                }
            }
            return $total_len;
        }
        
        /**
         * 循环读取数据
         * @param resource $fp
         * @return string
         */
        public static function read($fp, $line_length = 1024) {
            $str = null;
            while (($buffer = fgets($fp, $line_length)) !== false && $str .= $buffer);
            return $str;
        }
        
        public function __destruct() {
            
        }
    }
<?php
    /**
     * memcached缓存控制类
     * @author luyuting
     *
     */
    final class Base_Mc implements Interface_Cache {
        private $_mc = null;
        private static $_instance = null;
        
        private function __construct() {
            $this->_mc = new Memcached();
            $this->_mc->addServer('127.0.0.1', 11211);
        }
        
        public static function getInstance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
       
        public function query() {
            return $this->_mc;
        }
        
        public function set(array $cache_config, array $cache_params, callable $func) {
            $key = self::buildKey($cache_params, $cache_config['key']);
            $expire = $cache_config['expire'];
            
            $data = call_user_func($func);
            if (is_null($data)) {
                return false;
            }
            $expire += $expire == 0? 0: time();
            return $this->_mc->set($key, $data, $expire);
        }
        
        public function get(array $cache_config, array $cache_params, callable $func) {
            $key = self::buildKey($cache_params, $cache_config['key']);
            $expire = $cache_config['expire'];
            
            $data = null;
            if (false === ($data = $this->_mc->get($key))) {
                $data = call_user_func($func);
                if (is_null($data)) {
                    return null;
                }
                $expire += $expire == 0? 0: time();
                $this->_mc->set($key, $data, $expire);
            }
            return $data;
        }
        
        private static function buildKey(array $args, $format) {
            $key = vsprintf($format, $args);
            return $key;
        }

        
        public function __destruct() {
            unset($this->_mc);
        }
    }
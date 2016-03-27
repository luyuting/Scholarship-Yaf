<?php
    /**
     * redis缓存控制类
     * @author luyuting
     *
     */
    final class Base_Redis implements Interface_Cache {
        private $_redis = null;
        private static $_instance = null;
        
        private function __construct() {
            if (is_null($this->_redis)) {
                $this->_redis = new Redis();
                $this->_redis->connect('127.0.0.1', '6379', 3);
            }
        }
        
        public static function getInstance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        public function query() {
            return $this->_redis;
        }
        
        public function set(array $cache_config, array $cache_params, callable $func) {
            $key = self::buildKey($cache_params, $cache_config['key']);
            $expire = $cache_config['expire'];
            
            $type = null;
            $data = call_user_func($func);
            if (is_null($data)) {
                return false;
            }
            
            if (is_string($data) || is_numeric($data)) {
                $this->_redis->setex($key, $expire, $data);
            } elseif (is_array($data)) {
                $this->_redis->hMset($key, $data);
                $this->_redis->expire($key, $expire);
            }
            return true;
        }
        
        
        public function get(array $cache_config, array $cache_params, callable $func) {
            $key = self::buildKey($cache_params, $cache_config['key']);
            $expire = $cache_config['expire'];
            
            $data = null;
            $type = $this->_redis->type($key);
            $data = $type == Redis::REDIS_HASH? $this->_redis->hGetAll($key): $this->_redis->get($key);
            if (false === $data || empty($data)) {
                $data = call_user_func($func);
                
                if (is_null($data)) {
                    return null;
                }
                
                if (is_string($data) || is_numeric($data)) {
                    $this->_redis->setex($key, $expire, $data);
                } elseif (is_array($data)) {
                    $this->_redis->hMset($key, $data);
                    $this->_redis->expire($key, $expire);
                }
            }
            return $data;
        }
        
        private static function buildKey(array $args, $format) {
            $key = vsprintf($format, $args);
            $key = str_replace('_', ':', $key);
            return $key;
        }
           
        
        public function __destruct() {
            $this->_redis->close();
            unset($this->_redis);
        }
       
      
    
      
    
    }
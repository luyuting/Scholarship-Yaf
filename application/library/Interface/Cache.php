<?php
    /**
     * 缓存控制类接口
     * @author luyuting
     *
     */
    interface Interface_Cache {
        
        /**
         * 获取当前缓存控制类实例，单例模式
         */
        public static function getInstance();
        
        /**
         * 获取当前类中原生mc、redis对象
         */
        public function query();
        
        /**
         * 读取配置设置缓存
         * @param array $cache_config 缓存配置
         * @param array $cache_params 缓存参数，构造key
         * @param callable $func 回调函数，返回应当设置的值，返回值为null时，不设置缓存数据
         */
        public function set(array $cache_config, array $cache_params, callable $func);
        
        /**
         * 优先从缓存读取数据，如果缓存中不存在，执行回调函数获得数据返回，同时将数据写入缓存
         * @param array $cache_config 缓存配置
         * @param array $cache_params 缓存参数，构造key
         * @param callable $func 回调函数，返回查询的数据，返回值为null时，不写入缓存
         */
        public function get(array $cache_config, array $cache_params, callable $func);
        
        /**
         * 析构函数，关闭释放连接对象
         */
        public function __destruct();
        
    }
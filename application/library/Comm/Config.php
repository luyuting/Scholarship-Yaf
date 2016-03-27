<?php
    /**
     * 读取配置文件
     * @author luyuting
     *
     */
    class Comm_Config {
        
        public static function get($path) {
            $det = explode('.', $path);
            
            if (null !== ($static = Yaf_Registry::get($det[0]))) {
                return $static[$det[1]];
            }

            $config = new Yaf_Config_ini(APP_PATH . '/conf/' . $det[0] . '.ini');
            $config_arr = $config->toArray();
            
            if (!empty($config_arr)) {
               Yaf_Registry::set($det[0], $config_arr);
               return $config_arr[$det[1]];
            }
            
            return [];
        }
    }
<?php
    class AdminModel {
        
        public static function getAdminInfoByAccount($admin_account) {
            $params = ['ad_account' => $admin_account];
            $mc = Base_Mc::getInstance();
            $cache_config = Comm_Config::get('cache.admin');
            $info = $mc->get($cache_config, $params, function() use ($params){
                $admin_sql = Impl_Admin::getInstance();
                $rs = $admin_sql->auto()->buildQuery($params)->exec();
                if (empty($rs[0])) {
                    return null;
                }
                return $rs[0][0];
            });
            return $info;
        }
        
        public static function setAdmin(array $model) {
           $cache_params = [$model['ad_account']];
           $mc = Base_Mc::getInstance();
           $cache_config = Comm_Config::get('cache.admin');
           $res = $mc->set($cache_config, $cache_params, function() use ($model) {
               $admin_sql = Impl_Admin::getInstance();
               $rs = $admin_sql->auto()->bulidSave($model)->exec();
               if ($rs[0] == 0 || is_null($rs[0])) {
                   return null;
               } else {
                   return $model;
               }
           });
           // 设置操作记录
           
        }
        
        public static function getAdmin() {
           // 获得设置的管理人员列表
        }
    }
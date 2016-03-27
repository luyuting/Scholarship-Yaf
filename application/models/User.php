<?php
    class UserModel {
        
        public static function getUserInfoById($user_id) {
            $params = ['userId' => $user_id ];
            $mc = Base_Mc::getInstance();
            $cache_config = Comm_Config::get('cache.user');
            $info = $mc->get($cache_config, $params, function() use ($params){
                $user_sql = Impl_User::getInstance();
                $rs = $user_sql->auto()->buildQuery($params)->exec();
                if (empty($rs[0])) {
                    return null;
                }
                return $rs[0][0];
            });
            return $info;
        }
    }
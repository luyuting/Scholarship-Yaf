<?php
    class Impl_User extends Base_Sql {
    
        private static $_sql = null;
        
        public static function getInstance() {
            if (is_null(self::$_sql)) {
                self::$_sql = new self();
            }
            return self::$_sql;
        }
        
        protected function initTable() {
            $this->_table = 'tb_user';
        }
        
        public function __destruct() {
            
        }
    
    }
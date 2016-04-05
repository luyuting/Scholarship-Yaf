<?php
    class Impl_Admin extends Base_Sql {
    
        private static $_sql = null;
    
        public static function getInstance() {
            if (is_null(self::$_sql)) {
                self::$_sql = new self();
            }
            return self::$_sql;
        }
    
        protected function initTable() {
            $this->_table = Comm_T::TABLE_ADMIN;
        }
    
        public function __destruct() {
    
        }
    
    }
<?php
    class Impl_Score extends Base_Sql {
        
        private static $_sql = null;
        
        public static function getInstance() {
            if (is_null(self::$_sql)) {
                self::$_sql = new self();
            }
            return self::$_sql;
        }
        
        protected function initTable() {
            $this->_table = 'tb_item_score';
        }
        
        public function __destruct() {
        
        }
    }
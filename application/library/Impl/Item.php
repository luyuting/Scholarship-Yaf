<?php
    class Impl_Item extends Base_Sql {
        
        private static $_sql = null;
        
        public static function getInstance() {
            if (is_null(self::$_sql)) {
                self::$_sql = new self();
            }
            return self::$_sql;
        }
    
        protected function initTable() {
            
        }
        
        public function tAuto($table) {
            $this->_table = $table;
            return parent::auto();
        }
    }
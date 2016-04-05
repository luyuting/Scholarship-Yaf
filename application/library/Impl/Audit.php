<?php
    class Impl_Audit extends Base_Sql {
        private static $_sql = null;
        
        public static function getInstance() {
            if (is_null(self::$_sql)) {
                self::$_sql = new self();
            }
            return self::$_sql;
        }
        
        protected function initTable() {
            $this->_table = Comm_T::TABLE_AUDIT;
        }
        
        public function auditModel($apply_id, $admin_id, $state, $remark) {
            $model_card = [
                'au_apply' => $apply_id,
                'au_admin' => $admin_id,
                'au_state' => $state,
                'au_remark' => $remark
            ];
            return $model_card;
        }
        
        public function __destruct() {
            
        }
    }
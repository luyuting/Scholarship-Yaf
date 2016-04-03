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
        
        public function scoreModel($scholar_type_id, $name, $descr_a, $descr_b) {
            $model_card = [
                'its_type' => $scholar_type_id,
                'its_name' => $name,
                'its_describe_a' => $descr_a,
                'its_describe_b' => $descr_b
            ];
            return $model_card;
        }
        
        public function __destruct() {
        
        }
    }
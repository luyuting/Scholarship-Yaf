<?php
    class Impl_WorkScore extends Base_Sql {
        private static $_sql = null;
        
        public static function getInstance() {
            if (is_null(self::$_sql)) {
                self::$_sql = new self();
            }
            return self::$_sql;
        }
        
        protected function initTable() {
            $this->_table = Comm_T::TABLE_WORK_SCORE;
        }
        
        public function workScoreModel($scholar_type_id, $position, $score) {
            $card_model = [
                'ws_type' => $scholar_type_id,
                'ws_position' => $position,
                'ws_score' => $score
            ]; 
            return $card_model;
        }
    }
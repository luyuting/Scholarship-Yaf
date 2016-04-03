<?php
    trait Trait_Scholarship {
        
        const TABLE_APPLY = 'tb_apply';
        const TABLE_SCHOLARSHIP = 'tb_scholarship';
        
        private function __construct() {
            
        }
        
        public static function getType() {
            return self::$_type;
        }
        
        public static function getName() {
            return Scholarship_BaseModel::getScholarNameByType(self::_type);
        }
        
        public static function getOrderByAdmin() {
            
        }
        
        public static function getOrderByUser() {
            
        }
        
        private static function setApply($scholar_type_id, $student, $item_table, $item_id, $score) {
            $model = self::applyModel($scholar_type_id, $student, $item_table, $item_id, $score);
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->auto(self::TABLE_APPLY)->buildSave($model)->exec();
            if ($rs[0] == 0 || is_null($rs[0])) {
                return false;
            }
            return true;
        }
        
        private static function getScholarIdByUser($user_id) {
            $annual = date('Y');
            $info = UserModel::getUserInfoById($user_id);
            $grade = $info['user_grade'];
            $params = [
                'sc_annual' => $annual,
                'sc_grade' => $grade,
                'sc_type' => self::$_type
            ];
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->auto(self::TABLE_SCHOLARSHIP)->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return 0;
            }
            $scholar_assoc = $rs[0][0];
            return $scholar_assoc['sc_id'];
        }
        
        private static function applyModel($scholar_type_id, $student, $item_table, $item_id, $score) {
            $model_card = [
                'ap_scho_type' => $scholar_type_id,
                'ap_student' => $student,
                'ap_item_table' => $item_table,
                'ap_item_id' => $item_id,
                'ap_score' => $score,
                'ap_state' => 0,
                'ap_audit' => 0
            ];
            return $model_card;
        }
    }
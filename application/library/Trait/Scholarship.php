<?php
    trait Trait_Scholarship {
        use Trait_Apply;
        
        private function __construct() {
            
        }
        
        public static function getType() {
            return self::$_type;
        }
        
        public static function getName() {
            return Scholarship_BaseModel::getScholarNameByType(self::$_type);
        }
        
        public static function getOrderByAdmin() {
            
        }
        
        public static function getOrderByUser() {
            
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
            $rs = $item_sql->tAuto(Comm_T::TABLE_SCHOLARSHIP)->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return 0;
            }
            $scholar_assoc = $rs[0][0];
            return $scholar_assoc['sc_id'];
        }
    }
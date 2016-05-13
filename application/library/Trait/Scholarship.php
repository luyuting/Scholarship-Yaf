<?php
    trait Trait_Scholarship {
        use Trait_Apply;
        
        private function __construct() {
            
        }
        
        public static function getAuditProgress($admin_account) {
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, self::$_type);
            if ($scholar_type_id == 0) {
                return [];
            }
            $item_sql = Impl_Item::getInstance();
            $params = [
                'ap_scho_type' => $scholar_type_id,
                'ap_audit' => 0
            ];
            $rs = $item_sql->tAuto(Comm_T::TABLE_APPLY)->buildQuery($params, [], ['ap_student' => 'asc'])->exec();
            $data = $rs[0];
            $count_data = [];
            foreach ($data as $item) {
                !isset($count_data[$item['ap_student']]) && $count_data[$item['ap_student']] = 0;
                $count_data[$item['ap_student']] ++;
            }
            ksort($count_data);
            $process_data = [];
            foreach ($count_data as $k => $v) {
                $process_data[] = [
                    'student' => $k,
                    'uncount' => $v
                ];
            }
            return $process_data;
        }
        
        public static function getType() {
            return self::$_type;
        }
        
        public static function getName() {
            return Scholarship_BaseModel::getScholarNameByType(self::$_type);
        }
        
        public static function getOrderByAdmin($admin_account) {
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, self::$_type);
            return self::getScholarOrder($scholar_type_id);
        }
        
        public static function getOrderByUser($user_id) {
            $scholar_type_id = self::getScholarIdByUser($user_id);
            return self::getScholarOrder($scholar_type_id);
        }
        
        private static function getScholarOrder($scholar_type_id) {
            if ($scholar_type_id == 0) {
                return [];
            }
            $item_sql = Impl_Item::getInstance();
            $params = [
                'ap_scho_type' => $scholar_type_id,
                'ap_state' => '通过'
            ];
            $rs = $item_sql->tAuto(Comm_T::TABLE_APPLY)->buildQuery($params, [], ['ap_student' => 'asc'])->exec();
            $data = $rs[0];
            $score_data = [];
            foreach ($data as $info) {
                !isset($score_data[$info['ap_student']]) && $score_data[$info['ap_student']] = 0;
                $score_data[$info['ap_student']] += $info['ap_score'];
            }
            arsort($score_data);
            // 排名设置
            $rank_data = [];
            $last = [];
            $r = 1;
            foreach ($score_data as $k => $v) {
                $rank = !empty($last) && $last['score'] == $v? $last['rank']: $r;
                $last = [
                    'student' => $k,
                    'score' => $v,
                    'rank' => $rank
                ];
                $rank_data[] = $last;
                $r ++;
            }
            return $rank_data;
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
            return (int) $scholar_assoc['sc_id'];
        }
    }
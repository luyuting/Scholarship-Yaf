<?php
    class CompetitionModel {

        public static function getTeamRatio($student, $annual) {
            
        }
        
        public static function getCompetition($admin_account, $type) {
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, $type);
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->tAuto(Comm_T::TABLE_COMPETITION)->buildQuery(['cp_type' => $scholar_type_id])->exec();
            return empty($rs[0])? []: $rs[0];
        }
        
        public static function getCompetitionByUser($student, $type) {
            $scholar_type_id = self::getScholarIdByUser($student, $type);
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->tAuto(Comm_T::TABLE_COMPETITION)->buildQuery(['cp_type' => $scholar_type_id])->exec();
            return empty($rs[0])? []: $rs[0];
        }
        
        public static function setCompetition($name, $rate, $admin_account, $type) {
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, $type);
            $model = self::compModel($scholar_type_id, $name, $rate);
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->tAuto(Comm_T::TABLE_COMPETITION)->buildSave($model)->exec();
            return !($rs[0] == 0 || is_null($rs[0]));
        }
        
        private static function compModel($scholar_type_id, $name, $rate) {
            $card_model = [
                'cp_name' => $name,
                'cp_rate' => $rate,
                'cp_type' => $scholar_type_id
            ];
            return $card_model;
        }
        
        private static function getScholarIdByUser($user_id, $type) {
            $annual = date('Y');
            $info = UserModel::getUserInfoById($user_id);
            $grade = $info['user_grade'];
            $params = [
                'sc_annual' => $annual,
                'sc_grade' => $grade,
                'sc_type' => $type
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
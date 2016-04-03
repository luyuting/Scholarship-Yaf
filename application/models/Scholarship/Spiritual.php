<?php
    class Scholarship_SpiritualModel {
        use Trait_Scholarship;
        
        const TABLE_APPRAISAL = 'tb_appraisal';
        const TABLE_DORMITORY = 'tb_dormitory';
        const TABLE_SPIRITUAL_REWARD = 'tb_spiritual_reward';
        
        private static $_type = Scholarship_BaseModel::SCHOLAR_SPIRITUAL;
        
        public static function applyAppraisal($student, $ratio) {
            $item_sql = Impl_Item::getInstance();
            $model = self::appraisalModel($student, $ratio);
            $rs = $item_sql->auto(self::TABLE_APPRAISAL)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 民主评议排名换算得分
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, '民主评议', $ratio, '');
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score = (int) $item_score['its_score'];
            return self::setApply($scholar_type_id, $student, self::TABLE_APPRAISAL, $id, $score);
        }
        
        public static function applyDormitory($student, $score) {
            $item_sql = Impl_Item::getInstance();
            $model = self::dormitoryModel($student, $score);
            $rs = $item_sql->auto(self::TABLE_DORMITORY)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 文明寝室得分系数
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, '寝室环境建设', '文明寝室', '');
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score *= (int) $item_score['its_score_ratio'];
            return self::setApply($scholar_type_id, $student, self::TABLE_DORMITORY, $id, $score);
        }
        
        public static function applySpiritualReward($student, $name, $item, $rate, $time) {
            $item_sql = Impl_Item::getInstance();
            $model = self::spiritualRewarkModel($student, $name, $item, $rate, $time);
            $rs = $item_sql->auto(self::TABLE_SPIRITUAL_REWARD)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 精神文明奖项得分
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, $name, $item, $rate);
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score = (int) $item_score['its_score'];
            return self::setApply($scholar_type_id, $student, self::TABLE_SPIRITUAL_REWARD, $id, $score);
        }
        
        public static function delAppraisal($student, $apply_id) {
            return self::delApply($student, $apply_id, self::TABLE_APPRAISAL, 'app_id');
        }
        
        public static function delDomitory($student, $apply_id) {
            return self::delApply($student, $apply_id, self::TABLE_DORMITORY, 'do_id');
        }
        
        public static function delSpiritualReward($student, $apply_id) {
            return self::delApply($student, $apply_id, self::TABLE_SPIRITUAL_REWARD, 'spr_id');
        }
        
        public static function getAppraisal($student, $annual) {
            
        }
        
        public static function getDormitory($student, $annual) {
            
        }
        
        public static function getSpiritualReward($student, $annual) {
            
        }
        
        private static function appraisalModel($student, $ratio) {
            $model_card = [
                'app_student' => $student,
                'app_ratio' => $ratio
            ];
            return $model_card;
        }
        
        private static function dormitoryModel($student, $score) {
            $model_card = [
                'do_student' => $student,
                'do_score' => $score
            ];
            return $model_card;
        }
        
        private static function spiritualRewarkModel($student, $name, $item, $rate, $time) {
            $model_card = [
                'spr_student' => $student,
                'spr_name' => $name,
                'spr_item' => $item,
                'spr_rate' => $rate,
                'spr_time' => $time
            ];
            return $model_card;
        }
    }
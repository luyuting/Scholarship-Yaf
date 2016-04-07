<?php
    class Scholarship_WorkModel {
        use Trait_Scholarship;
        
        private static $_type = Scholarship_BaseModel::SCHOLAR_WORK;
        
        /**
         * 社会工作单项：学生工作申请
         * @param string $level 每学期两个职务，第一次序得分不变，第二次序减半
         * @param string $last_time 任职时长，满一学年计全分，满一学期不足一年得分减半，不足一学期不计分
         * @param string $student 申请学生
         * @param string $name 职位名称
         * @param string $begin_time 任职开始时间
         * @param string $end_time 任职结束时间
         * @param string $remark 备注信息
         * @return boolean 申请成功与否
         */
        public static function applyWorkCadre($level, $last_time, $student, $name, $begin_time,
            $end_time, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::workCadreModel($level, $last_time, $student, $name, $begin_time, $end_time, $remark);
            $rs = $item_sql->tAuto(Comm_T::TABLE_WORK_CADRE)->buildSave($model)->exec();
            $id = $rs[0];
            if ($id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 最多四个职务，一学期两个，第一职务全分，第二职务得分减半
            $level_ratio = 0;
            $last_time_ratio = 0;
            switch ($level) {
                case '秋季学期第一职务': ;
                case '春季学期第一职务': $level_ratio = 1; break;
                case '秋季学期第二职务': ;
                case '春季学期第二职务': $level_ratio = 0.5; break;
                default: break;
            }
            switch ($last_time) {
                case '一学期': $last_time_ratio = 0.5; break;
                // 实际上为了避免错选多选，一学年的选项不允许出现
                case '一学年': $last_time_ratio = 1; break;
                default: break;
            }
            // 职位分数在单独的表里，直接写sql查询职位对应分数
            $db = Base_Db::getInstance();
            $sql = 'select ws_score from tb_work_score where ws_type = ? and ws_position like ?';
            $params = [$scholar_type_id, '%' . $name .'%'];
            $rs = $db->query($sql, $params);
            if (empty($rs[0])) {
                return false;
            }
            $score =  $level_ratio * $last_time_ratio * ((int) $rs[0]['ws_score']);
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_WORK_CADRE, $id, $score);
        }
        
        /**
         * 社会工作单项：个人荣誉称号
         * @param string $name 荣誉嘉奖名称
         * @param string $student 申请学生
         * @param string $rate 奖励级别
         * @param string $time 获得时间
         * @return boolean 申请成功与否
         */
        public static function applyWorkReward($name, $student, $rate, $time) {
            $item_sql = Impl_Item::getInstance();
            $model = self::workRewardModel($name, $student, $rate, $time);
            $rs = $item_sql->tAuto(Comm_T::TABLE_WORK_REWARD)->buildSave($model)->exec();
            $id = $rs[0];
            if ($id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 荣誉称号
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, '社会工作优秀干部', $name, $rate);
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score = (int) $item_score['its_score'];
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_WORK_REWARD, $id, $score);
        }
        
        public static function delWorkCadre($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_WORK_CADRE, 'wc_id');
        }
        
        public static function delWorkReward($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_WORK_REWARD, 'wr_id');
        }
        
        public static function getWorkCadre($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_WORK_CADRE, 'wc_id');
        }
    
        public static function getWorkReward($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_WORK_REWARD, 'wr_id');
        }
        
        public static function getWorkScore($admin_account) {
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, self::$_type);
            $score_sql = Impl_WorkScore::getInstance();
            $rs = $score_sql->auto()->buildQuery(['ws_type' => $scholar_type_id])->exec();
            return empty($rs[0])? []: $rs[0];
        }
        
        public static function setWorkScore($admin_account, $position, $score) {
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, self::$_type);
            $score_sql = Impl_WorkScore::getInstance();
            $model = $score_sql->workScoreModel($scholar_type_id, $position, $score);
            $rs = $score_sql->auto()->buildSave($model)->exec();
            return !($rs[0] == 0 || is_null($rs[0]));
        }
        
        private static function workRewardModel($name, $student, $rate, $time) {
            $model_card = [
                'wr_name' => $name,
                'wr_student' => $student,
                'wr_rate' => $rate,
                'wr_time' => $time
            ];
            return $model_card;
        }
        
        private static function workCadreModel($level, $last_time, $student, $name, $begin_time,
            $end_time, $remark) {
            $model_card = [
                'wc_level' => $level,
                'wc_last_time' => $last_time,
                'wc_student' => $student,
                'wc_name' => $name,
                'wc_begin_time' => $begin_time,
                'wc_end_time' => $end_time,
                'wc_remark' => $remark
            ];
            return $model_card;
        }
    }
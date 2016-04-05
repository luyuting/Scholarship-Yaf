<?php
    class Scholarship_ScienceModel {
        use Trait_Scholarship;
  
        private static $_type = Scholarship_BaseModel::SCHOLAR_SCIENCE;
        
        /**
         * 科技创新单项：发明专利申请
         * @param string $student 申请学生
         * @param string $name  专利名称
         * @param string $account 专利号
         * @param string $team_num 团队人数，3人以上视为团队
         * @param string $team_order 团队内贡献排序
         * @param string $type 专利类型（发明型、实用型等）
         * @param string $time 获得时间，非必需
         * @param string $discuss_score 协商得分，14级以后必填，其他不填
         * @param string $remark 备注信息
         * @return boolean 申请成功与否
         */
        public static function applyInvention($student, $name, $account, $team_num, $team_order, $type, $time,
            $discuss_score, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::inventionModel($student, $name, $account, $team_num, $team_order, $type, $time, $discuss_score, $remark);
            $rs = $item_sql->tAuto(Comm_T::TABLE_INVENTION)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 分数计算
            $score = 0;
            // 团队协商得分
            if ($discuss_score != 0) {
                $score = $discuss_score;
            } else {
                $calc_sql = 'select ';
                $calc_params = [];
                $score_sql = Impl_Score::getInstance();
                $db = Base_Db::getInstance();
                // 3人以上视作团队，团队成员得分乘系数
                if ($team_num > 3) {
                    $params = $score_sql->scoreModel($scholar_type_id, '科技创新团队', $team_order, '');
                    $score_rs = $score_sql->buildQuery($params, [], [], ['its_score_ratio']);
                    $calc_sql .= '(' . $score_rs['sql'] . ')*';
                    $calc_params = array_merge($calc_params, $score_rs['params']);
                } 
                // 发明计算得分
                $params = $score_sql->scoreModel($scholar_type_id, '科技创新专利', $type, '');
                $score_rs = $score_sql->buildQuery($params, [], [], ['its_score']);
                $calc_sql .= '(' . $score_rs['sql'] . ') score';
                $calc_params = array_merge($calc_params, $score_rs['params']);
                // 最后得分
                $rs = $db->query($calc_sql, $calc_params);
                $score = (int) $rs[0]['score'];
            }
            self::setApply($scholar_type_id, $student, Comm_T::TABLE_INVENTION, $id, $score);
        }
        
        /**
         * 科技创新单项：学术论文申请
         * @param string $student 申请学生
         * @param string $name 论文名称
         * @param string $journal 期刊，非必需
         * @param string $level 发表论文的级别，影响得分计算
         * @param string $vol 卷号，非必需
         * @param string $ei_sci 是否被EI、SCI收录
         * @param string $team_num 团队人数，3人以上视为团队
         * @param string $team_order 作者顺序
         * @param string $time 发表时间，非必需
         * @param string $discuss_score 协商得分
         * @return boolean
         */
        public static function applyPaper($student, $name, $journal, $level, $vol, $ei_sci, $team_num,
            $team_order, $time, $discuss_score) {
            $item_sql = Impl_Item::getInstance();
            $model = self::paperModel($student, $name, $journal, $level, $vol, $ei_sci, $team_num, $team_order, $time, $discuss_score);
            $rs = $item_sql->tAuto(Comm_T::TABLE_PAPER)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 分数计算
            $score = 0;
            // 团队协商得分
            if ($discuss_score != 0) {
                $score = $discuss_score;
            } else {
                $score_sql = Impl_Score::getInstance();
                $params = $score_sql->scoreModel($scholar_type_id, '科技创新论文', $level, $team_order);
                $rs = $score_sql->auto()->buildQuery($params, [], [], ['its_score * its_score_ratio score'])->exec();
                if (empty($rs[0])) {
                    return false;
                }
                $item_score = $rs[0][0];
                $score = floatval($item_score['score']);
            }
            // 是否被EI、SCI收录
            if ($ei_sci == '是') {
                $score_sql = Impl_Score::getInstance();
                $params = $score_sql->scoreModel($scholar_type_id, '科技创新论文', '被EI、SCI收录', '');
                $rs = $score_sql->auto()->buildQuery($params, [], [], ['its_score'])->exec();
                if (empty($rs[0])) {
                    return false;
                }
                $item_score = $rs[0][0];
                $score += $item_score['its_score'];
            }
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_PAPER, $id, $score);
        }
        
        public static function applyScieTechComp($student, $name, $rate, $prize, $team_status, $team_num,
            $team_order, $host, $time, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::scieTechCompModel($student, $name, $rate, $prize, $team_status, $team_num, $team_order, $host, $time, $remark);
            $rs = $item_sql->tAuto(Comm_T::TABLE_SCIE_TECH_COMP)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            $calc_sql = 'select ';
            $calc_params = [];
            $score_sql = Impl_Score::getInstance();
            $db = Base_Db::getInstance();
            // 3人以上视作团队，团队成员得分乘系数
            if ($team_num >= 3) {
                $params = $score_sql->scoreModel($scholar_type_id, '科技创新团队', $team_order, '');
                $score_rs = $score_sql->buildQuery($params, [], [], ['its_score_ratio']);
                $calc_sql .= '(' . $score_rs['sql'] . ')*';
                $calc_params = array_merge($calc_params, $score_rs['params']);
            }
            // 竞赛得分
            $params = $score_sql->scoreModel($scholar_type_id, '科技创新竞赛', $rate, $prize);
            $score_rs = $score_sql->buildQuery($params, [], [], ['its_score']);
            $calc_sql .= '(' .$score_rs['sql'] . ') score';
            $calc_params = array_merge($calc_params, $score_rs['params']);
            $rs = $db->query($calc_sql, $calc_params);
            $score = (int) $rs[0]['score'];
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_SCIE_TECH_COMP, $id, $score);
        }
        
        public static function applyScieTechProject($student, $name, $rate, $prize, $team_num, $team_order,
            $time, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::scieTechProjectModel($student, $name, $rate, $prize, $team_num, $team_order, $time, $remark);
            $rs = $item_sql->tAuto(Comm_T::TABLE_SCIE_TECH_PROJECT)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, '科技创新项目', '大学生创新创业项目', $prize);
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score = (int) $item_score['its_score'];
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_ACTIVITY_ROLE, $id, $score);
        }
        
        public static function delInvention($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_INVENTION, 'in_id');
        }
        
        public static function delPaper($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_PAPER, 'pa_id');
        }
        
        public static function delScieTechComp($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_SCIE_TECH_COMP, 'stc_id');
        }
        
        public static function delScieTechProject($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_SCIE_TECH_PROJECT, 'stp_id');
        }
        
        public static function getInvention($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_INVENTION, 'in_id');
        }
        
        public static function getPaper($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_PAPER, 'pa_id');
        }
        
        public static function getScieTechComp($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_SCIE_TECH_COMP, 'stc_id');
        }
            	
        public static function getScieTechProject($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_SCIE_TECH_PROJECT, 'stp_id');
        }
        
        private static function inventionModel($student, $name, $account, $team_num, $team_order, $type, $time,
            $discuss_score, $remark) {
            $model_card = [
                'in_student' => $student,
                'in_name' => $name,
                'in_account' => $account,
                'in_team_num' => $team_num,
                'in_team_order' => $team_order,
                'in_type' => $type,
                'in_time' => $time,
                'in_discuss_score' => $discuss_score,
                'in_remark' => $remark
            ];
            return $model_card;
        }
        
        private static function paperModel($student, $name, $journal, $level, $vol, $ei_sci, $team_num,
            $team_order, $time, $discuss_score) {
            $model_card = [
                'pa_student' => $student,
                'pa_name' => $name,
                'pa_journal' => $journal,
                'pa_level' => $level,
                'pa_vol' => $vol,
                'pa_ei_sci' => $ei_sci,
                'pa_team_num' => $team_num,
                'pa_team_order' => $team_order,
                'pa_time' => $time,
                'pa_discuss_score' => $discuss_score
            ];
            return $model_card;
        }
        
        private static function scieTechCompModel($student, $name, $rate, $prize, $team_status, $team_num,
            $team_order, $host, $time, $remark) {
            $model_card = [
                'stc_student' => $student,
                'stc_name' => $name,
                'stc_rate' => $rate,
                'stc_prize' => $prize,
                'stc_team_status' => $team_status,
                'stc_team_num' => $team_num,
                'stc_team_order' => $team_order,
                'stc_host' => $host,
                'stc_time' => $time,
                'stc_remark' => $remark
            ];
            return $model_card;
        }
        
        private static function scieTechProjectModel($student, $name, $rate, $prize, $team_num, $team_order,
            $time, $remark) {
            $model_card = [
                'stp_student' => $student,
                'stp_name' => $name,
                'stp_rate' => $rate,
                'stp_prize' => $prize,
                'stp_team_num' => $team_num,
                'stp_team_order' => $team_order,
                'stp_time' => $time,
                'stp_remark' => $remark
            ];
            return $model_card;
        }
    }
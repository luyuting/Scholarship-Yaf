<?php
    class Scholarship_ActivityModel {
        use Trait_Scholarship;
        
        private static $_type = Scholarship_BaseModel::SCHOLAR_ACTIVITY;
        
        /**
         * 文体活动单项：文体竞赛
         * @param string $name 竞赛名称
         * @param string $student 申请学生
         * @param string $rate 竞赛级别
         * @param string $prize 获得奖项等级
         * @param string $role 角色：队员、替补队员，必填，影响记分
         * @param string $rule 加分规则，是否最高分，同一比赛获得多个级别奖项只允许一项记全分（最高分），其他减半
         * @param string $break 是否打破记录，主要针对体育类竞赛
         * @param string $team_num 所在团队人数
         * @param string $time 获奖时间
         * @param string $remark 备注信息
         * @return boolean 申请成功与否
         */
        public static function applyActivityComp($name, $student, $rate, $prize, $role, $rule, $break,
            $team_num, $time, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::activityCompModel($name, $student, $rate, $prize, $role, $rule, $break, $team_num, $time, $remark);
            $rs = $item_sql->tAuto(Comm_T::TABLE_ACTIVITY_COMP)->buildSave($model)->exec();
            $id = $rs[0];
            if ($id == 0 || is_null($id)) {
                return false;
            }
            
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 同一比赛不同级别奖项，只能选一个计算全分，其他得分减半
            $base_ratio = 1;
            if ($rule != '最高分') {
                $base_ratio = 0.5;
            } 
            // 分数计算
            $calc_sql = 'select ' . $base_ratio . '*';
            $calc_params = [];
            $score_sql = Impl_Score::getInstance();
            $db = Base_Db::getInstance();            
            // 3人及以上记为团队，记得分系数
            if ($team_num >= 3) {
                $team_rate = '市级及以上';
                if ($rate == '校级') {
                    $team_rate = $rate;
                } elseif ($rate == '学部级') {
                    $team_rate = '学部（学院）级';
                }
                $params = $score_sql->scoreModel($scholar_type_id, '文体活动团队', $role, $team_rate);
                $score_rs = $score_sql->buildQuery($params, [], [], ['its_score_ratio']);
                $calc_sql .= '(' . $score_rs['sql'] . ')*';
                $calc_params = array_merge($calc_params, $score_rs['params']);
            }
            // 竞赛得分
            $params = $score_sql->scoreModel($scholar_type_id, '文体活动竞赛', $rate, $prize);
            $score_rs = $score_sql->buildQuery($params, [], [], ['its_score']);
            $calc_sql .= '((' .$score_rs['sql'] . ')';
            $calc_params = array_merge($calc_params, $score_rs['params']);
            
            // 打破记录加分
            if ($break == '是') {
                $params = $score_sql->scoreModel($scholar_type_id, '文体活动', '打破记录', '');
                $score_rs = $score_sql->buildQuery($params, [], [], ['its_score']);
                $calc_sql .= '+(' .$score_rs['sql'] . ')';
                $calc_params = array_merge($calc_params, $score_rs['params']);
            }
            $calc_sql .= ') score';
            $rs = $db->query($calc_sql, $calc_params);
            $score = floatval($rs[0]['score']);
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_ACTIVITY_COMP, $id, $score);
        }
        
        /**
         * 文体活动单项：活动担任主持人/演员
         * @param string $name 活动名称
         * @param string $student 申请学生
         * @param string $time 活动时间
         * @param string $role 角色
         * @param string $rate 活动级别
         * @param string $host 主办方
         * @param string $remark 备注信息
         * @return boolean 申请成功与否
         */
        public static function applyActivityRole($name, $student, $time, $role, $rate, $host, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::activityRoleModel($name, $student, $time, $role, $rate, $host, $remark);
            $rs = $item_sql->tAuto(Comm_T::TABLE_ACTIVITY_ROLE)->buildSave($model)->exec();
            $id = $rs[0];
            if ($id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 主持人或演员
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, '文体活动', '演员/主持人', '');
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score = (int) $item_score['its_score'];
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_ACTIVITY_ROLE, $id, $score);
        }
        
        public static function delActivityComp($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_ACTIVITY_COMP, 'ac_id');
        }
        
        public static function delActivityRole($student, $apply_id) {
            return self::delApply($student, $apply_id, Comm_T::TABLE_ACTIVITY_ROLE, 'ar_id');
        }
               
        public static function getActivityComp($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_ACTIVITY_COMP, 'ac_id');
        }
        
        public static function getActivityRole($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_ACTIVITY_ROLE, 'ar_id');
        }
        
        private static function activityCompModel($name, $student, $rate, $prize, $role, $rule, $break,
            $team_num, $time, $remark) {
            $model_card = [
                'ac_name' => $name,
                'ac_student' => $student,
                'ac_rate' => $rate,
                'ac_prize' => $prize,
                'ac_role' => $role,
                'ac_rule' => $rule,
                'ac_break' => $break,
                'ac_team_num' => $team_num,
                'ac_time' => $time,
                'ac_remark' => $remark
            ];
            return $model_card;
        }
        
        private static function activityRoleModel($name, $student, $time, $role, $rate, $host, $remark) {
            $model_card = [
                'ar_name' => $name,
                'ar_student' => $student,
                'ar_time' => $time,
                'ar_role' => $role,
                'ar_rate' => $rate,
                'ar_host' => $host,
                'ar_remark' => $remark
            ];
            return $model_card;
        }
    }
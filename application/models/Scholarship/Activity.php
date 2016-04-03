<?php
    class Scholarship_ActivityModel {
        use Trait_Scholarship;
        
        const TABLE_ACTIVITY_COMP = 'tb_activity_comp';
        const TABLE_ACTIVITY_ROLE = 'tb_activity_role';
        
        private static $_type = Scholarship_BaseModel::SCHOLAR_ACTIVITY;
        
        public static function applyActivityComp($name, $student, $rate, $prize, $role, $rule, $break,
            $team_num, $time, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::activityCompModel($name, $student, $rate, $prize, $role, $rule, $break, $team_num, $time, $remark);
            $rs = $item_sql->auto(self::TABLE_ACTIVITY_COMP)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
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
            $score = (int) $rs[0]['score'];
            return self::setApply($scholar_type_id, $student, self::TABLE_ACTIVITY_COMP, $id, $score);
        }
        
        public static function applyActivityRole($name, $student, $time, $role, $rate, $host, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::activityRoleModel($name, $student, $time, $role, $rate, $host, $remark);
            $rs = $item_sql->auto(self::TABLE_ACTIVITY_ROLE)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 主持人或演员
            $score_sql = Impl_Score::getInstance();
            $params = $score_sql->scoreModel($scholar_type_id, '文体活动', $role, '');
            $rs = $score_sql->auto()->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return false;
            }
            $item_score = $rs[0][0];
            $score = (int) $item_score['its_score'];
            return self::setApply($scholar_type_id, $student, self::TABLE_ACTIVITY_ROLE, $id, $score);
        }
        
        public static function delActivityComp($apply_id) {
            return self::delApply($apply_id, self::TABLE_ACTIVITY_COMP, 'ac_id');
        }
        
        public static function delActivityRole($apply_id) {
            return self::delApply($apply_id, self::TABLE_ACTIVITY_ROLE, 'ar_id');
        }
               
        public static function getActivityComp($student, $annual) {
            return self::getApply($student, $annual, self::TABLE_ACTIVITY_COMP, 'ac_id');
        }
        
        public static function getActivityRole($student, $annual) {
            return self::getApply($student, $annual, self::TABLE_ACTIVITY_ROLE, 'ar_id');
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
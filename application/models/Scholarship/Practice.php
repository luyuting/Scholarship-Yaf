<?php
    class Scholarship_PracticeModel {
        use Trait_Scholarship;
        
        const TABLE_PRACTICE = 'tb_practice';
        
        private static $_type = Scholarship_BaseModel::SCHOLAR_PRACTICE;
        
        public static function applyPractice($title, $name, $student, $team_prize, $person_prize,
            $team_role, $remark) {
            $item_sql = Impl_Item::getInstance();
            $model = self::practiceModel($title, $name, $student, $team_prize, $person_prize, $team_role, $remark);
            $rs = $item_sql->auto(self::TABLE_PRACTICE)->buildSave($model)->exec();
            $id = $rs[0];
            if (id == 0 || is_null($id)) {
                return false;
            }
            $scholar_type_id = self::getScholarIdByUser($student);
            if ($scholar_type_id == 0) {
                return false;
            }
            // 不从数据库中读取规则
            $score = 0;
            if ($name == '社区挂职') {
                $score += 5;
            } elseif ($name == '其他类社会实践') {
                $score += 1;
                switch ($team_prize) {
                    case '国家级奖': $score += 8; break;
                    case '省级奖': $score += 6; break;
                    case '市级奖': $score += 4; break;
                    case '校级一等奖': $score += 3; break;
                    case '校级二等奖': $score += 2; break;
                    case '校级三等奖': $score += 1; break;
                    default: $team_prize=''; break;
                }
                switch ($person_prize) {
                    case '国家级优秀个人': $score += 10; break;
                    case '省级优秀个人': $score += 7; break;
                    case '市级优秀个人': $score += 4; break;
                    case '校级优秀个人一等奖': $score += 3; break;
                    case '校级优秀个人二等奖': $score += 2; break;
                    case '校级优秀个人三等奖': $score += 1; break;
                    case '个人调查报告一等奖': $score += 3; break;
                    case '个人调查报告二等奖': $score += 2; break;
                    case '个人调查报告三等奖': $score += 1; break;
                    case '先进个人': $score += 2; break;
                    case '锻炼标兵': $score += 4; break;
                    case '国家级': $score += 20; break;
                    case '省级': $score += 15; break;
                    case '市级': $score += 10; break;
                    case '校级': $score += 7; break;
                    case '学部（学院）级': $score += 5; break;
                    case '军训先进集体成员': $score += 2; break;
                    case '军训先进个人': $score += 2; break;
                    default: $person_prize = ''; break;
                }
                switch ($team_role) {
                    case '队长': $pr_score += 1;
                    case '队员': $pr_score += 2; break;
                    default: $team_role = ''; break;
                }
            }
            return self::setApply($scholar_type_id, $student, self::TABLE_PRACTICE, $id, $score);
        }
        
        public static function delPractice($student, $apply_id) {
            return self::delApply($student, $apply_id, self::TABLE_PRACTICE, 'pr_id');
        }
        
        public static function getPractice($student, $annual) {
            return self::getApply($student, $annual, self::TABLE_PRACTICE, 'pr_id');
        }
        
        private static function practiceModel($title, $name, $student, $team_prize, $person_prize,
            $team_role, $remark) {
            $model_card = [
                'pr_title' => $title,
                'pr_name' => $name,
                'pr_student' => $student,
                'pr_team_prize' => $team_prize,
                'pr_person_prize' => $person_prize,
                'pr_team_role' => $team_role,
                'pr_remark' => $remark
            ];
            return $model_card;
        }
    }
<?php
    class Scholarship_StudyModel {
        use Trait_Apply;
        /*
         * 一等学习奖学金和二等学习奖学金
         * 按成绩排名算
         * 考虑教师端导入而非学生手动录入
         * 如果是导入全体成绩是否需要选择人数比例，自动
         */     
        const TABLE_SCHOLARSHIP = 'tb_scholarship';
        
        public static function applyStudyScholar($student, $scholar_ratio) {
            $annual = date('Y');
            $info = UserModel::getUserInfoById($student);
            $grade = $info['user_grade'];
            $params = [
                'sc_annual' => $annual,
                'sc_grade' => $grade,
                'sc_ratio' => $scholar_ratio
            ];
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->auto(self::TABLE_SCHOLARSHIP)->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return 0;
            }
            $scholar_assoc = $rs[0][0];
            $scholar_type_id = $scholar_assoc['sc_id'];
            return self::setApply($scholar_type_id, $student, self::TABLE_SCHOLARSHIP, $scholar_type_id, 0);
        }
        
        public static function getStudyUnique($student, $annual) {
            $db = Base_Db::getInstance();
            $sql = "select ap_id, ap_scho_type, ap_state, sc_name, sc_ratio from tb_apply, tb_scholarship where ap_item_table = 't_scholarship' and
                ap_student = ? and sc_id = ap_scho_type and sc_annual = ? ";
            $params = [$student, $annual];
            return $db->query($sql, $params);
        }

        public static function delStudyScholar($student, $apply_id) {
            return self::delApply($student, $apply_id, null, null);
        }
    }
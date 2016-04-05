<?php
    class Scholarship_StudyModel {
        use Trait_Apply;
        /*
         * 一等学习奖学金和二等学习奖学金
         * 按成绩排名算
         * 考虑教师端导入而非学生手动录入
         * 如果是导入全体成绩是否需要选择人数比例，自动
         */     
        
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
            $rs = $item_sql->tAuto(Comm_T::TABLE_SCHOLARSHIP)->buildQuery($params)->exec();
            if (empty($rs[0])) {
                return 0;
            }
            $scholar_assoc = $rs[0][0];
            $scholar_type_id = $scholar_assoc['sc_id'];
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_SCHOLARSHIP, $scholar_type_id, 0);
        }
        
        public static function getStudyUnique($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_SCHOLARSHIP, 'sc_id');
        }

        public static function delStudyScholar($student, $apply_id) {
            return self::delApply($student, $apply_id, null, null);
        }
    }
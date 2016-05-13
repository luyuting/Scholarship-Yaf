<?php
    class Scholarship_StudyModel {
        use Trait_Apply;
        /*
         * 一等学习奖学金和二等学习奖学金
         * 按成绩排名算
         * 考虑教师端导入而非学生手动录入
         * 如果是导入全体成绩是否需要选择人数比例，自动设置
         */     
        
        /**
         * 学习优秀奖学金申请
         * @param string $student 学号
         * @param string $scholar_ratio 奖学金获得比率（成绩排名）
         * @return boolean
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
                return false;
            }
            $scholar_assoc = $rs[0][0];
            $scholar_type_id = $scholar_assoc['sc_id'];
            return self::setApply($scholar_type_id, $student, Comm_T::TABLE_SCHOLARSHIP, $scholar_type_id, 0);
        }
        
        /**
         * 删除已经申请的学习优秀奖学金
         * @param string $student
         * @param string $apply_id 申请项目id
         * @return boolean
         */
        public static function delStudyScholar($student, $apply_id) {
            return self::delApply($student, $apply_id, null, null);
        }
        
        /**
         * 获取当年已经申请的学习奖学金项目
         * @param string $student 学号
         * @param string $annual 申请年份
         * @return array
         */
        public static function getStudyUnique($student, $annual) {
            return self::getApply($student, $annual, Comm_T::TABLE_SCHOLARSHIP, 'sc_id');
        }
        
        public static function getAuditProgress($admin_account) {
            $db = Base_Db::getInstance();
            $sql = 'select sc_id from tb_scholarship where (sc_annual, sc_grade) in (select ad_annual,
                ad_grade from tb_admin where ad_account = ?) and sc_type in (?, ?)';
            $params = [$admin_account, Scholarship_BaseModel::SCHOLAR_STUDY_FIRST, Scholarship_BaseModel::SCHOLAR_STUDY_SECOND];
            $rs = $db->query($sql, $params);
            $params = [];
            for ($i = 0; $i < 2; $i ++) {
                $params[] = isset($rs[$i])? $rs[$i]['sc_id']: 0;
            }
            $sql = 'select ap_student student, count(*) uncount from tb_apply where ap_scho_type in (?, ?) 
                and ap_audit = 0 group by student order by student asc';
            return $db->query($sql, $params);
        }
    }
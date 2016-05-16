<?php
    class Scholarship_BaseModel {

        const SCHOLAR_NONE = 0;
        const SCHOLAR_STUDY_FIRST = 1;
        const SCHOLAR_STUDY_SECOND = 2;
        const SCHOLAR_SPIRITUAL = 3;
        const SCHOLAR_ACTIVITY = 4;
        const SCHOLAR_WORK = 5;
        const SCHOLAR_SCIENCE = 6;
        const SCHOLAR_PRACTICE = 7;
    
        // private static $_type = self::SCHOLAR_NONE;
        private function __construct() {
            
        }
        
        public static function editBaseSetting() {

        }
        
        public static function getAllBaseSetting($admin_account) {
            $db = Base_Db::getInstance();
            $sql = 'select i.*, a.* from tb_scholarship i, tb_admin a where a.ad_id = i.sc_admin and   
                (i.sc_annual, i.sc_grade) in (select ad_annual, ad_grade from tb_admin where ad_account = ?)';
            $params = [$admin_account];
            $rs = $db->query($sql, $params);
            return self::packSetting($rs);
        }
        
        public static function getBaseSetting($scholar_id) {     
            $db = Base_Db::getInstance();
            $sql = 'select i.*, a.* from tb_scholarship i, tb_admin a where a.ad_id = i.sc_admin
                and i.sc_id = ?';
            $params = [$scholar_id];
            $rs = $db->query($sql, $params);
            $info = self::packSetting($rs);
            return empty($info)? []: $info[0];
        }
        
        public static function getScholarId($admin_account, $type) {
            $db = Base_Db::getInstance();
            $sql = 'select sc_id from tb_scholarship where (sc_annual, sc_grade) in (select ad_annual,
                ad_grade from tb_admin where ad_account = ?) and sc_type = ? ';
            $params = [$admin_account, $type];
            $rs = $db->query($sql, $params);
            return empty($rs)? 0: (int) $rs[0]['sc_id'];
        }
        
        public static function getScholarItemScore($admin_account, $name) {
            $db = Base_Db::getInstance();
            $sql = 'select i.* from tb_item_score i, tb_scholarship where (sc_annual, sc_grade) in 
                (select ad_annual, ad_grade from tb_admin where ad_account = ? ) and sc_id = its_type and 
                its_name = ? order by its_score desc, its_describe_a desc, its_score_ratio desc';
            $params = [$admin_account, $name];
            $rs = $db->query($sql, $params);
            return $rs;
        }
        
        public static function getScholarNameByType($type) {
            $rs = null;
            switch ($type) {
                case self::SCHOLAR_STUDY_FIRST: $rs = '一等学习优秀奖学金'; break;
                case self::SCHOLAR_STUDY_SECOND: $rs = '二等学习优秀奖学金'; break;
                case self::SCHOLAR_SPIRITUAL: $rs = '精神文明奖学金'; break;
                case self::SCHOLAR_ACTIVITY: $rs = '文体活动奖学金'; break;
                case self::SCHOLAR_WORK: $rs = '社会工作奖学金'; break;
                case self::SCHOLAR_SCIENCE; $rs = '科技创新奖学金'; break;
                case self::SCHOLAR_PRACTICE: $rs = '社会实践奖学金'; break;
                default: break;
            }
            return $rs;
        }
        
        public static function scoreParams($type, $name, $descr_a, $descr_b, $score, $ratio) {
            $model = [
                'its_type' => $type,
                'its_name' => $name,
                'its_describe_a' => $descr_a,
                'its_describe_b' => $descr_b,
                'its_score' => $score,
                'its_score_ratio' => $ratio
            ];
        
            $score_sql = Impl_Score::getInstance();
            $rs = $score_sql->auto()->buildSave($model)->exec();
            if (is_null($rs[0]) || $rs[0] == 0) {
                return false;
            }
            return true;
        }
        
        public static function setBaseSetting($scholar_type, $scholar_ratio, $student_num, $admin_account) {
            $actual_num = round($scholar_ratio * $student_num / 100);
            $scholar_name = self::getScholarNameByType($scholar_type);
            
            $db = Base_Db::getInstance();
            $sql = 'insert into tb_scholarship(sc_type, sc_name, sc_ratio, sc_grade, sc_num, sc_annual,
                sc_admin) (select ?, ?, ?, ad_grade, ?, ad_annual, ad_id from tb_admin where ad_account = ?)';
            $params = [$scholar_type, $scholar_name, $scholar_ratio . '%', $actual_num, $admin_account];
            return $db->getId($sql, $params);
        }
        
        private static function packSetting(array $rs) {
            $info = [];
            if (!empty($rs)) {
                foreach ($rs as $setting) {
                    $info[] = [
                        'scho_id' => $setting['sc_id'],
                        'scho_name' => $setting['sc_name'],
                        'scho_ratio' => $setting['sc_ratio'],
                        'scho_grade' => $setting['sc_grade'],
                        'scho_num' => $setting['sc_num'],
                        'scho_annual' => $setting['sc_annual'],
                        'scho_admin' => $setting['ad_name'],
                        'scho_admin_id' => $setting['ad_id'],
                        'scho_edit_time' => $setting['sc_edit_time']
                    ];
                }
            }
            return $info;
        }
        
    }
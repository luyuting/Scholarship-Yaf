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
    
        protected $_type = self::SCHOLAR_NONE;
        
        public static function editBaseSetting() {
            $sql = '';
        }
        
        public static function getAllBaseSetting($admin_account) {
            $db = Base_Db::getInstance();
            $sql = 'select i.*, a.* from tb_scholarship i, tb_admin a where a.ad_id = i.sc_admin and 
                (i.sc_annual, i.sc_grade) in (select ad_annual, ad_grade from tb_admin where ad_id = ?)';
            $params = [$admin_account];
            $res = $db->query($sql, $params);
            return self::packSetting($res);
        }
        
        public static function getBaseSetting($scholar_id) {     
            $db = Base_Db::getInstance();
            $sql = 'select i.*, a.* from tb_scholarship i, tb_admin a where a.ad_id = i.sc_admin
                and i.sc_id = ?';
            $params = [$scholar_id];
            $res = $db->query($sql, $params);
            $info = self::packSetting($res);
            return empty($info)? []: $info[0];
        }
        
        public static function getScholarNameByType($type) {
            $res = null;
            switch ($type) {
                case self::SCHOLAR_STUDY_FIRST: $res = '一等学习优秀奖学金'; break;
                case self::SCHOLAR_STUDY_SECOND: $res = '二等学习优秀奖学金'; break;
                case self::SCHOLAR_SPIRITUAL: $res = '精神文明奖学金'; break;
                case self::SCHOLAR_ACTIVITY: $res = '文体活动奖学金'; break;
                case self::SCHOLAR_WORK: $res = '社会工作奖学金'; break;
                case self::SCHOLAR_SCIENCE; $res = '科技创新奖学金'; break;
                case self::SCHOLAR_PRACTICE: $res = '社会实践奖学金'; break;
                default: break;
            }
            return $res;
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
        
        private static function packSetting(array $res) {
            $info = [];
            if (!empty($res)) {
                foreach ($res as $setting) {
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
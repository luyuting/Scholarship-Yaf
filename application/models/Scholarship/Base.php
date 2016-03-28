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
        
        public static function getBaseSetting() {
            $sql = 'SELECT * FROM `tb_scholarship` WHERE `sc_grade` = ? AND `sc_annual` = ?';
        }
        
        public static function setBaseSetting() {
            $sql = 'INSERT INTO `tb_scholarship` () VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        }
    }
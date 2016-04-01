<?php
    trait Trait_Scholarship {
        
        private function __construct() {
            
        }
        
        public static function getType() {
            return self::$_type;
        }
        
        public static function getName() {
            return Scholarship_BaseModel::getScholarNameByType(self::_type);
        }
    }
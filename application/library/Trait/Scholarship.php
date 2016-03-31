<?php
    trait Trait_Scholarship {
        
        private function __construct() {
            
        }
        
        public static function getType() {
            return self::$_type;
        }
    }
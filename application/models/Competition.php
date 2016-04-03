<?php
    class CompetitionModel {
        
        private static function getCompetition($sc_annual, $type) {
            
        }
        
        public static function getActivityComp($sc_annual) {
            return self::getCompetition($sc_annual, Scholarship_ActivityModel::getType());
        }
        
        public static function getScieTechComp($sc_annual) {
            return self::getCompetition($sc_annual, Scholarship_ScienceModel::getType());
        }
        
        public static function getTeamRatio($user_id, $sc_annual) {
            
        }
    }
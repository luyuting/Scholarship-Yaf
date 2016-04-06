<?php
    class User_RuleController extends Abstract_Controller_Ajax {
        
        public function getActivityListAction() {
            $this->getCompetion(Scholarship_ActivityModel::getType());
        }
        
        public function getScieTechListAction() {
            $this->getCompetion(Scholarship_ScienceModel::getType());
        }
        
        private function getCompetion($type) {
            $user_id = $this->getUser();
            $info = CompetitionModel::getCompetitionByUser($user_id, $type);
            $this->success($info);
        }
    }
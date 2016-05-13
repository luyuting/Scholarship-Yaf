<?php
    class Admin_ProgressController extends Abstract_Controller_AjaxAd {
        
        public function studyAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_StudyModel::getAuditProgress($admin_account);
            $this->success($info);
        }
        
        public function spiritualAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_SpiritualModel::getAuditProgress($admin_account);
            $this->success($info);
        }
        
        public function activityAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_ActivityModel::getAuditProgress($admin_account);
            $this->success($info);
        }
        
        public function workAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_WorkModel::getAuditProgress($admin_account);
            $this->success($info);
        }
        
        public function scienceAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_ScienceModel::getAuditProgress($admin_account);
            $this->success($info);
        }
        
        public function practiceAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_PracticeModel::getAuditProgress($admin_account);
            $this->success($info);
        }
    }
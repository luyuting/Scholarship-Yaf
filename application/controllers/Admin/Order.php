<?php
    class Admin_OrderController extends Abstract_Controller_AjaxAd {
        
        public function spiritualAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_SpiritualModel::getOrderByAdmin($admin_account);
            $this->success($info);
        }
        
        public function activityAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_ActivityModel::getOrderByAdmin($admin_account);
            $this->success($info);
        }
        
        public function workAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_WorkModel::getOrderByAdmin($admin_account);
            $this->success($info);
        }
        
        public function scienceAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_ScienceModel::getOrderByAdmin($admin_account);
            $this->success($info);
        }
        
        public function practiceAction() {
            $admin_account = $this->getAdmin();
            $info = Scholarship_PracticeModel::getOrderByAdmin($admin_account);
            $this->success($info);
        }
    }
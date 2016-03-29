<?php
    class Admin_BaseController extends Abstract_Controller_AjaxAd {
        
        protected $_no_login = ['adminlogin'];
        public function adminLoginAction() {
            $req = $this->getRequest();
            $admin_account = $req->getPost('admin_account');
            $admin_pass = $req->getPost('admin_pass');
            
            if (!Comm_ArgsCheck::string($admin_account) || !Comm_ArgsCheck::string($admin_pass,
                    Comm_ArgsCheck::USER_PASS)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
            }
            $info = AdminModel::getAdminInfoByAccount($admin_account);
            if (empty($info)) {
                $this->error(Comm_Const::E_WRONG_ACCOUNT);
                return;
            }
            if ($admin_pass != $info['ad_pass']) {
                $this->error(Comm_Const::E_WRONG_PASS);
                return;
            }
            $this->setAdmin($admin_account);
            $this->setCookie($admin_account);
            $this->success($info);
        }
        
        public function scholarBaseAction() {
            $req = $this->getRequest();
            $scholar_type = $req->getPost('scho_type');
            $scholar_ratio = $req->getPost('scho_ratio');
            $student_num = $req->getPost('student_num');
            if (!Comm_ArgsCheck::int($scholar_type, 1, 7) || !Comm_ArgsCheck::int($scholar_ratio, 1, 100)
                || !Comm_ArgsCheck::int($student_num, 1)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $admin_account = $this->getAdmin();         
            $scholar_id = Scholarship_BaseModel::setBaseSetting($scholar_type, $scholar_ratio, $student_num, $admin_account);
            if ($scholar_id == 0) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $info = Scholarship_BaseModel::getBaseSetting($scholar_id);
            $this->success($info);
        }
        
        public function scholarBaseAllAction() {
            $req = $this->getRequest();
            $admin_account = $this->getAdmin();
            $info = Scholarship_BaseModel::getAllBaseSetting($admin_account);
            $this->success($info);
        }
        
        public function addAdminAction() {
            
        }
        
        public function delAdminAction() {
            
        }
    }
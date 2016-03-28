<?php
    class Admin_BaseController extends Abstract_Controller_AjaxAd {
        
        protected $_no_login = ['adminlogin'];
        public function adminLoginAction() {
            $req = $this->getRequest();
            $admin_account = $req->getPost('admin_account');
            $admin_pass = $req->getPost('admin_pass');
            
            if (!Comm_ArgsCheck::string($admin_account) || !Comm_ArgsCheck::string($admin_pass,
                    Comm_ArgsCheck::USER_PASS)) {
                $this->response(10013, ['cause' => 'invalid params']);
            }
            $info = AdminModel::getAdminInfoByAccount($admin_account);
            if (empty($info)) {
                $this->response(10011, ['cause' => 'no such user']);
                return;
            }
            if ($admin_pass != $info['ad_pass']) {
                $this->response(10012, ['cause' => 'wrong password']);
                return;
            }
            $this->setAdmin($admin_account);
            $this->setCookie($admin_account);
            $this->response(10010, [], $info);
        }
        
        public function addAdminAction() {
            
        }
        
        public function delAdminAction() {
            
        }
    }
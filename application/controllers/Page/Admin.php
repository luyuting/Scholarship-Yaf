<?php
    class Page_AdminController extends Abstract_Controller_DefaultAd {
        
        protected $_no_login = ['login'];
        
        public function loginAction() {
            
        }
        
        public function homeAction() {
            $admin_account = $this->getAdmin();
            
            $info = AdminModel::getAdminInfoByAccount($admin_account);
            
            $admin_authority = null;
            switch ($info['ad_rate']) {
                case 1: $admin_authority = '高级权限'; break;
                case 2: $admin_authority = '中等权限'; break;
                default: break;
            }
            $card = [
                'admin_id' => $info['ad_id'],
                'admin_account' => $info['ad_account'],
                'admin_name' => $info['ad_name'],
                'admin_grade' => $info['ad_grade'],
                'admin_rate' => $info['ad_rate'],
                'admin_authority' => $admin_authority
            ];
            $this->getView()->assign($card);
        }
        
        public function settingAction() {
            
        }       
    }
<?php
    class Page_AdminController extends Abstract_Controller_DefaultAd {
        
        protected $_no_login = ['login'];
        
        public function loginAction() {
            
        }
        
        public function auditAction() {
            
        }
        
        public function mainAction() {
            
        }
        
        public function baseRuleAction() {
            $admin_account = $this->getAdmin();
            $info = AdminModel::getAdminInfoByAccount($admin_account);
            $items = [
                [Scholarship_BaseModel::SCHOLAR_STUDY_FIRST, 5],
                [Scholarship_BaseModel::SCHOLAR_STUDY_SECOND, 20],
                [Scholarship_BaseModel::SCHOLAR_SPIRITUAL, 10],
                [Scholarship_BaseModel::SCHOLAR_ACTIVITY, 6],
                [Scholarship_BaseModel::SCHOLAR_WORK, 4],
                [Scholarship_BaseModel::SCHOLAR_SCIENCE, 4],
                [Scholarship_BaseModel::SCHOLAR_PRACTICE, 6]
            ];
            $scholar_items = [];
            foreach ($items as $item) {
                $scholar_items[] = [
                    'type' => $item[0],
                    'name' => Scholarship_BaseModel::getScholarNameByType($item[0]),
                    'ratio' => $item[1]
                ];
            }
            $_token = Util_Secure::generateRandom();
            $_SESSION['_token'] = $_token;
            $card = [
                'grade' => $info['ad_grade'], 
                'scholar_items' => $scholar_items,
                '_token' => $_token
            ];
            $this->getView()->assign($card);
        }
        
        public function ruleAction() {
            $_token = $this->getRequest()->getQuery('_token');
            if (!isset($_SESSION['_token']) || $_token != $_SESSION['_token']) {
                // $this->getResponse()->setHeader('HTTP/1.1', '400 Bad Request');
                $this->getResponse()->setRedirect('baserule');
                return;
            }
            unset($_SESSION['_token']);
        }
        /*
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
        }*/     
    }
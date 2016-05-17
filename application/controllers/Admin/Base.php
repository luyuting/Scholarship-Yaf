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
        
        public function logoutAction() {
            $this->unsetAdmin();
            $this->getResponse()->setRedirect('/page_admin/login');
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
        
        public function scholarStudyAction() {
            
        }
        
        public function scholarSpiritualAction() {
            $this->scholarItemCheck(Scholarship_SpiritualModel::getType());
        }
        
        public function scholarActivityAction() {
            $this->scholarItemCheck(Scholarship_ActivityModel::getType());
        }
        
        public function scholarWorkAction() {
            $this->scholarItemCheck(Scholarship_WorkModel::getType());
        }
        
        public function scholarWorkCadreAction() {
            $req = $this->getRequest();
            $position = $req->getPost('position');
            $score = $req->getPost('score');
            if (!Comm_ArgsCheck::string($position) || !Comm_ArgsCheck::int($score, 0)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $admin_account = $this->getAdmin();
            $rs = Scholarship_WorkModel::setWorkScore($admin_account, $position, $score);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function scholarScienceAction() {
            $this->scholarItemCheck(Scholarship_ScienceModel::getType());
        }
        
        public function scholarPracticeAction() {
            $this->scholarItemCheck(Scholarship_PracticeModel::getType());
        }
        
        public function scholarItemScoreAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            if (!Comm_ArgsCheck::string($name)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $admin_account = $this->getAdmin();
            $info = Scholarship_BaseModel::getScholarItemScore($admin_account, $name);
            $this->success($info);
        }
        
        public function scholarWorkScoreAction() {
            $req = $this->getRequest();
            $admin_account = $this->getAdmin();
            $info = Scholarship_WorkModel::getWorkScore($admin_account);
            $this->success($info);
        }
        
        public function getActivityCompAction() {
            $this->getCompetition(Scholarship_ActivityModel::getType());
        }
        
        public function getScieTechCompAction() {
            $this->getCompetition(Scholarship_ScienceModel::getType());
        }
        
        public function setActivityCompAction() {
            $this->setCompetition(Scholarship_ActivityModel::getType());
        }
        
        public function setScieTechCompAction() {
            $this->setCompetition(Scholarship_ScienceModel::getType());
        }
        
        public function addAdminAction() {
            
        }
        
        public function delAdminAction() {
            
        }
        
        public function ruleAction() {
            $data = [];
            $content = [];
            $fp = fopen(APP_PATH . '/static/ruletpl.txt', 'r');
            if (is_resource($fp)) {
                $lines = '';
                while (($buffer = fgets($fp)) !== false) {
                    $buffer = trim($buffer);
                    // 跳过注释或空白行
                    if (empty($buffer) || substr($buffer, 0, 1) == '#') {
                        continue;
                    }
                    // 声明奖学金类型
                    if (preg_match('/^\[(\\d)\]$/', $buffer, $matches) && isset($matches[1])) {
                        if (!empty($lines)) {
                            $content['data'] = $lines;
                            $data[] = $content;
                            $content = [];
                            $lines = '';
                        }
                        
                        $type = (int) $matches[1];
                        $name = Scholarship_BaseModel::getScholarNameByType($type);
                        $content = [
                            'type' => $type,
                            'name' => $name
                        ];
                    // 规则表
                    } elseif (substr($buffer, 0, 1) == ':') {
                        if (!empty($lines)) {
                            $content['data'] = $lines;
                            $data[] = $content;
                            unset($content['data']);
                            $lines = '';
                        }
                        
                        $info = explode(';', substr($buffer, 1));
                        $content['table'] = $info[0];
                        $content['basic_info'] = $info[1];
                    } else {
                        $line = '<tr><td>' . $buffer . '</td></tr>';
                        $line = str_replace(',', '</td><td>', $line);
                        $lines .= $line;
                    }
                }
                fclose($fp);
            }
            $this->success($data);
        }
        
        public function scholarItemAction() {
            $this->scholarItemCheck($this->getRequest()->getPost('type'));
        }
        
        private function scholarItemCheck($type) {
            if (!Comm_ArgsCheck::int($type, 1, 7)) {
                $this->error(Comm_Const::E_INTERNAL);
                return;
            }
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $descr_a = $req->getPost('descr_a');
            $descr_b = $req->getPost('descr_b');
            $score = $req->getPost('score');
            $ratio = $req->getPost('ratio');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($descr_a)
            || !Comm_ArgsCheck::string($descr_b, Comm_ArgsCheck::BASE_EMPTY_STR) || !Comm_ArgsCheck::int($score, null, null, 0) || 
            !Comm_ArgsCheck::float($ratio, 0, null, 1)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $admin_account = $this->getAdmin();
            $scholar_type_id = Scholarship_BaseModel::getScholarId($admin_account, $type);
            if ($scholar_type_id == 0) {
                $this->error(Comm_Const::E_INTERNAL);
                return;
            }
            if (!Scholarship_BaseModel::scoreParams($scholar_type_id, $name, $descr_a, $descr_b, $score, $ratio)) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        private function setCompetition($type) {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $rate = $req->getPost('rate');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($rate)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
            }
            $admin_account = $this->getAdmin();
            $rs = CompetitionModel::setCompetition($name, $rate, $admin_account, $type);
            if (!$rs) {
                $this->error(Comm_Const::E_DUPLICATE);
                return;
            }
            $this->success();
        }
        
        private function getCompetition($type) {
            $admin_account = $this->getAdmin();
            $info = CompetitionModel::getCompetition($admin_account, $type);
            $this->success($info);
        }
    }
<?php
    class User_BaseController extends Abstract_Controller_Ajax {
        
        // 申请学习奖学金
        public function applyStudyAction() {
            $req = $this->getRequest();
            $ratio = $req->getPost('ratio');
            if (!Comm_ArgsCheck::string($ratio, '/^\\d{1,}%$/')) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $check_unique = Scholarship_StudyModel::getStudyUnique($user_id, date('Y'));
            if (count($check_unique) > 0) {
                $this->error(Comm_Const::E_DUPLICATE);
                return;
            }
            $rs = Scholarship_StudyModel::applyStudyScholar($user_id, $ratio);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        } 
        // 取消申请学习奖学金
        public function delStudyAction() {
            $func = ['Scholarship_StudyModel', 'delStudyScholar'];
            $this->delScholarItem($func);
        }
        // 检查当年是否已经申请过学习奖学金
        public function studyUniqueAction() {
            $this->getScholarItem(['Scholarship_StudyModel', 'getStudyUnique']);
        }
        // 申请民主评议单项
        public function applyAppraisalAction() {
            $req = $this->getRequest();
            $ratio = $req->getPost('ratio');
            if (!Comm_ArgsCheck::string($ratio, '/^\\d{1,}-\\d{1,}%$/')) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $check_unique = Scholarship_SpiritualModel::getAppraisal($user_id, date('Y'));
            if (count($check_unique) > 0) {
                $this->error(Comm_Const::E_DUPLICATE);
                return;
            }
            $rs = Scholarship_SpiritualModel::applyAppraisal($user_id, $ratio);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delAppraisalAction() {
            $this->delScholarItem(['Scholarship_SpiritualModel', 'delAppraisal']);
        }

        public function getAppraisalAction() {
            $this->getScholarItem(['Scholarship_SpiritualModel', 'getAppraisal']);
        }
        // 文明寝室
        public function applyDormitoryAction() {
            $req = $this->getRequest();
            $score = $req->getPost('score');
            if (!Comm_ArgsCheck::int($score, 0, 10)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $check_unique = Scholarship_SpiritualModel::getDormitory($user_id, date('Y'));
            if (count($check_unique) > 0) {
                $this->error(Comm_Const::E_DUPLICATE);
                return;
            }
            $rs = Scholarship_SpiritualModel::applyDormitory($user_id, $score);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delDormitoryAction() {
            $this->delScholarItem(['Scholarship_SpiritualModel', 'delDormitory']);
        }
        
        public function getDormitoryAction() {
            $this->getScholarItem(['Scholarship_SpiritualModel', 'getDormitory']);
        }
        // 精神文明奖励
        public function applySpiritualRewardAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $item = $req->getPost('item');
            $rate = $req->getPost('rate');
            $time = $req->getPost('time');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($item) 
                || !Comm_ArgsCheck::string($rate, Comm_ArgsCheck::BASE_EMPTY_STR)
                || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_SpiritualModel::applySpiritualReward($user_id, $name, $item, $rate, $time);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delSpiritualRewardAction() {
            $this->delScholarItem(['Scholarship_SpiritualModel', 'delSpiritualReward']);
        }
        
        public function getSpiritualRewardAction() {
            $this->getScholarItem(['Scholarship_SpiritualModel', 'getSpiritualReward']);
        }
        // 文体活动竞赛
        public function applyActivityCompAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $rate = $req->getPost('rate');
            $prize = $req->getPost('prize');
            $role = $req->getPost('role');
            $rule = $req->getPost('rule');
            $break = $req->getPost('break');
            $team_num = $req->getPost('team_num');
            $time = $req->getPost('time');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($rate) || !Comm_ArgsCheck::string($prize)
                || !Comm_ArgsCheck::string($role) || !Comm_ArgsCheck::string($rule) || !Comm_ArgsCheck::string($break)
                || !Comm_ArgsCheck::int($team_num) || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME)
                || !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_ActivityModel::applyActivityComp($name, $user_id, $rate, $prize, $role, $rule, $break, $team_num, $time, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delActivityCompAction() {
            $this->delScholarItem(['Scholarship_ActivityModel', 'delActivityComp']);
        }
        
        public function getActivityCompAction() {
            $this->getScholarItem(['Scholarship_ActivityModel', 'getActivityComp']);
        }
        // 主持人或演员
        public function applyActivityRoleAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $rate = $req->getPost('rate');
            $role = $req->getPost('role');
            $host = $req->getPost('host');
            $time = $req->getPost('time');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($rate) || !Comm_ArgsCheck::string($role) 
                || !Comm_ArgsCheck::string($host, Comm_ArgsCheck::BASE_EMPTY_STR) || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME)
                || !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_ActivityModel::applyActivityRole($name, $user_id, $time, $role, $rate, $host, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delActivityRoleAction() {
            $this->delScholarItem(['Scholarship_ActivityModel', 'delActivityRole']);
        }
        
        public function getActivityRoleAction() {
            $this->getScholarItem(['Scholarship_ActivityModel', 'getActivityRole']);
        }
        // 学生工作
        public function applyWorkCadreAction() {
            $req = $this->getRequest();
            $level = $req->getPost('level');
            $last_time = $req->getPost('last_time');
            $name = $req->getPost('name');
            $begin_time = $req->getPost('begin_time');
            $end_time = $req->getPost('end_time');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($level) || !Comm_ArgsCheck::enum($last_time, ['一学年', '一学期'])
                || !Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($begin_time, Comm_ArgsCheck::DATETIME)
                || !Comm_ArgsCheck::string($end_time, Comm_ArgsCheck::DATETIME) || 
                !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }   
            $user_id = $this->getUser();
            $rs = Scholarship_WorkModel::applyWorkCadre($level, $last_time, $user_id, $name, $begin_time, $end_time, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delWorkCadreAction() {
            $this->delScholarItem(['Scholarship_WorkModel', 'delWorkCadre']);
        }
        
        public function getWorkCadreAction() {
            $this->getScholarItem(['Scholarship_WorkModel', 'getWorkCadre']);
        }
        // 荣誉称号
        public function applyWorkRewardAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $rate = $req->getPost('rate');
            $time = $req->getPost('time');
            
            $user_id = $this->getUser();
            $rs = Scholarship_WorkModel::applyWorkReward($name, $user_id, $rate, $time);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delWorkRewardAction() {
            $this->delScholarItem(['Scholarship_WorkModel', 'delWorkReward']);
        }
        
        public function getWorkRewardAction() {
            $this->getScholarItem(['Scholarship_WorkModel', 'getWorkReward']);
        }
        // 科技创新竞赛
        public function applyScieTechCompAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $rate = $req->getPost('rate');
            $prize = $req->getPost('prize');
            $team_status = $req->getPost('team_status');
            $team_num = $req->getPost('team_num');
            $team_order = $req->getPost('team_order');
            $host = $req->getPost('host');
            $time = $req->getPost('time');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($rate) || !Comm_ArgsCheck::string($prize)
                || !Comm_ArgsCheck::enum($team_status, ['队长', '队员']) || !Comm_ArgsCheck::int($team_num, 1, null, 1)
                || !Comm_ArgsCheck::string($team_order) || !Comm_ArgsCheck::string($host, Comm_ArgsCheck::BASE_EMPTY_STR)
                || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_ScienceModel::applyScieTechComp($user_id, $name, $rate, $prize, $team_status, $team_num, $team_order, $host, $time, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delScieTechCompAction() {
            $this->delScholarItem(['Scholarship_ScienceModel', 'delSienTechComp']);
        }
        
        public function getScieTechCompAction() {
            $this->getScholarItem(['Scholarship_ScienceModel', 'getSienTechComp']);
        }
        // 科技创新论文
        public function applyPaperAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $journal = $req->getPost('journal');
            $level = $req->getPost('level');
            $vol = $req->getPost('vol');
            $ei_sci = $req->getPost('ei_sci');
            $team_num = $req->getPost('team_num');
            $team_order = $req->getPost('team_order');
            $time = $req->getPost('time');
            $discuss_score = $req->getPost('discuss_score');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($journal, Comm_ArgsCheck::BASE_EMPTY_STR)
                || !Comm_ArgsCheck::string($level) || !Comm_ArgsCheck::string($vol, Comm_ArgsCheck::BASE_EMPTY_STR)
                || !Comm_ArgsCheck::enum($ei_sci, ['是', '否'])
                || !Comm_ArgsCheck::int($team_num, 1, null, 1) || !Comm_ArgsCheck::string($team_order)
                || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME) || !Comm_ArgsCheck::float($discuss_score, 0)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_ScienceModel::applyPaper($user_id, $name, $journal, $level, $vol, $ei_sci, $team_num, $team_order, $time, $discuss_score);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delPaperAction() {
            $this->delScholarItem(['Scholarship_ScienceModel', 'delPaper']);
        }
        
        public function getPaperAction() {
            $this->getScholarItem(['Scholarship_ScienceModel', 'getPaper']);
        }
        // 科技创新发明
        public function applyInventionAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $account = $req->getPost('account');
            $team_num = $req->getPost('team_num');
            $team_order = $req->getPost('team_order');
            $type = $req->getPost('type');
            $time = $req->getPost('time');
            $discuss_score = $req->getPost('discuss_score');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($account) || !Comm_ArgsCheck::string($type)
                || !Comm_ArgsCheck::int($team_num, 1, null, 1) || !Comm_ArgsCheck::string($team_order)
                || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME) || !Comm_ArgsCheck::float($discuss_score, 0)
                || !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                    $this->error(Comm_Const::E_INVALID_PARAM);
                    return;
                }
            $user_id = $this->getUser();
            $rs = Scholarship_ScienceModel::applyInvention($user_id, $name, $account, $team_num, $team_order, $type, $time, $discuss_score, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delInventionAction() {
            $this->delScholarItem(['Scholarship_ScienceModel', 'delInvention']);
        }
        
        public function getInventionAction() {
            $this->getScholarItem(['Scholarship_ScienceModel', 'getInvention']);
        }
        // 创新创业项目
        public function applyScieTechProjectAction() {
            $req = $this->getRequest();
            $name = $req->getPost('name');
            $rate = $req->getPost('rate');
            $prize = $req->getPost('prize');
            $team_num = $req->getPost('team_num');
            $team_order = $req->getPost('team_order');
            $time = $req->getPost('time');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($name) || !Comm_ArgsCheck::string($rate) || !Comm_ArgsCheck::string($prize)
                || !Comm_ArgsCheck::int($team_num, 1, null, 1) || !Comm_ArgsCheck::string($team_order)
                || !Comm_ArgsCheck::string($time, Comm_ArgsCheck::DATETIME) 
                || !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_ScienceModel::applyScieTechProject($user_id, $name, $rate, $prize, $team_num, $team_order, $time, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delScieTechProjectAction() {
            $this->delScholarItem(['Scholarship_ScienceModel', 'delSienTechProject']);
        }
        
        public function getScieTechProjectAction() {
            $this->getScholarItem(['Scholarship_ScienceModel', 'getSienTechProject']);
        }
        // 社会实践
        public function applyPracticeAction() {
            $req = $this->getRequest();
            $title = $req->getPost('title');
            $name = $req->getPost('name');
            $team_prize = $req->getPost('team_prize');
            $person_prize = $req->getPost('person_prize');
            $team_role = $req->getPost('team_role');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::string($title, Comm_ArgsCheck::BASE_EMPTY_STR) || !Comm_ArgsCheck::string($name)
                || !Comm_ArgsCheck::string($team_prize, Comm_ArgsCheck::BASE_EMPTY_STR) 
                || !Comm_ArgsCheck::string($person_prize, Comm_ArgsCheck::BASE_EMPTY_STR)
                || !Comm_ArgsCheck::string($team_role, Comm_ArgsCheck::BASE_EMPTY_STR)
                || !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_PracticeModel::applyPractice($title, $name, $user_id, $team_prize, $person_prize, $team_role, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        public function delPracticeAction() {
            $this->delScholarItem(['Scholarship_PracticeModel', 'delPractice']);
        }
        
        public function getPracticeAction() {
            $this->getScholarItem(['Scholarship_PracticeModel', 'getPractice']);
        }
        
        private function delScholarItem(callable $func) {
            $req = $this->getRequest();
            $apply_id = $req->getPost('apply_id');
            if (!Comm_ArgsCheck::int($apply_id, 1)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = call_user_func_array($func, [$user_id, $apply_id]);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        
        private function getScholarItem(callable $func) {
            $req = $this->getRequest();
            $annual = $req->getPost('annual', date('Y'));
            if (!Comm_ArgsCheck::string($annual, Comm_ArgsCheck::YEAR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $info = call_user_func_array($func, [$user_id, $annual]);
            $this->success($info);
        }
    }
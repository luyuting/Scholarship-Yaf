<?php
    class Admin_AuditController extends Abstract_Controller_AjaxAd {
        
        public function studyAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getStudyList($admin_account);
            $this->success($info);
        }
        
        public function appraisalAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getAppraisalList($admin_account);
            $this->success($info);
        }
        
        public function dormitoryAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getDormitoryList($admin_account);
            $this->success($info);
        }
        
        public function spiritualRewardAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getSpiritualRewardList($admin_account);
            $this->success($info);
        }
        
        public function activityCompAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getActivityCompList($admin_account);
            $this->success($info);
        }
        
        public function activityRoleAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getActivityRoleList($admin_account);
            $this->success($info);
        }
        
        public function workCadreAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getWorkCadreList($admin_account);
            $this->success($info);
        }
        
        public function workRewardAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getWorkRewardList($admin_account);
            $this->success($info);
        }
        
        public function scieTechCompAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getScieTechCompList($admin_account);
            $this->success($info);
        }
        
        public function paperAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getPaperList($admin_account);
            $this->success($info);
        }
        
        public function inventionAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getInventionList($admin_account);
            $this->success($info);
        }
        
        public function scieTechProjectAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getScieTechProjectList($admin_account);
            $this->success($info);
        }
        
        public function practiceAction() {
            $admin_account = $this->getAdmin();
            $info = AuditModel::getPracticeList($admin_account);
            $this->success($info);
        }
        
        public function auditAction() {
            $req = $this->getRequest();
            $apply_id = $req->getPost('apply_id');
            $state = $req->getPost('state');
            $remark = $req->getPost('remark');
            if (!Comm_ArgsCheck::int($apply_id, 1) || !Comm_ArgsCheck::string($state) || 
                !Comm_ArgsCheck::string($remark, Comm_ArgsCheck::BASE_EMPTY_STR)) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $admin_account = $this->getAdmin();
            $rs = AuditModel::auditScholar($apply_id, $admin_account, $state, $remark);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
    }
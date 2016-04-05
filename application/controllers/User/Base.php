<?php
    class User_BaseController extends Abstract_Controller_Ajax {
        
        public function applyStudyAction() {
            $req = $this->getRequest();
            $ratio = $req->getPost('ratio', '5%');
            if (!Comm_ArgsCheck::string($ratio, '/^\\d{1,}%$/')) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $rs = Scholarship_StudyModel::applyStudyScholar($user_id, $ratio);
            if (!$rs) {
                $this->error(Comm_Const::E_UNKOWN);
                return;
            }
            $this->success();
        }
        // 检查当年是否已经申请过学习奖学金
        public function studyUniqueAction() {
            $req = $this->getRequest();
            $annual = $req->getPost('annual', date('Y'));
            if (!Comm_ArgsCheck::string($annual, '/^\\d{4}$/')) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            }
            $user_id = $this->getUser();
            $info = Scholarship_StudyModel::getStudyUnique($user_id, $annual);
            $this->success($info);
        }
    }
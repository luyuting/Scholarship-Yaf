<?php
    class User_BaseController extends Abstract_Controller_Ajax {
        
        public function applyStudyAction() {
            
        }
        
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
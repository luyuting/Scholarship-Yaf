<?php
    class User_LoginController extends Abstract_Controller_Ajax {
        
        protected $_no_login = ['studentlogin'];
        
        public function studentLoginAction() {
            $req = $this->getRequest();
            $user_id = $req->getPost('user_id');
            $user_pass = $req->getPost('user_pass');
            
            if (!(Comm_ArgsCheck::string($user_id, Comm_ArgsCheck::USER_STUDENT_ID)
                && Comm_ArgsCheck::string($user_pass, Comm_ArgsCheck::USER_PASS))) {
                $this->error(Comm_Const::E_INVALID_PARAM);
                return;
            } 
            
            $info = UserModel::getUserInfoById($user_id);
            
            if (empty($info)) {
                $this->error(Comm_Const::E_WRONG_ACCOUNT);
                return;
            }
            if ($user_pass != $info['user_pass']) {
                $this->error(Comm_Const::E_WRONG_PASS);
                return;
            }
            
            $this->setUser($user_id);
            $this->setCookie($user_id);
            $this->success($info);
        }
        
    }
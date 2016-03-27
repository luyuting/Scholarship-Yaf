<?php
    class User_LoginController extends Abstract_Controller_Ajax {
        
        public function studentLoginAction() {
            $req = $this->getRequest();
            $user_id = $req->getPost('user_id');
            $user_pass = $req->getPost('user_pass');
            // Check Params
            if(!(Comm_ArgsCheck::string($user_id, Comm_ArgsCheck::USER_STUDENT_ID)
                && Comm_ArgsCheck::string($user_pass, Comm_ArgsCheck::USER_PASS))) {
                $this->response(10003, ['cause' => 'invalid params']);
                return;
            } 
            // Get Info
            $info = UserModel::getUserInfoById($user_id);
            // Validate Login
            if(empty($info)) {
                $this->response(10001, ['cause' => 'no such user']);
                return;
            }
            if($user_pass != $info['userPass']) {
                $this->response(10002, ['cause' => 'wrong password']);
                return;
            }
            $this->setCookie($user_id);
            $this->response(10000, [], $info);
        }
        
        private function setCookie($user_id, $expire = 7 * 86400) {
            $time = time();
            setcookie('ltime', $time, $time + $expire, '/');
            setcookie('sid', hash('sha256', $user_id . $time) . '.' . base64_encode($user_id), $time + $expire, '/');
        }
    }
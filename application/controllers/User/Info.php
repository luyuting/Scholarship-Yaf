<?php
    class User_InfoController extends Abstract_Controller_Ajax {
        
        public function infoAction() {
            $user_id = $this->getUser();
            $info = UserModel::getUserInfoById($user_id);
            unset($info['user_pass']);
            $this->success($info);
        }
        
    }
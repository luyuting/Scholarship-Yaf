<?php
    class Abstract_Controller_Default extends Yaf_Controller_Abstract {
        
        // 检查是否登录，设置免登录页面
        protected $_no_login = [];
        
        public function init() {
            if (empty($this->_no_login) || !in_array($this->getRequest()->getActionName(), $this->_no_login)) {
                try {
                    $this->loginValidate();
                } catch(Exception_NoLogin $e) {
                    $this->getResponse()->setRedirect('/page/login');
                }
            }
        }
        
        protected function loginValidate() {
            
            // validate, if not login, throw exception
            if (!empty($_COOKIE['sid']) && !empty($_COOKIE['ltime'])) {
                $det = explode('.', $_COOKIE['sid']);
                if (count($det) == 2) {
                    $sid = base64_decode($det[1], true);
                    if ($det[0] == hash('sha256', $sid . $_COOKIE['ltime'])) {
                        return;
                    }
                }
                
            }
            throw new Exception_NoLogin();
            
        }
    }
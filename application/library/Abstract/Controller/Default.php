<?php
    class Abstract_Controller_Default extends Yaf_Controller_Abstract {
        
        // 检查是否登录，设置免登录页面
        protected $_no_login = [];
        
        private $_login_user = null;
        
        public function init() {
            if (empty($this->_no_login) || !in_array($this->getRequest()->getActionName(), $this->_no_login)) {
                try {
                    $this->loginValidate();
                } catch(Exception_NoLogin $e) {
                    $this->getResponse()->setRedirect('/page_user/login');
                }
            }
        }
        
        private function loginValidate() {
            
            // validate, if not login, throw exception
            session_start();
            if (isset($_SESSION['sid'])) {
                $this->_login_user = $_SESSION['sid'];
                return;
            }
            
            if (!empty($_COOKIE['sid']) && !empty($_COOKIE['ltime'])) {
                $det = explode('.', $_COOKIE['sid']);
                if (count($det) == 2) {
                    $sid = base64_decode($det[1], true);
                    if ($det[0] == hash('sha256', $sid . $_COOKIE['ltime'])) {
                        $_SESSION['sid'] = $sid;
                        $this->_login_user = $sid;
                        return;
                    }
                }
                
            }
            throw new Exception_NoLogin();
            
        }
        
        protected final function setCookie($user_id, $expire = 7 * 86400) {
            $time = time();
            setcookie('ltime', $time, $time + $expire, '/');
            setcookie('sid', hash('sha256', $user_id . $time) . '.' . base64_encode($user_id), $time + $expire, '/');
        }
        
        protected final function setUser($user_id) {
            if (is_null($this->_login_user)) {
                session_start();
            }
            $_SESSION['sid'] = $user_id;
            $this->_login_user = $user_id;
        }
        
        protected final function getUser() {
            return $this->_login_user;
        }
        
        protected final function unsetUser() {
            unset($_SESSION['sid']);
            $this->_login_user = null;
        }
    }
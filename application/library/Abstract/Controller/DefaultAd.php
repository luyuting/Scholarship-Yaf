<?php
    class Abstract_Controller_DefaultAd extends Yaf_Controller_Abstract {

        // 检查是否登录，设置免登录页面
        protected $_no_login = [];
        
        private $_login_admin = null;
    
        public function init() {
            if (empty($this->_no_login) || !in_array($this->getRequest()->getActionName(), $this->_no_login)) {
                try {
                    $this->loginValidate();
                } catch(Exception_NoLogin $e) {
                    $this->getResponse()->setRedirect('/page_admin/login');
                }
            }
        }
    
        private function loginValidate() {
            
            // validate, if not login, throw exception
            session_start();
            if (isset($_SESSION['aid'])) {
                $this->_login_admin = $_SESSION['aid'];
                return;
            }
            
            if (!empty($_COOKIE['aid']) && !empty($_COOKIE['altime'])) {
                $det = explode('.', $_COOKIE['aid']);
                if (count($det) == 2) {
                    $aid = base64_decode($det[1], true);
                    if ($det[0] == hash('sha256', $aid . $_COOKIE['altime'])) {
                        $_SESSION['aid'] = $aid;
                        $this->_login_admin = $aid;
                        return;
                    }
                }
    
            }
            throw new Exception_NoLogin();
    
        }
        
        protected final function setCookie($admin_account, $expire = 7 * 86400) {
            $time = time();
            setcookie('altime', $time, $time + $expire, '/');
            setcookie('aid', hash('sha256', $admin_account . $time) . '.' . base64_encode($admin_account), $time + $expire, '/');
        }
        
        protected final function setAdmin($admin_account) {
            $this->_login_admin = $admin_account;
        }
            
        protected final function getAdmin() {
            return $this->_login_admin;
        }
    }
<?php
    class Exception_Abstract extends Exception {
        
        // 是否写入日志
        protected $_write_log = false;
        
        public function __construct($code, $message) {
            parent::__construct($message, $code);
        }
        
        public function __destruct() {
            if($this->_write_log) {
                // 写日志
            }
        }
    }
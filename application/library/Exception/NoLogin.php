<?php
    class Exception_NoLogin extends Exception_Abstract {
        
        public function __construct($message = null) {
            parent::__construct(10011, $message);
        }
        
        public function __destruct() {
            
        }
    }
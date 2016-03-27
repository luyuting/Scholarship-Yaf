<?php
    class Bootstrap extends Yaf_Bootstrap_Abstract {
        
        public function _initConfig(Yaf_Dispatcher $dispatcher) {
            mb_internal_encoding('utf-8');
            
            if($dispatcher->getRequest()->isCli()) {
                
            }
        }
    }

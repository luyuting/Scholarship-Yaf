<?php
    class Abstract_Controller_Ajax extends Abstract_Controller_Default {
        
        use Trait_Ajax;
        public function init() {
            $dispatcher = Yaf_Dispatcher::getInstance();
            $dispatcher->autoRender(false);
            $dispatcher->disableView();
            parent::init();
        }
    }
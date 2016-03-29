<?php
    class Abstract_Controller_AjaxAd extends Abstract_Controller_DefaultAd {
        
        use Trait_Ajax;
        public function init() {
            $dispatcher = Yaf_Dispatcher::getInstance();
            $dispatcher->autoRender(false);
            $dispatcher->disableView();
            parent::init();
        }
    }
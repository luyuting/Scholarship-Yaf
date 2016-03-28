<?php
    class Abstract_Controller_Ajax extends Abstract_Controller_Default {
        
        public function init() {
            $dispatcher = Yaf_Dispatcher::getInstance();
            $dispatcher->autoRender(false);
            $dispatcher->disableView();
            parent::init();
        }
        
        protected function response($code, array $message, $data = null) {
            $this->checkMessage($message);
            $res = [
              'code' => $code,
              'message' => $message,
              'data' => $data
            ];
            $this->getResponse()->setBody(json_encode($res));
        }
        
        private function checkMessage(&$message) {
            $result = 'success';
            if (isset($message['cause'])) {
                $result = 'fail';
            }
            $message['result'] = $result;
        }
    }
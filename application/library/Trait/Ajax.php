<?php
    trait Trait_Ajax {
        
        protected function response($code, array $message, $data = null) {
            $this->checkMessage($message);
            $res = [
              'code' => $code,
              'message' => $message,
              'data' => $data
            ];
            $this->getResponse()->setBody(json_encode($res));
        }
        
        protected function error($error_info = Comm_Const::E_UNKOWN) {
            $error_arr = explode(',', $error_info);
            $code = (int) $error_arr[0];
            $cause = isset($error_arr[1])? $error_arr[1]: '';
            $this->response($code, ['cause' => $cause]);
        }
        
        protected function success($data = null) {
            $this->response((int) Comm_Const::SUCCESS, [], $data);
        }
        
        private function checkMessage(&$message) {
            $message['result'] = isset($message['cause'])? 'fail': 'success';
        }
    }
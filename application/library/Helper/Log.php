<?php
    class Helper_Log {
        
        public static function writeLog($flie, $content, $flag = FILE_APPEND) {
            $log_dir = realpath(APP_PATH . '/../applogs');
            $file = $log_dir . date('Y/md') . $file . 'log';
            
            if (!is_dir($file)) {
                mkdir($file, 0755, true);
                chmod($file, 0755);
            }
            $content = '[' . date('Y-m-d H:i:s') . ']' . $content . "\r\n";
            file_put_contents($file, $content, $flag);
        }
        
    }
<?php
    class Helper_Log {
        
        public static function writeLog($file, $content, $flag = FILE_APPEND) {
            $log_dir = realpath(APP_PATH . '/../applogs');
            $file_dir = $log_dir . date('/Y/md');
            
            if (!is_dir($file_dir)) {
                mkdir($file_dir, 0755, true);
                chmod($file_dir, 0755);
            }
            $filename = $file_dir . '/' . $file . '.log';
            $content = '[' . date('Y-m-d H:i:s') . ']' . $content . "\r\n";
            file_put_contents($filename, $content, $flag);
        }
        
    }
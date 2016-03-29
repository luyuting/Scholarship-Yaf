<?php 
    /**
     * 数据库操作类，封装pdo-mysql
     * @author luyuting
     *
     */
    class Base_Db {
        private $_pdo = null;
        private $_stmt = null;
        private static $_instance = null;
        
        private function __construct() {
            $this->_pdo = new PDO('mysql:dbname=db_scholarship;host=127.0.0.1:3306', 'root', 'Lyt_0415');
            $this->_pdo->query('set names utf8');
        }
        
        public static function getInstance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        /**
         * 执行数据库查询操作
         * @param string $sql
         * @param array $params
         * @return array 查询的结果集，以关联数组的形式返回
         */
        public function query($sql, array $params = []) {
            $this->basic($sql, $params);
            return $this->getQueryList();
        }
        
        /**
         * 执行数据库增、删、改操作
         * @param string $sql
         * @param array $params
         * @return number|false 影响的行数，操作失败返回false
         */
        public function execute($sql, array $params = []) {
            $this->basic($sql, $params);
            return $this->affectedRows();
        }
        
        /**
         * 获得上一次插入操作生成的id（当表中有自增字段时使用）
         * @param string $sql
         * @param array $params
         */
        public function getId($sql, array $params = []) {
            $this->basic($sql, $params);
            return $this->_pdo->lastInsertId();
        }
        
        /**
         * 对查询的结果集逐行操作
         * @param string $sql
         * @param callable $func 执行操作的回调函数
         * @param array $params
         * @return array 经过处理后的结果集
         */
        public function packData($sql, callable $func, $params = []) {
            $this->basic($sql, $params);
            return $this->pack($func);
        }
        
        private function setPreparedStatement($sql) {
            $this->_stmt = $this->_pdo->prepare($sql);
        }
        
        /**
         * 动态参数绑定
         * @param array $params
         * @param PDOStatement $stmt
         */
        private function setParams(array $params, PDOStatement &$stmt = null) {
            is_null($stmt) && $stmt = $this->_stmt;
            if (!empty($params)) {
                array_walk($params, function($value, $key) use(&$stmt){
                    $data_type = PDO::PARAM_STR; 
                    if (is_int($value)) {
                        $data_type = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $data_type = PDO::PARAM_BOOL;
                    }
                    $key = is_int($key)? $key + 1: ':' . $key;
                    $stmt->bindParam($key, $value, $data_type);
                });
            }
        }
        
        private function executeStatement() {
            $this->_stmt->execute();
        }
        
        private function affectedRows() {
            if ($this->_stmt->errorCode() != PDO::ERR_NONE) {
                //var_dump($this->_stmt->errorInfo());
                return false;
            }
            return $this->_stmt->rowCount();
        }
        
        private function getQueryList($fetch_type = PDO::FETCH_ASSOC) {
            !is_int($fetch_type) && $fetch_type = PDO::FETCH_ASSOC;
            return $this->_stmt->fetchAll($fetch_type);
        }
        
        private function pack(callable $func, PDOStatement &$stmt = null) {
            is_null($stmt) && $stmt = $this->_stmt;
            $rs = [];
            while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) != null) {
                $func($row);
                $rs[] = $row;
            }
            return $rs;
        }
        
        private function basic($sql, array $params = []) {
            $this->setPreparedStatement($sql);
            $this->setParams($params);
            $this->executeStatement();
        }
        
        public function __destruct() {
            if(!is_null($this->_stmt) && $this->_stmt instanceof PDOStatement) {
                $this->_stmt->closeCursor();
                unset($this->_stmt);
            }
            unset($this->_pdo);
        }
    }
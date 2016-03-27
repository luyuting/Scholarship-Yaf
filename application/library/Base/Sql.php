<?php
    /**
     * 基于pdo-mysql的ORM实现类
     * @author luyuting
     *
     */
    abstract class Base_Sql {
        /**
         * 简单封装pdo-mysql的数据库操作对象
         * @var Base_Db
         */
        protected static $_db = null;
        
        /**
         * 表名
         * @var string
         */
        protected $_table = null;
        
        /**
         * 链式sql操作的结果集
         * @var array
         */
        protected $_rs = [];
        
        /**
         * 是否开启自动执行及链式操作
         * @var boolean
         */
        protected $_auto = false;
       
       
        protected final function __construct() {
           $this->initTable();
        }
        
        /**
         * 子类实现单例模式
         */
        public abstract static function getInstance();

        /**
         * 子类实现方法，初始化表名
         */
        protected abstract function initTable();
       
        /**
         * 开启自动执行sql语句，并支持链式调用
         * @return Base_Sql
         */
        public function auto() {
            if (is_null(self::$_db)) {
                self::$_db = new Base_Db();
            }
            if (!$this->_auto) {
                $this->_auto = true;
                $this->_rs = [];
            }
            return $this;
        }
        
        /**
         * 关闭自动执行，关闭链式调用模式，以数组的形式返回多次执行的结果
         * @return array 链式执行sql语句的结果集
         */
        public function exec() {
            $this->_auto = false;
            return $this->_rs;
        }
        
        /**
         * 拼装sql查询语句，如果自动执行开启，则立即执行当前sql，保存查询结果，并返回当前实例以支持链式调用；  
         * 直到exec()方法执行，返回所有操作的结果集。
         * @param array $where_params where查询条件，仅支持 “=” 条件，键名为字段名称
         * @param array $limit_params limit条件
         * @param array $order_params order by条件，键名为字段名称，值为'desc' 或 'asc'，不限制大小写，否则忽略该排序字段
         * @param array $column_name 要显示的字段（列）列表
         * @return Base_Sql|array 如果没有开启自动执行，返回sql语句和相应的参数列表
         */
        public function buildQuery(array $where_params, array $limit_params = [], array $order_params = [], array $column_name = ['*']) {
            $where_condition = $this->whereCondition($where_params);    
            $order_condition = $this->orderCondition($order_params);
            $limit_condition = $this->limitCondition($limit_params);
            $sql = "select " . implode(', ', $column_name) . " from {$this->_table} " . $where_condition . $order_condition . $limit_condition;
            
            $build = [
                'sql' => $sql,
                'params' => array_merge(array_values($where_params), array_values($limit_params))
            ];
            if ($this->_auto) {
                $this->_rs[] = self::$_db->query($build['sql'], $build['params']);
                return $this;
            }
            return $build;
        }
        
        /**
         * 拼装sql插入语句，如果自动执行开启，则立即执行当前sql，保存插入操作影响的行数，并返回当前实例以支持链式调用；
         * @param array $model 要保存的对象，键名对应字段名
         * @return Base_Sql|array 如果没有开启自动执行，返回sql语句和相应的参数列表
         */
        public function bulidSave(array $model) {
            $keys = array_keys($model);
            $sql = "insert into {$this->_table}(" . implode(', ', $keys) . " ) values(:" 
                . implode(', :', $keys) . " )";
            
            $build = [
                'sql' => $sql,
                'params' => $model
            ];
            if ($this->_auto) {
                $this->rs[] = self::$_db->execute($build['sql'], $build['params']);
                return $this;
            }
            return $build;
            
        }
        
        /**
         * 拼装sql删除语句，如果自动执行开启，则立即执行当前sql，保存删除操作影响的行数，并返回当前实例以支持链式调用；
         * @param array $where_params where查询条件，仅支持 “=” 条件，键名为字段名称
         * @return Base_Sql|array 如果没有开启自动执行，返回sql语句和相应的参数列表
         */
        public function bulidDelete(array $where_params) {
            $where_condition = $this->whereCondition($where_params);
            $sql = "delete from {$this->_table} " . $where_condition;
            
            $build = [
                'sql' => $sql,
                'params' => $where_params
            ];
            if ($this->_auto) {
                $this->rs[] = self::$_db->execute($build['sql'], $build['params']);
                return $this;
            }
            return $build;
            
        }
        
        /**
         * 拼装sql修改语句，如果自动执行开启，则立即执行当前sql，保存修改操作影响的行数，并返回当前实例以支持链式调用；
         * @param array $model 要修改的相应字段
         * @param array $where_params where查询条件，仅支持 “=” 条件，键名为字段名称
         * @return Base_Sql|array 如果没有开启自动执行，返回sql语句和相应的参数列表
         */
        public function buildUpdate(array $model, array $where_params) {
            $where_condition = $this->whereCondition($where_params);
            $sql = "update {$this->_table} set " . implode(' = ? , ', array_keys($model)) . " = ? ". $where_condition;
           
            $build = [
                'sql' => $sql,
                'params' => array_merge(array_values($model), array_values($where_params))
            ];
            if ($this->_auto) {
                $this->rs[] = self::$_db->execute($build['sql'], $build['params']);
                return $this;
            }
            return $build;
            
        }
       
        /**
         * 拼装where条件查询子句
         * @param array $where_params
         * @return string
         */
        private function whereCondition(array $where_params) {
            if (empty($where_params)) {
                return null;
            }
            return "where " . implode(' = ? and ', array_keys($where_params)) . " = ? ";
        }
        
        /**
         * 拼装order by条件查询子句
         * @param array $order_params
         * @return string
         */
        private function orderCondition(array &$order_params) {
            if (empty($order_params)) {
                return null;
            }
            $order = [];
            array_walk($order_params, function($value, $key) use (&$order){
                if(is_string($key) && in_array($value, ['desc', 'asc'])) {
                    $order[] = $key . " " . $value;
                }
            });
            return "order by " . implode(', ', $order) . " ";
        }
        
        /**
         * 拼装limit条件查询子句
         * @param array $limit_params
         * @return string 
         */
        private function limitCondition(array $limit_params) {
            if (empty($limit_params) || count($limit_params) != 2) {
                return null;
            }
            return "limit ?, ? ";
        }
        
        public function __destruct() {
           
        }
    }
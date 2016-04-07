<?php
    trait Trait_Apply {
                
        private static function applyModel($scholar_type_id, $student, $item_table, $item_id, $score) {
            $model_card = [
                'ap_scho_type' => $scholar_type_id,
                'ap_student' => $student,
                'ap_item_table' => $item_table,
                'ap_item_id' => $item_id,
                'ap_score' => $score,
                'ap_state' => '未审核',
                'ap_audit' => 0
            ];
            return $model_card;
        }
        
        /**
         * 删除特定的申请项目
         * @param string $student 申请的学生
         * @param string $apply_id 申请项目的id
         * @param string $table 申请项目对应的数据表
         * @param string $column 申请项目对应数据表的主键名称
         * @return boolean 删除成功与否
         */
        private static function delApply($student, $apply_id, $table, $column) {
            $db = Base_Db::getInstance();
            $params = [$apply_id];
            $check_sql = "select ap_student from tb_apply where ap_id = ? ";
            $rs = $db->query($check_sql, $params);
            if (empty($rs) || $rs[0]['ap_student'] != $student) {
                #@todo 说明不是本人操作，非法调用接口，抛出异常，记录日志
                return false;
            }
            if (!is_null($table)) {
                $delete_sql = "delete from {$table} where {$column} = (select ap_item_id from tb_apply where
                    ap_id = ?)";
                if ($db->execute($delete_sql, $params) != 1) {
                    return false;           
                }
            }
            $sql = "delete from tb_apply where ap_id = ?";
            return $db->execute($sql, $params) == 1? true: false;
        }
        
        /**
         * 按单项下设类别获得所有当年申请项目的具体信息
         * @param string $student 申请学生
         * @param string $annual 申请的年份，允许查询往年数据，但不能修改
         * @param string $table
         * @param string $column
         * @return array 查询的结果
         */
        private static function getApply($student, $annual, $table, $column) {
            $db = Base_Db::getInstance();
            $sql = "select ap_id, ap_scho_type, ap_student, ap_score, ap_time, ap_state, ap_audit, 
                k.* from tb_apply p, tb_scholarship t, {$table} k where t.sc_annual = ? and 
                t.sc_id = ap_scho_type and ap_item_table = ? and ap_student = ? and ap_item_id = 
                k.{$column} order by p.ap_time asc ";
            $params = [$annual, $table, $student];
            return $db->query($sql, $params);
        }
        
        /**
         * 申请项目及分数录入
         * @param string $scholar_type_id 奖学金对应id
         * @param string $student 申请学生
         * @param string $item_table 关联表，申请项目详情所在表
         * @param string $item_id 申请项目详情对应id
         * @param string $score 分数
         * @return boolean 录入申请表成功与否
         */
        private static function setApply($scholar_type_id, $student, $item_table, $item_id, $score) {
            $model = self::applyModel($scholar_type_id, $student, $item_table, $item_id, $score);
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->tAuto(Comm_T::TABLE_APPLY)->buildSave($model)->exec();
            if ($rs[0] == 0 || is_null($rs[0])) {
                return false;
            }
            return true;
        }
    }
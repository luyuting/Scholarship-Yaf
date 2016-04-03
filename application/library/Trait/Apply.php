<?php
    trait Trait_Apply {
        
        const TABLE_APPLY = 'tb_apply';
                
        private static function applyModel($scholar_type_id, $student, $item_table, $item_id, $score) {
            $model_card = [
                'ap_scho_type' => $scholar_type_id,
                'ap_student' => $student,
                'ap_item_table' => $item_table,
                'ap_item_id' => $item_id,
                'ap_score' => $score,
                'ap_state' => 0,
                'ap_audit' => 0
            ];
            return $model_card;
        }
        
        private static function delApply($student, $apply_id, $table, $column) {
            $db = Base_Db::getInstance();
            $params = [$apply_id];
            $check_sql = "select ap_student from tb_apply where ap_id = ? ";
            $rs = $db->query($check_sql, $params);
            if (empty($rs) || $rs[0]['ap_student'] != $student) {
                #@todo 抛出异常，记录日志
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
        
        private static function getApply($student, $annual, $table, $column) {
            $db = Base_Db::getInstance();
            $sql = "select p.*, k.* from tb_apply p, tb_scholarship t, {$table} k where t.sc_annual = ? 
                and t.sc_id = ap_scho_type and ap_item_table = ? and ap_student = ? and ap_item_id = 
                k.{$column} order by p.ap_time desc ";
            $params = [$annual, $table, $student];
            return $db->query($sql, $params);
            
        }
        
        private static function setApply($scholar_type_id, $student, $item_table, $item_id, $score) {
            $model = self::applyModel($scholar_type_id, $student, $item_table, $item_id, $score);
            $item_sql = Impl_Item::getInstance();
            $rs = $item_sql->auto(self::TABLE_APPLY)->buildSave($model)->exec();
            if ($rs[0] == 0 || is_null($rs[0])) {
                return false;
            }
            return true;
        }
    }
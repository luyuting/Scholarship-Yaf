<?php
    class AuditModel {     
        public static function getStudyList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_SCHOLARSHIP, 'sc_id', ['user_id asc', 'user_class desc']);
        }
        
        public static function getAppraisalList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_APPRAISAL, 'app_id', ['ap_score asc', 'user_id desc']);
        }
        
        public static function getDormitoryList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_DORMITORY, 'do_id');
        }
        
        public static function getSpiritualRewardList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_SPIRITUAL_REWARD, 'spr_id');
        }
        
        public static function getActivityCompList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_ACTIVITY_COMP, 'ac_id');
        }
        
        public static function getActivityRoleList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_ACTIVITY_ROLE, 'ar_id');
        }
        
        public static function getWorkCadreList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_WORK_CADRE, 'wc_id');
        }
        
        public static function getWorkRewardList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_WORK_REWARD, 'wr_id');
        }
        
        public static function getScieTechCompList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_SCIE_TECH_COMP, 'stc_id');
        }
        
        public static function getPaperList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_PAPER, 'stp_id');
        }
        
        public static function getInventionList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_INVENTION, 'in_id');
        }
        
        public static function getScieTechProjectList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_SCIE_TECH_PROJECT, 'stp_id');
        }
        
        public static function getPracticeList($admin_account) {
            return self::getList($admin_account, Comm_T::TABLE_PRACTICE, 'pr_id');
        }
        
        public static function auditScholar($apply_id, $admin_account, $state, $remark) {
            $info = AdminModel::getAdminInfoByAccount($admin_account);
            $admin_id = $info['admin_id'];
            $audit_sql = Impl_Audit::getInstance();
            $model = $audit_sql->auditModel($apply_id, $admin_id, $state, $remark);
            $rs = $audit_sql->auto()->buildSave($model)->exec();
            $id = $rs[0];
            if ($id == 0 || is_null($id)) {
                return false;
            }
            $item_sql = Impl_Item::getInstance();
            $update_rs = $item_sql->auto(self::TABLE_AUDIT)->buildUpdate(['ap_state' => $state, 'ap_audit' => $id],
                ['ap_id' => $apply_id])->exec();
            return $update_rs[0] == 1? true: false;
        }

        private static function getList($admin_account, $table, $column, array $order_condition = []) {
            $db = Base_Db::getInstance();
            $sql = "select user_id, user_name, user_major, user_class, k.*, ap_id, ap_scho_type, ap_student, ap_score, 
                ap_time, ap_state, ap_audit from tb_apply p , tb_user, {$table} k, tb_scholarship t where 
                t.sc_id = ap_scho_type and user_id = ap_student and (t.sc_grade, t.sc_annual) = (select ad_grade, 
                ad_annual from tb_admin where ad_account = ? ) and k.{$column} = ap_item_id and ap_item_table = ? order by ";
            if (!empty($order_condition)) {
                $sql .= implode(', ', $order_condition);
            } else {
                $sql .= "user_id desc, ap_time desc";
            }
            $params = [$admin_account, $table];
            return $db->query($sql, $params);
        }
    }
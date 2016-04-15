/**
 * 
 */
var listData = [];
$(function() {
	$.post('/user_rule/getactivitylist', {}, function(data, status) {
		var list = data.data;
		if (list !== null) {
			var len = list.length;
			if (len == 0) {
				listData = [['', ['']]];
			}
			for (var i = 0; i < len; i ++) {
				listData.push([list[i]['cp_name'], list[i]['cp_rate'].split('，')]);
			}
		}
		init();
	}, 'json');
});
var init = function() {
	applySet({
		id : 'activitycomp',
		title : '文体竞赛',
		tHead : ['竞赛名称', '级别', '奖项等级', '人数', '担任角色', '打破记录', '得分计算', '分数', '审核状态'],
		tBody : ['ap_id', 'ac_name', 'ac_rate', 'ac_prize', 'ac_team_num', 'ac_role', 'ac_break', 'ac_rule', 'ap_score', 'ap_state'],
		params : [{
			name : 'name, rate',
			display : '竞赛名称, 级别',
			type : 'assoc-select',
			value : listData
		}, {
			name : 'prize',
			display : '获奖等级',
			type : 'select',
			value : ['第一名', '第二名', '第三名', '第四名', '第五名', '第六名', '一等奖', '二等奖', '三等奖', '优胜奖及其他', '推荐未获奖']
		}, {
			name : 'role',
			display : '是否替补',
			type : 'select',
			value : [{
				k : '否',
				v : '队员'
			}, {
				k : '是',
				v : '替补队员'
			}]
		}, {
			name : 'rule',
			display : '得分计算',
			type : 'select',
			value : ['最高分', '非最高分']
		}, {
			name : 'break',
			display : '打破记录',
			type : 'select',
			value : ['否', '是']
		}, {
			name : 'team_num',
			display : '团队人数',
			type : 'input',
			value : '团队人数',
			regex : /^\d+$/
		}, {
			name : 'time',
			display : '获奖日期',
			type : 'date',
			value : ''
		}, {
			name : 'remark',
			dispaly : '备注',
			type : 'textarea',
			value : '备注信息',
			required : false
		}]
	});
	applySet({
		id : 'activityrole',
		title : '主持人或演员',
		tHead : ['活动名称', '级别', '活动日期', '担任角色', '分数', '审核状态'],
		tBody : ['ap_id', 'ar_name', 'ar_rate', 'ar_time', 'ar_role', 'ap_score', 'ap_state'],
		params : [{
			name : 'name',
			display : '活动名称',
			type : 'input',
			value : '活动名称'
		}, {
			name : 'time',
			display : '活动日期',
			type : 'date',
			value : ''
		}, {
			name : 'role',
			display : '担任角色',
			type : 'select',
			value : ['主持人', '演员']
		}, {
			name : 'rate',
			display : '活动级别',
			type : 'select',
			value : ['校级', '学部（学院）级']
		}, {
			name : 'host',
			display : '主办方',
			type : 'input',
			value : '主办方',
			required : false
		}, {
			name : 'remark',
			dispaly : '备注',
			type : 'textarea',
			value : '备注信息',
			required : false
		}]
	});
};
/**
 * 
 */
var listData = [];
$(function() {
	$.post('/user_rule/getscietechlist', {}, function(data, status) {
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
function init() {
	var order = ['0-25%', '26%-75%','76%-100%'];
	// 科创竞赛
	applySet({
		id : 'scietechcomp',
		title : '科创竞赛',
		tHead : ['竞赛名称', '级别', '奖项等级', '人数', '组内排序', '分数', '审核状态'],
		tBody : ['ap_id', 'stc_name', 'stc_rate', 'stc_prize', 'stc_team_num', 'stc_team_order', 'ap_score', 'ap_state'],
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
			name : 'team_num',
			display : '团队人数',
			type : 'input',
			value : '团队人数',
			regex : /^\d{1,}$/
		}, {
			name : 'team_order',
			display : '组内排序',
			type : 'select',
			value : order
		}, {
			name : 'team_status',
			display : '担任角色',
			type : 'select',
			value : ['队长', '队员']
		}, {
			name : 'host',
			display : '主办方',
			type : 'input',
			value : '主办方',
			required : false
		}, {
			name : 'time',
			display : '获奖时间',
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
	// 学术论文
	applySet({
		id : 'paper',
		title : '学术论文',
		tHead : ['文章名称', '发表级别', '发表时间', 'EI、SCI收录', '作者排序', '分数', '审核状态'],
		tBody : ['ap_id', 'pa_name', 'pa_level', 'pa_time', 'pa_ei_sci', 'pa_team_order', 'ap_score', 'ap_state'],
		params : [{
			name : 'name',
			display : '论文题目',
			type : 'input',
			value : '论文题目'
		}, {
			name : 'journal',
			display : '期刊名称',
			type : 'input',
			value : '期刊名称',
			required : false
		}, {
			name : 'level',
			display : '级别',
			type : 'select',
			value : ['核心期刊', '非核心期刊以及国际学术会议', '国家学术会议']
		}, {
			name : 'ei_sci',
			display : 'EI、SCI收录',
			type : 'select',
			value : ['否', '是'],
			
		}, {
			name : 'vol',
			display : '卷号期号',
			type : 'input',
			value : '卷号期号'
		}, {
			name : 'time',
			display : '出版时间',
			type : 'date',
			value : ''
		}, {
			name : 'team_num',
			display : '作者人数',
			type : 'input',
			value : '填写数字',
			regex : /^\d+$/
		}, {
			name : 'team_order',
			display : '第几作者',
			type : 'select',
			value : ['第一作者', '第二作者', '第三作者', '第四作者']
		}, {
			name : 'discuss_score',
			display : '协商得分',
			type : 'input',
			value : '2014级及以后需填写',
			regex : /^\s*|\d+\.?\d*$/
		}]
	});
	// 专利发明
	applySet({
		id : 'invention',
		title : '专利发明',
		tHead : ['专利名称', '专利类别', '获批时间', '专利号', '排名', '分数', '审核状态'],
		tBody : ['ap_id', 'in_name', 'in_type', 'in_time', 'in_account', 'in_team_order', 'ap_score', 'ap_state'],
		params : [{
			name : 'name',
			display : '名称',
			type : 'input',
			value : '名称',
		}, {
			name : 'account',
			display : '专利号',
			type : 'input',
			value : '专利号'
		}, {
			name : 'team_num',
			display : '团队人数',
			type : 'input',
			value : '团队人数',
			regex : /^\d+$/
		}, {
			name : 'time',
			display : '获批时间',
			type : 'date',
			value : ''
		}, {
			name : 'team_order',
			display : '组内排序',
			type : 'select',
			value : order
		}, {
			name : 'type',
			display : '专利类别',
			type : 'select',
			value : ['发明型专利',  '实用型专利', '外观型专利']
		}, {
			name : 'discuss_score',
			display : '协商得分',
			type : 'input',
			value : '2014级及以后需填写',
			regex : /^(\s*|\d+(\.\d*)?)$/
		}, {
			name : 'remark',
			display : '备注',
			type : 'textarea',
			value : '备注信息',
			required : false
		}]
	});
	// 创新项目
	applySet({
		id : 'scietechproject',
		title : '创新项目',
		tHead : ['项目名称', '级别', '评定等级', '时间', '组内排序', '分数', '审核状态'],
		tBody : ['ap_id', 'stp_name', 'stp_rate', 'stp_prize', 'stp_time', 'stp_team_order', 'ap_score', 'ap_state'],
		params : [{
			name : 'name',
			display : '项目名称',
			type : 'input',
			value : '项目名称',
		}, {
			name : 'time',
			display : '获批时间',
			type : 'date',
			value : '获批时间'
		}, {
			name : 'rate',
			display : '级别',
			type : 'select',
			value : ['国家级', '省级', '市级', '校级', '学部级']
		}, {
			name : 'prize',
			display : '评定等级',
			type : 'select',
			value : ['优', '良', '中', '及格']
		}, {
			name : 'team_order',
			display : '组内排序',
			type : 'select',
			value : order
		}, {
			name : 'team_num',
			display : '团队人数',
			type : 'input',
			value : '团队人数',
			regex : /^\d+$/
		}, {
			name : 'remark',
			display : '备注',
			type : 'textarea',
			value : '备注信息',
			required : false
		}]
	});
};
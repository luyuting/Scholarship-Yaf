/**
 * 
 */
$(function() {
	practiceData();
	init();
});

var init = function() {
	applySet({
		id : 'practice', 
		title : '社会实践', 
		tHead : ['名称', '类型', '团队奖励', '个人奖励', '担任角色', '分数', '审核状态'], 
		tBody : ['ap_id', 'pr_title', 'pr_name', 'pr_team_prize', 'pr_person_prize', 'pr_team_role', 'ap_score', 'ap_state'], 
		params : [{
			name : 'title', 
			display : '名称', 
			type : 'input', 
			value : '活动名称', 
			required : false
		}, {
			name : 'name, team_prize, person_prize, team_role', 
			display : '类型, 团队奖励, 个人奖项, 担任角色', 
			type : 'assoc-select', 
			value : listData
		}, {
			name : 'remark', 
			dispaly : '备注', 
			type : 'textarea', 
			value : '备注信息', 
			required : false
		}]
	});
};

var listData = [];
var practiceData = function() {
	listData.push(['社区挂职', ['暂无可选项'], ['先进个人', '锻炼标兵', '未获得奖项'], ['暂无可选项']]);
	listData.push(['优秀志愿者', ['暂无可选项'], ['国家级', '省级', '市级', '校级', '学部（学院）级', '暂无可选项']]);
	listData.push(['寒假社会调查', ['暂无可选项'], ['个人调查报告一等奖', '个人调查报告二等奖', '个人调查报告三等奖'], ['暂无可选项']]);
	listData.push(['暑假社会调查', ['暂无可选项'], ['个人调查报告一等奖', '个人调查报告二等奖', '个人调查报告三等奖'], ['暂无可选项']]);
	listData.push(['寒假社会实践', ['国家级奖', '省级奖', '市级奖', '校级一等奖', '校级二等奖', '校级三等奖', '未获得奖项'], ['国家级优秀个人', '省级优秀个人', '市级优秀个人', '校级优秀个人一等奖', '校级优秀个人二等奖', '校级优秀个人三等奖', '未获得奖项'], ['队长', '队员']]);
	listData.push(['暑假社会实践', ['国家级奖', '省级奖', '市级奖', '校级一等奖', '校级二等奖', '校级三等奖', '未获得奖项'], ['国家级优秀个人', '省级优秀个人', '市级优秀个人', '校级优秀个人一等奖', '校级优秀个人二等奖', '校级优秀个人三等奖', '未获得奖项'], ['去年暑假参与（不可计入参与分）'/*, '队长', '队员'*/]]);
	listData.push(['军训', ['暂无可选项'], ['军训先进集体成员', '军训先进个人'], ['暂无可选项']]);
	listData.push(['其他类社会实践', ['暂无可选项'], ['暂无可选项'], ['暂无可选项']]);
};
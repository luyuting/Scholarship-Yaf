/**
 * 
 */
$(function() {
	init();
});

function init() {
	applySet({
		id : 'study',
		title : '学习优秀',
		tHead : ['申请类型', '成绩排名比率', '审核状态'],
		tBody : ['ap_id', 'sc_name', 'sc_ratio', 'ap_state'],
		params : [{
			name : 'ratio',
			display : '成绩排名比率',
			type : 'select',
			value : ['5%', '20%']
		}]
	});
};
/**
 * 
 */
$(function() {
	reward();
	init();
});

var init = function() {
	applySet({
		id : 'appraisal', 
		title : '民主评议', 
		tHead : ['排名比率','分数', '审核状态'], 
		tBody : ['ap_id', 'app_ratio', 'ap_score', 'ap_state'], 
		params : [{
			name : 'ratio', 
			display : '民主评议排名', 
			type : 'select', 
			value : ['0-7%', '8%-20%', '21%-40%']
		}]
	});
	
	applySet({
		id : 'dormitory', 
		title : '文明寝室', 
		tHead : ['寝室得分','分数', '审核状态'], 
		tBody : ['ap_id', 'do_score', 'ap_score', 'ap_state'], 
		params : [{
			name : 'score', 
			display : '文明寝室得分', 
			type : 'input', 
			value : '寝室得分',
			regex : /^(10|\d{1}(\.\d{0,2})?)$/
		}]
	});
	
	applySet({
		id : 'spiritualreward', 
		title : '精神文明', 
		tHead : ['申请类型', '内容', '级别', '获评时间', '分数', '审核状态'], 
		tBody : ['ap_id', 'spr_name', 'spr_item', 'spr_rate', 'spr_time', 'ap_score', 'ap_state'], 
		params : [{
			name : 'name, item, rate', 
			display : '申请类型, 项目, 级别', 
			type : 'assoc-select', 
			value : listData,
			rate : 3
		}, {
			name : 'time',
			display : '获评时间',
			type : 'date',
			value : ''
		}]
	});
};
var listData = [];
var reward = function() {
	listData.push(['寝室环境建设', ['卫生平均成绩为满分', '千优寝室', '优秀寝室'], [[], ['校级'], ['市级']]]);
	listData.push(['个人荣誉称号', ['优秀学生党员', '自立自强标兵', '优秀团员标兵', '优秀团员'], 
	               [['校级', '学部(学院)级'], ['校级'], ['校级'], ['校级', '学部（学院）级']]]);
	listData.push(['公益活动', ['无偿献血'], [[]]]);
	listData.push(['好人好事', ['被校外媒体刊载好人好事', '具有较强社会影响效果的事迹'], [[], []]]);
	listData.push(['精神文明奖项', ['精神文明类表彰'], [['国家级', '省级', '市级及以上']]]);
};
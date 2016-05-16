/**
 * 
 */
$(function() {
	var navlist = [{
		title : '学习优秀奖学金',
		func : {
			audit : studyAudit,
			basic : 'study'
		}
	}, {
		title : '精神文明奖学金',
		func : {
			audit : spiritualAudit,
			basic : 'spiritual'
		}
	}, {
		title : '文体活动奖学金',
		func : {
			audit : activityAudit,
			basic : 'activity'
		}
	}, {
		title : '社会工作奖学金',
		func : {
			audit : workAudit,
			basic : 'work'
		}
	}, {
		title : '科技创新奖学金',
		func : {
			audit : scienceAudit,
			basic : 'science'
		}
	}, {
		title : '社会实践奖学金',
		func : {
			audit : practiceAudit,
			basic : 'practice'
		}
	}];
	
	var mainTables = function() {
		var selectedNavIndex = $('.nav li.selected').index();
		var func = navlist[selectedNavIndex].func;
		var selectedMenuIndex = $('.menu li.selected').index();
		switch (selectedMenuIndex) {
			case 0 : 
				$('div.audit-main-area').empty();
				progressSet({id : func.basic});
				break;
			case 1 : 
				$('div.audit-main-area').empty();
				var data = func.audit();
				for (var k = 0; k < data.length; k ++) {
					auditSet(data[k]);
				}; 
				break;
			case 2 : 
				$('div.audit-main-area').empty();
				orderSet({id : func.basic});
				break;
			default : break;
		}
	}
	
	var menulist = ['评审进度', '奖学金单项', '奖学金汇总'];	
	var menuul = $('<ul></ul>');
	for (var i = 0; i < menulist.length; i ++) {
		var menuitem = menulist[i];
		menuul.append($('<li></li>').text(menuitem)
			.on('click', function() {
				$(this).siblings().removeClass()
					.end()
					.addClass('selected');	
				mainTables();
			})
		);
	}
	var menu = $('<div></div>').addClass('menu')
		.append($('<img src="/img/me.png"/>').addClass('img')).append(menuul);
	
	var navul = $('<ul></ul>');
	for (var i = 0; i < navlist.length; i ++) {
		var navitem = navlist[i];
		navul.append($('<li></li>').text(navitem['title'])
			.on('click', function() {
				$(this).siblings().removeClass()
					.end()
					.addClass('selected');
				mainTables();
			})
		);
	}
	var nav = $('<div></div>').addClass('nav').append(navul);
	var search = $('<div></div>')
		.append($('<label></label>').text('请输入学号或姓名查找'))
		.append($('<input type="text" maxlength=20 />').attr('id', 'search-user'))
		.append($('<button type="button"></button>').text('查询').on('click', searchByUser));
	
	var audit = $('<div></div>').append($('<label></label>').text('条目审核'))
		.append($('<select id="state"></select>').append('<option>通过</option>').append('<option>不通过 </option>'))
		.append($('<input id="remark" type="text" maxlength=50 placeholder="未通过原因，50字以内"/>'));
	
	var container = $('<div></div>').addClass('audit');
	$('body').append(menu).append(container).append($('<div></div>').addClass('audit-main-area'));
	container.append(nav).append(search).append(audit);
	// init
	menuul.children().eq(1).addClass('selected');
	navul.children().eq(0).trigger('click');
});

var cloneTable = {};
var searchByUser = function(event, table) {
	var user = $('#search-user').val().trim();
	var tables = table? [table]: document.querySelectorAll('tbody');
	var len = tables.length;
	
	for (var i = 0; i < len; i ++) {
		$(tables[i]).empty();
		var trs = cloneTable[tables[i].id].childNodes;
		for (var j = 0; j < trs.length; j ++) {
			var tds = trs[j].childNodes;
			// 第二、三列分别为：学号、姓名
			if (user == 'all' || $(tds).eq(1).html() == user || $(tds).eq(2).html() == user) {
				tables[i].appendChild(trs[j].cloneNode(true));
			}
		}
	}
	if (user == 'all') {
		$('#search-user').val('');
	}
};

function progressSet(options) {
	var _id = options['id'];
	var _tHead = ['未审核完成学号', '未审核条目'];
	var _tBody = ['student', 'uncount']
	var _baseUri = '/admin_progress/';
	
	// get
	var _get = function() {
		$.post(_baseUri + _id, {}, function(data, status) {
			var bodyArr = data.data;
			if (bodyArr.length == 0) {
				bodyArr.push({student : '无', uncount : 0});
			}
			_setTBody(bodyArr);
			// 备份读取的表格数据
			var table = document.querySelector('#' + _id +' tbody');
			cloneTable[table.id] = table.cloneNode(true);
			if ($('#search-user').val().trim() != '' ) {
				searchByUser(null, table);
			}
			/*
			$(table).find('tr').find('td :not(:first)').click(function() {
				var studentVal = $(this).siblings().find('input[type="checkbox"]').val();
				if (studentVal == '无') {
					return;
				}
				$('#search-user').val(studentVal);
				$('.menu li :eq(1)').trigger('click');
			});*/ 
			// 以上做法在点击查询之后依旧无法起作用。
		}, 'json');
	}
	var _div = $('<div></div>').attr('id', _id).addClass('progress-div');
	var _table = $('<table></table>');
	var _thead = $('<thead></thead>');
	var _tbody = $('<tbody></tbody>').attr('id', _id + '-progress-info');
	_table.append(_thead).append(_tbody);
	_div.append(_table);
	// 开始设置表格头
	var _initTHead = function() {
		var tr = $('<tr></tr>');
		var len = _tHead.length;
		var th = $('<th></th>');
		tr.append(th);
		for (var i = 0; i < len; i ++) {
			th = $('<th></th>').text(_tHead[i]);
			tr.append(th);
		}
		_thead.append(tr);
	};
	_initTHead();
	
	// 设置表格主体
	var _setTBody = function(bodyArr) {
		_tbody.empty();
		var len = bodyArr.length;
		var colLen = _tBody.length;
		for (var k = 0; k < len; k ++) {
			var cols = bodyArr[k];
			var tr = $('<tr></tr>');
			_tbody.append(tr);
			for (var i = 0; i < colLen + 1; i ++) {
				var td = $('<td></td>').attr('text-align', 'center');
				tr.append(td);
				if (i == 0) { 
					var inp = $('<input type="checkbox"/>').attr('name', _id + '_operate').val(cols[_tBody[0]]);
					td.append(inp);
				} else {
					td.text(cols[_tBody[i - 1]]);
				}
			}
		}
	};
	
	var _appendArea = $('div.audit-main-area');
	if (_appendArea.length == 0) {
		_appendArea = $('<div></div>').addClass('audit-main-area');
		$('body').append(_appendArea);
	}
	$(_appendArea).append(_div);
	_get();
}

function orderSet(options) {
	var _id = options['id'];
	var _tHead = ['学号', '分数', '排名'];
	var _tBody = ['student', 'score', 'rank']
	var _baseUri = '/admin_order/';
	
	//get
	var _get = function() {
		$.post(_baseUri + _id, {}, function(data, status) {
			_setTBody(data.data);
			// 备份读取的表格数据
			var table = document.querySelector('#' + _id +' tbody');
			cloneTable[table.id] = table.cloneNode(true);
			if ($('#search-user').val().trim() != '' ) {
				searchByUser(null, table);
			}
		}, 'json');
	};
	var _div = $('<div></div>').attr('id', _id).addClass('order-div');
	var _table = $('<table></table>');
	var _thead = $('<thead></thead>');
	var _tbody = $('<tbody></tbody>').attr('id', _id + '-order-info');
	_table.append(_thead).append(_tbody);
	_div.append(_table);
	
	// 开始设置表格头
	var _initTHead = function() {
		var tr = $('<tr></tr>');
		var len = _tHead.length;
		var th = $('<th></th>');
		tr.append(th);
		for (var i = 0; i < len; i ++) {
			th = $('<th></th>').text(_tHead[i]);
			tr.append(th);
		}
		_thead.append(tr);
	};
	_initTHead();
	
	// 设置表格主体
	var _setTBody = function(bodyArr) {
		_tbody.empty();
		var len = bodyArr.length;
		var colLen = _tBody.length;
		for (var k = 0; k < len; k ++) {
			var cols = bodyArr[k];
			var tr = $('<tr></tr>');
			_tbody.append(tr);
			for (var i = 0; i < colLen + 1; i ++) {
				var td = $('<td></td>').attr('text-align', 'center');
				tr.append(td);
				if (i == 0) { 
					var inp = $('<input type="checkbox"/>').attr('name', _id + '_operate').val(cols[_tBody[0]]);
					td.append(inp);
				} else {
					td.text(cols[_tBody[i - 1]]);
				}
			}
		}
	};
	
	var _appendArea = $('div.audit-main-area');
	if (_appendArea.length == 0) {
		_appendArea = $('<div></div>').addClass('audit-main-area');
		$('body').append(_appendArea);
	}
	$(_appendArea).append(_div);
	_get();
};

function auditSet(options) {
	var _id = options['id'];
	var _title = options['title'];
	var _tHead = options['tHead'];
	var _tBody = options['tBody'];
	// var _isUnique = options['isUnique'] || false;
	var _csrf = options['csrf'] || true;
	var _baseUri = '/admin_audit/';
	
	// get
	var _get = function() {
		$.post(_baseUri + _id, {}, function(data, status) {
			var bodyArr = data.data;
			$('#' + _id + '_check').find('input[type="checkbox"]')[0].checked = false;
			_setTBody(bodyArr);
			
			// 备份修改过的表格数据
			var table = document.querySelector('#' + _id +' tbody');
			cloneTable[table.id] = table.cloneNode(true);
			if ($('#search-user').val().trim() != '' ) {
				searchByUser(null, table);
			}
		}, 'json'); 
	};
	
	var _div = $('<div></div>').attr('id', _id).addClass('audit-div')
		.append($('<span></span>').text(_title));
	var _table = $('<table></table>').addClass('audit-table');
	var _thead = $('<thead></thead>');
	var _tbody = $('<tbody></tbody>').attr('id', _id + '-audit-info');
	var _operate = $('<div></div>').addClass('operate-div').append($('<img src="/img/arrow.png" />'));
	var _checkAll = $('<span></span>').addClass('table-operate').attr('id', _id + '_check').bind('click', function() {
		// 全选操作
		var e = window.event || arguments.callee.caller.arguments[0];
		var ele = e.srcElement || e.target;
		var checkbox = $(this).find('input[type="checkbox"]')[0];
		if (ele.type != 'checkbox') {
			checkbox.checked = !checkbox.checked;
		} 
		$('#' + _id + ' tbody td').find('input[type="checkbox"]').each(function() {
			$(this).attr('checked', checkbox.checked);
		});
	}).append($('<input type="checkbox"/>')).append('全选').attr('title', '全选');
	var _audit = $('<span></span>').addClass('table-operate').bind('click', function() {
		var after = function(times, callback) {
			var count = 0;
			return function() {
				count ++;
				if (count == times) {
					callback();
				}
			};
		};
		var audits = $('#' + _id + ' tbody td').find('input[type="checkbox"]').filter('[checked="checked"]');
		var done = after(audits.length, _get);
		var state = $('#state').val(), remark = $('#remark').val().trim();
		audits.each(function() {
			$.post(_baseUri + 'audit', {apply_id : $(this).val(), state : state, remark : remark}, function() {
				done();
			});
		});
	}).append($('<img src="/img/edit.png"/>')).append('审核').attr('title', '审核');
	_table.append(_thead).append(_tbody);
	_operate.append(_checkAll).append(_audit);
	_div.append(_table).append(_operate);
	
	// 开始设置表格头
	var _initTHead = function() {
		var tr = $('<tr></tr>');
		var len = _tHead.length;
		var th = $('<th></th>');
		tr.append(th);
		for (var i = 0; i < len; i ++) {
			th = $('<th></th>').text(_tHead[i]);
			tr.append(th);
		}
		_thead.append(tr);
	};
	_initTHead();
	
	// 设置表格主体
	var _setTBody = function(bodyArr) {
		_tbody.empty();
		var len = bodyArr.length;
		var colLen = _tBody.length;
		for (var k = 0; k < len; k ++) {
			var cols = bodyArr[k];
			var tr = $('<tr></tr>');
			_tbody.append(tr);
			for (var i = 0; i < colLen; i ++) {
				var td = $('<td></td>').attr('text-align', 'center');
				tr.append(td);
				if (i == 0) { 
					var inp = $('<input type="checkbox"/>').attr('name', _id + '_operate').val(cols[_tBody[0]]);
					var hidden = $('<input type="hidden"/>').val(JSON.stringify(bodyArr[k]));
					td.append(inp).append(hidden);
				} else {
					td.text(cols[_tBody[i]]);
				}
			}
		}
	};
	var _appendArea = $('div.audit-main-area');
	if (_appendArea.length == 0) {
		_appendArea = $('<div></div>').addClass('audit-main-area');
		$('body').append(_appendArea);
	}
	$(_appendArea).append(_div);
	_get();
};

var studyAudit = function() {
	var data = [];
	data.push({
		id : 'study',
		title : '学习优秀',
		tHead : ['学号', '姓名', '专业', '奖学金类型', '成绩排名', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'user_major', 'sc_name', 'sc_ratio', 'ap_state']
	});
	return data;
};

var spiritualAudit = function() {
	var data = [];
	data.push({
		id : 'appraisal',
		title : '民主评议',
		tHead : ['学号', '姓名', '民主评议排名', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'app_ratio', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'dormitory',
		title : '文明寝室',
		tHead : ['学号', '姓名', '文明寝室得分', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'do_score', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'spiritualreward',
		title : '精神文明',
		tHead : ['学号', '姓名', '申请类型', '内容', '级别', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'spr_name', 'spr_item', 'spr_rate', 'ap_score', 'ap_state']
	});
	return data;
};

var activityAudit = function() {
	var data = [];
	data.push({
		id : 'activitycomp',
		title : '文体竞赛',
		tHead : ['学号', '姓名', '竞赛名称', '级别', '奖项等级', '担任角色', '打破记录', '得分计算', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'ac_name', 'ac_rate', 'ac_prize', 'ac_role', 'ac_break', 'ac_rule', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'activityrole',
		title : '主持人、演员',
		tHead : ['学号', '姓名', '活动名称', '担任角色', '级别', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'ar_name', 'ar_role', 'ar_rate', 'ap_score', 'ap_state']
	});
	return data;
};

var workAudit = function() {
	var data = [];
	data.push({
		id : 'workcadre',
		title : '学生工作',
		tHead : ['学号', '姓名', '申请类型', '任期时长', '职务名称', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'wc_level', 'wc_last_time', 'wc_name', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'workreward',
		title : '荣誉称号',
		tHead : ['学号', '姓名', '荣誉称号', '级别', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'wr_name', 'wr_rate', 'ap_score', 'ap_state']
	});
	return data;
};

var scienceAudit = function() {
	var data = [];
	data.push({
		id : 'scietechcomp',
		title : '科创竞赛',
		tHead : ['学号', '姓名', '竞赛名称', '级别', '获奖等级', '排序', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'stc_name', 'stc_rate', 'stc_prize', 'stc_team_order', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'paper',
		title : '学术论文',
		tHead : ['学号', '姓名', '论文名称', '发表级别', 'EI、SCI收录', '排序', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'pa_name', 'pa_level' ,'pa_ei_sci', 'pa_team_order', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'invention',
		title : '专利发明',
		tHead : ['学号', '姓名', '专利名称', '专利类别', '专利号', '排序', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'in_name', 'in_type', 'in_account', 'in_team_order', 'ap_score', 'ap_state']
	});
	data.push({
		id : 'scietechproject',
		title : '创新项目',
		tHead : ['学号', '姓名', '项目名称', '级别', '评定等级', '排序', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'stp_name', 'stp_rate', 'stp_prize', 'stp_team_order', 'ap_score', 'ap_state']
	});
	return data;
};

var practiceAudit = function() {
	var data = [];
	data.push({
		id : 'practice',
		title : '社会实践',
		tHead : ['学号', '姓名', '实践名称', '实践类型', '团队奖励', '个人奖项', '担任角色', '分数', '审核状态'],
		tBody : ['ap_id', 'user_id', 'user_name', 'pr_title', 'pr_name', 'pr_team_prize', 'pr_person_prize', 'pr_role', 'ap_score', 'ap_state']
	});
	return data;
};
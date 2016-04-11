/**
 * 
 */
$(function() {
	// 用专利发明部分进行测试
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
			regex : /^\d{1,}$/
		}, {
			name : 'time',
			display : '获批时间',
			type : 'date',
			value : '年-月-日'
		}, {
			name : 'team_order',
			display : '组内排序',
			type : 'select',
			value : ['0-25%', '26%-75%','76%-100%']
		}, {
			name : 'type',
			display : '专利类别',
			type : 'select',
			value : [{
			    	k : '发明型专利', 
			    	v : '发明型专利'
			    }, {
			    	k : '实用型专利', 
			    	v : '实用型专利'
			    }, {
			    	k : '外观型专利', 
			    	v : '外观型专利'
			    }]
		}, {
			name : 'discuss_score',
			display : '协商得分',
			type : 'input',
			value : '2014级及以后需填写',
			regex : /^\s*|\d{1,}$/
		}, {
			name : 'remark',
			display : '备注',
			type : 'textarea',
			value : '备注信息',
			required : false
		}, {
			// 二级联动菜单 测试
			name : 'first, second, third',
			display : 'first, second, third',
			type : 'assoc-select',
			value : [[
			    'first-1',
			    ['second-1', 'second-2'],
			    ['third-1', 'third-2']
			], [
			    'first-2',
			    ['second-3', 'second-4'],
			    ['third-3', 'third-4']
			]]
		}]
	});
});
function applySet(options) {
	var _id = options['id'];
	var _title = options['title'];
	var _tHead = options['tHead'];
	var _tBody = options['tBody'];
	var _params = options['params'];
	var _isUnique = options['isUnique'] || false;
	var _csrf = options['csrf'] || true;
	var _baseUri = '/user_base/';
	// get
	var _get = function() {
		var requestUri = _baseUri + 'get' + _id;
		$.post(requestUri, {}, function(data, status) {
			var bodyArr = data.data;
			$('#' + _id + '_check')[0].checked = false;
			_setTBody(bodyArr);
		}, 'json'); 
	};
	// del
	var _del = function() {
		var requestUri = _baseUri + 'del' + _id;
		$('#' + _id + ' tbody td').find('input[type="checkbox"]').each(function() {
			if ($(this)[0].checked) {
				$.post(requestUri, {apply_id : $(this).val()}, function(data, status) {
					if (data.code == 10000) {
						_get();
					}
				}, 'json');
			}
		});
	};
	// apply
	var _apply = function() {
		var requestUri = _baseUri + 'apply' + _id;
		var params = {};
		var parLen = _applyParams.length;
		for (var i = 0; i < parLen; i ++) {
			var each = _applyParams[i];
			var name = each['name'];
			var regex = each['regex'];
			var id = each['id'];
			
			var node = $('#' + id);
			var val = node.val().trim();
			if (!regex.test(val)) {
				node.focus();
				return false;
			}
			params[name] = val;
		}
		$.post(requestUri, params, function(data, status) {
			if (data.code == 10000) {
				_get();
			}
		}, 'json');
	};
	
	var _div = $('<div></div>').attr('id', _id).addClass('apply-div')
		.append($('<span></span>').text(_title))
		.append($('<img src="image/add.jpg"/>').bind('click', _apply));
	var _table = $('<table></table>').addClass('apply-table');
	var _thead = $('<thead></thead>');
	var _tbody = $('<tbody></tbody>');
	var _operate = $('<div></div>').addClass('operate-div').append($('<img src="image/arrow.png" />'));
	var _checkAll = $('<span></span>').addClass('table-operate').attr('id', _id + '_check').bind('click', function() {
		// 全选操作
		var e = window.event || arguments.callee.caller.arguments[0];
		var ele = e.srcElement || e.target;
		var checkbox = $(this).find('input[type="checkbox"]')[0];
		if(ele.type != 'checkbox') {
			checkbox.checked = !checkbox.checked;
		} 
		$('#' + _id + ' tbody td').find('input[type="checkbox"]').each(function() {
			$(this)[0].checked = checkbox.checked;
		});
	}).append($('<input type="checkbox"/>')).append('全选').attr('title', '全选');
	var _delete = $('<span></span>').addClass('table-operate').bind('click', _del).append($('<img src="image/delete.png"/>')).append('删除').attr('title', '删除');
	_table.append(_thead).append(_tbody);
	_operate.append(_checkAll).append(_delete);
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
					td.append(inp);
				} else {
					td.val(cols[_tBody[0]]).text(cols[_tBody[i]]).click(function() {});

				}
			}
		}
	};
	
	var _applyArea = $('<div></div>').addClass('container apply');
	var _applyForm = $('<form></form>');
	_applyArea.append(_applyForm);
	var _applyParams = [];
	var _init = function() {
		var baseEmpty = /^[^<|>|;|\\?|\\||'|&]*$/;
		var base = /^[^<|>|;|\\?|\\||'|&]+$/;
		var len = _params.length;
		for (var i = 0; i < len; i ++) {	
			var name = _params[i]['name'].trim();
			var display = _params[i]['display'];
			var type = _params[i]['type'];
			var regex = _params[i]['regex'] || ((_params[i]['required'] === false)? baseEmpty: base);
			var value = _params[i]['value'];
			if (type != 'assoc-select') {
				var id = _id + '_' + name;
				var item = $('<div></div>');
				item.append($('<label></label>').text(display));
				switch(type) {
					case 'input' :
						item.append($('<input type="text"/>').attr('name', name).attr('id', id)
							.attr('placeholder', value || ''));
						break;
					case 'date' :
						item.append($('<input type="date"/>').attr('name', name).attr('id', id)
							.attr('placeholder', value || ''));
						regex = /^\d{4}-\d{2}-\d{2}$/;
						break;
					case 'select' : 
						var select = $('<select/>').attr('id', id);
						item.append(select);
						for (var j = 0; j < value.length; j ++) {
							var k = value[j]['k'] || value[j];
							var v = value[j]['v'] || value[j];
							select.append($('<option></option>').text(k).val(v))
						}
						select = null;
						break;
					case 'textarea' :
						item.append($('<textarea></textarea>').attr('name', name).attr('id', id)
								.attr('placeholder', value || ''));
						break;
					default: break;
				}
				_applyParams.push({name : name, regex : regex, id : id});
				_applyForm.append(item);
			} else {
				var nameArr = name.split(',');
				var displayArr = display.split(',');
				var selects = [];
				for (var k = 0; k < nameArr.length; k ++) {
					var item = $('<div></div>');
					var id = _id + '_' + nameArr[k].trim();
					item.append($('<label></label>').text(displayArr[k].trim()));
					var select = $('<select></select>').attr('id', id);
					item.append(select);
					selects.push(select);
					_applyForm.append(item);
					_applyParams.push({name : nameArr[k].trim(), regex : regex, id : id});
				}
				for (var j = 0; j < value.length; j ++) {
					selects[0].append($('<option></option>').text(value[j][0]).val(value[j][0]));
				}
				selects[0].bind('change', function() {
					var index = $(this)[0].selectedIndex;
					var optionArr = value[index];
					for (var j = 1; j < optionArr.length; j ++) {
						selects[j].empty();
						for (var k = 0; k < optionArr[j].length; k ++) {
							var opv = optionArr[j][k];
							selects[j].append($('<option></option>').text(opv).val(opv));
						}
					}
				}).trigger('change');
			}
		}
		_applyForm.append($('<button type="button"></button>').text('确认'))
			.append($('<button type="button"></button>').text('取消'));
		_div.append(_applyArea);
	};
	_init();
	// test
	
	$('body').append(_div);
	// 初始化表格数据
	_get();
}
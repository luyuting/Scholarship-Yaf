/**
 * 
 */
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
			$('#' + _id + '_check').find('input[type="checkbox"]')[0].checked = false;
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
				$('#' + _id + '_form').empty();
				_get();
				_init();
			}
		}, 'json');
	};
	// update
	var _update = function() {
		$.post(_baseUri + 'del' + _id, {apply_id : _applyForm.attr('apply_id')}, function(data, status) {
			if (data.code == 10000) {
				_apply();
			}
		}, 'json');
		
	}
	var _div = $('<div></div>').attr('id', _id).addClass('apply-div')
		.append($('<span></span>').text(_title))
		.append($('<img src="/img/add.jpg"/>').bind('click', _apply));
	var _table = $('<table></table>').addClass('apply-table');
	var _thead = $('<thead></thead>');
	var _tbody = $('<tbody></tbody>');
	var _operate = $('<div></div>').addClass('operate-div').append($('<img src="/img/arrow.png" />'));
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
	var _delete = $('<span></span>').addClass('table-operate').bind('click', _del).append($('<img src="/img/delete.png"/>')).append('删除').attr('title', '删除');
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
					td.val(cols[_tBody[0]]).text(cols[_tBody[i]]).click(function() {
						var indexRow = $(this).parent().index();
						var trVal = bodyArr[indexRow];
						for (var k in trVal) {
							if (k.indexOf('ap_') == -1) {
								name = k.substr(k.indexOf('_') + 1);
								_applyForm.attr('apply_id', $(this).val()).find('[id$=' + name + ']').val(trVal[k]).trigger('change').end()
									.find('button').eq(0).text('修改').on('click', _update);
							}
						}
					});
				}
			}
		}
	};
	
	var _applyArea = $('<div></div>').addClass('container apply');
	var _applyForm = $('<form></form>').attr('id', _id + '_form');
	_applyArea.append(_applyForm);
	var _applyParams = [];
	var _init = function() {
		var baseEmpty = /^[^<|>|;|\\?|\\||'|&]*$/;
		var base = /^[^<|>|;|\\?|\\||'|&]+$/;
		var len = _params.length;
		var initParam = function(param) {
			var name = param['name'].trim();
			var display = param['display'];
			var type = param['type'];
			var regex = param['regex'] || ((param['required'] === false)? baseEmpty: base);
			var value = param['value'];
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
				var rate = param['rate'] || 2;
				var nameArr = name.split(',');
				var displayArr = display.split(',');
				var selects = [];
				for (var k = 0; k < nameArr.length; k ++) {
					var item = $('<div></div>');
					var id = _id + '_' + nameArr[k].trim();
					item.append($('<label></label>').text(displayArr[k].trim()));
					var select = $('<select></select>').attr('id', id).attr('index', k);
					item.append(select);
					selects.push(select);
					regex = baseEmpty;
					_applyForm.append(item);
					_applyParams.push({name : nameArr[k].trim(), regex : regex, id : id});
				}
				for (var j = 0; j < value.length; j ++) {
					selects[0].append($('<option></option>').text(value[j][0]).val(value[j][0]));
				}
				for (var n = 1; n < rate - 1; n ++) {
					var deepVal = value[0][n];
					for (var count = n - 1; count > 0; count --) {
						deepVal = deepVal[0];
					}
					for (var j = 0; j < deepVal.length; j ++) {
						selects[n].append($('<option></option>').text(deepVal[j]).val(deepVal[j]));
					}
				}
				var chain = [];
				for (var n = 0; n < rate - 1; n ++) {
					selects[n].bind('change', function() {
						var index = $(this)[0].selectedIndex;
						var self = parseInt($(this).attr('index'));
						chain[self] = index;
						var optionArr = value[chain[0]];
						if (self != rate - 2) {
							selects[self + 1].empty();
							var depthVal = optionArr[self + 1];
							for (var dep = 1; dep <= self; dep ++) {
								depthVal = depthVal[chain[dep]];
							}
							for (var j = 0; j < depthVal.length; j ++) {
								selects[self + 1].append($('<option></option>').text(depthVal[j]).val(depthVal[j]));
							}
							selects[self + 1].trigger('change');
						} else {
							for (var j = self + 1; j < optionArr.length; j ++) {
								selects[j].empty();
								var vals = optionArr[j];
								for (var dep = 1; dep <= self; dep ++) {
									vals = vals[chain[dep]];
								}
								for (var k = 0; k < vals.length; k ++) {
									selects[j].append($('<option></option>').text(vals[k]).val(vals[k]));
								}
							}
						}
					}).trigger('change');
				}
				/*
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
				}).trigger('change');*/
			}
		};
		for (var i = 0; i < len; i ++) {	
			initParam(_params[i]);
		}
		_applyForm.append($('<button type="button"></button>').text('确认').on('click', function() {
			console.log('hehe');
		}))
			.append($('<button type="button"></button>').text('取消'));
		_div.append(_applyArea);
	};
	_init();
	// test
	
	var _appendArea = $('div.apply_area');
	if (!_appendArea) {
		$('<div></div>').addClass('apply_area');
		$('body').append(_appendArea);
	}
	$(_appendArea).append(_div);
	// 初始化表格数据
	_get();
}
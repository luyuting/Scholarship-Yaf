/**
 * 
 */
$(function() {
	orderSet({id : 'science', title : '精神文明奖学金'});
});

function orderSet(options) {
	var _id = options['id'];
	var _title = options['title'];
	var _tHead = ['学号', '分数', '排名'];
	var _tBody = ['student', 'score', 'rank']
	var _baseUri = '/admin_order/';
	
	//get
	var _get = function() {
		$.post(_baseUri + _id, {}, function(data, status) {
			_setTBody(data.data);
		}, 'json');
	};
	var _div = $('<div></div>').attr('id', _id).addClass('order-div')
		.append($('<span></span>').text(_title));
	var _table = $('<table></table>');
	var _thead = $('<thead></thead>');
	var _tbody = $('<tbody></tbody>');
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
					td.val(cols[_tBody[0]]).text(cols[_tBody[i - 1]]);
				}
			}
		}
	};
	
	$('body').append(_div);
	_get();
}
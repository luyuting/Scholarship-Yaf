/**
 * 
 */

/**
 * 全选操作，非jquery实现
 * @param node 当前node节点
 */
function checkAll(node) {
	/*
	 * 获得在node内点击的元素，如果点击的已经是复选框，则不用再一次更改勾选状态
	 */
	var e = window.event || arguments.callee.caller.arguments[0]; //后者兼容firefox 
	var ele = e.srcElement || e.target;
	// querySelector选中第一个，选中多个用querySelectorAll
	var checkbox = node.querySelector('input[type="checkbox"]');
	if(ele.type != 'checkbox') {
		checkbox.checked = !checkbox.checked;
	} 
	var checkboxArr = node.parentNode.parentNode.querySelector('tbody').querySelectorAll('input[type="checkbox"]');
	// var checkboxArr = document.getElementsByName(node.parentNode.parentNode.querySelector('tbody').id + '_operate');
	for(var i = 0; i < checkboxArr.length; i ++) {
		checkboxArr[i].checked = checkbox.checked;
	}
}

/**
 * 
 * @param action 控制器对应action
 * @param params post传入参数
 * @param func 回调函数
 */
function ajax(action, params, func) {
	$.ajax({
		type: 'post',
		url: action,
		data: params,
		dataType: 'json',
		success: func,
		error: function(json) {
			var str = "status : " + json.status + "\nstatusText : " + json.statusText;
			alert("error\n" + str);
		}
	});
}

/**
 * Ajax执行增删改操作
 * @param id 需要清除复选框勾选的表格
 * @param action
 * @param params
 * @param tableFunc 表格更新等操作回调
 * @param failStr 操作失败提示的语句
 */
function executeAjax(id, action, params, tableFunc, failStr) {
	ajax(action, params, function(result) {
		if(result.code == 10000) {
			tableFunc();
			clear(id);
		} else {
			alert(failStr + "error-code : " + data.code + "\nerror-info : " + data.message.cause);
		}
	});
}

/**
 * 操作完成后消除对复选框的选择，考虑弃用这种设计
 * @param id 表格tbody的id
 */
function clear(id) {
	if(id == null) {
		return;
	}
	var checkboxArr = document.getElementById(id).querySelectorAll('input[type="checkbox"]');
	for(var i=0; i < checkboxArr.length; i ++) {
		checkboxArr[i].checked = false;
	}
}

/**
 * 复选框字符串拼接，如竞赛项目等级
 * @param name 复选框对应名称
 * @returns {String} 拼接后的字符串
 */
function rateStr(name) {
	var checkboxArr = document.getElementsByName(name);
	var rateStr = '';
	var count = 0;
	for(var i = 0; i < checkboxArr.length; i ++) {
		if(checkboxArr[i].checked) {
			if(count != 0) {
				rateStr += '，';
			}
			rateStr += checkboxArr[i].value;
			count ++;
		}
	}
	return rateStr;
}

/**
 * Ajax添加表格行
 * @param cols 待插入的数据
 * @param id 表格tbody的id
 * @param arr 展示的字段名称数组
 * @param index 表格行的索引
 */
function addTableRow(cols, id, arr, index) {
	var table = document.getElementById(id);
	var tr = table.insertRow(index);
	for(var i = 0; i < arr.length; i ++) {
		var td = tr.insertCell(i);
		td.setAttribute('text-align', 'center');
		if(i == 0) {
			var newInput = document.createElement('input');
			newInput.type = 'checkbox';
			newInput.name = id + '_operate';
			newInput.value = cols[arr[0]];
			td.appendChild(newInput);
		} else {
			td.value = cols[arr[0]];
			td.onclick = function() {
			}
			if(window.navigator.userAgent.toLowerCase().indexOf('firefox') != -1) {
	        	td.textContent = cols[arr[i]];
	        } else {
	        	td.innerText = cols[arr[i]];
	        }
		}
	}
}

/**
 * 更新整个表格中的数据
 * @param tableCols json数组
 * @param id 表格tbody的id
 * @param tableArr 展示的字段名
 */
function updateTable(tableCols, id, arr) {
	delTableRow(id);
	var len = tableCols.length;
	for(var i = 0; i < len; i ++) {
		addTableRow(tableCols[i], id, arr, i);
	}
}

/**
 * 清空表格
 * @param id 表格tbody的id
 */
function delTableRow(id) {
	// jquery实现：$.('#' + id).empty();
	var table = document.getElementById(id);
	var len = table.querySelectorAll('tr').length;
	while(len != 0) {
		table.deleteRow(0);
		len --;
	}
}

/**
 * 删除多行选中数据
 * @param action
 * @param node 删除按钮所在节点
 * @param tableFunc 更新表格的回调
 */
function multiDelTable(action, node, tableFunc) {
	var failStr = 'Delete Failed';
	var checkboxArr = node.parentNode.parentNode.querySelector('tbody').querySelectorAll('input[type="checkbox"]');
	var len = checkboxArr.length;
	for(var i = 0; i < len; i ++) {
		if(checkboxArr[i].checked) {
			var params = {"id": checkboxArr[i].value };
			executeAjax(null, action, params, tableFunc, failStr);
		}
	}
}

/**
 * ajax获得数据并设置表格中数据
 * @param tableId 表格tbody的id
 * @param action
 * @param params
 * @param arr
 */
function setTable(tableId, action, params, arr) {
	ajax(action, params, function(data) {
		var tableCols = data.data;
		updateTable(tableCols, tableId, arr);
	});
}
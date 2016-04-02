/**
 * 
 */
function setScoreTable(table, params, arr) {
	var action = '/admin_base/scholaritemscore';
	setTable(table, action, params, arr);
}

function setItemScore(id, params, tableFunc) {
	var action = '/admin_base/scholarspiritual';
	var failStr = 'Save Failed';
	executeAjax(id, action, params, tableFunc, failStr);
}
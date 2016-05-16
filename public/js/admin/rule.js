/**
 * 
 */
$(function() {
	$.post('/admin_base/rule', {}, function(data, status) {
		ruleSet(data.data);
	}, 'json');
	
});

function ruleSet(ruleArr) {
	var tableBase = $('table#base-rule tbody');
	var tpl = '<tr><td>{index}</td><td>{name}</td><td>{basic_info}<td><span>'
		+ '<span><img src="/img/show.png"/>查看</span></td></tr>';
	$(ruleArr).each(function(i) {
		this['index'] = i + 1;
		tableBase.append($(tpl.format(this)));
	});
	
	var selectedRuleTr = null, ruleIndex = 0;
	
	tableBase.find('td').find('span :eq(0)').click(function() {
		var selectedIndex = $(this).parent().parent().index();
		var rule = ruleArr[selectedIndex];
		var tHead = rule['table'] == 'work_score' ? '<th>职位</th><th>分数</th>' :
			'<th>名称</th><th>描述1</th><th>描述2</th><th>分数</th><th>系数</th>';
		$('table#detail-rule').find('thead tr').html(tHead)
			.end()
			.find('tbody').empty().html(rule['data']).attr('tp', rule['table'] !== 'work_score' ? 1 : 2)
			.find('tr').click(function() {
				if ($(this).parent().attr('tp') == '1') {
					selectedRuleTr = $(this);
					var td = $(this).find('td');
					scholar_name.value = td.eq(0).text();
					scholar_descr_a.value = td.eq(1).text();
					scholar_descr_b.value = td.eq(2).text();
					scholar_score.value = td.eq(3).text();
					scholar_ratio.value = td.eq(4).text();
				}
			});
		var tipsInfo = $('table#detail-rule tbody').attr('tp') == '1' ? '可编辑分数或系数' : '不可编辑';
		$('#tips').attr('title', tipsInfo).text(tipsInfo);
		ruleIndex = selectedIndex;
	});
	
	$('.rule-edit button').click(function() {
		if (selectedRuleTr == null) {
			return;
		}
		var td = selectedRuleTr.find('td');
		td.eq(3).text(scholar_score.value);
		td.eq(4).text(scholar_ratio.value);
		ruleArr[ruleIndex]['data'] = selectedRuleTr.parent().html();
	});
	
	var calcRatio = function(ratioStr) {
		if (ratioStr.trim() == '') {
			return 1;
		}
		var ratioArr = ratioStr.split('/');
		return ratioArr.length == 1 ? parseFloat(ratioArr[0]) : parseFloat(ratioArr[0]) / parseFloat(ratioArr[1]);
	}
	
	var after = function(times, callback) {
		var count = 0;
		return function() {
			count ++;
			if (count == times) {
				callback();
			}
		}
	};
	
	var setRule = function(rule, i) {
		var isWorkScore = rule['table'] == 'work_score';
		var requestUri = '/admin_base/' + (!isWorkScore ? 'scholaritem' : 'scholarworkcadre');
		var tmp = $(rule['data']);
		var done = after(tmp.length, function() {
			$('#base-rule tbody tr :eq(' + i + ')').css('background', '#f7f7a6');
		});
		var tmp = $(rule['data']);
		for (var j = 0; j < tmp.length; j ++) {
			var tmpTd = tmp.eq(j).find('td');
			var params = !isWorkScore ? {
				type : rule['type'],
				name : tmpTd.eq(0).text(),
				descr_a : tmpTd.eq(1).text(),
				descr_b : tmpTd.eq(2).text(),
				score : tmpTd.eq(3).text(),
				ratio : calcRatio(tmpTd.eq(4).text())
			} : {
				position : tmpTd.eq(0).text(),
				score : tmpTd.eq(1).text()
			};
			$.post(requestUri, params, function(data, status) {
				done();
			}, 'json');
		}
	};
	
	$('#complete').click(function () {
		for (var i = 0; i < ruleArr.length; i ++) {
			setRule(ruleArr[i], i);
		}
	});
}

String.prototype.format = function (obj) {
	return this.replace(/{([^}.]*)}/g, function(match, code) {
		return obj[code.trim()];
	});
}

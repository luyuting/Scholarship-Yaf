/**
 * 学习优秀奖学金申请
 */
$(function() {
	init();
});

function init() {
	buttonChange(false);
	// 设置学习排名比率
	var ratio = ['5%', '20%'];
	for (var i = 0;i < ratio.length; i ++) {
		study_ratio.options.add(new Option('前' + ratio[i], ratio[i]));
	}
	studyUnique();
}

function studyUnique() {
	$.post('/user_base/studyunique', {}, function(data, status) {
		var list = data.data;
		if (list == null) {
			return;
		}
		if (list.length == 0) {
			buttonChange(true);
			return;
		}
		$('#study_ratio').val(list[0]['sc_ratio']);
		var arr = ['ap_id', 'sc_name', 'sc_ratio', 'ap_state'];
		addTableRow(list[0], 'apply_study_info', arr, 0);
	}, 'json');
}

function applyStudy() {
	var params = {
		ratio : study_ratio.value,
	};
	// 点击后立刻将按钮置为不可用，防止多次点击
	buttonChange(false);
	$.post('/user_base/applystudy', params, function(data, status) {
		if (data.code == 10000) {
			studyUnique();
		} else {
			buttonChange(true);
		}
	}, 'json');	
}

function delStudy() {
	var id = 'apply_study_info';
	var params = {
		apply_id : $('#' + id).find('tr').eq(0).find('input[type="checkbox"]').val()
	};
	$.post('/user_base/delstudy', params, function(data, status) {
		if (data.code == 10000) {
			$('#' + id).empty();
			buttonChange(true);
		}
	}, 'json');
}

function buttonChange(enabled) {
	var btn = $('#apply_btn');
	var select = $('#study_ratio');
	if (enabled) {
		btn.text('点击申请').unbind().bind('click', applyStudy);
		select.removeAttr('disabled').removeAttr('title');
	} else {
		btn.text('取消申请').unbind().bind('click', delStudy);
		select.attr('disabled', true).attr('title', '修改之前请取消当前申请');
	}
}
/**
 * 
 */
$(function() {
	var setting = [];
	setting.push({id : 'study', display : '学习优秀', style : 'blue'});
	setting.push({id : 'spiritual', display : '精神文明', style : 'grey'});
	setting.push({id : 'work', display : '社会工作', style : 'deep'});
	setting.push({id : 'activity', display : '文体活动', style : 'deep'});
	setting.push({id : 'science', display : '科技创新', style : 'orange'});
	setting.push({id : 'practice', display : '社会实践', style : 'default'});
	// setting.push({id : 'progress', display : '评审进度', style : 'blue'});
	// setting.push({id : 'order', display : '查看排名', style : 'grey'});
	initNav(setting);
});

function initNav(setting) {
	var _title = $('<div></div>').addClass('page-title container').text('Scholarship Navigator');
	var _nav = $('<div></div>').addClass('wrap container').attr('id', 'nav');
	var _footer = $('<div></div>').addClass('footer container')
		.append($('<span></span>').attr('id', 'home').text('回到首页').on('click', function() {
			initNav(setting);
		})).append($('<span></span>').attr('id', 'mine').text('我的信息'))
		.append($('<span></span>').attr('id', 'logout').append($('<a></a>').text('退出登录').attr('href', '/user_login/logout')));
	var _navSet = function(setting) {
		var items = null;
		for (var i = 0; i < setting.length; i ++) {
			if (i % 3 == 0) {
				items = $('<div></div>').addClass('flex-box');
				_nav.append(items);
			}
			var set = setting[i];
			items.append($('<div></div>').append($('<span></span>')
				.addClass(set.style || 'default').text(set.display)).attr('id', 'nav_' + set.id)
				.on('click', function() {		
					$('.page-title').text($(this).text());
					$('#nav').empty().removeAttr('id').removeClass('container').addClass('apply-area');
					$.getScript('/js/user/' + $(this).attr('id').split('_')[1] + '.js');
				}));
		}
	}
	_navSet(setting);
	$('body').empty().addClass('body-flex-box').append(_title).append(_nav).append(_footer);
}
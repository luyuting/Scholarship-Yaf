/**
 * 
 */
$(function() {
	$('#login-button').click(function() {
		if (!valid()) {
			return false;
		}
		
		var params = {
			'admin_account': $('#userid').val().trim(),
			'admin_pass': $('#password').val().trim()
		};
		
		var url = '/admin_base/adminlogin';
		$.post(url, params, function(data, status) {
			if(data.code !== 10000) {
				alert("error-code: " + data.code + "\nerror-info: " + data.message.cause);
			} else {
				location.href = '/page_admin/setting';
			}
		}, 'json');
	});
	
	$(this).on('keypress', function(event) {
		var e = event || window.event; 
	    var keyCode = e.keyCode || e.which;
	    var isEnter = e.EnterKey || (keyCode == 13 ) || false ;
		if (isEnter) {
			$('#login-button').trigger('click');
		}
	});
	
	$('input.has-error').live('keyup', function() {
		$(this).removeClass('has-error');
	});
});

function valid() {
	var valid = true;
	
	var id_valid = /^[^<|>|;|\?|\||'|&]+$/;
	var pass_valid = /^[\w]{6,20}$/;
	if (!id_valid.test($('#userid').val().trim())) {
		$('#userid').removeClass().addClass('has-error').focus();
		valid = false;
	}
	if (!pass_valid.test($('#password').val().trim())) {
		$('#password').removeClass().addClass('has-error');
		if (!valid) {
			$('#password').focus();
			valid = false;
		}
	}
	
	return valid;
}
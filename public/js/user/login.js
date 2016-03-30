/**
 * 
 */
$(function() {
	$('#login-button').click(function() {
		if (!valid()) {
			return false;
		}
		
		var params = {
			'user_id': $('#userid').val().trim(),
			'user_pass': $('#password').val().trim()
		};
		
		var url = '/user_login/studentlogin';
		$.post(url, params, function(data, status) {
			if(data.code !== 10000) {
				alert("error-code: " + data.code + "\nerror-info: " + data.message.cause);
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
	
	var id_valid = /^(201)\d{6}$/;
	var pass_valid = /^[\w]{6,20}$/;
	if (!id_valid.test($('#userid').val().trim())) {
		$('#userid').removeClass().addClass('has-error').focus();
		valid = false;
	}
	if (!pass_valid.test($('#userid').val().trim())) {
		$('#password').removeClass().addClass('has-error');
		if (!valid) {
			$('#userid').focus();
			valid = false;
		}
	}
	
	return valid;
}
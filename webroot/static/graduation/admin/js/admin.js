var fileds = [
	{id:'username', nonnull:'true'},
	{id:'password', nonnull:'true'},
];

$("#btn").click(function(){
	var errs = $.verifyFileds(fileds);
	if ( errs.length > 0 ) {
		$.showErrTips(errs);
		return false
	} else {
		$.xNet({
			url: loginurl,
			type: 'post',
			errorCodes: "*",
			data: {
				username: $.trim( $('#username').val() ),
				password: $.trim( $('#password').val() ),
			},
			success: function(result) {
				window.location.href = result['data'];
			},
			error: function(result) {
				$.showMsgBox(result['msg']);
			}
		});
	}
});

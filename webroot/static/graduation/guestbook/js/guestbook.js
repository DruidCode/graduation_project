var fileds = [
	{id:'inputName2', nonnull:'true'},
	{id:'inputEmail3', nonnull:'true'},
	{id:'inputText', nonnull:'true'},
	{id:'code', nonnull:'true'},
];
//点击换验证码
$("#getcode_math").click(function(){
	$(this).attr("src",get_code + '?' + Math.random());
});

//提交
$("#sub").click(function(){
	var errs = $.verifyFileds(fileds);
	if ( errs.length > 0 ) {
		$.showErrTips(errs);
		return false
	} else {
		$.xNet({
			url: add_url,
			type: 'post',
			errorCodes: "*",
			data: {
				name: $.trim( $('#inputName2').val() ),
				email: $.trim( $('#inputEmail3').val() ),
				content: $.trim( $('#inputText').val() ),
				code: $.trim( $('#code').val() ),
			},
			success: function(result) {
				$.showMsgBox(result['msg']);
				window.location.reload();
			},
			error: function(result) {
				$.showMsgBox(result['msg']);
			}
		});
	}
});

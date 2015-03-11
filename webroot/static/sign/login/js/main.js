require(['zepto','ui/alertbox','util/xnet','util/verify'],function($,alertbox,xnet,verify) {
	var eles = [
		{id: 'mobile',type: 'text',nonnull: true},
		{id: 'vercode',type: 'text',nonnull: true}
	];
	$('#get-ver-code').on('click',function(){
		xnet({
			url: getVerCodeUrl,
			type: 'post',
			data: {
				mobile: $('#mobile').val()
			},
			errorCodes: '*',
			success: function(result) {
				alertbox.show(result['msg']);
			},
			error: function(result) {
				alertbox.show(result['msg']);
			}
		});
	});

	$('form').on('submit',function(){
		if(verify(eles).length == 0) {
			xnet({
				url: loginUrl,
				type: 'post',
				data: {
					mobile: $('#mobile').val(),
					vercode: $('#vercode').val()
				},
				errorCodes: '*',
				success: function(result) {
					window.location.href=result['data'];
				},
				error: function(result) {
					alertbox.show(result['msg']);
				}
			});
		}
		return false;
	});
});

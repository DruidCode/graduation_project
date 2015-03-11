require(['zepto','ui/alertbox','util/verify','util/xnet'],function($,alertbox,verify,xnet){
	var eles = [
			{id: 'name',type: 'text',nonnull: true},
			{id: 'mobile',type: 'text',nonnull: true},
		];

	 $('#submit-btn').on('click',function(){
	 	if(verify(eles).length > 0) {
			return false;
		} else {
			xnet({
				url: basicUrl,
				type: 'post',
				errorCodes: '*',
				data: {
					"name": $('#name').val(),
					"mobile": $('#mobile').val(),
					"email": $('#email').val(),
				},
				success: function(result) {
					window.location.href = result['data'];
				},
				error: function(result) {
					alertbox.show(result['msg']);
				}
			});
		}
	 });
});

require(['zepto','ui/alertbox','util/verify','util/xnet'],function($,alertbox,verify,xnet) {
	var eles = [
		{id: 'name',type: 'text',nonnull: true},
		{id: 'mobile',type: 'text',nonnull: true},
		{id: 'email',type: 'text',nonnull: true},
	];

	function gatherData() {
		var tmp = {};	
		var $eles = $('input');
		$eles.each(function(){
			var self = $(this);
			tmp[self.attr('name')] = self.val();
		});
		return tmp;
	}

	$('#submit-btn').on('click',function(){
		if(verify(eles).length == 0) {
			xnet({
				url: applyUrl,
				type: 'post',
				errorCodes: '*',
				data: gatherData(),
				success: function(result) {
					window.location.href = result['data'];
				},
				error: function(result) {
					alertbox.show(result['msg']);
				}
			});
		}
		return false;
	});
});

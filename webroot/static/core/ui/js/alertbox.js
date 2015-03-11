define(['zepto'],function($){
	var alertbox = {};
	alertbox.show = function(msg,delayTime) {
		var timeOutFlag;
		if ( $('#msg-box').length == 0 ) {
			$(window.document.body).append('<div class="msg-box" id="msg-box"><div class="msg-card"></div></div>');
			$('#msg-box').on('click',function() {
				clearTimeout(timeOutFlag);
				$(this).removeClass('show').addClass('hide');
			});
		} 
		var self = $('#msg-box');
		$('#msg-box>.msg-card').html(msg);
		self.removeClass('hide').addClass('show');
		timeOutFlag = setTimeout(function(){
			self.removeClass('show').addClass('hide');
		},delayTime?delayTime:3000);
	};
	return alertbox;
});

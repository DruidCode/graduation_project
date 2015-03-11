define(['zepto','zepto/detect'],function($,detect){
	var weixinTip,
		OSType,
		eleId = 'weixin-share-notice';
	weixinTip = {
		ele: null,
		show: function() {
			this.ele.addClass('show');
		},
		hide: function() {
			this.ele.removeClass('show');
		}
	}

	if(detect.os.ios) {
		OSType = 'ios';
	} else {
		OSType = 'android';
	}

	var tipEle = $('<div id="'+eleId+'" class="'+OSType+'"></div>');
	
	if ( !weixinTip.ele ) {
		tipEle.on('click',function(){
			weixinTip.hide();
		});
		weixinTip.ele = tipEle;
		$('body').append(tipEle);
	}
	
	return weixinTip;
});

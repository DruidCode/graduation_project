require(['ui/weixin-share-notice','util/weixinshare','zepto'],function(sharetip,wxshare,$){
	wxshare.init();
	$('#share-btn').on('click',function(){
		sharetip.show();
	});
});

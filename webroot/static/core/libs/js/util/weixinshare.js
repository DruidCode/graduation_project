/*
 *	分享页面链接给微信好友或朋友圈
 *	调用init方法进行初始化
 */
define(['zepto'],function($){
	var weixinshare = {};
	weixinshare.init = function () {
		var defaultData = {
			appId: "wx1ef47cd8b8835de5",
			img_width: "120",
			img_height: "120",
			callback: function(res) {
				//微信分享被调起后,分发 wxshared 事件.
				$(document).trigger('wxshared');
			}
		};

		var onBridgeReady = function () {
			WeixinJSBridge.on('menu:share:appmessage', function (argv) {
				WeixinJSBridge.invoke('sendAppMessage', shareData, function(res) {
					$.extend(window.shareData, defaultData);
					shareData.callback(res);
				});
			});
			WeixinJSBridge.on('menu:share:timeline', function (argv) {
				WeixinJSBridge.invoke('shareTimeline', shareData, function(res) {
					$.extend(window.shareData, defaultData);
					shareData.callback(res);
				});
			});
		}

		try {
			onBridgeReady();
		} catch(e) {
			if (document.addEventListener) {
				document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
			} else if (document.attachEvent) {
				document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
				document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
			}
		}
	};
	return weixinshare;
});

define(['zepto'],function($){
	function isString(obj) {
		return typeof obj == "string" || Object.prototype.toString.call(obj) === "[object String]";
	}

	/*
	 * jQuery.xNet:  Wrap "jQuery.ajax" method with JSON response style.
	 * @url:         Same explanation as "jQuery.ajax" method.
	 * @type:        Optional. Same explanation as "jQuery.ajax" method.
	 * @data:        Optional. Same explanation as "jQuery.ajax" method.
	 * @traditional: Optional. Defaut value is "true". Same explanation as "jQuery.ajax" method.
	 * @errorCodes:  Optional. Extended property, which used for list error codes you want to catching.
	 *               Codes list will separate by comma, or use "*" as the wildcard.
	 *               The default HTTP error code is -1. So, you can catch HTTP error also.
	 *               Example:
	 *                 errorCodes: "2,3,4", or errorCodes: "*"
	 * @success:     Optional. Callback for the Ajax response, when "code" value is "0".
	 * @error:       Optional. Callback for the Ajax response, when "code" value other than "0".
	 * @complete:    Optional. Same explanation as "jQuery.ajax" method.
	 */
	var xNet = function(settings) {

		// It's used for handling "success" and "error" callback.
		function callback(result) {
			if (!$.isPlainObject(result) || Number(result["code"]) == NaN ||
				(result["code"] != 0 && !result["msg"]) ||
				(result["code"] != 0 && !isString(result["msg"]))) {
				result = {
					code: -6,
					msg: "网络返回格式不正确。"
				};
			}

			if (result.code == 0) {
				if ($.isFunction(settings.success)) {
					settings.success(result);
				}
			}
			else if (settings.errorCodes == "*" ||
					(settings.errorCodes && new RegExp( "^" + result.code + "$|" +
												"^" + result.code + "[\s,]+|" +
												"[\s,]+" + result.code + "$|" +
												"[\s,]+" + result.code + "[\s,]+", "i").
												test(settings.errorCodes + ""))) {
				if ($.isFunction(settings.error)) {
					settings.error(result);
				}
			}
			else {
				// Below alert popup maybe not good for your project.
				// So, you can change the alert to any other warning way.
				$.alert({title: "错误", content: result.msg});
			}
		}

		return $.ajax({
			url: settings.url || window.location.href,
			type: settings.type || "get",
			data: settings.data,
			dataType: "json",
			traditional: (typeof settings.traditional == "boolean") ? settings.traditional : true,
			success: callback,
			error: function(xhr, status) {
				var map = {
					"abort": {
						code: -1,
						msg: "网络请求被取消。"
					},
					"parsererror": {
						code: -2,
						msg: "网络返回解析错误。"
					},
					"timeout": {
						code: -3,
						msg: "网络请求超时。"
					},
					"error": {
						code: -4,
						msg: "网络错误。"
					}
				};

				var result = map[status];
				if (!result) {
					result = {
						code: -5,
						msg: "未知的网络错误。"
					}
				}

				// You can change the default HTTP error code if it conflict.
				callback(result);
			},
			complete: settings.complete
		});
	}
	return xNet;
});

define(['zepto','ui/alertbox'],function($,alertbox){
	verifyFileds = function ( fields ) {
		var sortRule = ['nonnull','maxlength','minlength','email','number','max','min'];
		var rules = {
			nonnull: function(field) {
					if (field['type'] == 'select') {
						return $('#'+field['id']).val() == '0' ? false : true;
					} else {
						var fieldText = getText(field['id']);
						return fieldText.length>0 ? true : false;
					}
			},
			maxlength: function(field,limit) {
					var fieldText = getText(field['id']);
					return fieldText.length<=limit ? true: false;
			},
			minlength: function(field,limit) {
					var fieldText = getText(field['id']);
					return fieldText.length>=limit ? true: false;
			},
			email: /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/g,
			number: /^[0-9]*$/,
			max: function(field,limit) {
					var fieldText = getText(field['id']);
					return Number(fieldText)<=limit ? true: false;
			},
			min: function(field,limit) {
					var fieldText = getText(field['id']);
					return Number(fieldText)>=limit ? true: false;
			}
		}

		var result = [];
		for ( var i = 0 ; i < fields.length; i++ ) {

			var field = fields[i];

			if($('#'+field['id']).length == 0) {
				continue;
			}
			for ( var j in sortRule ) {
				var veriType = sortRule[j];
				if ( field[veriType] !=  undefined || field['type'] == veriType) {
					if ( typeof rules[veriType] == 'function' ) {
						if (!rules[veriType](field,field[veriType])) {
							addErrMsg(field['id'],veriType);
							break;
						}
					} else if ( typeof rules[veriType] == 'object' ) {
						var testObj = rules[veriType];
						var beTestStr = getText(field['id']);
						if (! testObj.test(beTestStr) ){
							addErrMsg(field['id'],veriType);
							break;
						}
					}
				}
			}
		}
		function getText( id ) {
			return $.trim($('#'+id).val());
		}
		function addErrMsg(id,err) {
			result.push({id:id,err:err});			
		}
		showErrTips(result);
		return result;
	}

	showErrTips = function (errMsgs) {
		for ( var i = 0; i < errMsgs.length; i++) {
			var msg = errMsgs[i];
			var ele = $('#'+msg['id']);
			if ( i == 0 ) {
				ele.focus();
			}
			var errTip = '';
			switch( msg['err']) {
				case 'nonnull':
					errTip = "不能为空";
				break;	
				case 'maxlength':
					errTip = "超过最大字数限制";
				break;
				case 'email':
				case 'mobile':
					errTip = '格式错误';
				break;
				case 'number': 
					errTip = '必须为数字';
				break;
				case 'max': 
					errTip = '超过最大限制';
				break;
				case 'min': 
					errTip = '低于最小限制';
				break;
			}
			var eleName = ele.attr('data-name') ? ele.attr('data-name') : ele.attr('placeholder');
			alertbox.show(eleName+errTip);
			break;
		}
	}
	return verifyFileds;
});

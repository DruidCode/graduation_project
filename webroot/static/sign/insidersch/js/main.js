require(['jquery','ui/alertbox'],function($,alertbox){
	$('.top-type').on('click',function(){
		$('.sub-type').hide();
		$(this).siblings('.sub-type').show();
		$('.sub-type:hidden input').each(function(){
			this.checked = false;
		});
	});

	function init() {
		if($('input:checked').length > 0 ) {
			$('input:checked').parents('.sub-type').show().siblings('.top-type').find('input')[0].checked=true;
		}
		//$('[data-type="top-type"]:checked').parent().siblings('.sub-type').show();
	}

	init();

	$('form').on('submit',function(){
		if(!check()) {
			return false;
		}
	});
	
	function check() {
		if($('[data-type="top-type"]:checked').length ==  0) {
			alertbox.show('请选择');
			return false;
		} else {
			if($('input:checked',$('[data-type="top-type"]:checked').parent().siblings('.sub-type')).length == 0) {
				alertbox.show('请选择一个行程');
				return false;
			} else {
				if($('[data-type="sports"]:checked').length != 0) {
					if($('#sports input:checked').length == 0) {
						alertbox.show('请选择一项运动');
						return false;
					}
				}
			}
		}
		return true;
	}
});

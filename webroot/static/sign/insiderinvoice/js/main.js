require(['zepto','util/verify'],function($,verify){
	var $detail = $('#invoice-detail');
	var eles = [
		{id: 'invoice_company',type: 'text',nonnull: true},
		{id: 'invoice_number',type: 'text',nonnull: true},
		{id: 'invoice_address',type: 'text',nonnull: true},
		{id: 'invoice_phone',type: 'text',nonnull: true},
		{id: 'invoice_bank',type: 'text',nonnull: true},
		{id: 'invoice_account',type: 'text',nonnull: true}
	];
	$('#invoice-type').on('change',function(){
		if($(this).val() == '0' || $(this).val() == '2') {
			$detail.hide();
		} else {
			$detail.show();
		}
	});

	if($('#invoice-type').val() != 0) {
		$detail.show();
	}

	$('form').on('submit',function() {
		if($('#invoice-type').val() != 0) {

			if(verify(eles).length > 0) {
				return false;
			}
		}
	});

});

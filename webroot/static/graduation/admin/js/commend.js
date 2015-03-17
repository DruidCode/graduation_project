$("#form1").on('submit', function(){
	var avatar = $('input[type="file"]').val();
	var avatar_def = $('#avatar_def').val();
	var name = $('#name').val();
	var brief = $('#brief').val();
	var detail = $('#detail').val();
	if ( !avatar_def && !avatar) {
		$.showMsgBox('请选择头像');
		return false;
	}
	if (!name) {
		$.showMsgBox('姓名不能为空');
		return false;
	}
	if (!brief) {
		$.showMsgBox('简介不能为空');
		return false;
	}
	if (!detail) {
		$.showMsgBox('详情不能为空');
		return false;
	}
});

$("#form1").on('submit', function(){
	var avatar = $('input[type="file"]').val();
	var avatar_def = $('#avatar_def').val();
	var name = $('#act_name').val();
	var act_info = $('#act_info').val();
	var act_route = $('#act_route').val();
	var begin = $('#begin_time').val();
	var end = $('#end_time').val();
	var sign = $('#sign_time').val();
	if ( !avatar_def && !avatar) {
		$.showMsgBox('请选择头像');
		return false;
	}
	if (!name) {
		$.showMsgBox('活动名不能为空');
		return false;
	}
	if (!act_info) {
		$.showMsgBox('活动简介不能为空');
		return false;
	}
	if (!act_route) {
		$.showMsgBox('活动行程不能为空');
		return false;
	}
	if (!begin) {
		$.showMsgBox('活动开始时间不能为空');
		return false;
	}
	if (!end) {
		$.showMsgBox('活动结束时间不能为空');
		return false;
	}
	if (!sign) {
		$.showMsgBox('活动截止时间不能为空');
		return false;
	}
});

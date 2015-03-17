$("#form1").on('submit', function(){
	var avatar = $('input[type="file"]').val();
	var avatar_def = $('#avatar_def').val();
	var title = $('#news_title').val();
	var source = $('#news_source').val();
	var info = $('#news_info').val();
	if ( !avatar_def && !avatar) {
		$.showMsgBox('请选择头像');
		return false;
	}
	if (!title) {
		$.showMsgBox('新闻标题不能为空');
		return false;
	}
	if (!source) {
		$.showMsgBox('来源不能为空');
		return false;
	}
	if (!info) {
		$.showMsgBox('新闻内容不能为空');
		return false;
	}
});

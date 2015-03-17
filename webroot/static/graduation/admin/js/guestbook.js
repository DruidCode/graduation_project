$("#delete").click(function(){
    if( !confirm('确认删除?') ) { 
        return false;
    }   
    var self = $(this);
    id = self.parents('tr').attr('id');
    $.xNet({
        url: del_url,
        type: "post",
        errorCodes : "*",
        data: {
            id: id, 
        },  
        success: function(result) {
            $.showMsgBox(result['msg']);
            window.location.reload();
        },  
        error: function(result) {
            $.showMsgBox(result['msg']);
        }   
    }); 
});

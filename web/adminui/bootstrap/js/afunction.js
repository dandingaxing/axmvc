// 隐藏显示
function IdShowHide(showID, hideId){
	$("#"+showID).show();
	$("#"+hideId).hide();
}


// 删除确认提示
function isDelete(){
	if(confirm("确定要删除此信息？"))
		return true;
	else
		return false;
}


function setIframeHeight(iframe) {
if (iframe) {
  var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
  if (iframeWin.document.body) {
    iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
  }
}
}


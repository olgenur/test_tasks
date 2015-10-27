var refresh_interval = 18000;

function timestamp_to_date(ts){
	return ts.substr(8,2)+'.'+ts.substr(5,2)+'.'+ts.substr(0,4)
				+' '+ts.substr(11,5);
}

function on_refresh(data){
	var html = '', mesIdStr;
	if(data.msgs.length>0){// if have new messages
		var common_msg_mark = ' <span class="mestype_common">ВСЕМ</span>';
		for(i in data.msgs){
					mesIdStr = ' id="mes'+data.msgs[i].mes_id+'" ';
					html += '<dt>'+data.msgs[i].room_name+' - '+timestamp_to_date(data.msgs[i].mes_date)
						+' - '+data.msgs[i].user_name+((data.msgs[i].type_id==2)?common_msg_mark:'')+'</dt>'
						+'<dd'+mesIdStr+'class="with-admin-controls"><div class="delete-control">X</div>'
						+data.msgs[i].mes_text+'</dd>';
		}
		$('.messages-list dl').append(html);
		$('.with-admin-controls .delete-control').click(function(){
			deleteMessage($(this).parent().attr('id'));
		});
	}// end new messages import

	last_date = data.last;
	scrollToLastMessage();
}

function scrollToLastMessage(){
	var block = document.querySelector('.messages-list .content');
	block.scrollTop = block.scrollHeight;
}

function get_json_error(e, x, settings, exception) {
    var message;
    var statusErrorMap = {
        '400' : "Server understood the request, but request content was invalid.",
        '401' : "Unauthorized access.",
        '403' : "Forbidden resource can't be accessed.",
        '500' : "Internal server error.",
        '503' : "Service unavailable."
    };
    if (x.status) {
        message =statusErrorMap[x.status];
        if(!message){
            message="Unknown Error "+x.status;
        }
    }else if(exception=='parsererror'){
        message="Error.\nParsing JSON Request failed.";
    }else if(exception=='timeout'){
        message="Request Time out.";
    }else if(exception=='abort'){
        message="Request was aborted by the server";
    }else {
        message="Unknown Error: "+exception;
    }
    return message;
}

function send_message(text)
{
	$.ajax({
			type: 'POST',
			url: "admin_messenger.php",
			dataType: 'json',
			cache: false,
			data: {message: text},
			success: function(data){
				$('.message-input textarea').val('');
				refreshList('.messages-list dl', last_date);
			},
			error: function(e, x, settings, exception){
					console.log(get_json_error(e, x, settings, exception));
			}
	});
}

function refreshList(list_control, last_date)
{
	$.ajax({
			type: 'POST',
			url: "admin_messenger.php",
			dataType: 'json',
			cache: false,
			data: {
				last_date: last_date
			},
			success: function(data){
				if(!data.zero) on_refresh(data);
			},
			error: function(e, x, settings, exception){
					console.log(get_json_error(e, x, settings, exception));
			}
	});
}

function mark_deleted_message(mesid){
	$('#mes'+mesid).html(
		'<span class="message-deleted">сообщение удалено администратором</span>'
	);
}

function deleteMessage(mesid)
{
	$.ajax({
			type: 'GET',
			url: "admin_del_msg.php",
			dataType: 'json',
			cache: false,
			data: {mesid: mesid.substr(3)},
			success: function(data){
				if(data.answer=='1'){
					mark_deleted_message(mesid.substr(3));
				}
			},
			error: function(e, x, settings, exception){
					console.log(get_json_error(e, x, settings, exception));
			}
	});
}

function Format00(n){
		return (n<10) ? ('0'+n) : n;
}

function ConvertToDate(ds){
		return ds.substr(-2,2)+'.'+ds.substr(5,2)+'.'+ds.substr(0,4);
}

$(document).ready(function(){
	scrollToLastMessage();
	$('.message-input form').submit(function(){
		var text = $('.message-input textarea').val();
		if(text.length < 5) return false;
		send_message(text);
		return false;
	})
	document.getElementsByTagName('textarea')[0].onkeypress = function(e) {
		if(e.keyCode == 13) {
			$('.message-input form').submit();
		}
		return true;
	};
	setInterval(function() {
			refreshList('.messages-list dl', last_date);
		}, refresh_interval);
	//clearInterval(timerId);
	$('.with-admin-controls .delete-control').click(function(){
		deleteMessage($(this).parent().attr('id'));
	});
});

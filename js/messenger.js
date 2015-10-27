var refresh_interval = 18000;

function on_message_sent(data){
	$('.message-input textarea').val('');
	refreshList('.messages-list dl', last_date, to_user_id, uid_list);
}

function timestamp_to_date(ts){
	return ts.substr(8,2)+'.'+ts.substr(5,2)+'.'+ts.substr(0,4)
				+' '+ts.substr(11,5);
}

function contacts_item_html(user_type, user_id, user_name){
	//room_name
	if(user_type==0)
		var user_class = "utype_user";
	else
		var user_class = "utype_admin";
	return '<li id="ul'+user_id+'"><a class="'+user_class
					+'" href="privat_chat.php?rid='+room_id
					+'&rn='+encodeURI(room_name)+'&uid='+user_id
					+'&un='+encodeURI(user_name)+'">'+user_name+'</a></li>';
}

function on_refresh(data){
	if(data.zero) return false;
	var html = '', mesIdStr;
	if(data.kickout){
		console.log('<<KICKOUT>>');
		window.location = 'login.php';
	}
	if(data.msgs.length>0){// if have new messages
		console.log('msgs>>', data.msgs);
		if(to_user_id==0){ //if room chat
			var utype_class_filter = {0: 'user',	1: 'admin'};
			var mestype_filter = {
				0: '<span class="mestype_privat">ЛИЧНО ВАМ</span>',
				1: '',
				2: '<span class="mestype_common">ВСЕМ ПОЛЬЗОВАТЕЛЯМ</span>'
			};
			for(i in data.msgs){
					mesIdStr = ' id="mes'+data.msgs[i].mes_id+'" ';
					if(data.msgs[i].from_user_id==my_id){
						html += '<dt class="my-message">'+timestamp_to_date(data.msgs[i].mes_date)
							+' - Я '+mestype_filter[data.msgs[i].type_id]+'</dt>'
							+'<dd'+mesIdStr+'class="my-message'+admin_class
							+'"><div class="delete-control">X</div>'+data.msgs[i].mes_text+'</dd>';
					}
					else{
						html += '<dt>'+timestamp_to_date(data.msgs[i].mes_date)
							+' <span class="'+utype_class_filter[data.msgs[i].user_type]+'">'
							+data.msgs[i].user_name+'</span> '+mestype_filter[data.msgs[i].type_id]+'</dt>'
							+'<dd'+mesIdStr+'class="'+admin_class+'"><div class="delete-control">X</div>'
							+data.msgs[i].mes_text+'</dd>';
					}
			}
		} // end room chat

		else{ // if privat chat
			for(i in data.msgs){
					if(data.msgs[i].from_user_id==my_id){
						html += '<dt class="my-message">'+timestamp_to_date(data.msgs[i].mes_date)+' - Я</dt>'
							+'<dd class="my-message">'+data.msgs[i].mes_text+'</dd>';
					}
					else{
						html += '<dt>'+timestamp_to_date(data.msgs[i].mes_date)+' - '+to_user_name+'</dt>'
							+'<dd>'+data.msgs[i].mes_text+'</dd>';
					}
			}
		} // end privat chat

		$('.messages-list dl').append(html);
		$('.with-admin-controls .delete-control').click(function(){
			deleteMessage($(this).parent().attr('id'));
		});

	}// end new messages import

	if(data.delmsgs.length>0 && to_user_id==0){
		// if have deleted messages and if room chat
		console.log('delmsgs>>', data.delmsgs);
		for(i in data.delmsgs){
			mark_deleted_message(data.delmsgs[i]);
		}
	}

	//users list refresh:
	if(to_user_id==0){ //if room chat
		var ulist = $('.contacts-list ul');
		if(data.gone.length>0){// if have gone users
			console.log('gone>>', data.gone);
			for(i in data.gone){
				ulist.find('#ul'+data.gone[i]).remove();
				uid_list.splice(uid_list.indexOf(data.gone[i]),1);
			}
		} //end gone users
		if(data.new.length>0){// if have new users
			console.log('new>>', data.new);
			for(i in data.new){
				ulist.append(
					contacts_item_html(data.new[i].user_type, data.new[i].user_id, data.new[i].user_name)
				);
				uid_list.push(data.new[i].user_id);
			}
		}// end new users
	} // end users list refresh

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

function send_message(text, to_user_id)
{
	//var message = JSON.stringify({'text':text,'room_id': room_id});
	$.ajax({
			type: 'POST',
			url: "push_message.php",
			dataType: 'json',
			cache: false,
			data: {message: text, to_user_id: to_user_id},
			success: function(data){
				on_message_sent(data);
			},
			error: function(e, x, settings, exception){
					console.log(get_json_error(e, x, settings, exception));
			}
	});
}

function refreshList(list_control, last_date, to_user_id, users_list)
{
	$.ajax({
			type: 'POST',
			url: "refresh.php",
			dataType: 'json',
			cache: false,
			data: {
				last_date: last_date,
				to_user_id: to_user_id,
				users_list: JSON.stringify(users_list)
			},
			success: function(data){
				console.log(data.zero, data.kickout);
				on_refresh(data);
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
		send_message(text, to_user_id);
		return false;
	})
	document.getElementsByTagName('textarea')[0].onkeypress = function(e) {
		if(e.keyCode == 13) {
			$('.message-input form').submit();
		}
		return true;
	};
	setInterval(function() {
			refreshList('.messages-list dl', last_date, to_user_id, uid_list);
		}, refresh_interval);
	//clearInterval(timerId);
	$('.with-admin-controls .delete-control').click(function(){
		deleteMessage($(this).parent().attr('id'));
	})
});

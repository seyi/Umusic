var variables = {
	base: base
};
var templates;

var echo;

$(document).ready(function(){
	var signin_options = {
		width		: '50%',
		height		: '45%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	};
        
        var register_options = {
		width		: '50%',
		height		: '58%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	};        
        
        var signout_options = {
		width		: '30%',
		height		: '20%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	};
	
	// Load Mustache templates
	templates = {
		'playlist-item':'partials/playlist-item',
		'recommendation-item':'partials/recommendation-item',
                'stream-item':'partials/stream-item',
		'register-dialog':'dialog/register',
		'signin-dialog':'dialog/signin',
		'signout-dialog':'dialog/signout',
		'search-page':'page/search',
		'playlist-page':'page/playlist'
	};
	
	$.each(templates,function(key,val){
		$.get(variables.base + 'media/templates/' + val + '.mustache', function(data){
			templates[key] = data;
		});
	});
	
	$('.action').live('click',function(event){
		event.preventDefault();
		var src = $(this);
		
		if(src.hasClass('user')){
			variables.errors = {};
			variables.values = {};
			switch(src.attr('href')){
				case '#signin-dialog':
					$.fancybox(Mustache.to_html(templates['signin-dialog'],variables),signin_options);
				break;
				case '#register-dialog':
					$.fancybox(Mustache.to_html(templates['register-dialog'],variables),register_options);
				break;
				case '#signout-dialog':
					$.fancybox(Mustache.to_html(templates['signout-dialog'],variables),signout_options);
				break;
				case '#signout':
					$.get(variables.base + "api/signout", function(response) {
						var info = $.parseJSON(response);
						if(info.status == 0) {
							$("#user").html('<a class="action user" href="#signin-dialog">Sign in</a><a class="action user" href="#register-dialog">Register</a>');
							variables.user = false;
							$.fancybox.close();
						}
					});
				break;
                                case '#close':
                                    $.fancybox.close();
                                break;
			}
		} else if(src.hasClass('page')) {
			if(src.attr('href') == 'playlist') {
                                $.get(variables.base + "api/playlist_get", function(response) {
                                        var info = $.parseJSON(response);
                                        if(info.status == 0) {
                                            $('#main').html(Mustache.to_html(templates[src.attr('href')+'-page'],variables));
                                            if(info.results) { 
                                                $.each(info.results, function(key,val){
                                                    var song = Mustache.to_html(templates['playlist-item'],$.merge(val[0],variables));
                                                    $('#main').append(song);
                                                }); 
                                            } else {
                                                 $('#main').append('Your playlist is empty.');
                                            }
                                        } else {
                                            alert(info.message);
                                        }
                                });
                        } else {
                                $('#main').html(Mustache.to_html(templates[src.attr('href')+'-page'],variables));
                        }
		} else if (src.hasClass('recommendation')) {
			var track_id = src.parent().parent().attr('id');
                        var action;
			if(src.hasClass('add')) {
				action = 'added';
                                $.post(variables.base + "api/playlist_add", {track_id:track_id}, function(response) {
                                    console.log(response);
                                });
			} else {
				action = 'removed';
			}
			
			$.post(variables.base + 'api/action', {action:action, track_id:track_id}, function(response){
				console.log(response);
                                var info = $.parseJSON(response);
                                if(info.status == 0 && action == 'removed') {
                                    var id = document.getElementById(track_id);
                                    id.parentNode.removeChild(id);
                                }
			});
		} else if (src.hasClass('playlist')) {
                        var track_id = src.parent().parent().attr('id');
                        console.log(track_id);
                        $.post(variables.base + 'api/playlist_remove', {track_id:track_id}, function(response) {
                            console.log(response);
                            var info = $.parseJSON(response);
                                if(info.status == 0) {
                                    var id = document.getElementById(track_id);
                                    id.parentNode.removeChild(id);
                                }
                        });
                }
		return false;
	});
	
	$("form").live("submit", function(event){
		event.preventDefault();
		var src = $(this);
		
		var data = {};
		$.each(src.children('input'),function(key, input){
			input = $(input);
			data[input.attr('name')] = input.val();
		})
		
		$.post(variables.base + "api/" + src.attr('action'), data, function(response) {
			var info = $.parseJSON(response);
			process(src, info);
		});
		
		return false;
	});
	
	function process(src, info) {
		var action = src.attr('action');
		switch (action) {
			case 'register':
			case 'signin':
				if(info.status == 0) {
					variables.user = info.user;
					$("#user").html('<a class="action user" href="#signout-dialog">Welcome, ' + variables.user.username + '</a>');
                                      
                                        $.get(variables.base + "api/playlist_get", function(response) {
                                            var info = $.parseJSON(response);
                                            if(info.status == 0) {
                                                if(info.results) { 
                                                    $('#playlist').html('');
                                                    $.each(info.results, function(key,val){
                                                        var song = Mustache.to_html(templates['stream-item'],$.merge(val[0],variables));
                                                        $('#playlist').append(song);
                                                    }); 
                                                } else {
                                                     $('#playlist').append('Your playlist is empty.');
                                                }
                                            } 
                                        });
                                        
                                        $.fancybox.close();
				} else {
					variables.errors = info.errors;
					variables.values = info.values;
				
					src.parent().html(Mustache.to_html(templates[action + '-dialog'],variables));
				}
			break;
			case 'search':
				if(info.status == 0) {
					$('#main').html('<h1>Search Results:</h1>');
					$.each(info.results, function(key,val){
                                                var song = Mustache.to_html(templates['recommendation-item'],$.merge(val,variables));
						$('#main').append(song);
					});
				} else {
					alert(info.message);
				}
			break;
		}
	}
	
});

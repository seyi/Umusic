var variables = {
	base: base
};
var templates;

var echo;

$(document).ready(function(){
	var fancybox_options = {
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	};
	
	// Load Mustache templates
	templates = {
		'playlist-item':'partials/playlist-item',
		'recommendation-item':'partials/recommendation-item',
		'register-dialog':'dialog/register',
		'signin-dialog':'dialog/signin',
		'user-dialog':'dialog/user',
		'search-page':'page/search'
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
					$.fancybox(Mustache.to_html(templates['signin-dialog'],variables),fancybox_options);
				break;
				case '#register-dialog':
					$.fancybox(Mustache.to_html(templates['register-dialog'],variables),fancybox_options);
				break;
				case '#user-dialog':
					$.fancybox(Mustache.to_html(templates['user-dialog'],variables),fancybox_options);
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
			}
		} else if(src.hasClass('page')) {
			
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
					$("#user").html('<a class="action user" href="#user-dialog">Welcome, ' + variables.user.username + '</a>');
					$.fancybox.close();
				} else {
					variables.errors = info.errors;
					variables.values = info.values;
				
					src.parent().html(Mustache.to_html(templates[action + '-dialog'],variables));
				}
			break;
		}
	}
	
});

function getInfo(artist,title,callback) {
	var url = 'http://developer.echonest.com/api/v4/song/search?api_key=4M0DFUG9R81A8U7OB&format=json&results=1&artist=' + artist + '&title=' + title + '&bucket=id:7digital-US&bucket=audio_summary&bucket=tracks';
	$.get(url, callback);
}
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
			$('#main').html(Mustache.to_html(templates[src.attr('href')+'-page'],variables));
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

function get_artwork(artist, title) {
	// cb4d0e82fd5feaceced5dcd045034065
	$.get('http://ws.audioscrobbler.com/2.0/',{
		method: 'track.getinfo',
		api_key: 'cb4d0e82fd5feaceced5dcd045034065',
		artist: artist,
		track: title
	},function(data){
		var images = $('image',data);
		var image = $(images[images.length - 1]).text();
		
		//RETURN IMAGE
	});
}

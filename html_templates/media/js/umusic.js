var templates;
$(document).ready(function(){
	var variables = {
		base: "/",
		
	};
	
	var fancybox_options = {
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: true,
		openEffect	: 'none',
		closeEffect	: 'none'
	};
	
	// Load Mustache templates
	templates = {'playlist-item':'','recommendation-item':'','register-dialog':'','signin-dialog':''};
	$.each(templates,function(key){
		$.get('media/templates/' + key + '.mustache', function(data){
			templates[key] = data;
		});
	});
	
	$('.action').live('click',function(event){
		event.preventDefault();
		var src = $(this);
		
		if(src.hasClass('user')){
			switch(src.attr('href')){
				case '#signin-dialog':
					$.fancybox(Mustache.to_html(templates['signin-dialog'],variables),fancybox_options);
				break;
				case '#register-dialog':
					$.fancybox(Mustache.to_html(templates['register-dialog'],variables),fancybox_options);
				break;
			}
		}
		
		return false;
	});
});
jQuery(document).ready(function($){
	function cleanHref(){
		var pdpath = arguments[0];
		var preg = /\/[^\/]{0,}$/ig;
		if( typeof pdpath == 'undefined') pdpath = 'null';
		if( pdpath[pdpath.length-1]=="/" ){
			pdpath = pdpath.substring(0,pdpath.length-1);
			return (pdpath.match(preg)+"/");
		}
		return pdpath.match(preg);
	}

	function quickView(){
		var qvpath = 'quickview/index/view';
		var baseUrl = SHOPGO_QV.SETTING.BASE_URL + qvpath;
		$.each($(arguments[0].wrapQuickView), function() {
			// Append quick view
			if( $(this).find("a.shopgo-btn-quickview").length <= 0 ){
				if( $(this).find("a").length > 0 ){
					link = $(this).find("a");
					var href = cleanHref(link.attr('href'))[0];
					href = (href[0] == "\/") ? href.substring(1, href.length) : href;
					href = baseUrl+"/path/" + href.replace(/^\s+|\s+$/g,""); //console.log(href);
					// product type
					href = href.replace('?options=cart', "");
					//href = href+'?is_quickview=1';
					$(this).append("<a class=\"shopgo-btn-quickview\" data-original-title=\""+SHOPGO_QV.SETTING.TEXT+"\" data-toggle=\"tooltip\" href=\""+href+"\"><span>"+SHOPGO_QV.SETTING.TEXT+"</span></a>");
				}
			}
		});
		// Insert popup for quick view
		$('.shopgo-btn-quickview').fancybox({
			'width'				: SHOPGO_QV.SETTING.POP_WIDTH,
			'height'			: SHOPGO_QV.SETTING.POP_HEIGHT,
			'autoScale'			: true,
			'padding'			: 20,
			'margin'			: 20,
			'scrolling'         : 'auto',
			'autoDimensions'    : false,
			'type'				: 'ajax',
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'elastic',
			'onComplete'        : function() {
				// By Sujoud , on 11/6/2018 :
 				ProductMediaManager.wireThumbnails();
//				$(".owl-carousel").each(function(){
//					$(this).data('owlCarousel').reinit();
//				});
			}
		});
	}
	quickView({wrapQuickView : SHOPGO_QV.SETTING.SELECTOR});
	setInterval(function(){ quickView({wrapQuickView : SHOPGO_QV.SETTING.SELECTOR}) },1000);
});


var $f = jQuery.noConflict();
jQuery(function($f) {
	var myhref,qsbtt;

	// base function
	
	//get IE version
	function ieVersion(){
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer'){
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		}
		return rv;
	}

	//read href attr in a tag
	function readHref(){
		var mypath = arguments[0];
		var patt = /\/[^\/]{0,}$/ig;
		if(mypath[mypath.length-1]=="/"){
			mypath = mypath.substring(0,mypath.length-1);
			return (mypath.match(patt)+"/");
		}
		return mypath.match(patt);
	}


	//string trim
	function strTrim(){
		return arguments[0].replace(/^\s+|\s+$/g,"");
	}

	function _qsJnit(){
	

		
		var selectorObj = arguments[0];			
		var listprod = $f(selectorObj.itemClass);
		var qsImg;
		var mypath = 'quickshop/index/view';
		if(TM.QuickShop.BASE_URL.indexOf('index.php') == -1){
			mypath = 'index.php/quickshop/index/view';
		}
		var baseUrl = TM.QuickShop.BASE_URL + mypath;
		
		var _qsHref = "<a id=\"tm_quickshop_handler\" href=\"#\" style=\"visibility:hidden;position:absolute;top:0;left:0\"><img  alt=\"quickshop\" src=\""+TM.QuickShop.QS_IMG+"\" /></a>";
		$f(document.body).append(_qsHref);
		
		var qsHandlerImg = $f('#tm_quickshop_handler img');

		$f.each(listprod, function(index, value) { 
			var reloadurl = baseUrl;
			
			//get reload url
			myhref = $f(value).children(selectorObj.aClass );
			var prodHref = readHref(myhref.attr('href'))[0];
			prodHref[0] == "\/" ? prodHref = prodHref.substring(1,prodHref.length) : prodHref;
			prodHref=strTrim(prodHref);
			
			reloadurl = baseUrl+"/path/"+prodHref;	
			version = ieVersion();	
			if(version < 8.0 && version > -1){
				reloadurl = baseUrl+"/path"+prodHref;
			}
			//end reload url

			
			$f(selectorObj.imgClass, this).bind('mouseover', function() {
				var o = $f(this).offset();
				$f('#tm_quickshop_handler').attr('href',reloadurl).show()
					.css({
						'top': o.top+($f(this).height() - qsHandlerImg.height())/2+'px',
						'left': o.left+($f(this).width() - qsHandlerImg.width())/2+'px',
						'visibility': 'visible'
					});
			});
			$f(value).bind('mouseout', function() {
				$f('#tm_quickshop_handler').hide();
			});
		});

		//fix bug image disapper when hover
		$f('#tm_quickshop_handler')
			.bind('mouseover', function() {
				$f(this).show();
			})
			.bind('click', function() {
				$f(this).hide();
			});
		//insert quickshop popup
		
		$f('#tm_quickshop_handler').fancybox({
				'width'				: TM.QuickShop.QS_FRM_WIDTH,
				'height'			: TM.QuickShop.QS_FRM_HEIGHT,
				'autoScale'			: false,
				'padding'			: 0,
				'margin'			: 0,				
				'type'				: 'iframe',
				onComplete: function() { 
					$f.fancybox.showActivity();
					$f('#fancybox-frame').unbind('load');
					$f('#fancybox-frame').bind('load', function() {
						$f.fancybox.hideActivity();
					});
				}
		}); 
	
	}

	//end base function


	_qsJnit({
		itemClass : '.products-grid li.item .product-block .product-block-inner , .product-carousel li.slider-item .product-block .product-block-inner ', //selector for each items in catalog product list,use to insert quickshop image
		aClass : 'a.product-image', //selector for each a tag in product items,give us href for one product
		imgClass: '.product-image img'//class for quickshop href
	}); 

});

jQuery("document").ready(function()
{
		 $f('#tm_quickshop_handler').hover(function()
		   {
			   	$f('#tm_quickshop_handler img').attr('src',TM.QuickShop.QS_IMGH);
		   },
	   function()
		   {
			   	$f('#tm_quickshop_handler img').attr('src',TM.QuickShop.QS_IMG);
		  });
});


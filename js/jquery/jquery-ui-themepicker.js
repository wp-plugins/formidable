//add js to html element for css selectors
document.documentElement.className = 'js';

//global for tracking open and focused toolbar panels on refresh
var openGroups = [];
var focusedEl = null;

//backbutton and hash bookmarks support
var hash = {
	storedHash: '',
	currentTabHash: '', //The hash that's only stored on a tab switch
	cache: '',
	interval: null,
	listen: true, // listen to hash changes?
	
	 // start listening again
	startListening: function() {
		setTimeout(function(){hash.listen = true;}, 600);
	},
	 // stop listening to hash changes
	stopListening:function(){hash.listen = false;},
	//check if hash has changed
	checkHashChange:function(){
		var locStr = hash.currHash();
		if(hash.storedHash != locStr) {
			if(hash.listen == true) hash.refreshToHash(); ////update was made by back button
			hash.storedHash = locStr;
		}
		if(!hash.interval) hash.interval = setInterval(hash.checkHashChange, 500);
	},
	
	//refresh to a certain hash
	refreshToHash: function(locStr) {
		if(locStr) var newHash = true;
		locStr = locStr || hash.currHash();
		updateCSS(locStr);
		// remember which groups are open
		openGroups = [];
		jQuery('div.theme-group-content').each(function(i){
			if(jQuery(this).is(':visible')){openGroups.push(i);}
		});
		
		// remember any focused element
		focusedEl = null;
		jQuery('form input, form select, form .texturePicker').each(function(i){
			if(jQuery(this).is('.focus')){focusedEl = i;}
		});

		// reload tab
		jQuery('#rollerTabs').tabs('url', 0, '/themeroller/_rollyourown.php?'+ locStr);
		jQuery('#rollerTabs').tabs('load', 0);
		
		// if the hash is passed
		if(newHash){ hash.updateHash(locStr, true); }
	},
	
	updateHash: function(locStr, ignore) {
		if(ignore == true){ hash.stopListening(); }
		window.location.hash = locStr;
		if(ignore == true){ 
			hash.storedHash = locStr; 
			hash.startListening();
		}
		
	},
	
	clean: function(locStr){return locStr.replace(/%23/g, "").replace(/[\?#]+/g, "");},
	
	currHash: function(){return hash.clean(window.location.hash);},
	
	currSearch: function(){return hash.clean(window.location.search);},
	
	init: function(){
		hash.storedHash = '';
		hash.checkHashChange();
	}	
};



/* jQuery plugin themeswitcher
---------------------------------------------------------------------*/
jQuery.fn.themeswitcher = function(settings){
	var options = jQuery.extend({
		initialText: 'Select Theme',
		width: 150,
		height: 200,
		buttonPreText: 'Theme: ',
		closeOnSelect: true,
		buttonHeight: 14,
		onOpen: function(){},
		onClose: function(){},
		onSelect: function(){}
	}, settings);

	//markup 
	var button = jQuery('<a href="#" class="jquery-ui-themeswitcher-trigger"><span class="jquery-ui-themeswitcher-icon"></span><span class="jquery-ui-themeswitcher-title">'+ options.initialText +'</span></a>');
	var switcherpane = jQuery('<div class="jquery-ui-themeswitcher"><div id="themeGallery">	<ul>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_ui_light.png" alt="UI Lightness" title="UI Lightness" />			<span class="themeName">UI lightness</span>		</a></li>				<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-darkness/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_ui_dark.png" alt="UI Darkness" title="UI Darkness" />			<span class="themeName">UI darkness</span>		</a></li>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_smoothness.png" alt="Smoothness" title="Smoothness" />			<span class="themeName">Smoothness</span>		</a></li>							<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/start/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_start_menu.png" alt="Start" title="Start" />			<span class="themeName">Start</span>		</a></li>				<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/redmond/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_windoze.png" alt="Redmond" title="Redmond" />			<span class="themeName">Redmond</span>		</a></li>						<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/sunny/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_sunny.png" alt="Sunny" title="Sunny" />			<span class="themeName">Sunny</span>		</a></li>						<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/overcast/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_overcast.png" alt="Overcast" title="Overcast" />			<span class="themeName">Overcast</span>				</a></li>						<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/le-frog/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_le_frog.png" alt="Le Frog" title="Le Frog" />			<span class="themeName">Le Frog</span>		</a></li>								<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/flick/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_flick.png" alt="Flick" title="Flick" />			<span class="themeName">Flick</span>				</a></li>				<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/pepper-grinder/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_pepper_grinder.png" alt="Pepper Grinder" title="Pepper Grinder" />			<span class="themeName">Pepper Grinder</span>				</a></li>								<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/eggplant/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_eggplant.png" alt="Eggplant" title="Eggplant" />			<span class="themeName">Eggplant</span>				</a></li>								<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/dark-hive/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_dark_hive.png" alt="Dark Hive" title="Dark Hive" />			<span class="themeName">Dark Hive</span>		</a></li>										<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/cupertino/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_cupertino.png" alt="Cupertino" title="Cupertino" />			<span class="themeName">Cupertino</span>				</a></li>				<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/south-street/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_south_street.png" alt="South St" title="South St" />			<span class="themeName">South Street</span>				</a></li>		<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/blitzer/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_blitzer.png" alt="Blitzer" title="Blitzer" />			<span class="themeName">Blitzer</span>		</a></li>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/humanity/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_humanity.png" alt="Humanity" title="Humanity" />			<span class="themeName">Humanity</span>		</a></li>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/hot-sneaks/jquery-ui.css">		<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_hot_sneaks.png" alt="Hot Sneaks" title="Hot Sneaks" />			<span class="themeName">Hot sneaks</span>		</a></li>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/excite-bike/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_excite_bike.png" alt="Excite Bike" title="Excite Bike" />			<span class="themeName">Excite Bike</span>			</a></li>		<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/vader/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_black_matte.png" alt="Vader" title="Vader" />			<span class="themeName">Vader</span>			</a></li>				<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/dot-luv/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_dot_luv.png" alt="Dot Luv" title="Dot Luv" />			<span class="themeName">Dot Luv</span>			</a></li>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/mint-choc/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_mint_choco.png" alt="Mint Choc" title="Mint Choc" />			<span class="themeName">Mint Choc</span>		</a></li>		<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/black-tie/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_black_tie.png" alt="Black Tie" title="Black Tie" />			<span class="themeName">Black Tie</span>		</a></li>		<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/trontastic/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_trontastic.png" alt="Trontastic" title="Trontastic" />			<span class="themeName">Trontastic</span>			</a></li>			<li><a href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/swanky-purse/jquery-ui.css">			<img src="http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_swanky_purse.png" alt="Swanky Purse" title="Swanky Purse" />			<span class="themeName">Swanky Purse</span>			</a></li>	</ul></div></div>').find('div').removeAttr('id');
	
	//button events
	button.click(
		function(){
			if(switcherpane.is(':visible')){ switcherpane.spHide(); }
			else{ switcherpane.spShow(); }
					return false;
		}
	);
	
	//menu events (mouseout didn't work...)
	switcherpane.hover(
		function(){},
		function(){if(switcherpane.is(':visible')){jQuery(this).spHide();}}
	);

	//show/hide panel functions
	jQuery.fn.spShow = function(){ jQuery(this).css({top: button.offset().top + options.buttonHeight + 6, left: button.offset().left}).slideDown(50); button.css(button_active); options.onOpen(); }
	jQuery.fn.spHide = function(){ jQuery(this).slideUp(50, function(){options.onClose();}); button.css(button_default); }
	
		
	/* Theme Loading
	---------------------------------------------------------------------*/
	switcherpane.find('a').click(function(){
		var css = jQuery(this).attr('href');
		updateCSS( css );
		var themeName = jQuery(this).find('span').text();
		button.find('.jquery-ui-themeswitcher-title').text( options.buttonPreText + themeName );
		jQuery('#frm_themepicker_input').html('<input type="hidden" value="'+css+'" id="frm_themepicker_css" name="frm_themepicker_css"><input type="hidden" value="'+themeName+'" id="frm_themepicker_name" name="frm_themepicker_name">');
		options.onSelect();
		if(options.closeOnSelect && switcherpane.is(':visible')){ switcherpane.spHide(); }
		return false;
	});
	
	//function to append a new theme stylesheet with the new style changes
	function updateCSS(locStr){
		var cssLink = jQuery('<link href="'+locStr+'" type="text/css" rel="Stylesheet" class="ui-theme" />');
		jQuery("head").append(cssLink);
		
		if( jQuery("link.ui-theme").size() > 3){
			jQuery("link.ui-theme:first").remove();
		}	
	}	
	
	/* Inline CSS 
	---------------------------------------------------------------------*/
	var button_default = {
		fontFamily: 'Trebuchet MS, Verdana, sans-serif',
		fontSize: '11px',
		color: '#fff',
		background: '#333',
		border: '1px solid #666',
		'-moz-border-radius': '4px',
		'-webkit-border-radius': '4px',
		textDecoration: 'none',
		padding: '3px 3px 3px 8px',
		width: options.width - 11,//minus must match left and right padding 
		display: 'block',
		height: options.buttonHeight,
		outline: '0'
	};
	var button_hover = {
		cursor: 'pointer'
	};
	var button_active = {
		borderBottom: 0,
		'-moz-border-radius-bottomleft': 0,
		'-webkit-border-bottom-left-radius': 0,
		'-moz-border-radius-bottomright': 0,
		'-webkit-border-bottom-right-radius': 0,
		outline: '0'
	};
	
	
	
	//button css
	button.css(button_default)
	.hover(
		function(){jQuery(this).css(button_hover);},
		function(){ 
		 if( !switcherpane.is(':animated') && switcherpane.is(':hidden') ){	jQuery(this).css(button_default);  }
		}	
	)
	.find('.jquery-ui-themeswitcher-icon').css({
		float: 'right',
		width: '16px',
		height: '16px',
		background: 'url(http://jqueryui.com/themeroller/themeswitchertool/images/icon_color_arrow.gif) 50% 50% no-repeat'
	});	
	//pane css
	switcherpane.css({
		position: 'absolute',
		float: 'left',
		fontFamily: 'Trebuchet MS, Verdana, sans-serif',
		fontSize: '12px',
		background: '#333',
		color: '#fff',
		padding: '8px 3px 3px',
		border: '1px solid #ccc',
		'-moz-border-radius-bottomleft': '6px',
		'-webkit-border-bottom-left-radius': '6px',
		'-moz-border-radius-bottomright': '6px',
		'-webkit-border-bottom-right-radius': '6px',
		borderTop: 0,
		zIndex: 999999,
		width: options.width-6//minus must match left and right padding
	})
	.find('ul').css({
		listStyle: 'none',
		margin: '0',
		padding: '0',
		overflow: 'auto',
		height: options.height
	}).end()
	.find('li').hover(
		function(){ 
			jQuery(this).css({
				'borderColor':'#555',
				'background': 'url(http://jqueryui.com/themeroller/themeswitchertool/images/menuhoverbg.png) 50% 50% repeat-x',
				cursor: 'pointer'
			}); 
		},
		function(){ 
			jQuery(this).css({
				'borderColor':'#333',
				'background': '#333',
				cursor: 'auto'
			}); 
		}
	).css({
		width: options.width-30,
		height: '',
		padding: '2px',
		margin: '1px',
		border: '1px solid #333',
		'-moz-border-radius': '4px',
		clear: 'left',
		float: 'left'
	}).end()
	.find('a').css({
		color: '#aaa',
		textDecoration: 'none',
		float: 'left',
		width: '100%',
		outline: '0'
	}).end()
	.find('img').css({
		float: 'left',
		border: '1px solid #333',
		margin: '0 2px'
	}).end()
	.find('.themeName').css({
		float: 'left',
		margin: '3px 0'
	}).end();

	jQuery(this).append(button);
	jQuery('body').append(switcherpane);
	switcherpane.hide();
	var themeName = document.getElementById("frm_themepicker_name").value;
	switcherpane.find('a:contains('+ themeName +')').trigger('click');

	return this;
};

jQuery.fn.spinDown = function() {
	return this.click(function() {
		var $this = jQuery(this);
		$this.next().slideToggle(100);
		$this.prev().toggleClass('not-active');
		$this.find('.icon').toggleClass('icon-triangle-1-s').end().toggleClass('state-active');
		$this.find('.ui-icon').toggleClass('ui-icon-triangle-1-s').end().toggleClass('ui-state-active');
		if($this.is('.corner-all')) { $this.removeClass('corner-all').addClass('corner-top'); }
		else if($this.is('.corner-top')) { $this.removeClass('corner-top').addClass('corner-all'); }
		if($this.is('.ui-corner-all')) { $this.removeClass('ui-corner-all').addClass('ui-corner-top'); }
		else if($this.is('.ui-corner-top')) { $this.removeClass('ui-corner-top').addClass('ui-corner-all'); }
		return false;
	});
};

// validation for hex inputs
jQuery.fn.validHex = function() {
	return this.each(function() {
		var value = jQuery(this).val();
		value = value.replace(/[^#a-fA-F0-9]/g, ''); // non [#a-f0-9]
		if(value.match(/#/g) && value.match(/#/g).length > 1) value = value.replace(/#/g, ''); // ##
		if(value.indexOf('#') == -1) value = '#'+value; // no #
		if(value.length > 7) value = value.substr(0,7); // too many chars
		jQuery(this).val(value);	
	});	
};

//color pickers setup (sets bg color of inputs)
jQuery.fn.applyFarbtastic = function() {
	return this.each(function() {
		jQuery('<div/>').farbtastic(this).remove();
	});
};


//function called after a change event in the form
function formChange(){
	var locStr = jQuery('form[name="frm_settings_form"]').serialize();
	locStr = hash.clean(locStr);
	updateCSS(locStr);
	hash.updateHash(locStr, true);
};

jQuery(document).ready(function($){
    // hover class toggles in app panel
    jQuery('.state-default').hover(
    	function(){ jQuery(this).addClass('state-hover'); }, 
    	function(){ jQuery(this).removeClass('state-hover'); }
    );

	$("#datepicker_sample").datepicker();
    
    $('div.theme-group .theme-group-header').addClass('corner-all').spinDown();
	$('#frm_form_editor_container .ui-accordion-header').addClass('ui-corner-all').spinDown();
    
    // focus and blur classes in form
	$('input, select').focus(function(){
		$('input.focus, select.focus').removeClass('focus');
		$(this).addClass('focus');
	}).blur(function(){ $(this).removeClass('focus');});
	
	// change event in form
	$('form[name="frm_settings_form"]').bind('change', function() {
		formChange();
		return false;
	});
	
	// hex inputs
	$('input.hex').validHex().keyup(function() {$(this).validHex();})
		.click(function(){
			$(this).addClass('focus');
			$('#picker').remove();
			$('div.picker-on').removeClass('picker-on');
			$('div.texturePicker ul:visible').hide(0).parent().css('position', 'static');
			$(this).after('<div id="picker"></div>').parent().addClass('picker-on');
			$('#picker').farbtastic(this);
			return false;
		})
		.wrap('<div class="hasPicker"></div>')
		.applyFarbtastic();
	
	$('body').click(function() {
		$('div.picker-on').removeClass('picker-on');
		$('#picker').remove();
		$('input.focus, select.focus').removeClass('focus');
		$('div.texturePicker ul:visible').hide().parent().css('position', 'static');
	});
	
	// texture pickers from select menus
		$('select.texture').each(function() {

			$(this).after('<div class="texturePicker"><a href="#"></a><ul></ul></div>');
			var texturePicker = $(this).next();
			var a = texturePicker.find('a');
			var ul = texturePicker.find('ul');
			var sIndex = texturePicker.prev().get(0).selectedIndex;

			// scrape options
			$(this).find('option').each(function(){
				ul.append('<li class="'+ $(this).attr('value') +'" data-texturewidth="16" data-textureheight="16" style="background: #555555 url('+$(this).attr('value')+') 50% 50% no-repeat"><a href="#" title="'+ $(this).text() +'">'+ $(this).text() +'</a></li>');
				if($(this).get(0).index == sIndex){texturePicker.attr('title',$(this).text()).css('background', '#555555 url('+$(this).attr('value')+') 50% 50% no-repeat');}
			});

			ul.find('li').click(function() {
				texturePicker.prev().get(0).selectedIndex = texturePicker.prev().find('option[value='+ $(this).attr('class') +']').get(0).index;
				texturePicker.attr('title',$(this).text()).css('background', '#555555 url('+$(this).attr('class')+')  50% 50% no-repeat');
				//ul.fadeOut(100);
				formChange();
				return false;
			});

			// hide the menu and select el
			ul.hide();

			// show/hide of menus
			texturePicker.click(function() {
				$(this).addClass('focus');
				$('#picker').remove();
				var showIt;
				if(ul.is(':hidden')){showIt = true;}
				$('div.texturePicker ul:visible').hide().parent().css('position', 'static');
				if(showIt == true){
					texturePicker.css('position', 'relative');
					ul.show();
				}
				return false;
			});
		});
});



// $Id: farbtastic.js,v 1.2 2007/01/08 22:53:01 unconed Exp $
// Farbtastic 1.2

jQuery.fn.farbtastic = function (callback) {
jQuery.farbtastic(this, callback);
return this;
};

jQuery.farbtastic = function (container, callback) {
var container = jQuery(container).get(0);
return container.farbtastic || (container.farbtastic = new jQuery._farbtastic(container, callback));
};

jQuery._farbtastic = function (container, callback) {
// Store farbtastic object
var fb = this;

// Insert markup
jQuery(container).html('<div class="farbtastic"><div class="color"></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker"></div><div class="sl-marker marker"></div></div>');
var e = jQuery('.farbtastic', container);
fb.wheel = jQuery('.wheel', container).get(0);
// Dimensions
fb.radius = 84;
fb.square = 100;
fb.width = 194;

// Fix background PNGs in IE6
if (navigator.appVersion.match(/MSIE [0-6]\./)) {
jQuery('*', e).each(function () {
if (this.currentStyle.backgroundImage != 'none') {
var image = this.currentStyle.backgroundImage;
image = this.currentStyle.backgroundImage.substring(5, image.length - 2);
jQuery(this).css({
'backgroundImage': 'none',
'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=crop, src='" + image + "')"
});
}
});
}

/**
* Link to the given element(s) or callback.
*/
fb.linkTo = function (callback) {
// Unbind previous nodes
if (typeof fb.callback == 'object'){jQuery(fb.callback).unbind('keyup', fb.updateValue);}

// Reset color
fb.color = null;

// Bind callback or elements
if (typeof callback == 'function'){fb.callback = callback;}
else if (typeof callback == 'object' || typeof callback == 'string') {
fb.callback = jQuery(callback);
fb.callback.bind('keyup', fb.updateValue);
if (fb.callback.get(0).value){fb.setColor(fb.callback.get(0).value);}
}
return this;
};
fb.updateValue = function (event) {
if (this.value && this.value != fb.color){fb.setColor(this.value);}
};

/**
* Change color with HTML syntax #123456
*/
fb.setColor = function (color) {
var unpack = fb.unpack(color);
if (fb.color != color && unpack) {
fb.color = color;
fb.rgb = unpack;
fb.hsl = fb.RGBToHSL(fb.rgb);
fb.updateDisplay();
}
return this;
};

/**
* Change color with HSL triplet [0..1, 0..1, 0..1]
*/
fb.setHSL = function (hsl) {
fb.hsl = hsl;
fb.rgb = fb.HSLToRGB(hsl);
fb.color = fb.pack(fb.rgb);
fb.updateDisplay();
return this;
};

/////////////////////////////////////////////////////

/**
* Retrieve the coordinates of the given event relative to the center
* of the widget.
*/
fb.widgetCoords = function (event) {
var x, y;
var el = event.target || event.srcElement;
var reference = fb.wheel;

if (typeof event.offsetX != 'undefined') {
// Use offset coordinates and find common offsetParent
var pos = { x: event.offsetX, y: event.offsetY };

// Send the coordinates upwards through the offsetParent chain.
var e = el;
while (e) {
e.mouseX = pos.x;
e.mouseY = pos.y;
pos.x += e.offsetLeft;
pos.y += e.offsetTop;
e = e.offsetParent;
};

// Look for the coordinates starting from the wheel widget.
var e = reference;
var offset = { x: 0, y: 0 };
while (e) {
if (typeof e.mouseX != 'undefined') {
x = e.mouseX - offset.x;
y = e.mouseY - offset.y;
break;
}
offset.x += e.offsetLeft;
offset.y += e.offsetTop;
e = e.offsetParent;
}

// Reset stored coordinates
e = el;
while (e) {
e.mouseX = undefined;
e.mouseY = undefined;
e = e.offsetParent;
}
}
else {
// Use absolute coordinates
var pos = fb.absolutePosition(reference);
x = (event.pageX || 0*(event.clientX + jQuery('html').get(0).scrollLeft)) - pos.x;
y = (event.pageY || 0*(event.clientY + jQuery('html').get(0).scrollTop)) - pos.y;
}
// Subtract distance to middle
return { x: x - fb.width / 2, y: y - fb.width / 2 };
};

/**
* Mousedown handler
*/
fb.mousedown = function (event) {
// Capture mouse
if (!document.dragging) {
jQuery(document).bind('mousemove', fb.mousemove).bind('mouseup', fb.mouseup);
document.dragging = true;
};

// Check which area is being dragged
var pos = fb.widgetCoords(event);
fb.circleDrag = Math.max(Math.abs(pos.x), Math.abs(pos.y)) * 2 > fb.square;

// Process
fb.mousemove(event);
return false;
};

/**
* Mousemove handler
*/
fb.mousemove = function (event) {
// Get coordinates relative to color picker center
var pos = fb.widgetCoords(event);

// Set new HSL parameters
if (fb.circleDrag) {
var hue = Math.atan2(pos.x, -pos.y) / 6.28;
if (hue < 0) hue += 1;
fb.setHSL([hue, fb.hsl[1], fb.hsl[2]]);
}else{
var sat = Math.max(0, Math.min(1, -(pos.x / fb.square) + .5));
var lum = Math.max(0, Math.min(1, -(pos.y / fb.square) + .5));
fb.setHSL([fb.hsl[0], sat, lum]);
}
return false;
};

/**
* Mouseup handler
*/
fb.mouseup = function () {
// Uncapture mouse
jQuery(document).unbind('mousemove', fb.mousemove);
jQuery(document).unbind('mouseup', fb.mouseup);
document.dragging = false;
formChange();
};

/**
* Update the markers and styles
*/
fb.updateDisplay = function () {
// Markers
var angle = fb.hsl[0] * 6.28;
jQuery('.h-marker', e).css({
left: Math.round(Math.sin(angle) * fb.radius + fb.width / 2) + 'px',
top: Math.round(-Math.cos(angle) * fb.radius + fb.width / 2) + 'px'
});

jQuery('.sl-marker', e).css({
left: Math.round(fb.square * (.5 - fb.hsl[1]) + fb.width / 2) + 'px',
top: Math.round(fb.square * (.5 - fb.hsl[2]) + fb.width / 2) + 'px'
});

// Saturation/Luminance gradient
jQuery('.color', e).css('backgroundColor', fb.pack(fb.HSLToRGB([fb.hsl[0], 1, 0.5])));

// Linked elements or callback
if (typeof fb.callback == 'object') {
// Set background/foreground color
jQuery(fb.callback).css({
backgroundColor: fb.color,
color: fb.hsl[2] > 0.5 ? '#000' : '#fff'
});

// Change linked value
jQuery(fb.callback).each(function() {
if (this.value && this.value != fb.color){this.value = fb.color;}
});
}else if (typeof fb.callback == 'function'){fb.callback.call(fb, fb.color);}
};

/**
* Get absolute position of element
*/
fb.absolutePosition = function (el) {
var r = { x: el.offsetLeft, y: el.offsetTop };
// Resolve relative to offsetParent
if (el.offsetParent) {
var tmp = fb.absolutePosition(el.offsetParent);
r.x += tmp.x;
r.y += tmp.y;
}
return r;
};

/* Various color utility functions */
fb.pack = function (rgb) {
var r = Math.round(rgb[0] * 255);
var g = Math.round(rgb[1] * 255);
var b = Math.round(rgb[2] * 255);
return '#' + (r < 16 ? '0' : '') + r.toString(16) +
(g < 16 ? '0' : '') + g.toString(16) +
(b < 16 ? '0' : '') + b.toString(16);
};

fb.unpack = function (color) {
if (color.length == 7) {
return [parseInt('0x' + color.substring(1, 3)) / 255,
parseInt('0x' + color.substring(3, 5)) / 255,
parseInt('0x' + color.substring(5, 7)) / 255];
}
else if (color.length == 4) {
return [parseInt('0x' + color.substring(1, 2)) / 15,
parseInt('0x' + color.substring(2, 3)) / 15,
parseInt('0x' + color.substring(3, 4)) / 15];
}
};

fb.HSLToRGB = function (hsl) {
var m1, m2, r, g, b;
var h = hsl[0], s = hsl[1], l = hsl[2];
m2 = (l <= 0.5) ? l * (s + 1) : l + s - l*s;
m1 = l * 2 - m2;
return [this.hueToRGB(m1, m2, h+0.33333),
this.hueToRGB(m1, m2, h),
this.hueToRGB(m1, m2, h-0.33333)];
};

fb.hueToRGB = function (m1, m2, h) {
h = (h < 0) ? h + 1 : ((h > 1) ? h - 1 : h);
if (h * 6 < 1) return m1 + (m2 - m1) * h * 6;
if (h * 2 < 1) return m2;
if (h * 3 < 2) return m1 + (m2 - m1) * (0.66666 - h) * 6;
return m1;
};

fb.RGBToHSL = function (rgb) {
var min, max, delta, h, s, l;
var r = rgb[0], g = rgb[1], b = rgb[2];
min = Math.min(r, Math.min(g, b));
max = Math.max(r, Math.max(g, b));
delta = max - min;
l = (min + max) / 2;
s = 0;
if (l > 0 && l < 1) {
s = delta / (l < 0.5 ? (2 * l) : (2 - 2 * l));
}
h = 0;
if (delta > 0) {
if (max == r && max != g) h += (g - b) / delta;
if (max == g && max != b) h += (2 + (b - r) / delta);
if (max == b && max != r) h += (4 + (r - g) / delta);
h /= 6;
}
return [h, s, l];
};

// Install mousedown handler (the others are set on the document on-demand)
jQuery('*', e).mousedown(fb.mousedown);

// Init color
fb.setColor('#000000');

// Set linked elements/callback
if (callback) {
fb.linkTo(callback);
}
};
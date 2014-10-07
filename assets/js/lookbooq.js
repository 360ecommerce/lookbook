jQuery.noConflict();
jQuery(document).ready(function($){

	var lookbooq = $('.js-lookbooq'),
		piqture = $('.js-piqture');

	// Slider
	lookbooq.find('.js-lookbooq-slider').bxSlider({
		nextText: '',
		prevText: ''
	});

	// Fancybox
	$('.js-lookbooq-fancybox:not(.bx-clone .js-lookbooq-fancybox)').fancybox();

	// Open tip/bullet
	piqture.on('click', '.pointer-bullet', function(event){
		var that 		= $(this),
			container 	= $(this).closest('.sqreen')
			pointer 	= that.closest('.pointer'),
			tip 		= pointer.find('.tip'),
			arrow 		= pointer.find('.tip-arrow');

		var container = {
				object: container,
				width: container.width(),
				height: container.height()
			};
		
		var pointer = {
				object: pointer,
				position: pointer.position(),
			};
		
		var tip = {
				object: tip,
				height: tip.height(),
				width: tip.width(),
				arrow: arrow
			};

		that.toggleClass('pointer-active');

		if( that.hasClass('pointer-active') ) {
			if( tip.width > (container.width - pointer.position.left) ) {
				var fix = (tip.width - (container.width - pointer.position.left) + 15);
				tip.object.css({ left: -(fix) });
				tip.arrow.css({ marginLeft: fix + 10 });
			} else {
				tip.object.css({ left: 0 });
			}
		} else {
			tip.object.css({ left: -9999 });
		}

		event.preventDefault();
	});

});
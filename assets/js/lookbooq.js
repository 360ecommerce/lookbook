jQuery.noConflict();
jQuery(document).ready(function($){

	var lookbooq = $('.js-lookbooq'),
		piqture = $('.js-piqture');

	// Slider
	lookbooq.find('.js-lookbooq-slider').bxSlider();

	// Open tip/bullet
	piqture.on('click', '.pointer-bullet', function(event){
		var that = $(this),
			pointer = that.closest('.pointer'),
			tip = pointer.find('.tip');

		tip.toggle();

		event.preventDefault();
	});

});
jQuery.noConflict();
jQuery(document).ready(function($){

	var lookbooq = $('.js-lookbooq');

	// 
	lookbooq.on('click', '.pointer-bullet', function(event){
		var that = $(this),
			pointer = that.closest('.pointer'),
			tip = pointer.find('.tip');

		tip.toggle();

		event.preventDefault();
	});

});
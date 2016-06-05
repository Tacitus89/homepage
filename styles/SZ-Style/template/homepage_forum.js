(function ($) {
	$(document).ready(function() {
		$('.fancybox-thumbs').fancybox({
			type	   : 'image',
			prevEffect : 'none',
			nextEffect : 'none',

			closeBtn  : true,
			arrows    : true,
			nextClick : true,

			helpers : {
				thumbs : {
					width  : 50,
					height : 50
				}
			}
		});
    });
}(jQuery));
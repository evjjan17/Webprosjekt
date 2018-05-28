jQuery(document).ready(function($) { 
	
	jQuery("#academia-gallery").flexslider({
        selector: ".academia-slides > .academia-gallery-slide",
	animationLoop: true,
        initDelay: 1000,
	smoothHeight: false,
	slideshow: true,
	slideshowSpeed: 5000,
	pauseOnAction: true,
        controlNav: false,
	directionNav: true,
	useCSS: true,
	touch: false,
        animationSpeed: 500
	});

});
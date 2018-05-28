jQuery(document).ready(function($) { 
	
	$('#menu-main-menu').superfish({ 
		delay:       1000,								// 0.1 second delay on mouseout 
		animation:   {opacity:'show',height:'show'},	// fade-in and slide-down animation
		animationOut: {opacity:'hide'},
		speed: 'fast',
		speedOut: 'fast',
		disableHI: true,
		useClick: false
	});

});
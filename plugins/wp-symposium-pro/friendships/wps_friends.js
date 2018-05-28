jQuery(document).ready(function() {

	// ***** Favourite Friendships (UI) *****	
    
    jQuery('body').on('click', '.wps_add_remove_favourite', function(event) {
        
        var action = jQuery(this).attr('rel');
        
        jQuery(this).remove();
        if (action == 'add') {
            jQuery('#wps_favourite_no_msg').show();
            jQuery.post(
                wps_friendships_ajax.ajaxurl,
                {
                    action : 'wps_add_favourite',
                    user_id: jQuery(this).data('user_id')
                },
                function(response) {
                }   
            );
        } else {
            jQuery('#wps_favourite_yes_msg').show();
            jQuery.post(
                wps_friendships_ajax.ajaxurl,
                {
                    action : 'wps_remove_favourite',
                    user_id: jQuery(this).data('user_id')
                },
                function(response) {
                }   
            );            
        }
        
    });
    

    jQuery('body').on('click', '.wps_add_favourite', function(event) {

		jQuery(this).attr('src', wps_friendships_ajax.fav_on);
		jQuery(this).removeClass('wps_add_favourite');
		jQuery(this).addClass('wps_remove_favourite');

		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_add_favourite',
		        user_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    }   
		);

	});

    jQuery('body').on('click', '.wps_remove_favourite', function(event) {

		jQuery(this).attr('src', wps_friendships_ajax.fav_off);
		jQuery(this).removeClass('wps_remove_favourite');
		jQuery(this).addClass('wps_add_favourite');

		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_remove_favourite',
		        user_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    }   
		);

	});


	// ***** Favourite Friendships (admin) *****	

	if (jQuery("#wps_favourite_member1").length) {

		if (jQuery("#wps_favourite_member1").val() == '') {
			jQuery("#wps_favourite_member1").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_friendships_ajax.ajaxurl,
					    {
					        action : 'wps_get_users',
					        term : query.term
					    },
					    function(response) {
					    	var json = jQuery.parseJSON(response);
					    	var data = {results: []}, i, j, s;
							for(var i = 0; i < json.length; i++) {
						    	var obj = json[i];
						    	data.results.push({id: obj.value, text: obj.label});
							}
							query.callback(data);	    	
					    }   
					);
			    }
			});
		}

		if (jQuery("#wps_favourite_member2").val() == '') {
			jQuery("#wps_favourite_member2").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_friendships_ajax.ajaxurl,
					    {
					        action : 'wps_get_users',
					        term : query.term
					    },
					    function(response) {
					    	var json = jQuery.parseJSON(response);
					    	var data = {results: []}, i, j, s;
							for(var i = 0; i < json.length; i++) {
						    	var obj = json[i];
						    	data.results.push({id: obj.value, text: obj.label});
							}
							query.callback(data);	    	
					    }   
					);
			    }
			});
		}

	}

/* -------------------------------------------------------------------- */
	// ***** Friendships (admin) *****	

	if (jQuery("#wps_member1").length) {

		if (jQuery("#wps_member1").val() == '') {
			jQuery("#wps_member1").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_friendships_ajax.ajaxurl,
					    {
					        action : 'wps_get_users',
					        term : query.term
					    },
					    function(response) {
					    	var json = jQuery.parseJSON(response);
					    	var data = {results: []}, i, j, s;
							for(var i = 0; i < json.length; i++) {
						    	var obj = json[i];
						    	data.results.push({id: obj.value, text: obj.label});
							}
							query.callback(data);	    	
					    }   
					);
			    }
			});
		}

		if (jQuery("#wps_member2").val() == '') {
			jQuery("#wps_member2").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_friendships_ajax.ajaxurl,
					    {
					        action : 'wps_get_users',
					        term : query.term
					    },
					    function(response) {
					    	var json = jQuery.parseJSON(response);
					    	var data = {results: []}, i, j, s;
							for(var i = 0; i < json.length; i++) {
						    	var obj = json[i];
						    	data.results.push({id: obj.value, text: obj.label});
							}
							query.callback(data);	    	
					    }   
					);
			    }
			});
		}

	}

	// ***** Friendships (user interface) *****	

	// Make (add) friendship request
	jQuery(".wps_friends_add").click(function (event) {

		jQuery("body").addClass("wps_wait_loading");		    			    	
		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_add',
		        user_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

	// Accept friendship request
	jQuery(".wps_pending_friends_accept").click(function (event) {

		jQuery("body").addClass("wps_wait_loading");		    			    	
		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_accept',
		        post_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

	// Reject friendship request
	jQuery(".wps_pending_friends_reject").click(function (event) {

		jQuery("body").addClass("wps_wait_loading");		    			    	
		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_reject',
		        post_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

	// Cancel friendship
	jQuery(".wps_friends_cancel").click(function (event) {

		jQuery("body").addClass("wps_wait_loading");		    	                	
		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_reject',
		        post_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

    // Remove all friends
	jQuery("#wps_remove_all_friends").click(function (event) {

        var answer = confirm(jQuery(this).data('sure'));
        if (answer) {

			jQuery("body").addClass("wps_wait_loading");		    	                	
            jQuery.post(
                wps_friendships_ajax.ajaxurl,
                {
                    action : 'wps_remove_all_friends'
                },
                function(response) {
                    location.reload();
                }   
            );

        }

	});
    

})

jQuery(function($) {
	
	// Settings tabs handler
	$('.cmmrm-settings-tabs a').click(function() {
		var match = this.href.match(/\#tab\-([^\#]+)$/);
		$('#settings .settings-category.current').removeClass('current');
		$('#settings .settings-category-'+ match[1]).addClass('current');
		$('.cmmrm-settings-tabs a.current').removeClass('current');
		$('.cmmrm-settings-tabs a[href="#tab-'+ match[1] +'"]').addClass('current');
		this.blur();
	});
	if (location.hash.length > 0) {
		$('.cmmrm-settings-tabs a[href="'+ location.hash +'"]').click();
	} else {
		$('.cmmrm-settings-tabs li:first-child a').click();
	}
	
	
	// Access custom cap handler
	var settingsAccessCustomCapListener = function() {
		var obj = $(this);
		var nextField = obj.parents('tr').first().next();
		if ('cmmrm_capability' == obj.val()) {
			nextField.show();
		} else {
			nextField.hide();
		}
	};
	$('select[name^=cmmrm_access_map_]').change(settingsAccessCustomCapListener);
	$('select[name^=cmmrm_access_map_]').change();
	
	
	$('#cmmrm-import-route-form').submit(function() {
		var form = $(this);
		var btn = form.find('input[type=submit]');
//		btn.hide();
		$('#cmmrm-import-frame').show();
	});
	
	
	$('.cmmrm-admin-notice .cmmrm-dismiss').click(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		var btn = $(this);
		var data = {action: btn.data('action'), nonce: btn.data('nonce'), id: btn.data('id')};
		$.post(btn.attr('href'), data, function(response) {
			btn.parents('.cmmrm-admin-notice').fadeOut('slow');
		});
	});
	
	// Custom taxonomies
	var deleteTaxHandler = function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		$(this).parents('.cmmrm-custom-tax-item').first().remove();
	};
	$('.cmmrm-custom-tax-setting .cmmmrm-custom-tax-delete a').click(deleteTaxHandler);
	$('.cmmrm-custom-tax-add-btn').click(function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
		var btn = $(this);
		var wrapper = btn.parents('.cmmrm-custom-tax-setting');
		var template = wrapper.data('template');
		btn.before(template);
		var item = wrapper.find('.cmmrm-custom-tax-item').last();
		item.find('.cmmrm-custom-tax-taxonomy').val('');
		item.find('.cmmrm-custom-tax-name-singular').val('');
		item.find('.cmmrm-custom-tax-name-plural').val('');
		item.find('.cmmmrm-custom-tax-delete a').click(deleteTaxHandler);
	});
	
	
	
	
});
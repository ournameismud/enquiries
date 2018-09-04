/**
 * Enquiries plugin for Craft CMS
 *
 * Notifications Field JS
 *
 * @author    @cole007
 * @copyright Copyright (c) 2018 @cole007
 * @link      http://ournameismud.co.uk/
 * @package   Enquiries
 * @since     1.0.0
 */

$(document).ready(function() {
	// alert('woot');
	var context = $('#recipients-field');
	$(context).find('.instructions p').append('<span class="instructions-fields"></span>');
	var insFields = $('.instructions-fields');
	$(insFields).html( emailFieldsCurrent );
	// var insFields = $(context).find('.instructions p').after('<ul class="instructions-fields"></ul>');
	$('#form').on('change',function() {
		var id = $(this).val();
		var fields = emailFields['form_' + id];
		if (fields) $(insFields).html( fields );
		else $(insFields).empty();
		// alert(id);
	})
	
});
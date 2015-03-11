// JavaScript Document

jQuery(document).ready(function($) {
    $('.pickDate').datetimepicker({
        dateFormat : 'dd-mm-yy',
		timeFormat : 'hh:mm tt'
    });
	
	$(function() {
		
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		$( ".lp_post_ids" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).autocomplete( "instance" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 3,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					$.getJSON(se_ajax_url + '?action=lp_lookup', {
            				q: extractLast( request.term ),
							n: this.value
          				}, response );
				},
				search: function() {
          			// custom minLength
          			var term = extractLast( this.value );
          			if ( term.length < 3 ) {
            			return false;
          			}
        		},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				},
				open: function() {
       				 $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      			},
      			close: function() {
        			 $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      			}
				
			});
		});
	
});

jQuery(document).ready(function() {
    jQuery('.lp-tabs .tab-links a').live('click', function(e) {
        var currentAttrValue = jQuery(this).attr('href');
        e.preventDefault();
 
         // Show/Hide Tab Content
		jQuery('.tabcontent').hide(300);
        jQuery(currentAttrValue).show(300);
 
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
    });
});

jQuery(document).ready(function() {
    jQuery('.lp-options .main_btn a').live('click', function(e) {
        var currentAttrValue = jQuery(this).attr('href');
		e.preventDefault();
         // Show/Hide Tab Content
		jQuery('.tabcontent').hide(300);
		jQuery(currentAttrValue).show(300);        
    });
});

jQuery(document).ready(function() {
    jQuery('.home_links .main_btn a').live('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
        e.preventDefault();
         // Show/Hide Tab Content
		jQuery('.tabcontent').hide(300);
        jQuery(currentAttrValue).show(300);
 
        // Change/remove current tab to active
        jQuery('.tab-links li').addClass('active').siblings().removeClass('active');
 
    });
});

jQuery( document ).ready( function() {

	jQuery( '.lp-duplicate-post' ).click( function( e ) {
		e.preventDefault();
	
		var data = {
			action: 'lp_duplicate_post',
			original_id: jQuery(this).attr('href'),
			security: jQuery(this).attr('rel')
		};
	
		jQuery.post( ajaxurl, data, function( response ) {
			location.reload();
		});
	});
});

jQuery(document).ready(function($) {
	$('.sub-control').on('click', function(e) {
			 // Show/Hide Tab Content
		$(this).parent().next('.sub-setting').toggle(300);
		
	});
});
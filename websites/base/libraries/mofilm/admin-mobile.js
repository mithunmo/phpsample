/**
 * Mofilm Admin Mobile JS Resource
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd
 * @package mofilm
 * @subpackage websites_baseAdminSite_libraries
 * @version $Rev: 11 $
 */
jQuery(document).ready(function(){
	
	var messageBox = function(status, message) {
		$('#body div.container').append('<div class="messageBox '+status+'"><p>'+message+'</p></div>');
		$('#body div.container div.messageBox').delay(2000).slideUp(200);
	};

	/*
	 * Make message boxes closeable
	 */
	if ( $('.messageBox').length > 0 ) {
		$('.messageBox.closeable').each(function() {
			$(this).append('<div class="click_to_close" style="cursor: pointer;"></div>');
		});
		$('.click_to_close').click(function() {
			$(this).parent().clearQueue();
			$(this).parent().slideUp(200);
		});
		$('div.messageBox').delay(5000).slideUp(200);
	}

	/*
	 * Add collapse/expand to dashboard
	 */
	if ( $('div.event.collapsed').length > 0 ) {
		$('div.event.collapsed').each(function(){
			$(this).find('.stats').hide();
			$(this).prepend('<div class="ui-icon ui-corner-all ui-state-default ui-icon-plusthick"></div>');
			$(this).click(function(){
				$(this).find('.eventIcon').toggle();
				$(this).find('.stats').toggle();
				if ( $(this).find('.ui-icon').hasClass('ui-icon-plusthick') ) {
					$(this).find('.ui-icon').removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick');
				} else {
					$(this).find('.ui-icon').removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
				}
			}).hover(
				function(){
					$(this).find('.ui-icon').addClass('ui-state-hover');
				},
				function() {
					$(this).find('.ui-icon').removeClass('ui-state-hover');
				}
			);
		});
	}
	
	/*
	 * Add event source stats
	 */
	if ( $('.sourceStats').length > 0 ) {
		$('.sourceStats').addClass('link');
		$('.sourceStats').click(function() {
			var oEle = $(this);

			if ( oEle.next('table.data').length < 1 ) {
				$.get(
					'/admin/eventadmin/eventManager/sourceStats',
					{
						EventID: $(this).attr('id').replace(/event_/, '')
					},
					function(data, textStatus, XMLHttpRequest) {
						oEle.next('.sourceStatsResults').replaceWith(data);
					},
					'html'
				);
			} else {
				oEle.next('table.data').toggle();
			}
			return false;
		});
	}
});
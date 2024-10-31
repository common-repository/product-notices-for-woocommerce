/**
 * Scripts loading on the edit screen of product notices.
 *
 * @package \Product Notices for WooCommerce
 * @author Rohit Dokhe
 * @since 1.3.0
 */

var editScreen = (function ($) {

	'use strict';

	/* Copy to clipboard */
	var clickToCopy = function () {

		var clipboard = new ClipboardJS( '.c2c' );

		clipboard.on(
			'success',
			function (e) {

				var x       = document.getElementById( "crwcpn_snackbar" );
				x.className = "show";
				setTimeout( function () { x.className = x.className.replace( "show", "" ); }, 3000 );
			}
		);

	},

	switchTabs = function() {
		$( '.crwcpn-tab-panel.tab-display-rules' ).addClass( 'active' );
		$( '#tab-display-rules' ).addClass( 'selected' );
		$( '.crwcpn-display-rules-tabs' ).click(
			function() {
				$( '.crwcpn-display-rules-tabs' ).removeClass( 'selected' );
				$( this ).addClass( 'selected' );

				var id = $( this ).attr( 'id' );

				$( '.crwcpn-tab-panel' ).removeClass( 'active' );

				if ( $( '.crwcpn-tab-panel' ).hasClass( id ) ) {
					$( '.crwcpn-tab-panel.' + id ).toggleClass( 'active' );
				}

			}
		)
	},

	toggleCall = function () {

		$( '.crwcpn-toggle-link' ).on(
			'click',
			function () {
				var post_id = $( this ).attr( 'id' );

				$.ajax(
					{
						type: 'POST',
						url: crwcpn_notice_status_update_Ajax.ajaxurl,
						data: {
							action: 'update_notice_status',
							post_id: post_id
						},
						beforeSend: function () {
							$( ".toggle-loader-" + post_id ).addClass( 'crwcpn-loader' );
						},
						complete: function () {
							$( ".toggle-loader-" + post_id ).removeClass( 'crwcpn-loader' );
						},
						success: function () {
							location.reload( true );
						}

					}
				);
			}
		);
	},

	ready = function () {

		// Run on document ready.
		clickToCopy();
		switchTabs();
		toggleCall();
	};

	return {
		ready: ready
	};

})( jQuery );
jQuery( editScreen.ready );

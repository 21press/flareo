/**
 * Flare list screen scripts.
 *
 * { jQuery, p21_flareo_flare_data }
 *
 * @package P21WL
 */

( function ( $, P21WL ) {
	$(
		function () {
			$( '.p21-flareo-flare_status input[type="checkbox"]' ).on(
				'change',
				function () {
					const flare_id   = $( this ).data( 'flare_id' );
					const is_active = $( this ).is( ':checked' ) ? 1 : 0;

					$.ajax(
						{
							url: P21WL.ajax_url,
							type: 'POST',
							data: {
								action: 'update_flare_status',
								flare_id,
								is_active: is_active,
								nonce: P21WL.nonce
							},
							success: function (response) {
								console.log( P21WL.translation_strings.status_updated, response );
							},
							error: function (error) {
								console.error( P21WL.translation_strings.status_error, error );
								console.log( P21WL.translation_strings.request_support );
							}
						}
					);
				}
			);
		}
	);
})( jQuery, window.p21_flareo_flare_data );
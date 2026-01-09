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
								action: 'flareo_update_flare_status',
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

			function initCopyHandler(options = {}) {
					const {
							selector = '.p21-flareo-copy-target',
							successClass = 'p21-flareo-show-success-icon',
							timeout = 500
					} = options;

					jQuery(document).on('click', selector, function (e) {

							const $button = jQuery(this);
							const prefix = $button.data('prefix') || '';
							const suffix = $button.data('suffix') || '';

							// Find the nearest input/textarea above the button
							const $target = $button
								.closest('*')
								.prevAll('input[type="text"]')
								.first();

							const value = $target.val();

							if (!value) {
								return;
							}

							navigator.clipboard.writeText(prefix + value + suffix);

							$button.addClass(successClass);
							setTimeout(() => {
								$button.removeClass(successClass);
							}, timeout);
					});
			}

			initCopyHandler();
		}
	);
})( jQuery, window.p21_flareo_flare_data );
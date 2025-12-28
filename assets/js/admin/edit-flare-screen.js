/**
 * Edit Flare screen scripts.
 *
 * @package P21WL
 */

( function ( $ ) {
	$(
		function () {
			// Initialize WP Color Picker.
			$( '.color-field' ).wpColorPicker();

			// Accordion behaviour for flare edit screen cards.
			// When a card header is clicked, toggle its body and close other open cards.
			$( document ).on( 'click', '#p21-flareo-flare-legendary-meta-box-ui .card .card-header', function ( e ) {
				e.preventDefault();
				var $header = $( this );
				var $card = $header.closest( '.card' );
				var $body = $card.children( '.card-body' );

				// If this card is already active, close it.
				if ( $card.hasClass( 'active' ) ) {
					$body.slideUp( 200 );
					$card.removeClass( 'active' );
					$header.attr( 'aria-expanded', 'false' );
					return;
				}

				// Close other cards.
				$card.siblings( '.card.active' ).each( function () {
					var $other = $( this );
					$other.removeClass( 'active' ).children( '.card-body' ).slideUp( 200 );
					$other.children( '.card-header' ).attr( 'aria-expanded', 'false' );
				} );

				// Open clicked card.
				$card.addClass( 'active' );
				$body.slideDown( 200 );
				$header.attr( 'aria-expanded', 'true' );
			} );

			// If no card is active on load, open the first one by default.
			var $cards = $( '#p21-flareo-flare-legendary-meta-box-ui .card' );
			if ( $cards.length && ! $cards.filter( '.active' ).length ) {
				var $first = $cards.first();
				$first.addClass( 'active' ).children( '.card-body' ).show();
				$first.children( '.card-header' ).attr( 'aria-expanded', 'true' );
			}

			// Collect controller field names referenced by conditional groups.
			const controllerNames = Array.from(
				new Set(
					$('.option-group[data-conditional-field]')
						.map(function () { return $(this).data('conditional-field'); })
						.get()
				)
			);

			function applyConditions() {
				$('.option-group[data-conditional-field]').each(function () {
					const $group = $(this);
					const fieldName = $group.data('conditional-field');

					const rawRequired = String($group.data('conditional-value') ?? '').trim();
					const requiredValues = rawRequired
						? rawRequired.split(/\s*[,|]\s*/).filter(Boolean)
						: [];

					const $controllers = $('[name="' + fieldName + '"]');
					let currentValue;

					if ($controllers.length) {
						if ($controllers.is(':radio') || $controllers.is(':checkbox')) {
							currentValue = $('[name="' + fieldName + '"]:checked').val();
						} else {
							currentValue = $controllers.val();
						}
					}

					const shouldShow = requiredValues.length
						? requiredValues.includes(String(currentValue))
						: false;

					// Show/hide only, no disabling.
					if (shouldShow) {
						$group.stop(true, true).slideDown(140);
					} else {
						$group.stop(true, true).slideUp(140);
					}
				});
			}

			// Listen on controller fields.
			controllerNames.forEach(function (name) {
				$(document).on('change input', '[name="' + name + '"]', applyConditions);
			});

			// Initial: hide all conditional groups to avoid flash, then evaluate.
			$('.option-group[data-conditional-field]').hide();
			applyConditions();

			// Tabs UI: initialize any .p21-flareo-tabs inside card bodies
			function initP21WLTabs( $root ) {
				$root = $root || $( document );
				$root.find( '.p21-flareo-tabs' ).each( function () {
					var $tabs = $( this );
					var $nav = $tabs.find( '.p21-flareo-tabs-nav' );
					var $panels = $tabs.find( '.p21-flareo-tabs-panel' );

					// Ensure first tab active if none.
					if ( ! $nav.find( '.p21-flareo-tab[aria-selected="true"]' ).length ) {
						$nav.find( '.p21-flareo-tab' ).first().attr( 'aria-selected', 'true' );
						$panels.hide().first().show();
					} else {
						$panels.hide();
						var sel = $nav.find( '.p21-flareo-tab[aria-selected="true"]' ).attr( 'aria-controls' );
						$tabs.find( '#' + sel ).show();
					}

					// Click handler
					$nav.on( 'click', '.p21-flareo-tab', function ( e ) {
						e.preventDefault();
						var $t = $( this );
						$nav.find( '.p21-flareo-tab' ).attr( 'aria-selected', 'false' );
						$t.attr( 'aria-selected', 'true' );
						$panels.hide();
						$tabs.find( '#' + $t.attr( 'aria-controls' ) ).show();
					} );

					// Keyboard navigation (left/right)
					$nav.on( 'keydown', '.p21-flareo-tab', function ( e ) {
						var $t = $( this );
						if ( e.key === 'ArrowRight' || e.key === 'Right' ) {
							var $next = $t.nextAll( '.p21-flareo-tab' ).first();
							if ( $next.length ) { $next.focus().click(); }
							return e.preventDefault();
						}
						if ( e.key === 'ArrowLeft' || e.key === 'Left' ) {
							var $prev = $t.prevAll( '.p21-flareo-tab' ).first();
							if ( $prev.length ) { $prev.focus().click(); }
							return e.preventDefault();
						}
						if ( e.key === 'Enter' || e.key === ' ' ) {
							$t.click();
							return e.preventDefault();
						}
					} );
				} );
			}

			initP21WLTabs( $( document ) );

			function initCopyHandler(options = {}) {
					const {
							selector = '.p21-flareo-copy-target',
							successClass = 'p21-flareo-show-success-icon',
							timeout = 500
					} = options;

					jQuery(document).on('click', selector, function (e) {

							const $button = jQuery(this);
							const target  = $button.data('target');
							const prefix  = $button.data('prefix') || '';
							const suffix  = $button.data('suffix') || '';
							const value   = target ? jQuery(target).val() : '';

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
})( jQuery );
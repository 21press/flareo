<?php
/**
 * Class Database_Upgrader.
 *
 * @package 21Press\Flareo
 */

namespace P21\Flareo\Core;

/**
 * Class Database_Upgrader
 */
class Database_Upgrader {

	/**
	 * The slug of database option.
	 *
	 * @var string
	 */
	const OPTION = 'p21_flareo_db_version';

	/**
	 * The slug of database option.
	 *
	 * @var string
	 */
	const PREVIOUS_OPTION = 'p21_flareo_previous_db_version';

	/**
	 * Hooked into admin_init and walks through an array of upgrade methods.
	 *
	 * @return void
	 */
	public function init() {
		$routines = array(
			'0.1.0' => 'upgrade_0_1_0',
		);

		$version = get_option( self::OPTION, '0.0.0' );

		if ( version_compare( P21_FLAREO_VERSION, $version, '=' ) ) {
			return;
		}

		array_walk( $routines, array( $this, 'run_upgrade_routine' ), $version );
		$this->finish_up( $version );
	}

	/**
	 * Runs the upgrade routine.
	 *
	 * @param string $routine The method to call.
	 * @param string $version The new version.
	 * @param string $current_version The current set version.
	 *
	 * @return void
	 */
	protected function run_upgrade_routine( $routine, $version, $current_version ) {
		if ( version_compare( $current_version, $version, '<' ) ) {
			$this->$routine( $current_version );
		}
	}

	/**
	 * Runs the needed cleanup after an update, setting the DB version to latest version, flushing caches etc.
	 *
	 * @param string $previous_version The previous version.
	 *
	 * @return void
	 */
	protected function finish_up( $previous_version ) {
		update_option( self::PREVIOUS_OPTION, $previous_version );
		update_option( self::OPTION, P21_FLAREO_VERSION );
	}

	/**
	 * Refresh the templates library.
	 *
	 * @return void
	 */
	protected function upgrade_0_1_0() {
		// Refresh templates library.
	}
}

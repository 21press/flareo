<?php
/**
 * Manages Flare Post Type fields.
 *
 * @package 21Press/Flareo
 */

namespace P21\Flareo\Core\PostType;

/**
 * Class Flare_Post_Fields
 */
class Flare_Post_Fields {
	/**
	 * Get all meta field keys for Flare post type.
	 *
	 * @return array
	 */
	public static function get_all() {
		$current_year = gmdate( 'Y' );

		return array(
			array(
				'key'    => 'type_config',
				'title'  => __( 'Type & Presets', 'flareo' ),
				'fields' => array(
					array(
						'id'      => 'p21_flareo_flare_by_type',
						'label'   => __( 'Flare Type', 'flareo' ),
						'type'    => 'radio',
						'default' => 'preset',
						'options' => array(
							'preset' => __( 'Presets', 'flareo' ),
						),
					),
					array(
						'id'          => 'p21_flareo_flare_by_preset_type',
						'label'       => __( 'Preset Type', 'flareo' ),
						'type'        => 'radio',
						'default'     => 'confetti',
						'options'     => array(
							'confetti'  => __( 'Confetti', 'flareo' ),
							'stars'     => __( 'Stars', 'flareo' ),
							'hearts'    => __( 'Hearts', 'flareo' ),
							'snow'      => __( 'Snow', 'flareo' ),
							'lightning' => __( 'Lightning', 'flareo' ),
							'sparkles'  => __( 'Sparkles', 'flareo' ),
							'meteor'    => __( 'Meteor', 'flareo' ),
						),
						'conditional' => array(
							'field' => 'p21_flareo_flare_by_type',
							'value' => 'preset',
						),
					),
					array(
						'id'          => 'p21_flareo_flare_by_preset_style',
						'label'       => __( 'Preset Style', 'flareo' ),
						'type'        => 'radio',
						'default'     => 'center-cannon',
						'options'     => array(
							'center-cannon'    => __( 'Center Cannon', 'flareo' ),
							'side-cannon'      => __( 'Side Cannon', 'flareo' ),
							'bottom-cannon'    => __( 'Bottom Cannon', 'flareo' ),
							'center-fountain'  => __( 'Center Fountain', 'flareo' ),
							'side-fountain'    => __( 'Side Fountain', 'flareo' ),
							'bottom-fountain'  => __( 'Bottom Fountain', 'flareo' ),
							'random-direction' => __( 'Random Direction', 'flareo' ),
							'fireworks'        => __( 'Fireworks', 'flareo' ),
							'burst'            => __( 'Burst', 'flareo' ),
							'falling'          => __( 'Falling', 'flareo' ),
							'meteor-shower'    => __( 'Meteor Shower', 'flareo' ),
						),
						'conditional' => array(
							'field' => 'p21_flareo_flare_by_type',
							'value' => 'preset',
						),
					),
				),
			),
			array(
				'key'   => 'insert_config',
				'title' => __( 'Insert & Trigger', 'flareo' ),
				'tabs'  => array(
					array(
						'id'     => 'insert',
						'label'  => __( 'Insert Method', 'flareo' ),
						'fields' => array(
							array(
								'id'      => 'p21_flareo_flare_insert_method',
								'label'   => __( 'Insert Method', 'flareo' ),
								'type'    => 'radio',
								'default' => 'auto-insert',
								'options' => array(
									'auto-insert' => __( 'Auto Insert', 'flareo' ),
									'shortcode'   => __( 'Shortcode', 'flareo' ),
								),
							),
							array(
								'id'          => 'p21_flareo_flare_method_shortcode',
								'label'       => __( 'Shortcode', 'flareo' ),
								'type'        => 'shortcode',
								'default'     => '[p21_flareo_flare id="' . get_the_ID() . '"]',
								'conditional' => array(
									'field' => 'p21_flareo_flare_insert_method',
									'value' => 'shortcode',
								),
							),
							array(
								'id'          => 'p21_flareo_flare_method_auto_insert_locations',
								'label'       => __( 'Location', 'flareo' ),
								'type'        => 'radio',
								'default'     => 'everywhere',
								'options'     => array(
									'everywhere' => __( 'Run Everywhere', 'flareo' ),
									'admin'      => __( 'Admin Only', 'flareo' ),
									'front'      => __( 'Front Only', 'flareo' ),
								),
								'conditional' => array(
									'field' => 'p21_flareo_flare_insert_method',
									'value' => 'auto-insert',
								),
							),
							array(
								'id'          => 'p21_flareo_flare_method_auto_insert_priority',
								'label'       => __( 'Priority', 'flareo' ),
								'type'        => 'number',
								'placeholder' => __( 'Set priority e.g. 10', 'flareo' ),
								'default'     => 10,
								'conditional' => array(
									'field' => 'p21_flareo_flare_insert_method',
									'value' => 'auto-insert',
								),
							),
						),
					),
					array(
						'id'     => 'trigger',
						'label'  => __( 'Trigger', 'flareo' ),
						'fields' => array(
							array(
								'id'      => 'p21_flareo_flare_trigger_method',
								'label'   => __( 'Trigger Method', 'flareo' ),
								'type'    => 'radio',
								'default' => 'page-load',
								'options' => array(
									'page-load' => __( 'On Page-Load', 'flareo' ),
									'on-click'  => __( 'On Click', 'flareo' ),
								),
							),
							array(
								'id'           => 'p21_flareo_trigger_has_delay',
								'label'        => __( 'Add Delay', 'flareo' ),
								'type'         => 'checkbox',
								'default'      => false,
								'toggle_label' => __( 'Enable', 'flareo' ),
							),
							array(
								'id'          => 'p21_flareo_trigger_delay',
								'label'       => __( 'Delay (ms)', 'flareo' ),
								'type'        => 'number',
								'default'     => 1000,
								'placeholder' => 1000,
								'conditional' => array(
									'field' => 'p21_flareo_trigger_has_delay',
									'value' => true,
								),
							),
							array(
								'id'          => 'p21_flareo_flare_css_target_selectors',
								'label'       => __( 'Enter CSS Target Selectors', 'flareo' ),
								'type'        => 'text',
								'placeholder' => __( '.my-class,#my-id', 'flareo' ),
							),
							array(
								'id'           => 'p21_flareo_flare_trigger_at_target_selector',
								'label'        => __( 'Trigger Flare at the Target Selector Area', 'flareo' ),
								'type'         => 'checkbox',
								'default'      => true,
								'toggle_label' => __( 'Enable', 'flareo' ),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Get SVG icon for a given section key.
	 *
	 * @param string $section_key Section key.
	 * @return string
	 */
	public static function get_section_icon( $section_key ) {
		$icons = array(
			'type_config'   => '<svg viewBox="0 0 1600 1600" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1450 500C1449.85 455.771 1435.06 412.839 1407.92 377.917C1380.78 342.996 1342.82 318.057 1300 307V150C1300 132.136 1290.47 115.631 1275 106.697C1259.53 97.765 1240.47 97.765 1225 106.697C1209.53 115.63 1200 132.135 1200 150V307C1143.11 322.459 1095.94 362.235 1071.09 415.693C1046.24 469.152 1046.24 530.849 1071.09 584.307C1095.94 637.765 1143.11 677.541 1200 693V1450C1200 1467.86 1209.53 1484.37 1225 1493.3C1240.47 1502.23 1259.53 1502.23 1275 1493.3C1290.47 1484.37 1300 1467.87 1300 1450V693C1342.82 681.943 1380.78 657.005 1407.92 622.083C1435.06 587.16 1449.85 544.228 1450 500ZM1250 600C1223.48 600 1198.04 589.464 1179.29 570.708C1160.54 551.959 1150 526.52 1150 500C1150 473.48 1160.54 448.041 1179.29 429.292C1198.04 410.537 1223.48 400 1250 400C1276.52 400 1301.96 410.536 1320.71 429.292C1339.46 448.041 1350 473.48 1350 500C1350 526.52 1339.46 551.959 1320.71 570.708C1301.96 589.463 1276.52 600 1250 600ZM850 907V150C850 132.136 840.469 115.631 825 106.697C809.531 97.765 790.469 97.765 775 106.697C759.531 115.63 750 132.135 750 150V907C693.109 922.459 645.937 962.235 621.088 1015.69C596.239 1069.15 596.239 1130.85 621.088 1184.31C645.937 1237.77 693.109 1277.54 750 1293V1450C750 1467.86 759.531 1484.37 775 1493.3C790.469 1502.23 809.531 1502.23 825 1493.3C840.469 1484.37 850 1467.87 850 1450V1293C906.891 1277.54 954.063 1237.77 978.912 1184.31C1003.76 1130.85 1003.76 1069.15 978.912 1015.69C954.063 962.235 906.891 922.459 850 907ZM800 1200C773.479 1200 748.041 1189.46 729.292 1170.71C710.537 1151.96 700 1126.52 700 1100C700 1073.48 710.536 1048.04 729.292 1029.29C748.041 1010.54 773.48 1000 800 1000C826.52 1000 851.959 1010.54 870.708 1029.29C889.463 1048.04 900 1073.48 900 1100C900 1126.52 889.464 1151.96 870.708 1170.71C851.959 1189.46 826.52 1200 800 1200ZM400 307V150C400 132.136 390.469 115.631 375 106.697C359.531 97.765 340.469 97.765 325 106.697C309.531 115.63 300 132.135 300 150V307C243.109 322.459 195.937 362.235 171.088 415.693C146.239 469.152 146.239 530.849 171.088 584.307C195.937 637.765 243.109 677.541 300 693V1450C300 1467.86 309.531 1484.37 325 1493.3C340.469 1502.23 359.531 1502.23 375 1493.3C390.469 1484.37 400 1467.87 400 1450V693C456.891 677.541 504.063 637.765 528.912 584.307C553.761 530.848 553.761 469.151 528.912 415.693C504.063 362.235 456.891 322.459 400 307ZM350 600C323.479 600 298.041 589.464 279.292 570.708C260.537 551.959 250 526.52 250 500C250 473.48 260.536 448.041 279.292 429.292C298.041 410.537 323.48 400 350 400C376.52 400 401.959 410.536 420.708 429.292C439.463 448.041 450 473.48 450 500C450 526.52 439.464 551.959 420.708 570.708C401.959 589.463 376.52 600 350 600Z" fill="currentColor"/>
</svg>',
			'insert_config' => '<svg viewBox="0 0 1600 1600" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1518.4 512C1513.6 481.599 1489.6 457.599 1457.6 452.803L1326.4 435.204C1323.2 435.204 1323.2 433.605 1321.6 432.006C1320 430.407 1320 428.808 1321.6 425.605L1385.6 308.802C1400 281.605 1395.2 248 1372.8 225.605C1350.39 203.204 1316.8 198.408 1289.6 214.407L1172.8 278.407C1171.2 280.006 1167.99 278.407 1166.39 278.407C1164.8 278.407 1163.2 276.808 1163.2 273.605L1145.6 142.408C1142.4 110.408 1118.4 86.4078 1086.4 81.6051C1056 76.803 1025.6 92.803 1012.8 121.605L955.203 241.605C953.605 244.803 952.006 244.803 950.401 244.803C948.802 244.803 947.203 244.803 945.599 243.204L854.402 147.204C833.599 124.803 800 118.401 771.204 132.803C742.402 147.204 728.007 177.605 734.402 208L758.402 339.197C758.402 342.395 758.402 344 756.803 344C755.204 345.598 753.605 345.598 752.001 345.598L620.803 321.598C590.402 315.197 560.001 331.197 545.606 358.401C531.205 385.599 537.606 419.204 560.007 441.598L656.007 532.796C657.606 534.395 657.606 535.994 657.606 537.598C657.606 539.197 656.007 540.796 654.409 542.4L534.409 599.999C505.606 612.801 489.606 643.196 496.007 675.196C500.809 705.597 524.81 729.597 556.81 734.393L688.007 751.992C689.606 751.992 689.606 751.992 689.606 753.591L113.606 1327.99C92.8088 1348.79 80.0074 1377.59 80.0074 1407.99C80.0074 1438.39 91.2053 1465.59 113.606 1487.99C134.409 1508.79 163.205 1519.99 192.007 1519.99C220.81 1519.99 249.606 1508.79 272.007 1487.99L849.607 910.391C849.607 910.391 851.206 911.99 851.206 913.589L868.805 1044.79C872.003 1076.79 896.002 1100.79 928.002 1105.59C931.2 1105.59 936.002 1107.19 939.2 1107.19C966.398 1107.19 990.398 1091.19 1003.2 1067.19L1060.8 947.188C1062.4 945.589 1064 943.99 1065.6 943.99C1067.2 943.99 1068.8 943.99 1070.4 945.589L1161.6 1041.59C1184 1063.99 1216 1070.39 1244.8 1055.99C1272 1041.59 1288 1011.19 1281.6 980.793L1256.01 847.99C1256.01 844.792 1256.01 843.188 1257.61 843.188C1259.21 841.589 1260.8 841.589 1264.01 841.589L1395.2 865.589C1425.61 871.99 1456.01 855.99 1470.4 828.786C1484.8 799.984 1478.4 767.984 1456 745.589L1360 654.392C1358.4 652.793 1358.4 651.194 1358.4 649.59C1358.4 647.991 1360 646.392 1361.6 644.788L1481.6 587.189C1507.2 572.793 1523.2 542.392 1518.41 511.992L1518.4 512ZM227.2 1441.6C217.601 1451.2 206.397 1456 193.601 1456C180.799 1456 168.003 1451.2 160.003 1441.6C148.805 1432 144.003 1420.8 144.003 1408C144.003 1395.2 148.805 1384 158.404 1374.4L780.804 752.003L848.001 819.2L227.2 1441.6ZM825.6 705.6L892.797 638.403L959.995 705.6L892.797 772.797L825.6 705.6ZM939.199 592.001L972.797 558.403C982.396 548.804 993.6 544.001 1006.4 544.001C1019.2 544.001 1031.99 548.804 1039.99 558.403C1049.59 568.002 1054.4 579.205 1054.4 592.001C1054.4 604.804 1049.59 617.6 1039.99 625.6L1008 660.803L939.199 592.001ZM1451.2 528.001L1331.2 585.6C1310.4 595.199 1296 616.001 1292.8 638.403C1289.6 660.804 1297.6 684.804 1313.6 700.804L1411.2 792.001C1412.8 793.6 1414.4 795.199 1412.8 798.403C1411.2 803.205 1408 801.6 1406.4 801.6L1275.2 777.6C1252.8 772.798 1228.8 780.798 1212.8 796.798C1196.8 812.797 1188.8 836.798 1193.6 859.199L1217.6 990.396C1217.6 991.995 1219.2 995.199 1214.4 996.797C1209.6 998.396 1208 996.797 1206.4 995.199L1115.21 899.199C1099.21 883.199 1075.21 875.199 1052.8 878.396C1030.4 881.594 1011.21 895.994 1000 916.797L944.001 1036.8C944.001 1038.4 942.402 1041.6 937.6 1040C932.798 1040 932.798 1036.8 932.798 1035.2L915.199 904.001C913.601 888.001 905.6 875.198 896.002 864.001L1088 672.001C1108.8 651.198 1120 622.402 1120 592.001C1120 561.6 1108.8 534.402 1088 512.001C1067.2 491.198 1038.4 480.001 1008 480.001C977.601 480.001 950.403 491.199 928.002 512.001L872.002 568.001L736.002 705.601C724.804 696.002 712.002 688.002 696.002 686.404L564.805 668.805C563.206 668.805 560.003 668.805 560.003 664.003C560.003 659.201 561.602 657.602 563.2 657.602L683.2 600.003C704.003 590.404 718.398 571.2 723.2 547.2C726.398 524.799 718.398 500.799 702.398 484.799L606.398 393.602C604.799 392.003 603.2 390.404 604.799 385.602C606.398 380.8 609.601 382.404 611.2 382.404L742.397 406.404C764.799 411.206 788.799 403.206 804.799 387.206C820.799 371.207 828.799 347.206 823.996 324.805L799.996 193.608C799.996 192.009 799.996 188.806 803.194 187.207C807.996 185.608 809.595 187.207 811.194 188.806L902.391 284.806C918.391 300.806 940.793 308.806 964.793 305.608C988.793 302.411 1007.99 286.411 1017.6 265.608L1072 148.806C1072 147.202 1073.6 144.004 1078.4 144.004C1083.21 144.004 1083.21 147.201 1083.21 148.806L1100.8 280.003C1104 302.404 1118.4 323.2 1139.21 332.806C1160.01 344.004 1184.01 342.405 1204.8 331.207L1321.61 267.207C1323.21 267.207 1326.41 265.608 1329.61 268.806C1332.8 272.004 1331.21 275.207 1331.21 276.806L1267.21 393.608C1256.01 414.411 1256.01 438.411 1265.61 459.207C1276.8 480.01 1296.01 494.404 1318.41 497.608L1449.61 515.207C1451.21 515.207 1454.41 515.207 1454.41 520.009C1456 526.405 1452.8 528.004 1451.2 528.004L1451.2 528.001Z" fill="currentColor"/>
</svg>',
		);

		return isset( $icons[ $section_key ] ) ? $icons[ $section_key ] : '';
	}

	/**
	 * Get field value for a given flare and field ID.
	 *
	 * @param int    $flare_id Flare post ID.
	 * @param string $field_id    Field meta key.
	 * @param mixed  $field_default    Default value if not set.
	 * @return mixed
	 */
	public static function get_field_value( $flare_id, $field_id, $field_default = null ) {
		// Get saved value.
		$value = get_post_meta( $flare_id, $field_id, true );

		$field_default = is_null( $field_default ) ? '' : $field_default;

		// Get default value from all fields above.
		if ( empty( $value ) ) {
			$all_fields = self::get_all();

			foreach ( $all_fields as $section ) {
				if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						if ( $field['id'] === $field_id ) {
							$field_default = isset( $field['default'] ) ? $field['default'] : $field_default;
							break 2;
						}
					}
				} elseif ( isset( $section['tabs'] ) && is_array( $section['tabs'] ) ) {
					foreach ( $section['tabs'] as $tab ) {
						if ( isset( $tab['fields'] ) && is_array( $tab['fields'] ) ) {
							foreach ( $tab['fields'] as $field ) {
								if ( $field['id'] === $field_id ) {
									$field_default = isset( $field['default'] ) ? $field['default'] : $field_default;
									break 3;
								}
							}
						}
					}
				}
			}
		}

			// Return default if no saved value.
			return isset( $value ) ? $value : $field_default;
	}

	/**
	 * Update field value for a given flare and field ID.
	 *
	 * @param int    $flare_id Flare post ID.
	 * @param string $field_id    Field meta key.
	 * @param mixed  $value       Value to set.
	 * @return int|bool
	 */
	public static function update_field_value( $flare_id, $field_id, $value ) {
		return update_post_meta( $flare_id, $field_id, $value );
	}
}

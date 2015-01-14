<?php
if ( !defined('ABSPATH') ) die('You are not allowed to call this page directly.');

class FrmListHelper extends WP_List_Table {

	function __construct($args) {
	    $args = wp_parse_args( $args, array(
			'params' => array()
		) );

		$this->params = $args['params'];

		parent::__construct( $args );
	}

	function ajax_user_can() {
		return current_user_can( 'administrator' );
	}

	function display_rows() {
		$style = '';
		foreach ( $this->items as $item ) {
			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
			echo "\n\t", $this->single_row( $item, $style );
		}
	}

}

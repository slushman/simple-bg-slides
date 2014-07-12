<?php

/**
 * Sanitize anything
 *
 * @package   Slushman Toolkit
 * @version   0.1
 * @since     0.1
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @link      http://slushman.com/plugins/slushman-toolkit
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( !class_exists( 'Slushman_Make_Sanitized' ) ) {

class Slushman_Make_Sanitized {

/**
 * The data to be sanitized
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $data = '';

/**
 * The type of data
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $type = '';



/* ==========================================================================
   Public Methods
   ========================================================================== */	

/**
 * Sets the class variables
 *
 * Params:
 * 	 data: the data to be sanitized
 * 	 type: the data type
 *
 * @access 	public
 * @since 	0.1
 * 
 * @param  array 		$params 		The data and data type to santize
 *
 * @uses 	WP_Error
 * @uses 	is_wp_error()
 * @uses 	wp_die()
 * @uses 	get_error_message()
 *
 * @return 	void
 */
	public function __construct( $params ) {

		if ( empty( $params['data'] ) ) { return; }

		$check = '';

		if ( empty( $params['type'] ) ) {

			$check = new WP_Error( 'forgot_type', __( 'Specify the data type to sanitize.', 'slushfolio' ) );

		}

		if ( is_wp_error( $check ) ) {

			wp_die( $check->get_error_message(), __( 'Forgot data type', 'slushfolio' ) );

		}

		$this->type = $params['type'];
		$this->data = $params['data'];

	} // End of __construct()

/**
 * Cleans the data
 *
 * @access 	public
 * @since 	0.1
 * 
 * @uses 	sanitize_email()
 * @uses 	sanitize_phone()
 * @uses 	esc_textarea()
 * @uses 	sanitize_text_field()
 * @uses 	esc_url()
 * 
 * @return  mixed         The sanitized data
 */
	public function clean() {

		$sanitized = '';

		/**
		 * Add additional santization before the default sanitization
		 */
		do_action( 'slushman_pre_sanitize', $sanitized );
		
		switch ( $this->type ) {

			case 'color'			: 
			case 'radio'			: 
			case 'select'			: $sanitized = $this->sanitize_random( $this->data ); break;

			case 'date'				: 
			case 'datetime'			: 
			case 'datetime-local'	: 
			case 'time'				:
			case 'week'				: $sanitized = strtotime( $this->data ); break;

			case 'number'			: 
			case 'range'			: $sanitized = intval( $this->data ); break;

			case 'hidden'			: 
			case 'month'			: 
			case 'text'				: 
			case 'uploader_gallery'	: $sanitized = sanitize_text_field( $this->data ); break;

			case 'uploader_single'	: 
			case 'url'				: $sanitized = esc_url( $this->data ); break;
			
			case 'checkbox'			: $sanitized = ( isset( $this->data ) ? 1 : 0 ); break;
			case 'email'			: $sanitized = sanitize_email( $this->data ); break;
			case 'file'				: $sanitized = sanitize_file_name( $this->data ); break;
			case 'tel'				: $sanitized = $this->sanitize_phone( $this->data ); break;
			case 'textarea'			: $sanitized = esc_textarea( $this->data ); break;
			
		} // End of switch

		/**
		 * Add additional santization after the default .
		 */
		do_action( 'slushman_post_sanitize', $sanitized );

		return $sanitized;

	} // End of clean()

/**
 * Validates data
 * 
 * @return [type] [description]
 */
	public function validate() {

		$check = $this->perform_validation();

		if ( !$check ) {

			$validated = '';

		} else {

			$validated = $check;

		}

		// send data to perform validation
		// if it comes back false, create WP_Error
		// if not, returns validated data

		return $validated;

	} // End of validate()

	protected function perform_validation() {

		switch ( $this->type ) {

			case 'color' 		: $validated = $this->sanitize_random( $this->data ); break;
			case 'checkbox'		: $validated = ( isset( $this->data ) ? 1 : 0 ); break;
			case 'date' 		: $validated = $this->validate_date( $this->data ); break;
			case 'email'		: $validated = is_email( $this->data ); break;
			case 'file' 		: $validated = sanitize_file_name( $this->data ); break;
			case 'number'		: $validated = intval( $this->data ); break;
			case 'tel'			: $validated = $this->sanitize_phone( $this->data ); break;
			case 'radio'		: $validated = $this->sanitize_random( $this->data ); break;
			case 'range'		: $validated = intval( $this->data ); break;
			case 'select'		: $validated = $this->sanitize_random( $this->data ); break;
			case 'textarea'		: $validated = esc_textarea( $this->data ); break;
			case 'text'			: $validated = sanitize_text_field( $this->data ); break;
			case 'url'			: $validated = esc_url( $this->data ); break;

		} // End of switch

	} // End of perform_validation()

/**
 * Checks a date against a format to ensure its validity
 *
 * @link 	http://www.php.net/manual/en/function.checkdate.php
 * 
 * @param  	string 		$date   		The date as collected from the form field
 * @param  	string 		$format 		The format to check the date against
 * 
 * @return 	string 		A validated, formatted date
 */	
	public function validate_date( $date, $format = 'Y-m-d H:i:s' ) {
	
		$version = explode( '.', phpversion() );
	
		if ( ( (int) $version[0] >= 5 && (int) $version[1] >= 2 && (int) $version[2] > 17 ) ) {
		
			$d = DateTime::createFromFormat( $format, $date );
		
		} else {
		
			$d = new DateTime( date( $format, strtotime( $date ) ) );
		
		}
		
		return $d && $d->format( $format ) == $date;

	} // End of validate_date()


/* ==========================================================================
   Private Methods
   ========================================================================== */

/**
 * Validates a phone number
 *
 * @access 	private
 * @since	0.1
 * @link	http://jrtashjian.com/2009/03/code-snippet-validate-a-phone-number/
 *
 * @param 	string 			$phone				A phone number string
 * 
 * @return	string|bool		$phone|FALSE		Returns the valid phone number, FALSE if not
 */
		private function sanitize_phone( $phone ) {
		
			if ( empty( $phone ) ) { return FALSE; }
			
			if ( preg_match( '/^[+]?([0-9]?)[(|s|-|.]?([0-9]{3})[)|s|-|.]*([0-9]{3})[s|-|.]*([0-9]{4})$/', $phone ) ) {
			
				return trim( $phone );
			
			} // End of $phone validation
			
			return FALSE;
			
		} // End of sanitize_phone()

/**
 * Performs general cleaning functions on data
 *
 * @param 	mixed 	$input 		Data to be cleaned
 * 
 * @return 	mixed 	$return 	The cleaned data
 */
	   	private function sanitize_random( $input ) {

				$one	= trim( $input );
				$two	= stripslashes( $one );
				$return	= htmlspecialchars( $two );

	   		return $return;

	   	} // End of sanitize_random()



	} // End of class

} // End of class check

?>
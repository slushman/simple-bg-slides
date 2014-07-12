<?php 

/**
 * A class for creating metaboxes
 *
 * @package   Slushman Toolkit
 * @version   0.1
 * @since     0.1
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @link      http://slushman.com/plugins/slushman-toolkit
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_field.php' );
require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_sanitized.php' );

if ( !class_exists( 'Slushman_Make_MetaBox' ) ) {

	class Slushman_Make_MetaBox {

/**
 * An array of optional args for the metabox
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
		private $args = array();

/**
 * Optional class attributes for the metabox
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
		private $class = array();

/**
 * Where on the page to display the metabox
 *
 * Options:
 * 	 normal: appears under the content editor - default value
 * 	 advanced
 * 	 side: appears on the right sidebar on the post/page edit page
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
		private $context = 'normal';

/**
 * Form fields to display within the metabox
 *
 * See make_field() class for field options
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
		private $fields = array();

/**
 * The internationalization domain
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
		private $i18n = '';

/**
 * The id attribute for the metabox
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
		private $id = '';

/**
 * Should the metabox have a nonce field or no
 *
 * @access 	private
 * @since 	0.1
 * @var 	boolean
 */
		private $nonce = FALSE;

/**
 * The name of the related CPT
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
		private $post_type = '';

/**
 * The importance in the context
 * 
 * Options:
 * 	 high
 * 	 core
 * 	 default - default value
 * 	 low
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
		private $priority = 'default';

/**
 * The title of the edit screen section (the box itself)
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
		private $title = '';

/**
 * Plugin version, used for cache-busting of style and script file references.
 *
 * @access 	protected
 * @since  	0.1
 * @var 	string
 */
	    protected $version = '';


/* ==========================================================================
   Public Methods
   ========================================================================== */

/**
 * Sets class variables and adds actions and filters
 *
 * Params:
 * 	 args: an optional array of arguments for the metabox
 * 	 class: An optional class attribute for the metabox
 * 	 context: where on the page to display the metabox, default: normal
 * 	 fields: form fields to display within the metabox
 * 	 i18n: the i18n domain
 * 	 id: the id attribute for the metabox
 * 	 nonce: should the metabox have a nonce or not, if so, what's the name
 *   post_type: the name of the CPT
 * 	 priority: the importance in the context, default: default
 * 	 title: the title of the edit screen section (the box itself)
 *
 * @access 	public
 * @since 	0.1
 * 
 * @return 	void
 */
		public function __construct() {

			// Define all class variables in class extension

		} // End of __construct()

/**
 * Add all the actions and filters to the WordPress actions
 *
 * @link 	http://stackoverflow.com/questions/5151409/wordpress-save-post-action-for-custom-posts
 * 
 * @uses 	add_action()
 * @uses 	plugin_basename()
 * @uses 	plugin_dir_path()
 * @uses 	add_filter()
 * 
 * @return 	void
 */
		protected function add_actions() {

			add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_scripts' ) );

			if ( is_admin() ) {

				add_action( 'add_meta_boxes', 					array( $this, 'create_metabox' ) );
				add_action( "save_post_{$this->post_type}", 	array( $this, 'save_meta' ) );

			}
			
			if ( !empty( $this->class ) ) {

				add_filter( "postbox_classes_{$this->post_type}_{$this->id}", array( $this, 'add_class' ) );

			}

		} // End of add_actions()

/**
 * Create a metabox
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	add_meta_box()
 *
 * @return 	void
 */
		public function create_metabox() {

			// Define in the class extension
			add_meta_box( $this->id, $this->title, array( $this, 'metabox_content' ), $this->post_type, $this->context, $this->priority, $this->args );

		} // End of create_metabox()

/**
 * Display content inside the metabox
 */
		protected function metabox_content( $post_obj, $metabox ) {

			// Define in class extension

		} // End of metabox_content()

/**
 * Runs all the set functions
 *
 * @access 	protected
 * @since  	0.1
 *
 * @uses 	set_settings()
 * @uses 	set_fields()
 * @uses 	set_menu()
 * @uses 	set_sections()
 * @uses 	set_tabs()
 * 
 * @return 	void
 */
		protected function setup( $params ) {

			$reqs = array( 'id', 'nonce', 'post_type', 'title' );
			$opts = array( 'args', 'class', 'context', 'fields', 'priority' );

			foreach ( $reqs as $req ) {

				$this->set_class_var( $req, $params[$req], 'required' );

			}

			foreach ( $opts as $opt ) {

				$this->set_class_var( $opt, $params[$opt] );				

			}

		} // End of setup()


/* ==========================================================================
   Add Methods
   ========================================================================== */

/**
 * Add a class to the metabox container
 *
 * @access 	public
 * @since 	0.1
 *
 * @param 	array 		$classes		An array of current classes for the metabox
 *
 * @uses 	sanitize_html_class()
 * 
 * @return 	array 						An array of classes to use in the metabox's class attribute
 */
		public function add_class( $classes = array() ) {

			if ( empty( $this->class ) ) { return $classes; }

			foreach ( $this->class as $class ) {

				if ( !in_array( $class, $classes ) ) {

					$classes[] = sanitize_html_class( $class );

				}

			} // End of foreach loop

			return $classes;

		} // End of add_class()

/**
 * Add forms fields to a metabox
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	get_post_custom()
 * @uses 	make_field()
 *
 * @return 	void
 */
		public function add_fields( $post_id ) {

			if ( empty( $this->fields ) ) { return; }

			$this->add_nonce_field();

			$custom = get_post_custom( $post_id );

			foreach ( $this->fields as $field ) {

				$field['value']	= ( empty( $custom[$field['atts']['id']] ) ? '' : $custom[$field['atts']['id']] );

				echo '<p>' . $this->make_field( $field ) . '</p>';

			} // End of foreach loop

		} // End of add_fields()

/**
 * Adds a nonce field to a metabox
 *
 * @uses 	 wp_create_nonce()
 *
 * @return 	void
 */
		private function add_nonce_field() {

			if ( FALSE !== $this->nonce ) {

				return wp_nonce_field( 'save_' . $this->id, $this->nonce );

			}

		} // End of add_nonce_field()



/* ==========================================================================
   Check Methods
   ========================================================================== */

	   	protected function check_value( $value, $method ) {

	   		$check = '';

	   		if ( empty( $value ) ) {

				$check = new WP_Error( "forgot_value", __( "Add the value to the {$method} call.", $this->i18n ) );

			}

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot value', $this->i18n ) );

			}

	   	} // End of check_value()



/* ==========================================================================
   Save Methods
   ========================================================================== */	

/**
 * Checks if the submission is an autosave or revision and validates the nonce
 *
 * @link 	https://gist.github.com/tommcfarlin/4468321
 * @access 	private
 * @since 	0.1
 *
 * @param  	int 		$post_id  		The ID of the post being saved
 *
 * @uses 	wp_is_post_autosave()
 * @uses 	wp_is_post_revision()
 * @uses 	wp_verify_nonce()
 * 
 * @return 	boolean 					Whether or not the user has the ability to save this post
 */
		private function can_save( $post_id ) {

			$autosave	= wp_is_post_autosave( $post_id );
			$revision	= wp_is_post_revision( $post_id );
			$nonce		= check_admin_referer( 'save_' . $this->id, $this->nonce );
			$cpt 		= $this->post_type == get_post_type( $post_id );

			return !( $autosave || $revision ) && $nonce && !$cpt;

		} // End of can_save()

/**
 * Saves data from the metabox form
 *
 * @access 	public
 * @since 	0.1
 * 
 * @param  	int 		$post_id  		The ID of the post being saved
 *
 * @uses 	can_save()
 * @uses 	get_post_custom()
 * @uses 	sanitize()
 * @uses 	update_post_meta()
 * @uses 	delete_post_meta()
 * @uses 	add_post_meta()
 *
 * @return 	void
 */
	   	public function save_meta( $post_id ) {

	   		$this->can_save( $post_id );

			$custom = get_post_custom( $post_id );

			foreach ( $this->fields as $field ) {

				$key 		= ( empty( $field['atts']['name'] ) ? $field['atts']['id'] : $field['atts']['name'] );
				$value		= ( empty( $custom[$key][0] ) ? '' : $custom[$key][0] );
				$new_value	= $this->sanitize( $field['type'], $_POST[$key] );

				if ( $new_value && $new_value != $value ) {
				
					// If the new meta value does not match the old value, update it.
					update_post_meta( $post_id, $key, $new_value );

				} elseif ( '' == $new_value && $value ) {

					// If there is no new meta value but an old value exists, delete it.
					delete_post_meta( $post_id, $key, $value );

				} elseif ( $new_value && '' == $value ) {
				
					// If a new meta value was added and there was no previous value, add it.
					add_post_meta( $post_id, $key, $new_value, true );
				
				} // End of meta value checks

			} // End of foreach loop
		    
		} // End of save_meta()



/* ==========================================================================
   Set Methods
   ========================================================================== */

/**
 * Sets the args class variable
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param 	string  	$var_name  		The name of the class variable
 * @param 	mixed  		$value  		The potential value for the class variable
 * @param 	string  	$priority  		Is this class variable required or not?
 *
 * @return 	void
 */
		protected function set_class_var( $var_name, $value, $priority = 'default' ) {

			if ( 'required' == $priority ) {

				$this->check_value( $value, __METHOD__ );

			} else {

				if ( empty( $value ) ) { return; }

			} // End of priority check

			$this->{$var_name} = $value;

		} // End of set_class_var()



/* ==========================================================================
   Style & Script Methods
   ========================================================================== */

/**
 * Register and enqueue admin-specific style sheet.
 * 
 * Uses KidSysco's jQuery UI Month Picker
 * @link 	https://github.com/KidSysco/jquery-ui-month-picker
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	get_current_screen()
 * @uses 	wp_enqueue_script()
 * @uses 	plugins_url()
 *
 * @return 	void
 */
		public function enqueue_admin_scripts() {

			if ( is_admin() ) {

				wp_enqueue_script( $this->i18n .'-admin-script', plugins_url( 'admin/js/admin.min.js', dirname( __FILE__ ) ), array( 'jquery', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->version, TRUE );

				// https://github.com/KidSysco/jquery-ui-month-picker
				wp_enqueue_script( 'monthpicker', plugins_url( 'admin/js/monthpicker.2.1.min.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-button' ), $this->version, TRUE );

				// https://github.com/trentrichardson/jQuery-Timepicker-Addon
				wp_enqueue_script( 'timepicker', plugins_url( 'admin/js/datetimepicker.min.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-datepicker' ), $this->version, TRUE );

				if ( !did_action( 'wp_enqueue_media' ) ) { wp_enqueue_media(); }

			}

		} // End of enqueue_admin_scripts()

/**
 * Register and enqueue admin-specific style sheet.
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	get_current_screen()
 * @uses 	wp_enqueue_style()
 * @uses 	plugins_url()
 *
 * @return 	void
 */
		public function enqueue_admin_styles() {

			if ( is_admin() ) {

				wp_enqueue_style( $this->i18n .'-admin-styles', plugins_url( 'admin/css/admin.css', dirname( __FILE__ ) ), array(), $this->version );
				wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'monthpicker', plugins_url( 'admin/css/monthpicker.css', dirname( __FILE__ ) ), array(), $this->version );
			
			}

		} // End of enqueue_admin_styles()



/* ==========================================================================
   Injection Containers

   These remove any dependency and coupling of the public functions from the
   other classes needed by this class.
   ========================================================================== */

/**
 * Returns an HTML form field from the Slushman_Toolkit_Make_Field class
 *
 * @access 	private
 * @since 	0.1
 * 
 * @param 	array 		$field 		An array of args for the field
 *
 * @uses 	Slushman_Toolkit_Make_Fields
 * @uses 	create_field()
 * 
 * @return 	mixed 		a formatted HTML input field
 */
		private function make_field( $field ) {

			$make_field	= new Slushman_Toolkit_Make_Field( $field );

			return $make_field->create_field();

		} // End of make_field()
	   	
/**
 * Returns data sanitized by the Slushman_Make_Sanitized class
 *
 * @access 	private
 * @since 	0.1
 * 
 * @param 	string 		$type 		The data type
 * @param 	mixed 		$data 		The data to be sanitized
 *
 * @uses 	Slushman_Make_Sanitized
 * @uses 	clean()
 * 
 * @return 	mixed 		The sanitized data
 */
		private function sanitize( $type, $data ) {

			$sanitize	= new Slushman_Make_Sanitized( array( 'type' => $type, 'data' => $data ) );
			$return		= $sanitize->clean();

			unset( $sanitize );

			return $return;

		} // End of sanitize()



} // End of class

} // End of class check

?>
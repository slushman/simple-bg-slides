<?php

/**
 * A class for creating options pages
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

if ( !class_exists( 'Slushman_Make_Admin' ) ) {

	class Slushman_Make_Admin {

/**
 * An array of option fields to process
 *
 * See make_field() class for field options
 *
 * Required: setting, section
 *
 * @param 	string 		section 	The name of the section this field belmongs to
 * @param 	string 		setting 	The name of the setting this field belongs to
 * 
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $fields = array();

/**
 * An array of settings to register
 *
 * At least one is required
 *
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $settings = array();

/**
 * The internationalization domain
 *
 * @access 	protected
 * @since 	0.1
 * @var 	string
 */
		protected $i18n = '';

/**
 * Instance of this class.
 *
 * @access 	protected
 * @since 	0.1
 * @var 	object
 */
		protected static $instance = null;

/**
 * An array of options for the menu and setting page
 *
 * Required: cap, page, slug, title, top_slug
 *
 * @param 	string 		cap 		the capability required to see this menu item, default: manage_options
 * @param 	url 		icon 		the url of the icon for the parent menu
 * @param 	bool 		link 		include the settings link on the plugins page or no? Default: TRUE
 * @param 	string 		parent 		the slug of the parent page (if its a submenu)
 * @param 	int 		position 	where this menu item should appear in the Dashboard menu, default: 24
 * @param 	string 		slug 		the unique slug for this menu
 * @param 	string 		tab 		The default tab
 * @param 	string 		title 		the menu title text
 * @param 	string 		top_slug 	the slug of the parent for this menu item
 * 
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $menu = array();

/**
 * The name of the plugin
 *
 * @access 	protected
 * @since  	0.1
 * @var 	string
 */
	    protected $name = '';

/**
 * Values of the options from the database
 *
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $options = array();

/**
 * An array of sections to add to settings
 *
 * Required: setting, id, name
 * 
 * @param 	bool 		box 		should this be displayed as as metabox or no? default: FALSE
 * @param 	string 		desc 		the text to use in the section description area
 * @param 	string 		id 			the text to use in the id attribute
 * @param 	string 		name 		the properly capitalized, full name of the section
 * @param 	string 		setting 	the setting this section belongs inside
 * 
 * @access 	protected
 * @since 	0.1
 * @var 	array
 */
		protected $sections = array();

/**
 * Settings tabs array
 *
 * Required: name, title
 *
 * @param 	string 		setting 	the name of the setting to display on this tab
 * @param 	string 		title 		text to use as the title on this tabbed page
 *
 * @access 	protected
 * @since  	0.1
 * @var 	array
 */
	    protected $tabs = array();	

/**
 * Plugin version, used for cache-busting of style and script file references.
 *
 * @access 	protected
 * @since  	0.1
 * @var 	string
 */
	    protected $version = '';

/**
 * Sets class variables
 *
 * @access 	public
 * @since 	0.1
 * 
 * @return  void
 */
		public function __construct() {

			// Define in class extension

		} // End of __construct()

/**
 * Add all the actions and filters to the WordPress actions
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
			add_action( 'admin_init', 				array( $this, 'add_settings' ) );
			add_action( 'admin_init', 				array( $this, 'add_sections' ) );
			add_action( 'admin_init', 				array( $this, 'add_fields' ) );
			add_action( 'admin_menu', 				array( $this, 'add_menu' ) );
			add_action( 'above_tabs' . $this->i18n, array( $this, 'above_tabs' ) );
			add_action( 'below_tabs' . $this->i18n, array( $this, 'below_tabs' ) );

			if ( !empty( $this->menu ) ) {

				$basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->menu['slug'] . '.php' );

				add_filter( 'plugin_action_links_' . $basename, array( $this, 'add_settings_link' ), 10, 2 );
				add_filter( 'plugin_row_meta', 					array( $this, 'add_meta_links' ), 10, 2 );

			} // End of menu empty check

		} // End of add_actions()

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
		protected function setup( $fields = array(), $menu = array(), $sections = array(), $settings = array(), $tabs = array() ) {

			$this->set_settings( $settings );
			$this->set_fields( $fields );
			$this->set_menu( $menu );
			$this->set_sections( $sections );
			$this->set_tabs( $tabs );

		} // End of setup()

/**
 * Creates the settings page
 *
 * @access 	public
 * @since	0.1
 *
 * @uses 	get_tabs_or_title()
 * @uses	settings_fields()
 * @uses	do_settings_sections()
 * @uses	submit_button()
 *
 * @return 	mixed 		A page of settings
 */
		public function build_page() {

			$cur_tab = $this->get_current_tab( $_GET );
			$display = ( empty( $cur_tab ) ? $this->settings[0] : $cur_tab );

			// get additional classes and id for icon div
			// check if the current icon code actually works
		
			?><section class="settings_wrap">
			<header class="settings_header">
				<h2 class="settings_page_title"><?php

					echo esc_html( get_admin_page_title() ); 

				?></h2><!-- End of .settings_page_title --><?php

				do_action( 'above_tabs' . $this->i18n );

				?><h3 class="nav-tab-wrapper"><?php
		
					echo $this->get_tabs( $cur_tab );

				?></h3><!-- End of .nav-tab-wrapper -->
			</header><!-- End of .settings_header -->
			<form method="post" action="options.php"><?php

				settings_fields( $display );
				do_settings_sections( $display );	
				submit_button();
				
			?></form><br />
			</section><footer class="settings_footer"><!-- End of .setting_wrap --><?php

				do_action( 'below_tabs' . $this->i18n );

			?></footer><!-- .settings_footer --><?php
		
		} // End of build_page()

/**
 * Creates the options, sets the default values, 
 * and creates a class variable with the database values in it
 *
 * @access 	protected
 * @since 	0.1
 *
 * @uses 	add_option()
 * @uses 	get_option()
 *
 * @return 	void
 */
		protected function initialize_settings( $fields, $settings ) {

			// Create an empty array
			$defaults = array();

			// Assign each field's value to the defaults array within each setting
			foreach ( $fields as $field ) {

				// Don't initialize hidden fields
				if ( 'hidden' == $field['type'] ) { continue; }

				$defaults[$field['setting']][$field['id']] = $field['value'];

			} // End of foreach loop

			// Update each setting's option
			foreach ( $settings as $setting ) {

				add_option( $setting, $defaults[$setting] );

				$this->{$setting} = (array) get_option( $setting );

			} // End of foreach loop

		} // End of initialize_settings()

/**
 * Sanitizes the data submitted from the settings
 *
 * @access 	public
 * @since 	0.1
 * 
 * @param  	array 	$input 			An array of data from the settings fields
 *
 * @uses 	sanitize()
 * 
 * @return 	array 	$cleaned 		An array of sanitized data from the settings fields
 */
		public function validate_options( $input ) {

			$cleaned = array();

			foreach ( $this->fields as $field ) {

				// validate each data type, then ship off to sanitize()
				switch ( $field['type'] ) {

					case 'email' : 
						if ( is_email( $input[$field['id']] ) ) {

							$valid = $input[$field['id']];

						} else {

							add_settings_error( $field['setting'], $field['id'] . '-error', 'Please submit a valid email address in the ' . $field['label'] . ' field.' );

						}
						break;
					
					default: $valid = $input[$field['id']];

				} // End of switch

				//if ( 'wpmi_uploader_gallery' == $field['id'] ) { wp_die( 'yes' ); }

				$cleaned[$field['id']] = $this->sanitize( $field['type'], $valid );

			} // End of foreach loop

			return $cleaned;

		} // End of validate_options()

/**
 * Display content above the tabs on the settings page
 */
		protected function above_tabs() {

			// Define in class extension

		} // End of above_tabs()

/**
 * Display content below the tabs on the settings page
 */
		protected function below_tabs() {

			// Define in class extension

		} // End of below_tabs()		



/* ==========================================================================
   Add Methods
   ========================================================================== */

/**
 * Adds fields to a settings section
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	get_post_custom()
 * @uses 	make_field()
 *
 * @return 	void
 */
		public function add_fields() {

			if ( empty( $this->fields ) ) { return; }

			foreach ( $this->fields as $field ) {

				$id				= $field['id'];
				$label			= $field['label']; unset( $field['label'] );
				$section 		= $field['section'];
				$setting		= $field['setting'];
				$field 			= $this->set_field_name( $field );
				$field			= $this->set_field_value( $field );
				$field['class'] = $this->maybe_add_classes( $field );
				$field['type'] 	= $this->maybe_change_type( $field );

				add_settings_field( $id, $label, array( $this, 'make_field' ), $setting, $section, $field );

			} // End of foreach loop

		} // End of add_fields()

/**
 * Adds a page to the Dashboard menu
 * 
 * @access 	public
 * @since 	0.1
 *
 * @uses 	add_comments_page()
 * @uses 	add_dashboard_page()
 * @uses 	add_links_page()
 * @uses 	add_media_page()
 * @uses 	add_pages_page()
 * @uses 	add_plugins_page()
 * @uses 	add_posts_page()
 * @uses 	add_options_page()
 * @uses 	add_theme_page()
 * @uses 	add_management_page()
 * @uses 	add_users_page()
 * @uses 	add_submenu_page()
 * @uses 	add_menu_page()
 *
 * @return 	void
 */
		public function add_menu() {

			if ( empty( $this->menu ) ) { return; }

			extract( $this->menu ); // make array keys into variables

			$function = array( $this, 'build_page' ); // assign default function

			switch( $this->menu['top_slug'] ) {

	            case 'comments':	add_comments_page( $page, $title, $cap, $slug, $function); break;
	            case 'dashboard': 	add_dashboard_page( $page, $title, $cap, $slug, $function); break;
	            case 'links':		add_links_page( $page, $title, $cap, $slug, $function); break;
	            case 'media':		add_media_page( $page, $title, $cap, $slug, $function); break;
	            case 'pages':		add_pages_page( $page, $title, $cap, $slug, $function); break;
	            case 'plugins':		add_plugins_page( $page, $title, $cap, $slug, $function); break;
	            case 'posts': 		add_posts_page( $page, $title, $cap, $slug, $function); break;
	            case 'settings':	add_options_page( $page, $title, $cap, $slug, $function); break;
	            case 'theme':		add_theme_page( $page, $title, $cap, $slug, $function ); break;
	            case 'tools':		add_management_page( $page, $title, $cap, $slug, $function ); break;
	            case 'users':		add_users_page( $page, $title, $cap, $slug, $function); break;
	            case 'cpt':			add_submenu_page( $parent, $page, $title, $cap, $slug, $function ); break;
	            default: 			add_menu_page( $page, $title, $cap, $slug, $function, $icon, $position ); break;

			} // End of switch			

		} // End of add_menu()

/**
 * Adds links to the plugin row meta on the plugins admin screen
 *
 * @access 	public
 * @since	0.1
 *
 * @param 	array 		$links 		An array of the links already in the plugin row
 * @param 	string 		$file 		The name of the plugin file
 * 
 * @return 	array 		$links 		An amended array including the additional links
 */			
		public function add_meta_links( $links, $file ) {

			if ( strpos( $file, $this->i18n . '.php' ) !== false ) {
			
				$meta[] = '<a href="http://slushman.com/contact">' . __( 'Plugin support', $this->i18n ) . '</a>';
				$meta[] = '<a href="http://wordpress.org/plugins/' . $this->i18n . '">' . __( 'Rate plugin', $this->i18n ) . '</a>';
				$meta[] = '<a href="http://slushman.com/donate">' . __( 'Donate', $this->i18n ) . '</a>';
			
			} // End of file check
		
			return $links;

		} // End of add_meta_links()

/**
 * Adds sections to settings
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	add_settings_section()
 * 
 * @return 	void
 */
		public function add_sections() {

			if ( empty( $this->sections ) ) { return; }

			foreach ( $this->sections as $section ) {

				if ( empty( $section['desc'] ) ) {

					$callback = '__return_false';

				} else {

					$callback = create_function( '', 'echo "<div class=\"inside\">' . __( $section['desc'], $this->i18n ) . '</div>";' );

				} // End of desc check

				add_settings_section( $section['id'], $section['name'], $callback, $section['setting'] );

			} // End of foreach loop

		} // End of add_sections()

/**
 * Registers settings settings
 *
 * @access 	public
 * @since 	0.1
 *
 * @uses 	register_setting()
 * @uses 	validate_options()
 *
 * @return 	void
 */
		public function add_settings() {

			foreach ( $this->settings as $setting_name ) {

				register_setting( $setting_name, $setting_name, array( $this, 'validate_options' ) );

			} // End of foreach loop

		} // End of add_settings()

/**
 * Adds a link to the plugin settings page to the plugin's listing on the plugins admin screen
 *
 * @access 	public
 * @since	0.1
 *
 * @param 	array 		$links 		An array of the links already on the plugin listing
 * 
 * @uses	get_menu_slug()
 *
 * @return 	array 		$links 		An amended array including the additional links
 */			
		public function add_settings_link( $links ) {

			$text 			= apply_filters( 'link_text_' . $this->i18n, 'Settings' );
			$settings_link 	= sprintf( '<a href="%s">%s</a>', $this->get_menu_slug(), __( $text, $this->i18n ) );
			
			array_unshift( $links, $settings_link );
			
			return $links;

		} // End of add_settings_link()


/* ==========================================================================
   Check Methods
   ========================================================================== */	

/**
 * Checks an array for the required value
 * If the value is not present, dies and displays a "value missing" error message
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param 	array 		$array 		The array containing the value to check
 * @param 	string 		$key 		The required key to check
 * 
 * @return 	mixed 		The value from the checked key
 */
		protected function check_required( $array, $key ) {

			$check = '';

			if ( empty( $array ) ) {

				$check = new WP_Error( "forgot_array", __( "Add the array to the check_required call.", $this->i18n ) );

			}

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot array', $this->i18n ) );

			}

			if ( empty( $array[$key] ) ) {

				$check = new WP_Error( "forgot_{$key}", __( "Add {$key} to the array.", $this->i18n ) );

			}

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot part', $this->i18n ) );

			}

			return $array[$key];

		} // End of check_required()

/**
 * Returns the optional value, otherwise returns the default value
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param 	array 		$array 		The array containing the value to check
 * @param 	string 		$key 		The required key to check
 * 
 * @return 	mixed 		The value from the checked key
 */
		protected function check_optional( $array, $key ) {

			$check = '';

			if ( empty( $array ) ) {

				$check = new WP_Error( "forgot_array", __( "Add the array to the check_optional call.", $this->i18n ) );

			}

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot array', $this->i18n ) );

			}

			$return = '';

			if ( empty( $array[$key] ) ) {

				$return = $this->get_default_value( $key );

			} else {

				$return = $array[$key];

			} // End of empty check

			return $return;

		} // End of check_optional()



/* ==========================================================================
   Get Methods
   ========================================================================== */

/**
 * Returns either the current tab, the default tab, or nothing
 * 
 * @param  	mixed 		$_GET 		The GET global
 * 
 * @return 	string 		Either the current tab, the default tab, or nothing
 */
   	protected function get_current_tab( $get ) {

   		if ( isset( $get['tab'] ) ) {

			$return = $get['tab'];

		} elseif ( empty( $this->menu['tab'] ) ) {

			$return = $this->menu['tab'];

		} else {

			$return = $this->settings[0];

		} // End of tab check

		return $return;

   	} // End of get_current_tab()

/**
 * Return the default value
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param 	string 		$key 	The default value to return
 * 
 * @return 	mixed 		The default value
 */
		protected function get_default_value( $key ) {

			switch( $key ) {

				case 'box' 		: $return = FALSE; break;
				case 'cap'		: $return = 'manage_options'; break;
				case 'link'		: $return = TRUE; break;
				case 'position'	: $return = 24; break;
				default			: $return = ''; break;

			} // End of switch

			return $return;

		} // End of get_default_value()

/**
 * Returns the corrent page slug based on the top slug
 *
 * @access 	protected
 * @since 	0.1
 *
 * @uses 	post_type_exists()
 * 
 * @return 	string 	$menu_slug 		The menu slug
 */
		protected function get_menu_slug() {

			$menu_slug = '';

			switch( $this->menu['top_slug'] ) {

				case 'comments'	 :	$menu_slug .= 'edit-comments.php?page=' . $this->menu['slug']; break;
				case 'dashboard' : 	$menu_slug .= 'index.php?page=' . $this->menu['slug']; break;
				case 'links'	 :	$menu_slug .= 'link-manager.php?page=' . $this->menu['slug']; break;
				case 'media'	 :	$menu_slug .= 'upload.php?page=' . $this->menu['slug']; break;
				case 'pages'	 :	$menu_slug .= 'edit.php?post_type=page&page=' . $this->menu['slug']; break;
				case 'plugins'	 :	$menu_slug .= 'plugins.php?page=' . $this->menu['slug']; break;
				case 'posts'	 : 	$menu_slug .= 'edit.php?page=' . $this->menu['slug']; break;
				case 'settings'	 :	$menu_slug .= 'options-general.php?page=' . $this->menu['slug']; break;
				case 'theme'	 :	$menu_slug .= 'themes.php?page=' . $this->menu['slug']; break;
				case 'tools'	 :	$menu_slug .= 'tools.php?page=' . $this->menu['slug']; break;
				case 'users'	 :	$menu_slug .= 'users.php?page=' . $this->menu['slug']; break;
	            default:
	            	if ( post_type_exists( $this->menu['top_slug'] ) ) {

	            		$menu_slug .= 'edit.php?post_type=';

	            	}

	            	$menu_slug .= $this->menu['top_slug'];
	            	break;

	        } // End of switch

	        return admin_url( $menu_slug );

		} // End of get_menu_slug()

/**
 * Returns the text to use for the menu
 *
 * @access 	protected
 * @since 	0.1
 *
 * @uses 	esc_attr()
 * 
 * @return 	string 		The escaped text for the menu
 */
		protected function get_menu_title() {

			return esc_attr( $this->menu['menu_title'] );

		} // End of get_menu_title()

/**
 * Returns the page title
 *
 * @access 	protected
 * @since 	0.1
 *
 * @uses 	esc_attr()
 * 
 * @return 	string 		The page title text
 */
		protected function get_page_title() {

			$title = apply_filters( 'slushman_wpmi_page_title', esc_attr( $this->menu['page'] ) );

			return '<h1>' . $title . '</h1>';

		} // End of get_page_title()

/**
 * Returns tab links or a page title
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @return 	mixed 		Either the page title text or links to be styled as tabs
 */
		protected function get_tabs( $current_tab ) {

			$return = '';

			if ( empty( $this->tabs ) ) { return ''; }

			foreach ( $this->tabs as $tab ) {

				// Determine active tab
				$active = ( $current_tab == $tab['setting'] ? 'nav-tab-active' : '' );

				// Display the tab
				$return .= '<a href="?page=' . $this->i18n . '&tab=' . $tab['setting'] . '" class="nav-tab ' . $active . '">' . $tab['title'] . '</a>';

			} // End of $tabs foreach loop

			return $return;

		} // End of get_tabs()



/* ==========================================================================
   Maybe Methods
   ========================================================================== */

/**
 * 
 * 
 * @param  [type] $field [description]
 * 
 * @return [type]        [description]
 */
   		public function maybe_add_classes( $field ) {

   			$class = '';

   			switch ( $field['type'] ) {

				case 'color'			: $class = 'slushman_color_picker'; break;
				case 'date'				: $class = 'slushman_date_picker'; break;
				case 'datetime'			: $class = 'slushman_datetime_picker'; break;
				case 'datetime-local'	: $class = 'slushman_datetime_local_picker'; break;
				case 'month'			: $class = 'slushman_month_picker'; break;
				case 'range'			: $class = 'slushman_range_slider'; break;
				case 'time'				: $class = 'slushman_time_picker'; break;
				case 'uploader_gallery'	:
				case 'uploader_single'	: $class = 'slushman_uploader'; break;
				case 'week'				: $class = 'slushman_week_picker'; break;

			} // End of switch

   			if ( empty( $field['class'] ) ) {

   				$return = $class;

   			} else {

   				$return = $field['class'] . ' ' . $class;

   			}

   			return $return;

   		} // End of maybe_add_classes()

/**
 * 
 * 
 * @param  [type] $field [description]
 * 
 * @return [type]        [description]
 */
	   	private function maybe_change_type( $field ) {

	   		$type = $field['type'];

	   		switch ( $field['type'] ) {

				case 'color'			:
				case 'date'				:
				case 'datetime'			:
				case 'datetime-local'	:
				case 'time'				:
				case 'week'				: $type = 'text'; break;

			} // End of switch

			return $type;

	   	} // End of maybe_change_type()



/* ==========================================================================
   Set Methods
   ========================================================================== */

/**
 * Sets the fields class variables
 *
 * The only required items here are setting and section.
 * Field-specific items are checked in the make_field class.
 * 
 * @param [type] $fields [description]
 */
		protected function set_fields( $fields ) {

			$reqs = array( 'setting', 'section' );

			foreach ( $fields as $field ) {

				$final = array();

				foreach ( $reqs as $req ) {

					$final[$req] = $this->check_required( $field, $req );

				} // End of $reqs foreach loop

				$temp = $field;

				unset( $temp['setting'] );
				unset( $temp['section'] );

				$this->fields[] = array_merge( $final, $temp );

			} // End of $sections foreach loop

		} // End of set_fields()

/**
 * Adds a field name to a field's array
 * 
 * @param 	array 		$field 		An array of data for a field
 *
 * @return 	array 		$field 		The new field array
 */
		protected function set_field_name( $field ) {

			if ( !isset( $field['name'] ) || empty( $field['name'] ) ) {

				$field['name'] = $field['setting'] . '[' . $field['id'] . ']';

			}

			return $field;

		} // End of set_field_name()

/**
 * Adds a field value to a field's array
 * 
 * @param 	array 		$field 		An array of data for a field
 *
 * @return 	array 		$field 		The new field array
 */
		protected function set_field_value( $field ) {

			$current = $this->{$field['setting']}[$field['id']];

			if ( 'date' == $field['type'] && !empty( $current ) ) {

				$field['value'] = date( 'm/d/Y', $current );

			} elseif ( ( 'datetime' == $field['type'] || 'datetime-local' == $field['type'] ) && !empty( $current ) ) {

				$field['value'] = date( 'm/d/Y g:i a', $current );

			} elseif ( 'time' == $field['type'] && !empty( $current ) ) {

				$field['value'] = date( 'g:i a', $current );

			} elseif ( 'week' == $field['type'] && !empty( $current ) ) {

				$field['value'] = date( 'W', $current );

			} else {

				$field['value'] = $current;

			}

			return $field;

		} // End of set_field_value()

/**
 * Sets the menu class variable
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param 	array 		$menu 		An array of menu data
 *
 * @uses 	check_required()
 * @uses 	check_optional()
 *
 * @return 	void
 */
		protected function set_menu( $menu ) {

			$final	= array();
			$reqs	= array( 'cap', 'page', 'slug', 'title', 'top_slug' );
			$opts 	= array( 'icon', 'link', 'parent', 'position', 'tab' );

			foreach ( $reqs as $req ) {

				$final[$req] = $this->check_required( $menu, $req );

			} // End of foreach loop

			foreach ( $opts as $opt ) {

				$final[$opt] = $this->check_optional( $menu, $opt );

			} // End of foreach loop

			$this->menu = $final;

		} // End of set_menu()

/**
 * Sets the sections class variable
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param 	array 		$sections 		An array of section data
 *
 * @uses 	check_required()
 * @uses 	check_optional()
 * 
 * @return 	void
 */
		protected function set_sections( $sections ) {

			$reqs = array( 'setting', 'id', 'name' );
			$opts = array( 'box', 'desc' );

			foreach ( $sections as $section ) {

				$final = array();

				foreach ( $reqs as $req ) {

					$final[$req] = $this->check_required( $section, $req );

				} // End of $reqs foreach loop

				foreach ( $opts as $opt ) {

					$final[$opt] = $this->check_optional( $section, $opt );

				} // End of foreach loop

				$this->sections[] = $final;

			} // End of $sections foreach loop

		} // End of set_sections()

/** 
 * Sets the settings class variable
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param  	array 		$settings 		The settings to be created
 *
 * @return 	void
 */
		protected function set_settings( $settings ) {

			$check = '';

			if ( empty( $settings ) ) {

				$check = new WP_Error( "forgot_setting", __( "You need at least one setting.", $this->i18n ) );

			}

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot setting', $this->i18n ) );

			}

			$this->settings = $settings;

		} // End of set_settings()

/**
 * Set the tabs class variable
 * 
 * @param [type] $tabs [description]
 */
		protected function set_tabs( $tabs ) {

			if ( empty( $tabs ) ) { return; }

			$reqs = array( 'setting', 'title' );

			foreach ( $tabs as $tab ) {

				$final = array();

				foreach ( $reqs as $req ) {

					$final[$req] = $this->check_required( $tab, $req );

				} // End of $reqs foreach loop

				$this->tabs[] = $final;

			} // End of $sections foreach loop

		} // End of set_tabs()



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

			if ( ! isset( $this->i18n ) ) { return; }

			$screen = get_current_screen();

			if ( 'settings_page_' . $this->i18n == $screen->id ) {

				wp_enqueue_script( $this->i18n .'-admin-script', plugins_url( 'js/admin.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), $this->version, TRUE );

				wp_enqueue_script( $this->i18n .'-toolkit-script', plugins_url( 'js/toolkit.min.js', __FILE__ ), array( 'jquery', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->version, TRUE );

				// https://github.com/KidSysco/jquery-ui-month-picker
				wp_enqueue_script( 'monthpicker', plugins_url( 'js/monthpicker.2.1.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-button' ), $this->version, TRUE );

				// https://github.com/trentrichardson/jQuery-Timepicker-Addon
				wp_enqueue_script( 'timepicker', plugins_url( 'js/datetimepicker.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), $this->version, TRUE );

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

			if ( ! isset( $this->i18n ) ) { return; }

			$screen = get_current_screen();

			if ( 'settings_page_' . $this->i18n == $screen->id ) {

				wp_enqueue_style( $this->i18n .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
				wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'monthpicker', plugins_url( 'css/monthpicker.css', __FILE__ ), array(), $this->version );
			
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
 * @access 	public
 * @since 	0.1
 * 
 * @param 	array 		$field 		An array of args for the field
 *
 * @uses 	Slushman_Toolkit_Make_Fields
 * @uses 	create_field()
 * 
 * @return 	mixed 		a formatted HTML input field
 */
		public function make_field( $field ) {

			$make_field	= new Slushman_Toolkit_Make_Field( $field );

			echo $make_field->create_field();

		} // End of make_field()
	   	
/**
 * Returns data sanitized by the Slushman_Make_Sanitized class
 *
 * @access 	public
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
		public function sanitize( $type, $data ) {

			$sanitize	= new Slushman_Make_Sanitized( array( 'type' => $type, 'data' => $data ) );
			$return		= $sanitize->clean();

			unset( $sanitize );

			return $return;

		} // End of sanitize()



	} // End of class

} // End of class check

?>
<?php 

/**
 * A class for creating custom post types
 *
 * Example of use:
 *
 * // Make Custom Post Type - required args only
 * $cpt_args['i81n']		= 'slushman-portfolio';
 * $cpt_args['plural']		= 'Portfolio Items';
 * $cpt_args['post_type']	= 'slushman_portfolio';
 * $cpt_args['single']		= 'Portfolio Item';
 * $cpt_args['cpt_args']	= array();
 * 
 * $cpt = new Slushman_Make_Custom_Post_Type( $cpt_args );
 * $cpt->create_cpt();
 *
 * @package   Slushman Toolkit
 * @version   0.1
 * @since     0.1
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @link      http://slushman.com/plugins/slushman-toolkit
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( !class_exists( 'Slushman_Make_Custom_Post_Type' ) ) {

class Slushman_Make_Custom_Post_Type {

/**
 * Metaboxes for the custom post type
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
	private $boxes = array();

/**
 * The capacity type
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $cap_type = '';

/**
 * Arguments for the custom post type
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
	private $args = array();

/**
 * Help menu options
 *
 * Params:
 *   
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
	private $help = array();	

/**
 * The internationalization domain
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $i18n = '';

/**
 * The name of this CPT
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $post_type = '';

/**
 * The plural version of a CPT item
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $plural = '';

/**
 * The singular version of a CPT item
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $single = '';

/**
 * Taxonomies related to this CPT
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
	private $taxes = array();	

/**
 * Sets class variables
 * 
 * Params:
 *   args: an array, empty or containing optional arguments for the CPT, see link for details
 *   boxes: an array of info for metaboxes for this CPT
 *   help: 
 *   i18n: the i18n domain
 *   plural: the name of a group of these CPTs (ie: Locations, Items, Events)
 *   post_type: the name of the CPT
 *   single: the singular version of the CPT name (ie: Location, Item, Event)
 *
 * @access 	public
 * @since 	0.1
 *
 * @param 	array 		$params		An array of parameters
 *
 * @uses 	WP_Error
 * @uses 	is_wp_error()
 * @uses 	wp_die()
 * @uses 	get_error_message()
 * @uses 	Slushman_Toolkit
 * @uses 	parse_args_recursive()
 * @uses 	_get_defaults()
 *
 * @return 	void
 */
	public function __construct( $params ) {

		$reqs = array( 'i18n', 'post_type', 'plural', 'single' );

		foreach ( $reqs as $req ) {

			$check = '';

			if ( empty( $params[$req] ) ) {

				$check = new WP_Error( "forgot_{$req}", __( "Add {$req} to the array for this custom post type.", 'slushfolio' ) );

			}

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot CPT part', 'slushfolio' ) );

			}

			$this->{$req} = $params[$req];

		} // End of foreach loop

		$opts = array( 'args', 'boxes', 'help', 'taxes' );

		foreach ( $opts as $opt ) {

			if ( !empty( $params[$opt] ) ) {

				$this->{$opt} = $params[$opt];

			}

		} // End of foreach loop

		$toolkit	= new Slushman_Toolkit();
		$this->args	= $toolkit->parse_args_recursive( $this->args, $this->_get_defaults() );

		add_action( 'add_meta_boxes', array( $this, 'create_meta_boxes' ) );

	} // End of __construct()

/**
 * Registers a new custom post type
 *
 * @link 	http://codex.wordpress.org/Function_Reference/register_post_type
 * @access 	public
 * @since 	0.1
 * 
 * @param 	An array of parameters for constructing a custom post type
 *
 * @uses 	register_post_type()
 *
 * @return 	void
 */
	public function create_cpt() {

		register_post_type( $this->post_type, $this->args );

		$this->create_taxes();

	} // End of create_cpt()

/**
 * Returns my default custom post type settings
 *
 * @access 	private
 * @since 	0.1
 * 
 * @return 	array 	An array of custom post type defaults
 */
	private function _get_defaults() {

		$cap_type = 'post';

		$opts['can_export']								= TRUE;
		$opts['capability_type']						= $cap_type;
		$opts['description']							= '';
		$opts['exclude_from_search']					= FALSE;
		$opts['has_archive']							= FALSE;
		$opts['hierarchical']							= FALSE;
		$opts['map_meta_cap']							= TRUE;
		$opts['menu_icon']								= '';
		$opts['menu_position']							= 25;
		$opts['public']									= TRUE;
		$opts['publicly_querable']						= TRUE;
		$opts['query_var']								= TRUE;
		$opts['register_meta_box_cb']					= array( $this, 'create_meta_boxes' );
		$opts['rewrite']								= FALSE;
		$opts['show_in_admin_bar']						= TRUE;
		$opts['show_in_menu']							= TRUE;
		$opts['show_in_nav_menu']						= TRUE;
		$opts['show_ui']								= TRUE;
		$opts['supports']								= array( 'title', 'editor', 'thumbnail' );
		$opts['taxonomies']								= array();

		$opts['capabilities']['delete_others_posts']	= "delete_others_{$cap_type}s";
		$opts['capabilities']['delete_post']			= "delete_{$cap_type}";
		$opts['capabilities']['delete_posts']			= "delete_{$cap_type}s";
		$opts['capabilities']['delete_private_posts']	= "delete_private_{$cap_type}s";
		$opts['capabilities']['delete_published_posts']	= "delete_published_{$cap_type}s";
		$opts['capabilities']['edit_others_posts']		= "edit_others_{$cap_type}s";
		$opts['capabilities']['edit_post']				= "edit_{$cap_type}";
		$opts['capabilities']['edit_posts']				= "edit_{$cap_type}s";
		$opts['capabilities']['edit_private_posts']		= "edit_private_{$cap_type}s";
		$opts['capabilities']['edit_published_posts']	= "edit_published_{$cap_type}s";
		$opts['capabilities']['publish_posts']			= "publish_{$cap_type}s";
		$opts['capabilities']['read_post']				= "read_{$cap_type}";
		$opts['capabilities']['read_private_posts']		= "read_private_{$cap_type}s";

		$opts['labels']['add_new']						= __( "Add New {$this->single}", $this->i18n );
		$opts['labels']['add_new_item']					= __( "Add New {$this->single}", $this->i18n );
		$opts['labels']['all_items']					= __( $this->plural, $this->i18n );
		$opts['labels']['edit_item']					= __( "Edit {$this->single}" , $this->i18n);
		$opts['labels']['menu_name']					= __( $this->plural, $this->i18n );
		$opts['labels']['name']							= __( $this->plural, $this->i18n );
		$opts['labels']['name_admin_bar']				= __( $this->single, $this->i18n );
		$opts['labels']['new_item']						= __( "New {$this->single}", $this->i18n );
		$opts['labels']['not_found']					= __( "No {$this->plural} Found", $this->i18n );
		$opts['labels']['not_found_in_trash']			= __( "No {$this->plural} Found in Trash", $this->i18n );
		$opts['labels']['parent_item_colon']			= __( "Parent {$this->plural} :", $this->i18n );
		$opts['labels']['search_items']					= __( "Search {$this->plural}", $this->i18n );
		$opts['labels']['singular_name']				= __( $this->single, $this->i18n );
		$opts['labels']['view_item']					= __( "View {$this->single}", $this->i18n );

		$opts['rewrite']['ep_mask']						= EP_PERMALINK;
		$opts['rewrite']['feeds']						= FALSE;
		$opts['rewrite']['pages']						= TRUE;
		$opts['rewrite']['slug']						= __( strtolower( $this->plural ), $this->i18n );
		$opts['rewrite']['with_front']					= FALSE;

		return $opts;

	} // End of _get_defaults()

/**
 * Create taxonomies
 *
 * @access 	private
 * @since 	0.1
 * 
 * @uses 	make_tax
 * 
 * @return 	void
 */			
	private function create_taxes() {

		if ( empty( $this->taxes ) ) { return; }

		foreach ( $this->taxes as $key => $tax ) {

			$tax['i18n']		= $this->i18n;
			$tax['post_type']	= $this->post_type;
			$tax['taxonomy']	= $key;

			$this->make_tax( $tax );

		} // End of foreach loop

	} // End of create_taxes()

/**
 * Create Metaboxes for the Custom Post Type pages
 *
 * @access 	private
 * @since 	0.1
 *
 * @param 	object 		The post object
 *
 * @uses 	make_metabox
 * 
 * @return 	void
 */
	public function create_meta_boxes( $post_obj ) {

		if ( empty( $this->boxes ) ) { return; }

		foreach ( $this->boxes as $key => $metabox ) {

			$metabox['i18n']		= $this->i18n;
			$metabox['id']			= $key;
			$metabox['post_type']	= $this->post_type;

			$this->make_metabox( $metabox );

		} // End of foreach loop

	} // End of create_meta_boxes()



/* ==========================================================================
   Injection Containers

   These remove any dependency and coupling of the public functions from the
   other classes needed by this class.
   ========================================================================== */

/**
 * Creates a metabox from the Slushman_Make_MetaBox class
 *
 * @access 	private
 * @since 	0.1
 * 
 * @param 	array 		$box 		An array of args for the metabox
 *
 * @uses 	Slushman_Make_MetaBox
 * @uses 	create_metabox()
 * 
 * @return 	void
 */
	private function make_metabox( $box ) {

		$make_box = new Slushman_Make_MetaBox( $box );

		$make_box->create_metabox();

	} // End of make_metabox()

/**
 * Creates a taxonomy from the Slushman_Make_Taxonomy class
 *
 * @access 	private
 * @since 	0.1
 * 
 * @param 	array 		$tax 		An array of args for the taxonomy
 *
 * @uses 	Slushman_Make_Taxonomy
 * @uses 	create_taxonomy()
 * 
 * @return 	void
 */
	private function make_tax( $tax ) {

		$make_tax = new Slushman_Make_Taxonomy( $tax );

		$make_tax->create_taxonomy();

	} // End of make_tax()



} // End of class

} // End of class check

?>
<?php 

/**
 * A class for creating taxonomies for custom post types
 *
 * Example of use:
 *
 * // Make Taxonomies for Custom Post Type
 * foreach ( $cpt_args['tax_names'] as $tax ) {
 *
 * 	 $cpt->create_tax( $tax );
 * 
 * }
 *
 * $taxes
 *
 * @package   Slushman Toolkit
 * @version   0.1
 * @since     0.1
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @link      http://slushman.com/plugins/slushman-toolkit
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( !class_exists( 'Slushman_Make_Taxonomy' ) ) {

class Slushman_Make_Taxonomy {

/**
 * Settings for the taxonomy
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
	private $args = array();

/**
 * The internationalization domain
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $i18n = '';

/**
 * Settings for the taxonomy metabox
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
	private $metabox = array( 'type' => '' );

/**
 * Should the metabox have a nonce field or no
 *
 * @access 	private
 * @since 	0.1
 * @var 	boolean
 */
	private $nonce = FALSE;

/**
 * The plural name of the taxonomy
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $plural = '';

/**
 * The name of the related CPT
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $post_type = '';

/**
 * The singular name of the taxonomy
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $single = '';

/**
 * Name of the taxonomy
 *
 * @access 	private
 * @since 	0.1
 * @var 	string
 */
	private $taxonomy = '';

/**
 * Constructor function, use create_tax() instead.
 *
 * Params:
 *   args: optional arguments for the taxonomy
 * 	 i18n: the i18n domain
 *   metabox: Optional settings for the taxonomy metabox
 *   nonce: should the metabox have a nonce or not, if so, what's the name?
 *   plural: the name of a group of these CPTs (ie: Locations, Items, Events)
 *   post_type: the name of the CPT
 *   single: the singular version of the CPT name (ie: Location, Item, Event)
 * 	 taxonomy: the name of the taxonomy
 *
 * @access 	public
 * @since 	0.1
 *
 * @param 	An array of parameters for constructing a taxonomy
 *
 * @uses 	WP_Error
 * @uses 	is_wp_error()
 * @uses 	wp_die()
 * @uses 	get_error_message()
 * @uses 	Slushman_Toolkit
 * @uses 	parse_args_recursive()
 * @uses 	_get_defaults
 *
 * @return 	void
 */
	public function __construct( $params ) {

		$reqs = array( 'i18n', 'plural', 'post_type', 'single', 'taxonomy' );

		foreach ( $reqs as $req ) {

			$check = '';

			if ( empty( $params[$req] ) ) {

				$check = new WP_Error( "forgot_{$req}", __( "Add {$req} to the array for this taxonomy.", 'slushfolio' ) );

			}			

			if ( is_wp_error( $check ) ) {

				wp_die( $check->get_error_message(), __( 'Forgot taxonomy part', 'slushfolio' ) );

			}

			$this->{$req} = $params[$req];

		} // End of foreach loop

		$toolkit = new Slushman_Toolkit();

		if ( !empty( $params['args'] ) ) {

			$this->args = $toolkit->parse_args_recursive( $params['args'], $this->_get_defaults() );

		}

		if ( !empty( $params['metabox'] ) ) {

			$this->metabox = $toolkit->parse_args_recursive( $params['metabox'], $this->metabox );

		}

		if ( !empty( $params['nonce'] ) ) {

			$this->nonce = $params['nonce'];

		}

	} // End of __construct()

/**
 * Creates a taxonomy
 *
 * @link 	https://codex.wordpress.org/Function_Reference/register_taxonomy
 * @access 	public
 * @since 	0.1
 * 
 * @uses 	register_taxonomy()
 * 
 * @return 	void
 */
	public function create_taxonomy() {

		register_taxonomy( $this->taxonomy, $this->post_type, $this->args );
	
	} // End of create_taxonomy()

/**
 * Returns my default taxonomy settings
 *
 * @access 	private
 * @since 	0.1
 * 
 * @return 	array 	An array of taxonomy defaults
 */
	function _get_defaults() {

		$opts['hierarchical']							= FALSE;
		$opts['meta_box_cb']							= array( $this, 'meta_box_content' );
		$opts['public']									= TRUE;
		$opts['query_var']								= TRUE;
		$opts['rewrite']								= FALSE;
		$opts['show_in_admin_column']					= TRUE;
		$opts['show_in_nav_menu']						= TRUE;
		$opts['show_tagcloud']							= TRUE;
		$opts['show_ui']								= TRUE;
		$opts['sort']									= FALSE;
		
		// Come back to this later
		//$opts['update_count_callback']					= array( $this, 'custom_update_count' );

		$opts['capabilities']['assign_terms']			= 'edit_posts';
		$opts['capabilities']['delete_terms']			= 'manage_categories';
		$opts['capabilities']['edit_terms']				= 'manage_categories';
		$opts['capabilities']['manage_terms']			= 'manage_categories';

		$opts['labels']['add_new_item']					= __( "Add New {$this->single}", $this->i18n );
		$opts['labels']['add_or_remove_items']			= __( "Add or remove {$this->plural}",$this->i18n );
		$opts['labels']['all_items']					= __( $this->plural, $this->i18n );
		$opts['labels']['choose_from_most_used']		= __( "Choose from the most used {$this->plural}", $this->i18n );
		$opts['labels']['edit_item']					= __( "Edit {$this->single}" , $this->i18n);
		$opts['labels']['menu_name']					= __( $this->plural, $this->i18n );
		$opts['labels']['name']							= __( $this->plural, $this->i18n );
		$opts['labels']['new_item_name']				= __( "New {$this->single}", $this->i18n );
		$opts['labels']['not_found']					= __( "No {$this->plural} Found", $this->i18n );
		$opts['labels']['parent_item']					= __( "Parent {$this->plural} :", $this->i18n );
		$opts['labels']['parent_item_colon']			= __( "Parent {$this->plural} :", $this->i18n );
		$opts['labels']['popular_items']				= __( "Popular {$this->plural}", $this->i18n );
		$opts['labels']['search_items']					= __( "Search {$this->plural}", $this->i18n );
		$opts['labels']['separate_items_with_commas']	= __( "Separate {$this->plural} with commas", $this->i18n );
		$opts['labels']['singular_name']				= __( $this->single, $this->i18n );
		$opts['labels']['update_item']					= __( "Update {$this->single}", $this->i18n );
		$opts['labels']['view_item']					= __( "View {$this->single}", $this->i18n );

		$opts['rewrite']['ep_mask']						= EP_PERMALINK;
		$opts['rewrite']['hierarchial']					= FALSE;
		$opts['rewrite']['slug']						= __( strtolower( $this->plural ), $this->i18n );
		$opts['rewrite']['with_front']					= FALSE;

		return $opts;

	} // End of _get_defaults()

/**
 * Create Metaboxes for the Custom Post Type pages
 *
 * @access 	private
 * @since 	0.1
 *
 * @param 	object 		$post_obj		The post object
 *
 * @uses 	get_terms()
 * @uses 	make_field()
 * 
 * @return [type] [description]
 */
	public function meta_box_content( $post_obj ) {

		// If type is empty, return
		if ( empty( $this->metabox['type'] ) ) { return; }

		$this->add_nonce_field();

		$terms = get_terms( $this->taxonomy, array( 'get' => 'all' ) );

		//Slushman_Toolkit::get_instance()->print_array( $terms );

		if ( 'radios' == $this->metabox['type'] ) {

			$field['class']			= $this->taxonomy . '_radios';
			$field['id']			= $this->taxonomy . '_radios';
			$field['type']			= 'radio';

		} elseif ( 'select' == $this->metabox['type'] ) {

			$field['class']			= $this->taxonomy . '_select_menu';
			$field['id']			= $this->taxonomy . '_select_menu';
			$field['type']			= 'select';

		}

		$field['selections'] = array();

		foreach ( $terms as $term ) {

			$field['selections'][] = array( 'label' => $term->name, 'value' => $term->term_id );

		} // End of foreach loop

		echo $this->make_field( $field );

	} // End of meta_box_content()

/**
 * Updates the count
 * 
 * @return [type] [description]
 */
	public function custom_update_count() {



   	} // End of custom_update_count()



/* ==========================================================================
   Private Methods
   ========================================================================== */

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
 * @uses 	Slushman_Toolkit_Make_Field
 * @uses 	create_field()
 * 
 * @return 	mixed 		a formatted HTML input field
 */
	private function make_field( $field ) {

		$make_field	= new Slushman_Toolkit_Make_Field( $field );

		return $make_field->create_field();

	} // End of make_field()


} // End of class

} // End of class check

?>
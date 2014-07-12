<?php 

/**
 * A class for creating columns for custom post types
 *
 * Example of use:
 *
 * // Make Custom Post Type - required args only
 * $col_args[''] = ;
 * 
 * $col = new Slushman_Make_CPT_Columns( $col_args );
 * $col->create_columns();
 *
 * @package   Slushman Toolkit
 * @version   0.1
 * @since     0.1
 * @author    Slushman <chris@slushman.com>
 * @copyright Copyright (c) 2014, Slushman
 * @link      http://slushman.com/plugins/slushman-toolkit
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
if ( !class_exists( 'Slushman_Make_CPT_Columns' ) ) {

	class Slushman_Make_CPT_Columns {

/**
 * Holds the instance of this class.
 *
 * @access 	private
 * @since  	0.1
 * @var    	object
 */
		private static $instance;

/**
 * Custom columns for a custom post type
 *
 * Contents:
 * 	 name: name of the column
 * 	 slug: slug for the column
 * 	 sort: boolean, TRUE for sortable, FALSE for not, FALSE is default
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
		private $columns;

/**
 * Name of the custom post type
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
		private $cpt;

/**
 * The output of each column
 *
 * @access 	private
 * @since 	0.1
 * @var 	array
 */
		private $output;

/**
 * Constructor function
 *
 * Required Params:
 *   columns: an array with the name and slug of each column and if its sortable
 *   cpt: the name of the custom post type
 *   
 * @access 	public
 * @since 	0.1
 *
 * @param 	array 	An array of parameters, see details above
 *
 * @link 	http://codex.wordpress.org/Function_Reference/register_post_type
 */
		public function __construct( $params ) {

			// check params here - return error(s) if these items aren't assigned:
			// i18n, post_type, options->labels->name, options->labels->singular_name

			$requires = array( 'columns', 'cpt' );
			foreach ( $requires as $required ) {

				$this->{$required} = $params[$required];

			} // End of foreach loop

			add_filter( "manage_edit-{$this->cpt}_columns", array( $this, 'create_columns' ) );
			add_action( "manage_{$this->cpt}_posts_custom_column", array( $this, 'create_column_output' ), 10, 2 );
			add_filter( "manage_edit-{$this->cpt}_sortable_columns", array( $this, 'sortable_columns' ) );

		} // End of __construct()



/* ==========================================================================
   Custom Columns Methods
   ========================================================================== */

/**
 * Create columns for the edit CPT page
 *
 * @access 	protected
 * @since 	0.1
 *
 * @param 	array 	An array containing the existing columns 
 * 
 * @return 	array 	An array containing the new columns
 */
		protected function create_columns( $cols ) {

			$cols['cb'] = '<input type="checkbox" />';

			foreach ( $this->columns as $column ) {

				$cols[$column['slug']] = __( $column['name'], $this->i18n ); 

			} // End of foreach loop

			return $cols;

		} // End of create_columns()

/**
 * Fills the content of each cell within the column
 *
 * @access 	protected
 * @since 	0.1
 * 
 * @param  	int 	$post_id 	The ID # of the post
 * @param  	string 	$column  	The column slug
 * @param 	array 	$args 		Optional arguments
 * 
 * @return [type]          [description]
 */
		protected function create_column_output( $column, $post_id ) {

			if ( $this->output['type'] == 'post_meta' ) {

				$meta = get_post_meta( $post_id, $column, true );

				if ( $this->output['linked'] == TRUE ) {

					echo '<a href="' . $meta . '">' . $meta. '</a>';

				} else {

					echo $meta;

				} // End of linked check
				
			} // End of type check

		} // End of create_column_output()

/**
 * Make these columns sortable
 *
 * @return array The sortable columns for this custom post type
 */
		protected function sortable_columns( $columns ) {

			foreach ( $this->columns as $column ) {

				if ( $column['sort'] ) {

					$columns[$column['slug']] = $column['slug'];

				}

			} // End of foreach loop

			return $columns;

		} // End of sortable_columns()

/**
 * [get_data_from_postmeta description]
 * 
 * @param  [type] $post_id  [description]
 * @param  [type] $meta_key [description]
 * 
 * @return [type]           [description]
 */
		protected function get_data_from_postmeta( $post_id, $meta_key ) {

			if ( empty( $post_id ) || empty( $meta_key ) ) { return; }

			return get_post_meta( $post_id, $meta_key, true );

		} // End of get_data_from_postmeta



	} // End of class

} // End of class check
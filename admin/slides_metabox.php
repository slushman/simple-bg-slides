<?php

/**
 * Simple BG Slides
 *
 * This is a class for creating metaboxes within the admin.
 *
 * @package   Simple BG Slides
 * @author    Slushman <chris@slushman.com>
 * @license   GPL-2.0+
 * @link      http://slushman.com/plugins/simple-bg-slides
 * @copyright Copyright (c) 2014, Slushman
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) { die; }

require_once( plugin_dir_path( __FILE__ ) . '../toolkit/make_metabox.php' );

if ( !class_exists( 'Simple_BG_Slides_Metabox' ) ) {

	class Simple_BG_Slides_Metabox extends Slushman_Make_MetaBox {

/**
 * Instance of this class.
 *
 * @access 	protected
 * @since 	0.1
 * @var 	object
 */
		protected static $instance = null;

/**
 * The Constructor
 *
 * @access 	public
 * @since 	0.1
 *
 * @return 	void
 */
		public function __construct() {

			$plugin									= Simple_BG_Slides::get_instance();
			$this->i18n								= $plugin->get_i18n();	
			$this->version							= $plugin->get_version();		
			
			$args['args']							= '';
			$args['class']							= '';
			$args['context']						= 'side';
			$args['id']								= 'sbgs_slides';
			$args['nonce']							= 'sbgs_slides';
			$args['post_type']						= 'page';
			$args['priority']						= 'default';
			$args['title']							= 'Background Slideshow';
			
			$i										= 0;
			$args['fields'][$i]['atts']['id']		= '_sbgs_uploader_gallery';
			$args['fields'][$i]['atts']['name']		= '_sbgs_uploader_gallery';
			$args['fields'][$i]['type']				= 'hidden';
			$args['fields'][$i]['value']			= '';
			$i++;
			
			// speed
			$args['fields'][$i]['atts']['class']	= 'sbgs_mbfield';
			$args['fields'][$i]['atts']['id']		= 'sbgs_speed_field';
			$args['fields'][$i]['atts']['name']		= 'sbgs_speed_field';
			$args['fields'][$i]['atts']['step']		= 250;
			$args['fields'][$i]['desc']				= 'Speed of the transitions (in milliseconds)';
			$args['fields'][$i]['label']			= 'Transition Speed';
			$args['fields'][$i]['type']				= 'number';
			$args['fields'][$i]['value']			= 1000;
			$i++;
			
			// timeout
			$args['fields'][$i]['atts']['class']	= 'sbgs_mbfield';
			$args['fields'][$i]['atts']['id']		= 'sbgs_timeout_field';
			$args['fields'][$i]['atts']['name']		= 'sbgs_timeout_field';
			$args['fields'][$i]['atts']['step']		= 250;
			$args['fields'][$i]['desc']				= 'How long slides are displayed (in milliseconds)';
			$args['fields'][$i]['label']			= 'Slide Display Time';
			$args['fields'][$i]['type']				= 'number';
			$args['fields'][$i]['value']			= 4000;
			$i++;
			
			$this->setup( $args );
			$this->add_actions();

		} // End of __construct()

/**
 * Return an instance of this class.
 *
 * @access 	public
 * @since 	0.1
 *
 * @return 	object 		A single instance of this class.
 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {

				self::$instance = new self;
			
			}

			return self::$instance;
		
		} // End of get_instance()

/**
 * Display content inside the metabox
 *
 * @access 	public
 * @since 	0.1
 *
 * @param 	object 		$post-obj 		The post object
 * @param 	object 		$metabox 		The metabox object
 *
 * @uses 	get_post_custom()
 * @uses 	wp_get_attachment_image_src()
 * @uses 	esc_url()
 * @uses 	get_edit_post_link()
 * @uses 	add_fields()
 *
 * @return  mixed 		Output for slides metabox
 */
		public function metabox_content( $post_obj, $metabox ) {

			$post_id	= $post_obj->ID;
			$custom		= get_post_custom( $post_id );

			//echo '<pre>'; print_r( $custom ); echo '</pre>';

			?><div id="sbgs_uploader_container">
				<ul class="sbgs_thumbnails"><?php

			if ( !empty( $custom['_sbgs_uploader_gallery'][0] ) ) {

				$images	= explode( ',', $custom['_sbgs_uploader_gallery'][0] );

				foreach ( $images as $id ) { 

					if ( !empty( $id ) ) {

						$image = wp_get_attachment_image_src( absint( $id ), 'thumbnail' );
					
					}

					$url = ( !empty( $image ) && isset( $image[0] ) ? $image[0] : '' );

					?><li class="sbgs_item" data-attachment_id="<?php echo $id; ?>">
						<img class="sbgs_thumbnail" src="<?php echo esc_url( $url ); ?>"/>
						<ul class="actions">
							<li>
								<a href="#" class="delete dashicons-no-alt" title="<?php _e( 'Delete image', $this->i18n ); ?>">&times;</a>
							</li>
							<li>
								<a href="<?php echo get_edit_post_link( $id ); ?>" class="edit dashicons-edit" title="<?php _e( 'Edit Image', $this->i18n ); ?>"><?php _e( 'Edit image', $this->i18n ); ?></a>
							</li>
						</ul>
					</li><?php

				} // End of foreach loop

			} // End of images check

				?></ul><!-- End of .sbgs_thumbnails -->
			</div><!-- End of #sbgs_uploader_container -->
			<p class="sbgs_uploader_link"
				><a href="#" class="add_slides"><?php _e( 'Add Slides', $this->i18n ); ?></a>
			</p>
			<div class="sbgs_options"><?php

			$this->add_fields( $post_id );

			?></div><?php

		} // End of metabox_content()



	} // End of class

} // End of class check

?>
jQuery(document).ready(function($){

	/**
	 * The following code deals with the custom media modal frame.  It is a modified version 
	 * of Thomas Griffin's New Media Image Uploader example plugin.
	 *
	 * @link        https://github.com/thomasgriffin/New-Media-Image-Uploader
	 * @license     http://www.opensource.org/licenses/gpl-license.php
	 * @author      Thomas Griffin <thomas@thomasgriffinmedia.com>
	 * @copyright   Copyright 2013 Thomas Griffin
	 */
	var sbgs_uploader;
	var $image_gallery_ids = $( '#_sbgs_uploader_gallery' );
	var $gallery_images = $( '#sbgs_uploader_container ul.sbgs_thumbnails' );

	$( '.add_gallery_images' ).click(

		function( event ){

			event.preventDefault();

			var image_ids = $image_gallery_ids.val();

			if ( sbgs_uploader ) {
				sbgs_uploader.open();
				return;
			}

			sbgs_uploader = wp.media.frames.sbgs_uploader = wp.media({
				button: {
					text: 'Choose Images',
				},
				className: 'media-frame sbgs-uploader-frame',
				frame: 'select',
				library: {
					type: 'image'
				},
				multiple: true,
				title: 'Choose Images'
			});

			sbgs_uploader.on( 'select', function() {

				var selection = sbgs_uploader.state().get( 'selection' );

				selection.map( function( image ) {

					image = image.toJSON();

					console.log(image);

					if ( image.id ) {

						image_ids = image_ids ? image_ids + "," + image.id : image.id;

						$gallery_images.append( '' +
							'<li class="sbgs_item" data-attachment_id="' + image.id + '">' +
								'<img src="' + image.sizes.thumbnail.url + '" class="sbgs_thumbnail" />' +
								'<ul class="actions">' +
									'<li><a href="#" class="delete" title="Delete Image">&times;</a></li>' +
								'</ul>' +
							'</li>'
						);
					
					} // End of image ID check

				}); // End of selection.map()

				$image_gallery_ids.val( image_ids );
			
			}); // End of sbgs_uploader.on() 

			sbgs_uploader.open();
		
		} // End of event()

	);

	// Image ordering
	$gallery_images.sortable({
		items: 'li.sbgs_item',
		cursor: 'move',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'uploader-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css( 'background-color','#f6f6f6' );
		},
		stop:function(event,ui){
			ui.item.removeAttr( 'style' );
		},
		update: function(event, ui) {
			var image_ids = '';
			$( '.sbgs_thumbnails li.sbgs_item' ).css( 'cursor','default' ).each(
				function() {
					var image_id = jQuery(this).attr( 'data-attachment_id' );
					image_ids = image_ids ? image_ids + "," + image_id : image_id;
				}
			);
			
			$image_gallery_ids.val( image_ids );
		}
	});

	// Remove images
	$( '.sbgs_thumbnails' ).on( 'click', 'a.delete', function() {
		
		$(this).closest( 'li.sbgs_item' ).remove();

		var image_ids = '';

		$( '.sbgs_thumbnails li.sbgs_item' ).css( 'cursor','default' ).each(
			function() {
				var image_id = jQuery(this).attr( 'data-attachment_id' );
				image_ids = image_ids ? image_ids + "," + image_id : image_id;
			}
		);

		$image_gallery_ids.val( image_ids );

		return false;
	});

	/* === End image uploader JS. === */

	var sbgs_val = $( 'input#_sbgs_uploader_gallery' ).val();
	var val_split = sbgs_val.split(',');

	if ( val_split.length > 1 ) {

		/* Show the 'remove background image' link, the image, and extra options. */
		$( '.sbgs_options' ).show();

	} else {

		/* Show the 'remove background image' link, the image, and extra options. */
		$( '.sbgs_options' ).hide();

	}

});
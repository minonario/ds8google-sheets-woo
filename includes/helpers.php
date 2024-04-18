<?php

if ( ! function_exists( 'ds8_get_post_image' ) ) :

	/**
	 * Get post thumbnail markup.
	 *
	 * @since 1.0.0
	 * @param int|WP_Post  $post Optional. Post ID or WP_Post object.  Default is global `$post`.
	 * @param string|array $size Optional. Image size to use. Accepts any valid image size, or
	 *                           an array of width and height values in pixels (in that order).
	 *                           Default 'post-thumbnail'.
	 * @param boolean      $caption Optional. Display image caption.
	 * @return string The post thumbnail image tag.
	 */
	function ds8_get_post_image( $post = null, $size = 'post-thumbnail', $caption = false ) {

		$attachment_id  = get_post_thumbnail_id( $post );
		$attachment_alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ); // phpcs:ignore
		$attachment_alt = empty( $attachment_alt ) ? get_the_title( $post ) : $attachment_alt;

		$atts = array(
			'alt' => $attachment_alt,
		);

		if ( ds8__get_schema_markup( 'image' ) ) {
			$atts['itemprop'] = 'image';
		}

		$size = apply_filters( 'ds8__post_thumbnail_default_size', $size );
		$atts = apply_filters( 'ds8__post_thumbnail_default_size', $atts );

		$html = get_the_post_thumbnail( $post, $size, $atts );

		if ( $caption ) {

			$caption = wp_get_attachment_caption( $attachment_id );

			if ( ! empty( $caption ) ) {
				$caption = '<div class="post-thumb-caption">' . wp_kses( $caption, ds8__get_allowed_html_tags( 'button' ) ) . '</div>';
			}

			$html .= $caption;
		}

		return apply_filters( 'ds8__post_thumbnail_html', $html, $post, $attachment_id, $size, $atts );
	}
endif;
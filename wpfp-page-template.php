<?php
    $wpfp_before = "";
    echo "<div class='wpfp-span'>";
    if (!empty($user)) {
        if (wpfp_is_user_favlist_public($user)) {
            $wpfp_before = "$user's Favorite Posts.";
        } else {
            $wpfp_before = "$user's list is not public.";
        }
    }

    if ($wpfp_before):
        echo '<div class="wpfp-page-before">'.$wpfp_before.'</div>';
    endif;

    if ($favorite_post_ids) {
		$favorite_post_ids = array_reverse($favorite_post_ids);
        $post_per_page = wpfp_get_option("post_per_page");
        $page = intval(get_query_var('paged'));
        $post_type_setting = wpfp_get_option('add_posttype');

	    $qry = array( 'post__in'       => $favorite_post_ids,
	                  'posts_per_page' => $post_per_page,
	                  'orderby'        => 'post__in',
	                  'paged'          => $page
	    );
	    // custom post type support can easily be added with a line of code like below.
	    if ( ! empty( $post_type_setting ) ) {
		    $qry['post_type'] = $post_type_setting;
		    if ( wpfp_get_option( 'with_post_and_page' ) == "1" ) {
			    $qry['post_type'] = array( 'post', 'page', $post_type_setting );
		    }
	    }
        query_posts($qry);

        echo wpfp_get_option( 'item_before' );
        while ( have_posts() ) : the_post();
	        $fav_permalink = get_permalink();
	        $fav_title     = get_the_title();
	        if ( has_post_thumbnail() ) {
		        $fav_thumbnail = get_the_post_thumbnail();
	        } else {
		        $fav_thumbnail = '<img src="' . plugins_url() . '/wp-favorite-posts-custom-fix/img/no_image.png" >';
	        }
	        $fav_get_id          = get_the_id();
	        $fav_remove          = "<a id='rem_" . $fav_get_id . "' class='wpfp-link remove-parent' href='?wpfpaction=remove&amp;page=1&amp;postid=" . $fav_get_id . "' title='" . wpfp_get_option( 'rem' ) . "' rel='nofollow'>" . wpfp_get_option( 'rem' ) . "</a>";
	        $fav_tags            = wpfp_get_option( 'item_tag' );
	        $fav_replace_setting = array( '%permalink%', '%title%', "%thumbnail%", '%remove%' );
	        $fav_replace_word    = array( $fav_permalink, $fav_title, $fav_thumbnail, $fav_remove );
	        $fav_item_tags       = str_replace( $fav_replace_setting, $fav_replace_word, $fav_tags );
	        echo $fav_item_tags;
        endwhile;
	    echo wpfp_get_option( 'item_after' );

        echo '<div class="navigation">';
            if(function_exists('wp_pagenavi')) { wp_pagenavi(); } else { ?>
            <div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
            <div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>
            <?php }
        echo '</div>';

        wp_reset_query();
    } else {
        $wpfp_options = wpfp_get_options();
        echo "<ul><li>";
        echo $wpfp_options['favorites_empty'];
        echo "</li></ul>";
    }

    echo '<p>'.wpfp_clear_list_link().'</p>';
    echo "</div>";
    wpfp_cookie_warning();

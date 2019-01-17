<?php 
/*
 Plugin Name: Veterinary Pet Care Pro Posttype
 Plugin URI: https://www.themesglance.com/
 Description: Creating new post type for Veterinary Pet Care Pro Theme
 Author: Themesglance
 Version: 1.0
 Author URI: https://www.themesglance.com/
*/

define( 'veterinary_pet_care_pro_posttype_version', '1.0' );
add_action( 'init', 'veterinary_pet_care_pro_posttype_create_post_type' );

function veterinary_pet_care_pro_posttype_create_post_type() {

  register_post_type( 'services',
    array(
        'labels' => array(
            'name' => __( 'Services','veterinary-pet-care-pro-posttype' ),
            'singular_name' => __( 'Services','veterinary-pet-care-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );

  register_post_type( 'team',
    array(
        'labels' => array(
            'name' => __( 'Team','veterinary-pet-care-pro-posttype' ),
            'singular_name' => __( 'Team','veterinary-pet-care-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-welcome-learn-more',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );
  register_post_type( 'testimonials',
	array(
		'labels' => array(
			'name' => __( 'Testimonials','veterinary-pet-care-pro-posttype-pro' ),
			'singular_name' => __( 'Testimonials','veterinary-pet-care-pro-posttype-pro' )
			),
		'capability_type' => 'post',
		'menu_icon'  => 'dashicons-businessman',
		'public' => true,
		'supports' => array(
			'title',
			'editor',
			'thumbnail'
			)
		)
	);
  
}

// --------------- Services ------------------
// Serives section
function veterinary_pet_care_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('veterinary-pet-care-pro-posttype-pro-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'veterinary_pet_care_pro_posttype_images_metabox_enqueue');
// Services Meta
function veterinary_pet_care_pro_posttype_bn_custom_meta_services() {

    add_meta_box( 'bn_meta', __( 'Services Meta', 'veterinary-pet-care-pro-posttype-pro' ), 'veterinary_pet_care_pro_posttype_bn_meta_callback_services', 'services', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'veterinary_pet_care_pro_posttype_bn_custom_meta_services');
}

function veterinary_pet_care_pro_posttype_bn_meta_callback_services( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $service_image = get_post_meta( $post->ID, 'meta-image', true );
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <p>
            <label for="meta-image"><?php echo esc_html('Icon Image'); ?></label><br>
            <input type="text" name="meta-image" id="meta-image" class="meta-image regular-text" value="<?php echo esc_attr( $service_image ); ?>">
            <input type="button" class="button image-upload" value="Browse">
          </p>
          <div class="image-preview"><img src="<?php echo $bn_stored_meta['meta-image'][0]; ?>" style="max-width: 250px;"></div>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function veterinary_pet_care_pro_posttype_bn_meta_save_services( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  // Save Image
  if( isset( $_POST[ 'meta-image' ] ) ) {
      update_post_meta( $post_id, 'meta-image', esc_url_raw($_POST[ 'meta-image' ]) );
  }
  
}
add_action( 'save_post', 'veterinary_pet_care_pro_posttype_bn_meta_save_services' );

/* Services shortcode */
function veterinary_pet_care_pro_posttype_services_func( $atts ) {
  $services = '';
  $services = '<div class="row">';
  $query = new WP_Query( array( 'post_type' => 'services') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=services');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $services_image= get_post_meta(get_the_ID(), 'meta-image', true);
        if(get_post_meta($post_id,'meta-services-url',true !='')){$custom_url =get_post_meta($post_id,'meta-services-url',true); } else{ $custom_url = get_permalink(); }
        $services .= '<div class="col-lg-4 col-md-4 col-sm-6">
                        <div class="services-box">
                          <div class="">
                             <div class="services_icon">
                             <img class="" src="'.esc_url($services_image).'">
                          </div>
                        </div>
                      <div class="">
                        <h4><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h4>
                        <p>
                          '.$excerpt.'
                        </p>
                    </div>
                  </div>
                </div>';


    if($k%2 == 0){
      $services.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $services = '<h2 class="center">'.esc_html__('Post Not Found','veterinary-pet-care-pro-posttype').'</h2>';
  endif;
  $services .= '</div>';
  return $services;
}

add_shortcode( 'list-services', 'veterinary_pet_care_pro_posttype_services_func' );


/* ----------------- Team ---------------- */
function veterinary_pet_care_pro_posttype_bn_designation_meta() {
    add_meta_box( 'veterinary_pet_care_pro_posttype_bn_meta', __( 'Enter Designation','veterinary-pet-care-pro-posttype' ), 'veterinary_pet_care_pro_posttype_bn_meta_callback', 'team', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'veterinary_pet_care_pro_posttype_bn_designation_meta');
}
/* Adds a meta box for custom post */
function veterinary_pet_care_pro_posttype_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'veterinary_pet_care_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $meta_designation = get_post_meta( $post->ID, 'meta-designation', true );
    $meta_team_email = get_post_meta( $post->ID, 'meta-team-email', true );
    $meta_team_call = get_post_meta( $post->ID, 'meta-team-call', true );
    $meta_team_face = get_post_meta( $post->ID, 'meta-facebookurl', true );
    $meta_team_twit = get_post_meta( $post->ID, 'meta-twitterurl', true );
    $meta_team_gplus = get_post_meta( $post->ID, 'meta-googleplusurl', true );
    $meta_team_pint = get_post_meta( $post->ID, 'meta-pinteresturl', true );
    $meta_team_inst = get_post_meta( $post->ID, 'meta-instagramurl', true );
    ?>
    <div id="team_custom_stuff">
        <table id="list-table">         
          <tbody id="the-list" data-wp-lists="list:meta">
              <tr id="meta-9">
                <td class="left">
                  <?php esc_html_e( 'Designation', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_attr($meta_designation); ?>" />
                </td>
              </tr>
              <tr id="meta-9">
                <td class="left">
                  <?php esc_html_e( 'Email', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="text" name="meta-team-email" id="meta-team-email" value="<?php echo esc_attr($meta_team_email); ?>" />
                </td>
              </tr>
               <tr id="meta-9">
                <td class="left">
                  <?php esc_html_e( 'Phone', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="text" name="meta-team-call" id="meta-team-call" value="<?php echo esc_attr($meta_team_call); ?>" />
                </td>
              </tr>
              <tr id="meta-3">
                <td class="left">
                  <?php esc_html_e( 'Facebook Url', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-facebookurl" id="meta-facebookurl" value="<?php echo esc_attr($meta_team_face); ?>" />
                </td>
              </tr>
              <tr id="meta-5">
                <td class="left">
                  <?php esc_html_e( 'Twitter Url', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-twitterurl" id="meta-twitterurl" value="<?php echo esc_attr($meta_team_face); ?>" />
                </td>
              </tr>
              <tr id="meta-6">
                <td class="left">
                  <?php esc_html_e( 'GooglePlus URL', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-googleplusurl" id="meta-googleplusurl" value="<?php echo esc_attr($meta_team_gplus); ?>" />
                </td>
              </tr>
              <tr id="meta-7">
                <td class="left">
                  <?php esc_html_e( 'Pinterest URL', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-pinteresturl" id="meta-pinteresturl" value="<?php echo esc_attr($meta_team_pint); ?>" />
                </td>
              </tr>
               <tr id="meta-8">
                <td class="left">
                  <?php esc_html_e( 'Instagram URL', 'veterinary-pet-care-pro-posttype' )?>
                </td>
                <td class="left" >
                  <input type="url" name="meta-instagramurl" id="meta-instagramurl" value="<?php echo esc_attr($meta_team_inst); ?>" />
                </td>
              </tr>
              
          </tbody>
        </table>
    </div>
    <?php
}
/* Saves the custom fields meta input */
function veterinary_pet_care_pro_posttype_bn_metadesig_team_save( $post_id ) {
    if( isset( $_POST[ 'meta-desig' ] ) ) {
        update_post_meta( $post_id, 'meta-desig', sanitize_text_field($_POST[ 'meta-desig' ]) );
    }
    if( isset( $_POST[ 'meta-call' ] ) ) {
        update_post_meta( $post_id, 'meta-call', sanitize_text_field($_POST[ 'meta-call' ]) );
    }
    // Save facebookurl
    if( isset( $_POST[ 'meta-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-facebookurl', esc_url_raw($_POST[ 'meta-facebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-linkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-linkdenurl', esc_url_raw($_POST[ 'meta-linkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-twitterurl', esc_url_raw($_POST[ 'meta-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-googleplusurl', esc_url_raw($_POST[ 'meta-googleplusurl' ]) );
    }

    // Save Pinterest
    if( isset( $_POST[ 'meta-pinteresturl' ] ) ) {
        update_post_meta( $post_id, 'meta-pinteresturl', esc_url_raw($_POST[ 'meta-pinteresturl' ]) );
    }

     // Save Instagram
    if( isset( $_POST[ 'meta-instagramurl' ] ) ) {
        update_post_meta( $post_id, 'meta-instagramurl', esc_url_raw($_POST[ 'meta-instagramurl' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', sanitize_text_field($_POST[ 'meta-designation' ]) );
    }

    // Save Email
    if( isset( $_POST[ 'meta-team-email' ] ) ) {
        update_post_meta( $post_id, 'meta-team-email', sanitize_text_field($_POST[ 'meta-team-email' ]) );
    }
    // Save Call
    if( isset( $_POST[ 'meta-team-call' ] ) ) {
        update_post_meta( $post_id, 'meta-team-call', sanitize_text_field($_POST[ 'meta-team-call' ]) );
    }
}
add_action( 'save_post', 'veterinary_pet_care_pro_posttype_bn_metadesig_team_save' );

/* team shorthcode */
function veterinary_pet_care_pro_posttype_team_func( $atts ) {
    $team = ''; 
    $custom_url ='';
    $team = '<div class="row">';
    $query = new WP_Query( array( 'post_type' => 'team' ) );
    if ( $query->have_posts() ) :
    $k=1;
    $new = new WP_Query('post_type=team'); 
    while ($new->have_posts()) : $new->the_post();
    	$post_id = get_the_ID();
    	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
      if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
		  $url = $thumb['0'];
      $excerpt = wp_trim_words(get_the_excerpt(),25);
      $designation= get_post_meta($post_id,'meta-designation',true);
      $call= get_post_meta($post_id,'meta-call',true);
      $facebookurl= get_post_meta($post_id,'meta-facebookurl',true);
      $linkedin=get_post_meta($post_id,'meta-linkdenurl',true);
      $twitter=get_post_meta($post_id,'meta-twitterurl',true);
      $googleplus=get_post_meta($post_id,'meta-googleplusurl',true);
      $pinterest=get_post_meta($post_id,'meta-pinteresturl',true);
      $instagram=get_post_meta($post_id,'meta-instagramurl',true);
      $team .= '<div class="team_box col-lg-4 col-md-6 col-sm-6">
                    <div class="image-box ">
                      <div class="box image-overlay">
                        <img class="client-img" src="'.esc_url($thumb_url).'" alt="team-thumbnail" />
                        <div class="box-content team-box">
                          <h4 class="team_name"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>
                          <p class="designation">'.esc_html($designation).'</p>
                        </div>
                      </div>
                    </div>
                  <div class="content_box w-100 float-left">
                    <div class="short_text">'.$excerpt.'</div>
                    <div class="about-socialbox">
                      <p>'.$call.'</p>
                      <div class="team_socialbox">';
                        if($facebookurl != ''){
                          $team .= '<a class="" href="'.esc_url($facebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                        } if($twitter != ''){
                          $team .= '<a class="" href="'.esc_url($twitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';
                        } if($googleplus != ''){
                          $team .= '<a class="" href="'.esc_url($googleplus).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                        } if($linkedin != ''){
                          $team .= '<a class="" href="'.esc_url($linkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                        }if($pinterest != ''){
                          $team .= '<a class="" href="'.esc_url($pinterest).'" target="_blank"><i class="fab fa-pinterest-p"></i></a>';
                        }if($instagram != ''){
                          $team .= '<a class="" href="'.esc_url($instagram).'" target="_blank"><i class="fab fa-instagram"></i></a>';
                        }
                      $team .= '</div>
                    </div>
                  </div>
                </div>';

      if($k%2 == 0){
          $team.= '<div class="clearfix"></div>'; 
      } 
      $k++;         
  endwhile; 
  wp_reset_postdata();
  $team.= '</div>';
  else :
    $team = '<h2 class="center">'.esc_html_e('Not Found','veterinary-pet-care-pro-posttype').'</h2>';
  endif;
  return $team;
}
add_shortcode( 'team', 'veterinary_pet_care_pro_posttype_team_func' );

/* customer section */
/* Adds a meta box to the customer editing screen */
function veterinary_pet_care_pro_posttype_bn_customer_meta_box() {
	add_meta_box( 'veterinary-pet-care-pro-posttype-pro-customer-meta', __( 'Enter Designation', 'veterinary-pet-care-pro-posttype-pro' ), 'veterinary_pet_care_pro_posttype_bn_customer_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'veterinary_pet_care_pro_posttype_bn_customer_meta_box');
}

/* Adds a meta box for custom post */
function veterinary_pet_care_pro_posttype_bn_customer_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'veterinary_pet_care_pro_posttype_posttype_customer_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
	$desigstory = get_post_meta( $post->ID, 'customer-desig', true );
  $tes_facebook = get_post_meta( $post->ID, 'meta-tes-facebookurl', true );
  $tes_twitter = get_post_meta( $post->ID, 'meta-tes-twitterurl', true );
  $tes_gplus = get_post_meta( $post->ID, 'meta-tes-googleplusurl', true );
  $test_pinterest = get_post_meta( $post->ID, 'meta-tes-pinteresturl', true );
  $tes_instagram = get_post_meta( $post->ID, 'meta-tes-instagramurl', true );
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-9">
          <td class="left">
            <?php esc_html_e( 'Designation', 'veterinary-pet-care-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="customer-desig" id="customer-desig" value="<?php echo esc_attr($desigstory); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php esc_html_e( 'Facebook Url', 'veterinary-pet-care-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-facebookurl" id="meta-tes-facebookurl" value="<?php echo esc_attr($tes_facebook); ?>" />
          </td>
        </tr>
        <tr id="meta-5">
          <td class="left">
            <?php esc_html_e( 'Twitter Url', 'veterinary-pet-care-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-twitterurl" id="meta-tes-twitterurl" value="<?php echo esc_attr($tes_twitter); ?>" />
          </td>
        </tr>
        <tr id="meta-6">
          <td class="left">
            <?php esc_html_e( 'GooglePlus URL', 'veterinary-pet-care-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-googleplusurl" id="meta-tes-googleplusurl" value="<?php echo esc_attr($tes_gplus); ?>" />
          </td>
        </tr>
        <tr id="meta-7">
          <td class="left">
            <?php esc_html_e( 'Pinterest URL', 'veterinary-pet-care-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-pinteresturl" id="meta-tes-pinteresturl" value="<?php echo esc_attr($test_pinterest); ?>" />
          </td>
        </tr>
        <tr id="meta-8">
          <td class="left">
            <?php esc_html_e( 'Instagram URL', 'veterinary-pet-care-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-instagramurl" id="meta-tes-instagramurl" value="<?php echo esc_attr($tes_instagram); ?>" />
          </td>
        </tr>
      </tbody>
		</table>
	</div>
	<?php
}

/* Saves the custom meta input */
function veterinary_pet_care_pro_posttype_bn_metadesig_save( $post_id ) {
	if (!isset($_POST['veterinary_pet_care_pro_posttype_posttype_customer_meta_nonce']) || !wp_verify_nonce($_POST['veterinary_pet_care_pro_posttype_posttype_customer_meta_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Save desig.
	if( isset( $_POST[ 'veterinary_pet_care_pro_posttype_posttype_customer_desigstory' ] ) ) {
		update_post_meta( $post_id, 'veterinary_pet_care_pro_posttype_posttype_customer_desigstory', sanitize_text_field($_POST[ 'veterinary_pet_care_pro_posttype_posttype_customer_desigstory']) );
	}
  
  // Course Name
  if( isset( $_POST[ 'customer-desig' ] ) ) {
    update_post_meta( $post_id, 'customer-desig', sanitize_text_field($_POST[ 'customer-desig' ]) );
  } 

  // Save facebookurl
    if( isset( $_POST[ 'meta-tes-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tes-facebookurl', esc_url_raw($_POST[ 'meta-tes-facebookurl' ]) );
    }
    
    if( isset( $_POST[ 'meta-tes-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tes-twitterurl', esc_url_raw($_POST[ 'meta-tes-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-tes-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tes-googleplusurl', esc_url_raw($_POST[ 'meta-tes-googleplusurl' ]) );
    }

    // Save Pinterest
    if( isset( $_POST[ 'meta-tes-pinteresturl' ] ) ) {
        update_post_meta( $post_id, 'meta-tes-pinteresturl', esc_url_raw($_POST[ 'meta-tes-pinteresturl' ]) );
    }

     // Save Instagram
    if( isset( $_POST[ 'meta-tes-instagramurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tes-instagramurl', esc_url_raw($_POST[ 'meta-tes-instagramurl' ]) );
    }

}

add_action( 'save_post', 'veterinary_pet_care_pro_posttype_bn_metadesig_save' );

/* testimonials shortcode */
function veterinary_pet_care_pro_posttype_customer_func( $atts ) {
	$customer = '';
	$customer = '<div class="row">';
	$query = new WP_Query( array( 'post_type' => 'testimonials') );

    if ( $query->have_posts() ) :

	$k=1;
	$new = new WP_Query('post_type=testimonials');

	while ($new->have_posts()) : $new->the_post();
        $custom_url = '';
      	$post_id = get_the_ID();
      	$excerpt = wp_trim_words(get_the_excerpt(),25);
        $course= get_post_meta($post_id,'customer-desig',true);

        $tfacebookurl= get_post_meta($post_id,'meta-tes-facebookurl',true);
        $tlinkedin=get_post_meta($post_id,'meta-linkdenurl',true);
        $ttwitter=get_post_meta($post_id,'meta-tes-twitterurl',true);
        $tgoogleplus=get_post_meta($post_id,'meta-tes-googleplusurl',true);
        $tpinterest=get_post_meta($post_id,'meta-tes-pinteresturl',true);
        $tinstagram=get_post_meta($post_id,'meta-tes-instagramurl',true);

      	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
		    if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $customer .= '
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="customer_box mb-3">
              <div class="image-box">
                <div class="customer-overlay">
                  <img class="testi-img" src="'.esc_url($thumb_url).'" />
                    <h4 class="customer_name post"><a href="'.get_permalink().'">'.esc_html(get_the_title()) .'</a></h4>
                    <p class="desig-name"> '.esc_html($course).'</p>
                </div>
              </div>
              <div class="short_text pt-1"><p>'.$excerpt.'</p></div>
              <div class="customer_socialbox">';
                if($tfacebookurl != ''){
                  $customer .= '<a class="" href="'.esc_url($tfacebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                } if($ttwitter != ''){
                 $customer .= '<a class="" href="'.esc_url($ttwitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';
                } if($tgoogleplus != ''){
                  $customer .= '<a class="" href="'.esc_url($tgoogleplus).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                } if($tlinkedin != ''){
                  $customer .= '<a class="" href="'.esc_url($tlinkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                }if($tpinterest != ''){
                  $customer .= '<a class="" href="'.esc_url($tpinterest).'" target="_blank"><i class="fab fa-pinterest-p"></i></a>';
                }if($tinstagram != ''){
                  $customer .= '<a class="" href="'.esc_url($tinstagram).'" target="_blank"><i class="fab fa-instagram"></i></a>';
                }
              $customer .= '</div>
            </div>
          </div>';
		if($k%3 == 0){
			$customer.= '<div class="clearfix"></div>';
		}
      $k++;
  endwhile;
  else :
  	$customer = '<h2 class="center">'.esc_html__('Post Not Found','veterinary-pet-care-pro-posttype-pro').'</h2>';
  endif;
  $customer .= '</div>';
  return $customer;
}
add_shortcode( 'testimonials', 'veterinary_pet_care_pro_posttype_customer_func' );






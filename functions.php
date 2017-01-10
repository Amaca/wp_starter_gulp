<?php

/*--------------------------------------------------
Inclusione JQUERY
--------------------------------------------------*/
function switch_jquery(){
    if ( !is_admin() ){
        wp_deregister_script('jquery');
        wp_register_script('jquery', ( get_template_directory_uri() . '/dist/js/vendor/jquery-3.1.0.min.js'), false, '3.1.0', false);
        wp_enqueue_script('jquery');

    }
}
add_action('wp_enqueue_scripts', 'switch_jquery');


/*---------------------------------------------------
Including scripts
--------------------------------------------------*/
function include_scripts(){

    if (defined('WP_DEBUG') && true === WP_DEBUG) { //se è in debug mode prende da /src

        wp_register_script( 'ismobile',
        get_template_directory_uri() . '/src/js/vendor/isMobile.js', false, '0.4.1', true );
        wp_enqueue_script( 'ismobile' );

        wp_register_script( 'easing',
        get_template_directory_uri() . '/src/js/vendor/jquery.easing.min.js', array('jquery') , '1.3', true );
        wp_enqueue_script( 'easing' );

        wp_register_script( 'slick',
        get_template_directory_uri() . '/src/js/vendor/slick.js', false, '1.6.0', true );
        wp_enqueue_script( 'slick' );

        wp_register_script( 'tether',
        get_template_directory_uri() . '/src/js/vendor/tether.js', false, '1.3.3', true );
        wp_enqueue_script( 'tether' );

        wp_register_script( 'bootstrap',
        get_template_directory_uri() . '/src/js/vendor/bootstrap.js', false, 'v4.0.0-alpha.5', true );
        wp_enqueue_script( 'bootstrap' );

        wp_register_script( 'main',
        get_template_directory_uri() . '/src/js/main.js', false, '1.0', true );
        wp_enqueue_script( 'main' );

        //css
        wp_register_style( 'bootstrap-css',
        get_template_directory_uri() . '/dist/css/bootstrap.css');
        wp_enqueue_style( 'bootstrap-css' );

        wp_register_style( 'main-css',
        get_template_directory_uri() . '/dist/css/main.css');
        wp_enqueue_style( 'main-css' );

    } else { //se non è in debug mode prende da /dist
           
        wp_register_script( 'main-min', 
        get_template_directory_uri() . '/dist/js/main.min.js', array('jquery'), '1.0', true );
        wp_enqueue_script( 'main-min' );

        //css
        wp_register_style( 'bootstrap-css',
        get_template_directory_uri() . '/dist/css/bootstrap.min.css');
        wp_enqueue_style( 'bootstrap-css' );

        wp_register_style( 'main-css',
        get_template_directory_uri() . '/dist/css/main.min.css');
        wp_enqueue_style( 'main-css' );

    }
    //font
    wp_register_style( 'font-hind','https://fonts.googleapis.com/css?family=Hind:400,500,600,700&amp;subset=latin-ext');
    wp_enqueue_style( 'font-hind' );
 
}
add_action( 'wp_enqueue_scripts', 'include_scripts' );


/*---------------------------------------------------
Register Custom Menus
--------------------------------------------------*/
register_nav_menus( array(
    'main_menu' => 'Menù principale di testata',
    'footer_menu' => 'Menù secondario su footer',
) );


/*---------------------------------------------------
Add search bar to main menu
--------------------------------------------------*/
function your_custom_menu_item ( $items, $args ) {

    if( $args->menu->name == 'Main Menu')  {
        $url = home_url(); 
        $items .=   '<li>
                        <div class="search-form">
                            <a href="#" class="search-toggle"></a>
                            <form role="search" method="get" action="'. $url .'">
                                <input type="text" class="search-field" value="" name="s" />
                                <button type="submit" class="search-submit" value="Cerca">cerca</button>
                            </form>
                        </div>
                    </li>';
    }

    return $items;
}
add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );


/*---------------------------------------------------
Language Selector
--------------------------------------------------*/
function language_selector(){
    $languages = icl_get_languages('skip_missing=0&orderby=code');
    if(!empty($languages)){
        foreach($languages as $l){
            if(!$l['active']){
                echo '<a href="'.$l['url'].'">' . $l['translated_name'] . '</a>';
            }
        }
    }
}


/*---------------------------------------------------
Search Filters
--------------------------------------------------*/
function SearchFilter($query) {
    if ($query->is_search) {
        $query->set('post_type', array( 'progetto', 'ricerche' ) );
    }
    return $query;
}
add_filter('pre_get_posts','SearchFilter');


/*---------------------------------------------------
ACF Option page
--------------------------------------------------*/
if( function_exists('acf_add_options_page') ) { 
    acf_add_options_page(); 
}


/*---------------------------------------------------
Disable admin bar
--------------------------------------------------*/
function my_function_admin_bar(){
    return false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');


/*---------------------------------------------------
Allow SVG uploads
--------------------------------------------------*/
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


/*---------------------------------------------------
Single post gallery
--------------------------------------------------*/
function my_post_gallery($output, $attr) {

    global $post;  

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }
 
    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 3,
        'size' => 'thumbnail',
        'include' => '',
        'exclude' => ''
    ), $attr));

    $id = intval($id);
    if ('RAND' == $order) $orderby = 'none';

    if (!empty($include)) {
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    }

    if (empty($attachments)) return '';

    // Here's your actual output, you may customize it to your need
    $output = "<div class=\"blog-gallery\">\n";

    // Now you loop through each attachment
    foreach ($attachments as $id => $attachment) {
        // Fetch the thumbnail (or full image, it's up to you)
              $img = wp_get_attachment_image_src($id, 'medium');
        //      $img = wp_get_attachment_image_src($id, 'my-custom-image-size');
        $img = wp_get_attachment_image_src($id, 'full');

        $output .= "<div class=\"item\">\n";
        $output .= "<img src=\"{$img[0]}\"  alt=\"\" />\n";
        $output .= "</div>\n";
    }

    $output .= "</div>\n";

    return $output;
}
add_filter('post_gallery', 'my_post_gallery', 10, 2);


/*---------------------------------------------------
Archive pagination
--------------------------------------------------*/
function pagination() {
$prev .= '<button class="whitebg" type="button">
            <div class="btntop">Precedenti</div>
          </button>';

$next .= '<button class="whitebg" type="button">
            <div class="btntop">Successivi</div>
         </button>';
posts_nav_link( ' ', $prev, $next ); 
}


?>
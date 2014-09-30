<?php
/*
Plugin Name: Client Reviews
Plugin URI: http://www.graphicandweb.com
Description: Declares a plugin that will create a custom post type displaying client reviews.
Version: 1.0
Author: Mark Smallman | GraphicAndWeb.com
Author URI: http://www.graphicandweb.com
License: GPLv2
*/
?>
<?php
add_action( 'init', 'create_client_review' );


function create_client_review() {
    register_post_type( 'client_reviews',
        array(
            'labels' => array(
                'name' => 'Client Reviews',
                'singular_name' => 'Client Review',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Client Review',
                'edit' => 'Edit',
                'edit_item' => 'Edit Client Review',
                'new_item' => 'New Client Review',
                'view' => 'View',
                'view_item' => 'View Client Review',
                'search_items' => 'Search Client Reviews',
                'not_found' => 'No Client Reviews found',
                'not_found_in_trash' => 'No Client Reviews found in Trash',
                'parent' => 'Parent Client Review'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/reviews-icon.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

add_action( 'admin_init', 'my_admin' );

function my_admin() {
    add_meta_box( 'client_review_meta_box',
        'Client Name',
        'display_client_review_meta_box',
        'client_reviews', 'normal', 'high'
    );
}

function display_client_review_meta_box( $client_review ) {
    // Retrieve the name of the client
    $client_name = esc_html( get_post_meta( $client_review->ID, 'client_director', true ) );
    $client_rating = intval( get_post_meta( $client_review->ID, 'client_rating', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Client Name</td>
            <td><input type="text" size="50" name="client_name" value="<?php echo $client_name; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Client Rating</td>
            <td>
                <select style="width: 100px" name="client_review_rating">
                <?php
                // Generate all items of drop-down list
                for ( $rating = 5; $rating >= 1; $rating -- ) {
                ?>
                    <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $client_rating ); ?>>
                    <?php echo $rating; ?> stars <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post', 'add_client_review_fields', 10, 2 );

function add_client_review_fields( $client_review_id, $client_review ) {
    // Check post type for client reviews
    if ( $client_review->post_type == 'client_reviews' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['client_name'] ) && $_POST['client_name'] != '' ) {
            update_post_meta( $client_review_id, 'client_name', $_POST['client_name'] );
        }
        if ( isset( $_POST['client_review_rating'] ) && $_POST['client_review_rating'] != '' ) {
            update_post_meta( $client_review_id, 'client_rating', $_POST['client_review_rating'] );
        }
    }
}

add_filter( 'template_include', 'include_template_function', 1 );

function include_template_function( $template_path ) {
    if ( get_post_type() == 'client_reviews' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-client_reviews.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-client_reviews.php';
            }
        }
    }
    return $template_path;
}

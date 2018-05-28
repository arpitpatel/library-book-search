<?php
   /*
   Plugin Name: Library Book Search
   Plugin URI: #
   description: Allow you to manage the library features, allow you to book search with some options.
   Version: 1.0
   Author: Mr. Arpit
   Author URI: #
   License: GPL2
   */
add_action( 'init', 'library_book_register' );
function library_book_register() {
   $labels = array(
      'name'               => __( 'Books' ),
      'singular_name'      => 'Book',
      'menu_name'          => 'Books',
      'name_admin_bar'     => 'Book',
      'add_new'            => 'Add New',
      'add_new_item'       => 'Add New Book',
      'new_item'           => 'New Book',
      'edit_item'          => 'Edit Book',
      'view_item'          => 'View Book',
      'all_items'          => 'All Books',
      'search_items'       => 'Search Books',
      'parent_item_colon'  => 'Parent Books:',
      'not_found'          => 'No books found.',
      'not_found_in_trash' => 'No books found in Trash.'
   );

   $args = array(
      'labels'             => $labels,
       'description'        =>'This will allow you to add Books',
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => 'book' ),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array( 'title', 'editor'),
      'menu_icon'   => 'dashicons-book',
   );

   register_post_type( 'book', $args );
}

add_action( 'init', 'library_author_tax' );
function library_author_tax() {
   register_taxonomy(
      'lbs_author',
      'book',
      array(
         'label' => __( 'Author' ),
         'rewrite' => array( 'slug' => 'lbs_author' ),
         'hierarchical' => true,
      )
   );
}

add_action( 'init', 'library_publisher_tax' );
function library_publisher_tax() {
   register_taxonomy(
      'publisher',
      'book',
      array(
         'label' => __( 'Publisher' ),
         'rewrite' => array( 'slug' => 'publisher' ),
         'hierarchical' => true,
      )
   );
}

add_action( 'admin_init', 'library_meta' );
function library_meta() {
    add_meta_box( 'book_meta_box',
        'Book Details',
        'display_book_meta_box',
        'book', 'normal', 'high'
    );
}
function display_book_meta_box( $book ) {
    // get current book Price and Rating based on book ID
    $price = esc_html( get_post_meta( $book->ID, 'lbs_price', true ) );
    $star_rating =  get_post_meta( $book->ID, 'lbs_rating', true ) ;
    ?>
    <table>
        <tr>
            <td style="width: 100%">Price</td>
            <td><input type="text" size="80" name="book_price" value="<?php echo $price; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Star Rating</td>
            <td>
                <select style="width: 100px" name="book_rating">
                <?php
                // Generate all items of drop-down list
                for ( $rating = 5; $rating >= 1; $rating -- ) {
                ?>
                    <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $star_rating ); ?>>
                    <?php echo $rating; ?> stars <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'save_post', 'lbs_save_meta_fields', 10, 2 );
function lbs_save_meta_fields( $book_id, $book ) {
    // Check post type for book
    if ( $book->post_type == 'book' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['book_price'] ) && $_POST['book_price'] != '' ) {
            update_post_meta( $book_id, 'lbs_price', $_POST['book_price'] );
        }
        if ( isset( $_POST['book_rating'] ) && $_POST['book_rating'] != '' ) {
            update_post_meta( $book_id, 'lbs_rating', $_POST['book_rating'] );
        }
    }
}
add_filter( 'manage_edit-book_columns', 'lbs_edit_columns' ) ;

function lbs_edit_columns( $columns ) {

   $columns = array(
      'cb' => '<input type="checkbox" />',
      'title' => __( 'Book' ),
      'price' => __( 'Price' ),
      'lbs_author' => __( 'Author' ),
      'publisher' => __( 'Publisher' ),
      'date' => __( 'Date' )
   );

   return $columns;
}
add_action( 'manage_posts_custom_column', 'lbs_columns', 10, 2 );

function lbs_columns( $column, $post_id ) {
   global $post;
   switch( $column ) {
      /* If displaying the 'price' column. */
      case 'price' :
         /* Get the post meta. */
         $price = get_post_meta( $post_id, 'lbs_price', true );
         /* If no duration is found, output a default message. */
         if ( empty( $price ) )
            echo __( '—' );
         /* If there is a duration, append '$' to the text string. */
         else
            printf( __( '$%s' ), $price );
         break;

      /* If displaying the 'publisher' column. */
      case 'publisher' :
         /* Get the genres for the post. */
         $publishers = get_the_terms( $post_id, 'publisher' );
         /* If terms were found. */
         if ( !empty( $publishers ) ) {
            $out = array();
            /* Loop through each term, linking to the 'edit posts' page for the specific term. */
            foreach ( $publishers as $publisher ) {
               $out[] = sprintf( '<a href="%s">%s</a>',
                  esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'publisher' => $publisher->slug ), 'edit.php' ) ),
                  esc_html( sanitize_term_field( 'name', $publisher->name, $publisher->term_id, 'publisher', 'display' ) )
               );
            }
            /* Join the terms, separating them with a comma. */
            echo join( ', ', $out );
         }
         /* If no terms were found, output a default message. */
         else {
            _e( '—' );
         }
         break;

         case 'lbs_author' :
         /* Get the authors for the post. */
         $authors = get_the_terms( $post_id, 'lbs_author' );
         /* If terms were found. */
         if ( !empty( $authors ) ) {
            $out = array();
            /* Loop through each term, linking to the 'edit posts' page for the specific term. */
            foreach ( $authors as $author ) {
               $out[] = sprintf( '<a href="%s">%s</a>',
                  esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'lbs_author' => $author->slug ), 'edit.php' ) ),
                  esc_html( sanitize_term_field( 'name', $author->name, $author->term_id, 'lbs_author', 'display' ) )
               );
            }
            /* Join the terms, separating them with a comma. */
            echo join( ', ', $out );
         }
         /* If no terms were found, output a default message. */
         else {
            _e( '—' );
         }
         break;
      /* Just break out of the switch statement for everything else. */
      default :
         break;
   }
}

add_filter( 'manage_edit-book_sortable_columns', 'lbs_sortable_columns');
function lbs_sortable_columns( $columns ) {
  $columns['price'] = 'lbs_price';
  return $columns;
}

add_action( 'pre_get_posts', 'smashing_posts_orderby' );
function smashing_posts_orderby( $query ) {
  if( ! is_admin() || ! $query->is_main_query() ) {
    return;
  }

  if ( 'lbs_price' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', 'lbs_price' );
    $query->set( 'meta_type', 'numeric' );
  }
}

function load_book_template($template) {
    global $post;
    if ($post->post_type == "book" && $template !== locate_template(array("single-book.php"))){
        return plugin_dir_path( __FILE__ ) . "single-book.php";
    }
   
    return $template;
}
add_filter('single_template', 'load_book_template');

add_filter('template_include', 'add_category_template');
function add_category_template( $template ){
if( is_tax('lbs_author')){
    $template = plugin_dir_path( __FILE__ ) . "taxonomy-lbs_author.php";
}  
if( is_tax('publisher')){
    $template = plugin_dir_path( __FILE__ ) . "taxonomy-publisher.php";
} 
return $template;
}
include( plugin_dir_path( __FILE__ ) . '/shortcode.php');


function lbs_script()
{     
   wp_enqueue_script( 'lbs_script', plugin_dir_url( __FILE__ ) . 'assets/js/jquery-1.11.3.min.js', array('jquery'), '1.0.0', false );
   wp_enqueue_script( 'lbs_ui_script', plugin_dir_url( __FILE__ ) . 'assets/js/jquery-ui.min.js', array('jquery'), '1.0.0', true );
   wp_enqueue_script( 'lbs_price_script', plugin_dir_url( __FILE__ ) . 'assets/js/price-range.js', array('jquery'), '1.0.0', true );
  wp_enqueue_style( 'lbs_style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', '', '1.0.0');
  wp_enqueue_style( 'lbs_ui_style', plugin_dir_url( __FILE__ ) . 'assets/css/jquery-ui.css', '', '1.0.0');
}
add_action('wp_enqueue_scripts', 'lbs_script');

add_action('admin_menu', 'book_menu');

function book_menu() {
add_submenu_page('edit.php?post_type=book', 'Book Shortcode', 'Book Shortcode', 'manage_options', 'bookhelp', 'book_help');
}
function book_help(){
  ?>
    <div class="wrap">
      <?php _e( '<h2>Book Shortcode</h2>'); ?>
      <p>Place below shortcode into the page/post to view the Book search on the front end.</p>
      <p>[lbslist]</p>
    </div>
  <?php
}
?>
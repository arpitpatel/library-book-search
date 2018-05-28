<?php
 /*Template Name: Shortcode
 */
function lbs_display()
{
	$output = "";
	$output .= "<div class='lbs-wrapper'>";
	$output .= "<div class='lbs-form-area'><h1>Book Saerch</h1>";
	$output .= search_form();
	$output .= "</div><div class='lbs-list-area'>"; 
	$output .= lbs_listing();
	$output .= "</div>"; 
	$ajax_url =  admin_url('admin-ajax.php');
	 ?>
<script type='text/javascript'>

     jQuery(document).ready(function(){
    	//alert();

    	 jQuery('#lbs_filter').click(function(){
    	 	var name = jQuery('#book-name').val();
    	 	var author = jQuery('#author').val();
    	 	var publisher = jQuery('#publisher').val();
    	 	var rating = jQuery('#rating').val();
    	 	var minprice = jQuery('.price-range-min').text();
    	 	var maxprice = jQuery('.price-range-max').text();
			 jQuery.ajax({
		       type:'POST',
		       url: '<?php echo $ajax_url; ?>',
		       data:'action=book_saerch&name='+ name + '&author='+ author + '&publisher='+ publisher + '&rating='+ rating + '&minprice='+ minprice + '&maxprice='+ maxprice,
		       success:function(data){
		    	   jQuery('#book_search_result').html(data);
		         //  $('#search').val('');
		         //alert(data);
		           jQuery('#book_search_result').hide();
		           jQuery('#book_search_result').fadeIn(1500);
		     },
		   
		     });
		});
		});
					
		</script><?php 

	return $output;
 }
add_shortcode('lbslist', 'lbs_display');
function publisher_lists()
{
	 $args = array(
               'taxonomy' => 'publisher',
               'orderby' => 'name',
               'order'   => 'ASC'
           );

   $cats = get_categories($args);

   $catlist = '';
	$catlist .= '<select name="publisher" id="publisher">';
		$catlist .= '<option value="-1">All</option>';
	   foreach($cats as $cat) {
	   	$catlist .= '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
	   }
	   $catlist .= '</select>';
	   return $catlist;
}
function search_form()
{
$form = '<form action="#" method="post" name="book-filter" id="book-filter">
	<div class="book-filter-row">
		<div class="book-filter-row-lft"><label>Book Name:</label><input type="text" name="book-name" id="book-name"></div>
		<div class="book-filter-row-rgt"><label>Author:</label><input type="text" name="author" id="author"></div>
	</div>
	<div class="book-filter-row">
		<div class="book-filter-row-lft"><label>Publisher:</label>'.publisher_lists().'</div>
		<div class="book-filter-row-rgt"><label>Rating:</label>
			<select name="rating" id="rating">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
			</select>
		</div>
	</div>
	<div class="book-filter-row"><div class="book-filter-row-lft price-range"><label>Price:</label><div class="book-filter-row-price"><div id="slider"></div></div></div></div>
	<div class="book-filter-btn">
		<input type="button" name="book_filter" id="lbs_filter" value="Saerch">
	</div>

</form>';
return $form;
}


function lbs_searchbox() {

  global $wpdb;
  global $post;
    $publisher  = isset($_REQUEST['publisher']) && $_REQUEST['publisher'] != '-1' ? $_REQUEST['publisher'] : '';
    $author = isset($_REQUEST['author']) && $_REQUEST['author'] != '-1' ? $_REQUEST['author'] : '';
    $and = ''; 
    $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE ";
    
	if( !empty($_POST['name']) ){
                $querystr .= "post_title = '".$_POST['name']."' AND ";
             }
    if ( !empty( $publisher ) ) {
        $querystr .= "EXISTS( SELECT 1 FROM $wpdb->term_relationships WHERE  $wpdb->posts.ID = $wpdb->term_relationships.object_id AND $wpdb->term_relationships.term_taxonomy_id IN ( '".$publisher."' ) ) AND ";
    }   
    if ( !empty( $author ) ) {
    	$authorid = "SELECT term_id FROM $wpdb->terms WHERE  $wpdb->terms.name = '".$author."'";
        $querystr .= "EXISTS( SELECT 1 FROM $wpdb->term_relationships WHERE  $wpdb->posts.ID = $wpdb->term_relationships.object_id AND $wpdb->term_relationships.term_taxonomy_id IN ( ".$authorid." ) ) AND ";
    } 
    if( !empty($_POST['rating']) ){
        $querystr .= "EXISTS( SELECT 1 FROM $wpdb->postmeta WHERE post_id = $wpdb->posts.ID AND $wpdb->postmeta.meta_key = 'lbs_rating' AND $wpdb->postmeta.meta_value = '".$_POST['rating']."'  ) AND ";
    }
    if ( !empty($_POST['maxprice']) ) {
        $querystr .= "EXISTS( SELECT 1 FROM $wpdb->postmeta WHERE post_id = $wpdb->posts.ID AND $wpdb->postmeta.meta_key = 'lbs_price' AND $wpdb->postmeta.meta_value <= '".$_POST['maxprice']."') AND ";
    }

    if (  !empty($_POST['minprice'] )) {
        $querystr .= "EXISTS( SELECT 1 FROM $wpdb->postmeta WHERE post_id = $wpdb->posts.ID AND $wpdb->postmeta.meta_key = 'lbs_price' AND $wpdb->postmeta.meta_value >= '".$_POST['minprice']."' ) AND ";
    }

    $querystr .=" $wpdb->posts.post_type = 'book'
        AND ($wpdb->posts.post_status = 'publish')
        GROUP BY $wpdb->posts.ID
        ORDER BY $wpdb->posts.post_date DESC";    
       $result = $wpdb->get_results($querystr);	 
       $book_list = "";
    if ( $result ) {
$i = 1;
    	  foreach($result as $post){
             setup_postdata($post); 
             print_r($post->ID);
      			 $authors = get_the_terms( $post->ID, 'lbs_author' );
            if ( !empty( $authors ) ) {
                $author_name = array();
                foreach ( $authors as $author ) {
                   $author_name[] = $author->name;
                }
            }
             $publishers = get_the_terms( $post->ID, 'publisher' );
             if ( !empty( $publishers ) ) {
                $publishers_name = array();
                foreach ( $publishers as $publisher ) {
                   $publishers_name[] = $publisher->name;
                }
             }
             $stars = get_post_meta( $post->ID, 'lbs_rating', true );
             $starsrate = '';
                for($x=1;$x<=$stars;$x++) {
                    $starsrate .=  '<img src="'.plugin_dir_url( __FILE__ ) .'assets/images/star.png" />';
                }
                while ($x<=5) {
                    $starsrate .= '<img src="'.plugin_dir_url( __FILE__ ) .'assets/images/blank-star.png" />';
                    $x++;
                }
               $price = get_post_meta( $post->ID, 'lbs_price', true ); ?>
             <tr><td class="book-no"><?php echo $i; ?></td>
             <td class="book-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
             <td class="book-price"><?php echo '$' . $price;  ?></td>
             <td class="book-row"><?php echo join( ', ', $publishers_name ); ?></td>
             <td class="book-row"><?php echo join( ', ', $author_name ); ?></td>
             <td class="book-row"><?php echo $starsrate; ?></td></tr>
           <?php  $i++;
    }
       
    } else { echo "<tr><td colspan='4'>Sorry, No books found in your criteria.</td></tr>";
					
}	 
wp_reset_query();
die();
}
add_action('wp_ajax_book_saerch', 'lbs_searchbox', 10 );
add_action('wp_ajax_nopriv_book_saerch', 'lbs_searchbox', 10 );//for users that are not logged in.

function lbs_listing()
{
include(plugin_dir_path( __FILE__ ) . "list-book.php");
  return  $book_list;
}
?>
<?php
 /*Template Name: Book list Page
 */
 ?>

<?php 
 global $wpdb;
  global $post;
    $and = '';
    $book_list = '';
    $querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE  $wpdb->posts.post_type = 'book'
        AND ($wpdb->posts.post_status = 'publish')
        GROUP BY $wpdb->posts.ID
        ORDER BY $wpdb->posts.post_date DESC";
        $result = $wpdb->get_results($querystr);

$book_list .= '<table border="0">
  <thead>
    <tr>
      <th class="book-no">No.</th>
      <th class="book-name">Book Name</th>
      <th class="book-price">Price</th>
      <th class="book-row">Author</th>
      <th class="book-row">Publisher</th>
      <th class="book-row">Rating</th>
    </tr>
  </thead>
  <tbody id="book_search_result">';
  if ( $result ) 
  {
      $i = 1;
          foreach($result as $post)
          {
            setup_postdata($post); 
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
             $book_list .= '<tr><td class="book-no">'.$i.'</td>';
             $book_list .= '<td class="book-name"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></td>';
             $book_list .= '<td class="book-price">$'.get_post_meta( $post->ID, 'lbs_price', true ).'</td>';
             $book_list .= '<td class="book-row">'.join( ', ', $publishers_name ).'</td>';
             $book_list .= '<td class="book-row">'.join( ', ', $author_name ).'</td>';
             $book_list .= '<td class="book-row">'.$starsrate.'</td></tr>';
             $i++;
          }
       
    } 
    else { $book_list .= "<tr><td colspan='4'>No books found.</td></tr>"; }
     $book_list .= " </tbody>
</table>";
        
<?php
 /*Template Name: Book Detail Page
 */
get_header(); ?>
<div class="book-detail-wrapper"> 
   <?php if ( have_posts() ) : 
    while ( have_posts() ) : the_post();
        $authors = get_the_terms( $post->ID, 'lbs_author' );
            if ( !empty( $authors ) ) {
                $author_name = array();
                foreach ( $authors as $author ) {
                   $author_name[] = '<a href='.get_term_link(  $author->term_id, 'lbs_author' ) . '>' . $author->name . '</a>';;
                }
            }
             $publishers = get_the_terms( $post->ID, 'publisher' );
             if ( !empty( $publishers ) ) {
                $publishers_name = array();
                foreach ( $publishers as $publisher ) {
                   $publishers_name[] = '<a href='.get_term_link(  $publisher->term_id, 'publisher' ) . '>' . $publisher->name . '</a>';
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
     ?>
     <?php the_title( '<h1>', '</h1>'); ?>
       <div class="book-info-area">
            <div class="book-info">
                <strong>Author: </strong><?php echo join( ', ', $author_name ); ?>
            </div>
            <div class="book-info">
                <strong>Publisher: </strong><?php echo join( ', ', $publishers_name ); ?>
            </div>
        </div>
       <div class="book-content"><?php the_content(); ?></div>
       <div class="book-rating"><span class="star-rate"><strong>Rating: </strong></span><span class="star-rate"> <?php echo $starsrate; ?></span>
       <div class="book-rating"><span class="star-rate"><strong>Price: </strong></span><span class="star-rate"> <?php echo '$' . get_post_meta( $post->ID, 'lbs_price', true ); ?></span>
       </div>
    <?php endwhile;
endif; ?>
</div>
<?php get_footer(); ?>
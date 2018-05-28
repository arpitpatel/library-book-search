<?php 
/*
 The template for displaying Author archive pages
 */
get_header(); ?>
<div class="archive-wrapper">

	<?php if ( have_posts() ) : ?>
		<div class="archive-header">
			<?php
				the_archive_title( '<h1>', '</h1>' );
				the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
		</div>
	<?php endif; ?>
	<div class="archive-list-area">
			<?php
			if ( have_posts() ) : 
				while ( have_posts() ) : the_post(); ?>
					<div class="archive-list">
						<h2>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<?php the_excerpt(); ?>
					</div><?php
				endwhile;
			endif; ?>
	</div>
<?php get_footer();

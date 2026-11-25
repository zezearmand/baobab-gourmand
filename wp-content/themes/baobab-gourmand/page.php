<?php get_header(); while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="wrap"><h1><?php the_title(); ?></h1></div></section>
<div class="wrap content"><div class="prose"><?php the_content(); ?></div></div>
<?php endwhile; get_footer(); ?>

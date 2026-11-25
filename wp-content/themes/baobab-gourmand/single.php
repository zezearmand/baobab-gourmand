<?php get_header(); while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="wrap">
  <span class="tag"><?php $c = get_the_category(); echo $c ? esc_html($c[0]->name) : ''; ?></span>
  <h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_date()); ?></p>
</div></section>
<div class="wrap content"><article class="prose">
  <?php if (has_post_thumbnail()) { the_post_thumbnail('large'); } ?>
  <?php the_content(); ?>
  <p><a class="btn" href="<?php echo esc_url(as_page_url('contact')); ?>">Réserver une table</a></p>
</article></div>
<?php endwhile; get_footer(); ?>

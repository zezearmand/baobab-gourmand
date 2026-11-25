<?php get_header(); ?>
<section class="page-hero"><div class="wrap">
  <h1><?php echo is_archive() ? esc_html(wp_strip_all_tags(get_the_archive_title())) : 'Blog'; ?></h1>
  <?php if (is_category() && category_description()) { echo '<p>' . wp_kses_post(category_description()) . '</p>'; } ?>
</div></section>
<div class="wrap content">
  <div class="grid">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="card">
      <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('as-card'); } ?></a>
      <div class="body">
        <span class="tag"><?php $c = get_the_category(); echo $c ? esc_html($c[0]->name) : ''; ?></span>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
        <p class="meta"><?php echo esc_html(get_the_date()); ?></p>
      </div>
    </article>
  <?php endwhile; else : ?><p>Aucun contenu pour le moment.</p><?php endif; ?>
  </div>
  <div class="pagination"><?php the_posts_pagination(); ?></div>
</div>
<?php get_footer(); ?>

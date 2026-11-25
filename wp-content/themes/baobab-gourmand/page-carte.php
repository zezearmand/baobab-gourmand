<?php
/* Template Name: Carte & menus */
get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="wrap"><h1><?php the_title(); ?></h1></div></section>
<div class="wrap content">
  <?php if (get_the_content()) : ?><div class="prose center" style="margin-bottom:3rem"><?php the_content(); ?></div><?php endif; ?>
<?php endwhile;
$terms = get_terms(array('taxonomy' => 'rubrique_carte', 'hide_empty' => true, 'orderby' => 'id', 'order' => 'ASC'));
if (!is_wp_error($terms)) :
  foreach ($terms as $t) :
    $q = new WP_Query(array('post_type' => 'plat', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC',
      'tax_query' => array(array('taxonomy' => 'rubrique_carte', 'field' => 'term_id', 'terms' => $t->term_id))));
    if (!$q->have_posts()) { continue; } ?>
    <section class="rubrique <?php echo esc_attr($t->slug); ?>" id="<?php echo esc_attr($t->slug); ?>">
      <h2><?php echo esc_html($t->name); ?></h2>
      <div class="<?php echo $t->slug === 'menus' ? 'menus' : ''; ?>" style="max-width:860px;margin:2rem auto 0">
        <?php while ($q->have_posts()) : $q->the_post(); as_render_dish(get_the_ID()); endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
<?php endforeach; endif; ?>
  <p class="center meta">Prix en francs CFA (FCFA), taxes et service compris. Allergènes (arachide, poisson, crustacés) disponibles sur demande.</p>
</div>
<?php get_footer(); ?>

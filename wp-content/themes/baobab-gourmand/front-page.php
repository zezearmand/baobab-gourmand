<?php get_header(); $i = as_info(); $img = get_template_directory_uri() . '/assets/img/'; ?>
<section class="hero" style="background-image:url('<?php echo esc_url($img . 'hero.jpg'); ?>')">
  <div class="wrap">
    <span class="eyebrow">Cuisine sénégalaise authentique</span>
    <h1>La teranga à table : les saveurs du Sénégal</h1>
    <p>Une cuisine généreuse, les poissons du jour et les produits de nos marchés. Bienvenue au Baobab, où l'on mange comme en famille.</p>
    <div class="btns">
      <a class="btn" href="<?php echo esc_url(as_page_url('contact')); ?>">Réserver une table</a>
      <a class="btn btn-ghost" href="<?php echo esc_url(as_page_url('carte')); ?>">Découvrir la carte</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap split">
    <img src="<?php echo esc_url($img . 'about.jpg'); ?>" alt="Notre cheffe dressant un plat de thieboudienne" width="1000" height="1250">
    <div>
      <span class="eyebrow">Notre histoire</span>
      <?php while (have_posts()) : the_post(); ?>
        <div class="entry"><?php the_content(); ?></div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="wrap center">
    <span class="eyebrow">Nos spécialités</span>
    <h2>Les incontournables de la maison</h2>
    <p class="lead">Les grands classiques de la cuisine sénégalaise, préparés comme à la maison.</p>
    <div class="grid" style="margin-top:2.5rem;text-align:left">
      <?php
      $q = new WP_Query(array('post_type' => 'plat', 'posts_per_page' => 3, 'meta_key' => '_as_badge', 'meta_value' => 'Signature', 'orderby' => 'menu_order title', 'order' => 'ASC'));
      while ($q->have_posts()) : $q->the_post(); ?>
        <article class="card">
          <?php if (has_post_thumbnail()) { the_post_thumbnail('as-card'); } ?>
          <div class="body">
            <h3><?php the_title(); ?></h3>
            <p><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p>
            <span class="price"><?php echo esc_html(get_post_meta(get_the_ID(), '_as_prix', true)); ?> FCFA</span>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <p style="margin-top:2.5rem"><a class="btn" href="<?php echo esc_url(as_page_url('carte')); ?>">Voir toute la carte</a></p>
  </div>
</section>

<section class="section dark">
  <div class="wrap center">
    <span class="eyebrow">Notre identité</span>
    <h2>Trois engagements</h2>
    <div class="values" style="text-align:left">
      <div><h3>Local</h3><p>Poissons des pirogues de la côte, légumes des Niayes, céréales et condiments du pays.</p></div>
      <div><h3>Teranga</h3><p>Le plat du jour suit l'arrivage du matin et les saisons des fruits et légumes.</p></div>
      <div><h3>Fait maison</h3><p>Marinades, sauces, jus et desserts sont préparés chaque jour dans notre cuisine.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap center">
    <span class="eyebrow">Blog culinaire</span>
    <h2>Dernières publications</h2>
    <div class="grid" style="margin-top:2.5rem;text-align:left">
      <?php $b = new WP_Query(array('posts_per_page' => 3, 'ignore_sticky_posts' => true));
      while ($b->have_posts()) : $b->the_post(); ?>
        <article class="card">
          <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('as-card'); } ?></a>
          <div class="body">
            <span class="tag"><?php $c = get_the_category(); echo $c ? esc_html($c[0]->name) : ''; ?></span>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p class="meta"><?php echo esc_html(get_the_date()); ?></p>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
</section>

<section class="section alt center">
  <div class="wrap">
    <h2>Une table vous attend</h2>
    <p class="lead"><?php echo esc_html($i['address']); ?> · <?php echo esc_html($i['phone']); ?></p>
    <a class="btn" href="<?php echo esc_url(as_page_url('contact')); ?>">Réserver</a>
  </div>
</section>
<?php get_footer(); ?>

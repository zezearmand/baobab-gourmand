<?php
/* Template Name: Contact */
get_header(); $i = as_info(); ?>
<?php while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="wrap"><h1><?php the_title(); ?></h1><p>Une question, une réservation, un événement privé ? Écrivez-nous.</p></div></section>
<div class="wrap content contact-grid">
  <aside class="info-box">
    <h2>Nos coordonnées</h2>
    <p><strong>Adresse</strong><br><?php echo esc_html($i['address']); ?></p>
    <p><strong>Téléphone</strong><br><a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $i['phone'])); ?>"><?php echo esc_html($i['phone']); ?></a></p>
    <p><strong>E-mail</strong><br><a href="mailto:<?php echo esc_attr($i['email']); ?>"><?php echo esc_html($i['email']); ?></a></p>
    <h3>Horaires d'ouverture</h3>
    <ul class="hours">
      <?php foreach ($i['hours'] as $h) : ?><li><span><?php echo esc_html($h[0]); ?></span><span><?php echo esc_html($h[1]); ?></span></li><?php endforeach; ?>
    </ul>
  </aside>
  <section><h2>Envoyez-nous un message</h2><?php the_content(); ?></section>
</div>
<?php endwhile; get_footer(); ?>

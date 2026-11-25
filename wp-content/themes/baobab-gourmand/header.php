<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Aller au contenu</a>
<header class="site-header">
  <div class="wrap bar">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav">Menu</button>
    <nav id="primary-nav" class="primary-nav" aria-label="Navigation principale">
      <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'menu', 'fallback_cb' => false)); ?>
    </nav>
    <a class="btn btn-sm" href="<?php echo esc_url(as_page_url('contact')); ?>">Réserver</a>
  </div>
</header>
<main id="main">

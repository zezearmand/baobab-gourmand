<?php
if (!defined('ABSPATH')) { exit; }

/** Coordonnées du restaurant (modifiables via le filtre as_info). */
function as_info() {
    return apply_filters('as_info', array(
        'address' => 'Route de la Corniche Ouest, Les Almadies, Dakar, Sénégal',
        'phone'   => '+221 33 800 12 34',
        'email'   => 'contact@baobab-gourmand.example',
        'hours'   => array(
            array('Lundi', 'Fermé'),
            array('Mardi – Jeudi', '12h00 – 15h00 · 19h00 – 23h00'),
            array('Vendredi – Samedi', '12h00 – 15h00 · 19h00 – 00h00'),
            array('Dimanche', '12h00 – 16h00'),
        ),
    ));
}

function as_page_url($slug) {
    $p = get_page_by_path($slug);
    return $p ? get_permalink($p) : home_url('/');
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');
    register_nav_menus(array(
        'primary' => 'Menu principal',
        'footer'  => 'Menu pied de page',
    ));
    add_image_size('as-card', 800, 600, true);
});

add_action('wp_enqueue_scripts', function () {
    $v = wp_get_theme()->get('Version');
    wp_enqueue_style('as-fonts', 'https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@500;700&display=swap', array(), null);
    wp_enqueue_style('as-style', get_stylesheet_uri(), array(), $v);
    wp_enqueue_script('as-main', get_template_directory_uri() . '/assets/js/main.js', array(), $v, true);
});

/** Type de contenu « Plat » + taxonomie « Rubrique de la carte » : le gérant gère la carte depuis l'admin. */
add_action('init', function () {
    register_post_type('plat', array(
        'labels' => array(
            'name' => 'Carte & offres', 'singular_name' => 'Plat / offre',
            'add_new' => 'Ajouter', 'add_new_item' => 'Ajouter un plat ou une offre',
            'edit_item' => 'Modifier le plat', 'menu_name' => 'Carte & offres',
        ),
        'public' => true, 'has_archive' => false, 'show_in_rest' => true,
        'menu_icon' => 'dashicons-food', 'menu_position' => 5,
        'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
        'rewrite' => array('slug' => 'plat'),
    ));
    register_taxonomy('rubrique_carte', 'plat', array(
        'labels' => array('name' => 'Rubriques de la carte', 'singular_name' => 'Rubrique'),
        'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true,
        'rewrite' => array('slug' => 'rubrique'),
    ));
});

add_action('add_meta_boxes', function () {
    add_meta_box('as_plat_meta', 'Tarif et mention', 'as_plat_meta_cb', 'plat', 'side');
});

function as_plat_meta_cb($post) {
    wp_nonce_field('as_plat_save', 'as_plat_nonce');
    $prix  = get_post_meta($post->ID, '_as_prix', true);
    $badge = get_post_meta($post->ID, '_as_badge', true);
    echo '<p><label for="as_prix"><strong>Prix (FCFA)</strong></label><br>';
    echo '<input type="text" id="as_prix" name="as_prix" value="' . esc_attr($prix) . '" style="width:100%"></p>';
    echo '<p><label for="as_badge"><strong>Mention</strong> (ex. Signature, Végétarien, Épicé)</label><br>';
    echo '<input type="text" id="as_badge" name="as_badge" value="' . esc_attr($badge) . '" style="width:100%"></p>';
    echo '<p class="description">La mention « Signature » affiche le plat sur la page d\'accueil. L\'ordre se règle avec « Attributs de page &gt; Ordre ».</p>';
}

add_action('save_post_plat', function ($post_id) {
    if (!isset($_POST['as_plat_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['as_plat_nonce'])), 'as_plat_save')) { return; }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $post_id)) { return; }
    $prix  = isset($_POST['as_prix']) ? sanitize_text_field(wp_unslash($_POST['as_prix'])) : '';
    $badge = isset($_POST['as_badge']) ? sanitize_text_field(wp_unslash($_POST['as_badge'])) : '';
    update_post_meta($post_id, '_as_prix', $prix);
    update_post_meta($post_id, '_as_badge', $badge);
});

/** Affiche une ligne de plat (utilisée par la carte). */
function as_render_dish($id) {
    $prix  = get_post_meta($id, '_as_prix', true);
    $badge = get_post_meta($id, '_as_badge', true);
    $has   = has_post_thumbnail($id);
    echo '<article class="dish' . ($has ? '' : ' noimg') . '">';
    if ($has) { echo get_the_post_thumbnail($id, 'thumbnail'); }
    echo '<div><h3>' . esc_html(get_the_title($id)) . '</h3>';
    if ($badge) { echo '<span class="tag">' . esc_html($badge) . '</span> '; }
    echo '<p>' . wp_kses_post(get_post_field('post_content', $id)) . '</p></div>';
    if ($prix !== '') { echo '<div class="price">' . esc_html($prix) . ' FCFA</div>'; }
    echo '</article>';
}

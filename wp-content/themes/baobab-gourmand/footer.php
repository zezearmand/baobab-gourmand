</main>
<?php $i = as_info(); ?>
<footer class="site-footer">
  <div class="wrap fgrid">
    <div>
      <h3><?php bloginfo('name'); ?></h3>
      <p><?php bloginfo('description'); ?></p>
      <?php wp_nav_menu(array('theme_location' => 'footer', 'container' => false, 'fallback_cb' => false)); ?>
    </div>
    <div>
      <h3>Horaires</h3>
      <ul>
        <?php foreach ($i['hours'] as $h) : ?>
          <li><strong><?php echo esc_html($h[0]); ?></strong><br><?php echo esc_html($h[1]); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h3>Contact</h3>
      <p><?php echo esc_html($i['address']); ?><br>
      <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $i['phone'])); ?>"><?php echo esc_html($i['phone']); ?></a><br>
      <a href="mailto:<?php echo esc_attr($i['email']); ?>"><?php echo esc_html($i['email']); ?></a></p>
    </div>
  </div>
  <div class="wrap copy">© <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?> — Restaurant fictif réalisé à des fins de démonstration.</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

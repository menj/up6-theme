<?php
/**
 * Site Footer
 *
 * Two-column layout:
 *   Column 1 — Brand name, description (from Theme Options → Footer),
 *              social icons row (6 platforms: Facebook, X, Instagram,
 *              Threads, Telegram, WhatsApp)
 *   Column 2 — Contact block: address, email (ROT13-obfuscated in HTML,
 *              decoded by JS to prevent spam harvesting), Google Maps embed
 *
 * Below: secondary navigation (About, Editorial Policy, Privacy, Disclaimer,
 * Contact, Corrections, FAQ, Advertise) + copyright line.
 *
 * Footer email uses a two-stage anti-spam approach:
 *   1. HTML source contains ROT13-encoded text
 *   2. Inline JS decodes and populates the <a> element at render time
 *   3. onclick fallback handles delayed JS execution
 *
 * @package UP6
 * @since   2.0
 */
?>
  </div><!-- #content -->

  <footer id="colophon" class="site-footer" role="contentinfo">

    <!-- ── Main body: 2-column grid ── -->
    <div class="footer-main">
      <div class="footer-main-inner">

        <!-- Column 1: Brand + description + social -->
        <div class="footer-brand-col">

          <div class="footer-brand-name">
            <?php if ( has_custom_logo() ) : ?>
              <?php the_custom_logo(); ?>
            <?php else : ?>
              <span class="footer-brand-dot" aria-hidden="true"></span>
            <?php endif; ?>
            <?php up6_logo(); ?>
          </div>

          <div class="footer-description">
            <?php echo do_shortcode( up6_brand_inline( wp_kses_post( get_theme_mod( 'ss_footer_description', 'Providing independent, high-quality journalism for the modern digital landscape.' ) ) ) ); ?>
          </div>

          <?php
          $social_links = [
              'ss_social_facebook' => [
                  'label' => 'Facebook',
                  'icon'  => '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>',
                  'fill'  => true,
              ],
              'ss_social_x' => [
                  'label' => 'X',
                  'icon'  => '<path d="M4 4l16 16M4 20L20 4"/>',
                  'fill'  => false,
              ],
              'ss_social_instagram' => [
                  'label' => 'Instagram',
                  'icon'  => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
                  'fill'  => false,
              ],
              'ss_social_threads' => [
                  'label' => 'Threads',
                  'icon'  => '<path d="M12 2a7 7 0 00-7 7c0 2.5 1.5 4.5 3.5 5.5-.1.5-.2 1-.2 1.5A3 3 0 0011.5 19a3 3 0 003-2.8c1.8-1 3-3 3-5.2a7 7 0 00-5.5-6.85V4"/><circle cx="12" cy="9" r="2"/>',
                  'fill'  => false,
              ],
              'ss_social_telegram' => [
                  'label' => 'Telegram',
                  'icon'  => '<path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/>',
                  'fill'  => false,
              ],
              'ss_social_whatsapp' => [
                  'label' => 'WhatsApp',
                  'icon'  => '<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>',
                  'fill'  => false,
              ],
          ];
          $has_social = false;
          foreach ( $social_links as $key => $data ) {
              if ( up6_social_url( $key ) ) { $has_social = true; break; }
          }
          ?>
          <?php if ( $has_social ) : ?>
          <div class="footer-social" aria-label="<?php esc_attr_e( 'Social media links', 'up6' ); ?>">
            <?php foreach ( $social_links as $key => $data ) :
                $url = up6_social_url( $key );
                if ( ! $url ) continue;
                $fill_attr = $data['fill']
                    ? 'fill="currentColor"'
                    : 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
            ?>
            <a href="<?php echo esc_url( $url ); ?>" class="social-icon" aria-label="<?php echo esc_attr( $data['label'] ); ?>" target="_blank" rel="noopener noreferrer">
              <svg viewBox="0 0 24 24" <?php echo $fill_attr; ?> aria-hidden="true"><?php echo $data['icon']; ?></svg>
            </a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

        </div><!-- .footer-brand-col -->

        <!-- Column 2: Contact details -->
        <?php
        $addr  = get_theme_mod( 'ss_contact_address', '' );
        $phone = get_theme_mod( 'ss_contact_phone',   '' );
        $email = get_theme_mod( 'ss_contact_email',   '' );
        if ( $addr || $phone || $email ) :
        ?>
        <div class="footer-contact-col">
          <h4 class="footer-col-title"><?php esc_html_e( 'Contact', 'up6' ); ?></h4>
          <ul class="footer-contact-list">
            <?php if ( $addr ) : ?>
            <li class="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/>
              </svg>
              <span><?php echo nl2br( esc_html( $addr ) ); ?></span>
            </li>
            <?php endif; ?>
            <?php if ( $phone ) : ?>
            <li class="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.63A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
              </svg>
              <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
            </li>
            <?php endif; ?>
            <?php if ( $email ) : ?>
            <li class="footer-contact-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
              </svg>
              <?php
              // ROT13-obfuscate the email so it never appears in plain text in the HTML source.
              // JS decodes at click time; no-JS users see the noscript fallback (CSS-reversed).
              $rot = str_rot13( esc_attr( $email ) );
              $rot_display = str_rot13( esc_html( $email ) );
              ?>
              <a href="#"
                 class="obfuscated-email"
                 data-e="<?php echo $rot; ?>"
                 aria-label="<?php esc_attr_e( 'Email us', 'up6' ); ?>"
                 onclick="var e=this.dataset.e.replace(/[a-zA-Z]/g,function(c){return String.fromCharCode((c<='Z'?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26)});window.location='mailto:'+e;return false;"
              ><span class="email-display" data-e="<?php echo $rot_display; ?>"></span></a>
              <script>
              (function(){
                var a=document.currentScript.previousElementSibling;
                var r=a.dataset.e;
                var d=r.replace(/[a-zA-Z]/g,function(c){return String.fromCharCode((c<='Z'?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);});
                a.querySelector('.email-display').textContent=d;
                a.setAttribute('href','mailto:'+d);
                a.removeAttribute('onclick');
              })();
              </script>
            </li>
            <?php endif; ?>
          </ul>
        </div><!-- .footer-contact-col -->
        <?php endif; ?>

      </div>
    </div><!-- .footer-main -->

    <!-- ── Bottom bar: secondary nav + copyright ── -->
    <div class="footer-bar">
      <div class="footer-bar-inner">
        <?php
        wp_nav_menu( [
          'theme_location'  => 'secondary',
          'container'       => 'nav',
          'container_class' => 'footer-utility-nav',
          'depth'           => 1,
          'fallback_cb'     => function() {
            echo '<nav class="footer-utility-nav">';
            echo '<a href="' . esc_url( home_url( '/about' ) ) . '">'     . esc_html__( 'About',     'up6' ) . '</a>';
            echo '<a href="' . esc_url( home_url( '/contact' ) ) . '">'   . esc_html__( 'Contact',   'up6' ) . '</a>';
            echo '<a href="' . esc_url( home_url( '/advertise' ) ) . '">' . esc_html__( 'Advertise', 'up6' ) . '</a>';
            echo '<a href="' . esc_url( home_url( '/archives' ) ) . '">'  . esc_html__( 'Archives',  'up6' ) . '</a>';
            echo '</nav>';
          },
        ] );
        ?>
        <p class="footer-copyright">
          <?php echo up6_brand_inline( wp_kses_post( get_theme_mod( 'ss_copyright', '© ' . date( 'Y' ) . ' UP6. All rights reserved.' ) ) ); ?>
        </p>
      </div>
      <?php
      $legal_notice = up6_opt( 'ss_legal_notice' );
      if ( $legal_notice ) :
      ?>
      <div class="footer-legal">
        <div class="footer-legal-inner">
          <span class="footer-legal-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3v18M3 9l9-6 9 6M5 21h14"/><path d="M5 9l-2 6h4L5 9zM19 9l-2 6h4L19 9z"/>
            </svg>
          </span>
          <p class="footer-legal-text"><?php echo up6_brand_inline( wp_kses_post( $legal_notice ) ); ?></p>
        </div>
      </div>
      <?php endif; ?>
    </div><!-- .footer-bar -->

  </footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>

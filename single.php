<?php
/**
 * Single Post Template — full editorial article layout
 *
 * Stepped width hierarchy:
 *   Entry header (title, badges, byline, share bar): 64rem max
 *   Reading column (content, voting, tags, TOC):     54rem max
 *
 * Layout sections (top to bottom):
 *   1. Scroll progress bar — red line at bottom of header
 *   2. Breadcrumb trail — Home → Category → Title
 *   3. Category badges — per-category colours via up6_category_colour()
 *   4. Headline (h1) + optional subtitle/dek (_up6_subtitle meta)
 *   5. Excerpt/standfirst (post excerpt field)
 *   6. Byline — avatar, author, published date, Hijri date, updated,
 *      view count (≥100), reading time estimate
 *   7. Social share bar — 10 platforms (WhatsApp, Telegram, Facebook, etc.)
 *   8. Featured image with optional caption overlay
 *   9. Sticky Table of Contents — auto-generated from h2/h3 (≥3 required)
 *  10. Article content (the_content)
 *  11. Article voting — thumbs up/down (if enabled in Theme Options)
 *  12. Topic tags
 *  13. Related News grid — same-category posts, card.php partial
 *  14. Comments
 *
 * All data from native WP. No plugins required.
 *
 * @package UP6
 * @since   2.6
 * @updated 2.8 — category colours, TOC, card partial, voting
 */
get_header();
?>

<div id="up6-scroll-progress" role="progressbar" aria-hidden="true"></div>

<div class="site-content-inner single-content-inner">

    <!-- ===================== ARTICLE ===================== -->
    <main id="main" class="site-main" role="main">
      <?php
      while ( have_posts() ) :
        the_post();
        $cats = get_the_category();
      ?>

      <?php up6_breadcrumb(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?> data-permalink="<?php echo esc_url( get_permalink() ); ?>">

        <!-- Entry header -->
        <header class="entry-header">

          <!-- Category + Breaking badge -->
          <div class="entry-badges">
            <?php if ( $cats ) : ?>
              <?php foreach ( $cats as $cat ) : ?>
              <a class="entry-category-badge" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" style="background:<?php echo esc_attr( up6_category_colour( $cat ) ); ?>">
                <?php echo esc_html( $cat->name ); ?>
              </a>
              <?php endforeach; ?>
            <?php endif; ?>
            <?php if ( is_sticky() ) : ?>
              <span class="entry-category-badge is-breaking">
                <?php esc_html_e( 'BREAKING NEWS', 'up6' ); ?>
              </span>
            <?php endif; ?>
          </div>

          <h1 class="entry-title"><?php the_title(); ?></h1>

          <!-- Subtitle / dek line (stored as _up6_subtitle post meta) -->
          <?php
          $subtitle = up6_get_subtitle();
          if ( $subtitle ) :
          ?>
            <p class="entry-subtitle"><?php echo esc_html( $subtitle ); ?></p>
          <?php endif; ?>

          <!-- Excerpt / standfirst (stored as post excerpt) -->
          <?php if ( has_excerpt() ) : ?>
            <p class="entry-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
          <?php endif; ?>

          <!-- Byline: author + metadata in one row -->
          <?php
          $hijri_str   = up6_get_hijri();
          $views       = (int) get_post_meta( get_the_ID(), '_up6_views', true );
          $pub_date    = get_the_date( 'j F Y' );
          $pub_time    = get_the_date( 'g:i A' );
          $mod_date    = get_the_modified_date( 'j F Y' );
          $mod_time    = get_the_modified_date( 'g:i A' );
          $is_modified = get_the_modified_date( 'U' ) - get_the_date( 'U' ) > 86400;
          ?>
          <div class="entry-byline">
            <?php echo up6_author_avatar( get_the_author_meta( 'ID' ) ); ?>
            <div class="entry-byline-body">
              <div class="entry-byline-row">
                <a class="entry-author-name"
                   href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                  <?php the_author(); ?>
                </a>
              </div>
              <div class="entry-meta-details">
                <span class="meta-item meta-item--published">
                  <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( $pub_date . ', ' . $pub_time ); ?></time>
                </span>
                <span class="meta-item meta-item--hijri">
                  <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/></svg>
                  <span><?php echo esc_html( $hijri_str ); ?></span>
                </span>
                <?php if ( $is_modified ) : ?>
                <span class="meta-item meta-item--updated">
                  <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.5 2v6h-6"/><path d="M21.34 15.57a10 10 0 1 1-.57-8.38L21.5 8"/></svg>
                  <span><?php printf( esc_html__( 'Updated %s', 'up6' ), esc_html( $mod_date . ', ' . $mod_time ) ); ?></span>
                </span>
                <?php endif; ?>
                <?php if ( $views >= 100 ) : ?>
                <span class="meta-item meta-item--views">
                  <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <span><?php printf( esc_html__( '%s reads', 'up6' ), number_format_i18n( $views ) ); ?></span>
                </span>
                <?php endif; ?>
                <span class="meta-item meta-item--reading-time" aria-label="<?php esc_attr_e( 'Estimated reading time', 'up6' ); ?>">
                  <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                  <span><?php echo esc_html( up6_reading_time() ); ?> <?php esc_html_e( 'minit', 'up6' ); ?></span>
                </span>
              </div>
            </div>
          </div>

          <!-- Share bar -->
          <?php
          $share_url   = esc_attr( urlencode( get_permalink() ) );
          $share_title = esc_attr( urlencode( get_the_title() ) );
          $share_text  = esc_attr( urlencode( get_the_title() . ' — ' . get_bloginfo( 'name' ) ) );
          ?>
          <div class="entry-share" aria-label="<?php esc_attr_e( 'Share this article', 'up6' ); ?>">
            <span class="entry-share-label"><?php esc_html_e( 'Share', 'up6' ); ?></span>
            <div class="entry-share-buttons">
              <a href="https://api.whatsapp.com/send?text=<?php echo $share_text . '%20' . $share_url; ?>"
                 class="share-btn share-btn--whatsapp" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              </a>
              <a href="https://t.me/share/url?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
                 class="share-btn share-btn--telegram" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on Telegram', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0 12 12 0 0011.944 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
              </a>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
                 class="share-btn share-btn--facebook" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on Facebook', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
                 class="share-btn share-btn--x" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on X', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              </a>
              <a href="https://www.threads.net/intent/post?text=<?php echo $share_text . '%20' . $share_url; ?>"
                 class="share-btn share-btn--threads" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on Threads', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.343-.783-.927-1.41-1.69-1.82-.005.59-.063 1.17-.184 1.725-.324 1.483-1.03 2.637-2.1 3.432-1.022.76-2.31 1.07-3.6.995-1.66-.095-3.035-.795-3.876-1.97-.736-1.028-1.065-2.392-.927-3.843.236-2.481 1.903-4.353 4.211-4.726 1.327-.215 2.574-.073 3.596.416v-.003c.232.112.453.24.66.385l.028.02c-.005-.08-.01-.16-.019-.238-.112-1.07-.505-1.87-1.168-2.378-.724-.554-1.77-.83-3.112-.82l-.046.002c-1.725.024-3.04.596-3.907 1.702l-1.622-1.263c1.217-1.553 3.03-2.378 5.396-2.453l.203-.003h.018c1.822-.013 3.303.434 4.4 1.327 1.024.834 1.633 2.022 1.81 3.535.064.545.082 1.12.054 1.713.717.623 1.28 1.4 1.636 2.282.791 1.96.633 4.545-1.199 6.34-1.752 1.717-3.94 2.476-7.046 2.5zm-.597-5.924c.884.054 1.702-.164 2.345-.659.612-.472 1.066-1.208 1.282-2.195.083-.379.128-.778.135-1.187-.57-.248-1.204-.398-1.894-.398h-.04c-.192.003-.4.02-.618.053-1.567.254-2.61 1.435-2.756 3.127-.065.749.102 1.398.487 1.934.45.627 1.188.998 2.081 1.054l.128.007.138.002c.074 0 .147-.002.22-.006l.137-.003.156-.002z"/></svg>
              </a>
              <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>"
                 class="share-btn share-btn--linkedin" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
              </a>
              <a href="https://reddit.com/submit?url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>"
                 class="share-btn share-btn--reddit" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on Reddit', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 01-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.051 1.604.022.178.033.359.033.541 0 2.689-3.13 4.867-6.99 4.867-3.862 0-6.99-2.178-6.99-4.867 0-.184.012-.366.034-.546a1.745 1.745 0 01-1.043-1.6A1.757 1.757 0 015.766 10.5c.475 0 .897.183 1.207.49 1.193-.855 2.849-1.416 4.67-1.487l.885-4.182a.342.342 0 01.14-.197.345.345 0 01.238-.042l2.906.617a1.214 1.214 0 011.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 00-.231.094.33.33 0 000 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 00.029-.463.33.33 0 00-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 00-.232-.095z"/></svg>
              </a>
              <a href="https://pinterest.com/pin/create/button/?url=<?php echo $share_url; ?>&description=<?php echo $share_title; ?>"
                 class="share-btn share-btn--pinterest" target="_blank" rel="noopener noreferrer"
                 aria-label="<?php esc_attr_e( 'Share on Pinterest', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12.017 24c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641 0 12.017 0z"/></svg>
              </a>
              <a href="mailto:?subject=<?php echo $share_title; ?>&body=<?php echo $share_text . '%0A%0A' . $share_url; ?>"
                 class="share-btn share-btn--email"
                 aria-label="<?php esc_attr_e( 'Share via email', 'up6' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </a>
              <button class="share-btn share-btn--copy" data-url="<?php echo esc_url( get_permalink() ); ?>"
                      aria-label="<?php esc_attr_e( 'Copy link', 'up6' ); ?>">
                <svg class="icon-link" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none"><polyline points="20 6 9 17 4 12"/></svg>
              </button>
            </div>
          </div>

        </header><!-- .entry-header -->

        <!-- Featured image -->
        <?php if ( has_post_thumbnail() ) :
          $up6_photo_caption = up6_get_photo_caption();
        ?>
          <figure class="entry-thumbnail<?php echo $up6_photo_caption ? ' has-caption' : ''; ?>">
            <?php the_post_thumbnail( 'ss-single', [
              'alt'           => get_the_title(),
              'fetchpriority' => 'high',
              'loading'       => 'eager',
              'decoding'      => 'async',
              'sizes'         => '(max-width: 640px) 100vw, (max-width: 1200px) 100vw, 1200px',
            ] ); ?>
            <?php if ( $up6_photo_caption ) : ?>
            <figcaption class="entry-thumbnail-caption"><?php echo esc_html( $up6_photo_caption ); ?></figcaption>
            <?php endif; ?>
          </figure>
        <?php endif; ?>

        <!-- Table of Contents — auto-generated by JS from h2/h3 headings -->
        <nav class="article-toc" aria-label="<?php esc_attr_e( 'Table of Contents', 'up6' ); ?>" hidden>
          <button class="article-toc-toggle" aria-expanded="false">
            <span class="article-toc-label"><?php esc_html_e( 'Contents', 'up6' ); ?></span>
            <svg class="article-toc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <ol class="article-toc-list"></ol>
        </nav>

        <!-- Content -->
        <div class="entry-content">
          <?php the_content(); ?>
        </div>

        <!-- Article voting -->
        <?php if ( (int) up6_opt( 'up6_vote_enabled' ) ) :
          $votes     = up6_get_votes();
          $already   = up6_user_has_voted( get_the_ID() );
          $threshold = max( 1, (int) up6_opt( 'up6_vote_threshold' ) );
          $show_counts = ( $votes['up'] + $votes['down'] ) >= $threshold;
          $vote_label  = up6_opt( 'up6_vote_label' );
        ?>
        <div class="entry-vote" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
          <?php if ( $vote_label ) : ?>
          <span class="entry-vote-label"><?php echo esc_html( $vote_label ); ?></span>
          <?php endif; ?>
          <div class="entry-vote-buttons">
            <button class="vote-btn vote-btn--up<?php echo $already === 'up' ? ' is-voted' : ''; ?>"
                    data-type="up" aria-label="<?php esc_attr_e( 'Vote up', 'up6' ); ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
              <?php if ( $show_counts ) : ?>
              <span class="vote-count vote-count--up"><?php echo esc_html( $votes['up'] ); ?></span>
              <?php endif; ?>
            </button>
            <button class="vote-btn vote-btn--down<?php echo $already === 'down' ? ' is-voted' : ''; ?>"
                    data-type="down" aria-label="<?php esc_attr_e( 'Vote down', 'up6' ); ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3H10zM17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/></svg>
              <?php if ( $show_counts ) : ?>
              <span class="vote-count vote-count--down"><?php echo esc_html( $votes['down'] ); ?></span>
              <?php endif; ?>
            </button>
          </div>
        </div>
        <?php endif; ?>

        <!-- Topic tags (native WP taxonomy — no plugins) -->
        <?php
        $tags = get_the_tags();
        if ( $tags ) :
        ?>
        <div class="entry-tags">
          <span class="entry-tags-label"><?php esc_html_e( 'Topic Tags', 'up6' ); ?></span>
          <?php foreach ( $tags as $tag ) : ?>
            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
              <?php echo esc_html( $tag->name ); ?>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

      </article>

      <?php
      // ── Related News ──────────────────────────────────────────
      $related_count = max( 3, min( 12, (int) up6_opt( 'up6_related_count' ) ) );
      if ( $related_count > 0 ) :
          $cats = get_the_category( get_the_ID() );
          if ( $cats ) :
              $cat_ids = wp_list_pluck( $cats, 'term_id' );

              $related = new WP_Query( [
                  'category__in'        => $cat_ids,
                  'post__not_in'        => [ get_the_ID() ],
                  'posts_per_page'      => $related_count,
                  'orderby'             => 'date',
                  'order'               => 'DESC',
                  'ignore_sticky_posts' => true,
              ] );
              if ( $related->have_posts() ) :
      ?>
      <section class="related-news" aria-label="<?php esc_attr_e( 'Related News', 'up6' ); ?>">
        <div class="section-header">
          <div class="section-header-label">
            <span class="section-dot" aria-hidden="true"></span>
            <h2><?php esc_html_e( 'Related News', 'up6' ); ?></h2>
          </div>
        </div>
        <div class="related-grid">
          <?php while ( $related->have_posts() ) : $related->the_post(); ?>
          <?php get_template_part( 'template-parts/card' ); ?>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </section>
      <?php
              endif;
          endif;
      endif;
      ?>

      <?php
      // Comments (native WordPress — no plugins required).
      if ( comments_open() || get_comments_number() ) {
          comments_template();
      }
      ?>

      <?php endwhile; ?>
    </main>

</div>

<?php get_footer(); ?>

<?php
/**
 * Comments template
 *
 * @package UP6
 * @since   2.1.0
 */

// Prevent direct access to this file.
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

  <?php if ( have_comments() ) : ?>

    <h2 class="comments-title">
      <?php
      printf(
          esc_html( _n( '%d Comment', '%d Comments', get_comments_number(), 'up6' ) ),
          number_format_i18n( get_comments_number() )
      );
      ?>
    </h2>

    <ol class="comment-list">
      <?php
      wp_list_comments( [
          'style'       => 'ol',
          'short_ping'  => true,
          'avatar_size' => 40,
      ] );
      ?>
    </ol>

    <?php
    the_comments_navigation( [
        'prev_text' => __( '← Older Comments', 'up6' ),
        'next_text' => __( 'Newer Comments →', 'up6' ),
    ] );
    ?>

  <?php endif; ?>

  <?php
  // Display "Comments are closed" only if there are existing comments.
  if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
  ?>
    <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'up6' ); ?></p>
  <?php endif; ?>

  <?php
  comment_form( [
      'title_reply'        => __( 'Leave a Comment', 'up6' ),
      'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
      'title_reply_after'  => '</h3>',
  ] );
  ?>

</div><!-- #comments -->

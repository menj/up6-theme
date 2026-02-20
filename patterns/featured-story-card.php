<?php
/**
 * Title: Featured Story Card
 * Slug: up6-suara-semasa/featured-story-card
 * Categories: up6-cards
 * Keywords: featured, story, card, sidebar
 * Description: Compact featured story with thumbnail, kicker, title, and date.
 */
?>
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"},"style":{"spacing":{"blockGap":"0.75rem"}}} -->
<div class="wp-block-group">

	<!-- wp:image {"width":"4.5rem","aspectRatio":"1","style":{"border":{"radius":"0.75rem"}},"className":"up6-thumb-square"} -->
	<figure class="wp-block-image up6-thumb-square" style="border-radius:0.75rem"><img alt="" style="aspect-ratio:1;width:4.5rem;object-fit:cover"/></figure>
	<!-- /wp:image -->

	<!-- wp:group {"layout":{"type":"default"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"textColor":"secondary-blue","fontSize":"kicker-label","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.06em","fontWeight":"600"}}} -->
		<p class="has-secondary-blue-color has-text-color has-kicker-label-font-size" style="font-weight:600;letter-spacing:0.06em;text-transform:uppercase">Category</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":3,"fontSize":"metadata-text","style":{"typography":{"fontWeight":"600","lineHeight":"1.35"}},"textColor":"primary-deep-blue"} -->
		<h3 class="has-primary-deep-blue-color has-text-color has-metadata-text-font-size" style="font-weight:600;line-height:1.35"><a href="#">Story headline goes here with enough text to wrap</a></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"textColor":"tertiary-blue","fontSize":"kicker-label"} -->
		<p class="has-tertiary-blue-color has-text-color has-kicker-label-font-size">19 Feb</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

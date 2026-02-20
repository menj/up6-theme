<?php
/**
 * Title: Breaking News Banner
 * Slug: up6-suara-semasa/breaking-news-banner
 * Categories: up6-suara-semasa
 * Keywords: breaking, news, alert, banner, live
 * Description: Red-accented breaking news banner that sits below the header.
 */
?>
<!-- wp:group {"backgroundColor":"accent-red","textColor":"surface-white","className":"up6-breaking-banner","layout":{"type":"constrained","contentSize":"72rem"},"style":{"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"}}}} -->
<div class="wp-block-group up6-breaking-banner has-surface-white-color has-accent-red-background-color has-text-color has-background" style="padding-top:0.5rem;padding-bottom:0.5rem">

	<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"},"style":{"spacing":{"blockGap":"0.75rem"}}} -->
	<div class="wp-block-group">

		<!-- wp:paragraph {"className":"is-style-breaking"} -->
		<p class="is-style-breaking">Breaking</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"textColor":"surface-white","fontSize":"metadata-text","style":{"typography":{"fontWeight":"500"}}} -->
		<p class="has-surface-white-color has-text-color has-metadata-text-font-size" style="font-weight:500"><a href="#" style="color:inherit;text-decoration:underline;text-underline-offset:2px">Breaking news headline goes here — tap to read more</a></p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

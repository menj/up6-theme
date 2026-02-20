<?php
/**
 * Title: Article Header
 * Slug: up6-suara-semasa/article-header
 * Categories: up6-article
 * Keywords: article, header, kicker, headline
 * Description: Full article header with kicker label, headline, dateline, and abstract box.
 * Block Types: core/post-content
 */
?>
<!-- wp:group {"className":"up6-article-header","layout":{"type":"constrained","contentSize":"42rem"},"style":{"spacing":{"padding":{"top":"3rem","bottom":"2rem"}}}} -->
<div class="wp-block-group up6-article-header" style="padding-top:3rem;padding-bottom:2rem">

	<!-- wp:paragraph {"className":"is-style-kicker"} -->
	<p class="is-style-kicker">Category</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1,"fontFamily":"system-serif","fontSize":"hero-headline","style":{"typography":{"fontWeight":"700","lineHeight":"1.15"}}} -->
	<h1 class="has-system-serif-font-family has-hero-headline-font-size" style="font-weight:700;line-height:1.15">Article Headline Goes Here</h1>
	<!-- /wp:heading -->

	<!-- wp:group {"className":"is-style-dateline","layout":{"type":"flex","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"0.5rem"}}} -->
	<div class="wp-block-group is-style-dateline">
		<!-- wp:paragraph {"textColor":"primary-deep-blue","fontSize":"metadata-text","style":{"typography":{"fontWeight":"600"}}} -->
		<p class="has-primary-deep-blue-color has-text-color has-metadata-text-font-size" style="font-weight:600">KUALA LUMPUR</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"textColor":"tertiary-blue","fontSize":"metadata-text"} -->
		<p class="has-tertiary-blue-color has-text-color has-metadata-text-font-size">— 20 Februari 2026 · Oleh Author Name</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"className":"up6-article-abstract","style":{"border":{"left":{"color":"var:preset|color|primary-deep-blue","width":"4px"}},"spacing":{"padding":{"top":"1.25rem","bottom":"1.25rem","left":"1.25rem","right":"1.25rem"}}}} -->
	<p class="up6-article-abstract" style="border-left-color:var(--wp--preset--color--primary-deep-blue);border-left-width:4px;padding-top:1.25rem;padding-right:1.25rem;padding-bottom:1.25rem;padding-left:1.25rem">Summary or abstract of the article goes here. This provides readers with a quick overview before diving into the full story.</p>
	<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

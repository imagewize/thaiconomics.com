<?php
/**
 * Title: Single Featured Post
 * Slug: moiraine/post-single-featured
 * Description: Displays a single selected post in a featured format
 * Categories: moiraine/posts
 * Keywords: post, single, feature
 * Viewport Width: 1280
 * Block Types: core/query
 * Post Types:
 * Inserter: false
 */
?>
<!-- wp:query {"queryId":2,"query":{"perPage":1,"pages":1,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"postIn":[26]},"align":"full"} -->
<div class="wp-block-query alignfull">
    <!-- wp:post-template {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|large"}},"layout":{"type":"default"}} -->
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|large","bottom":"var:preset|spacing|large"}}},"backgroundColor":"tertiary","layout":{"type":"constrained"}} -->
        <div class="wp-block-group has-tertiary-background-color has-background" style="padding-top:var(--wp--preset--spacing--large);padding-bottom:var(--wp--preset--spacing--large)">
            <!-- wp:post-featured-image {"isLink":true,"align":"wide"} /-->
            <!-- wp:post-title {"isLink":true,"align":"wide"} /-->
            <!-- wp:post-excerpt /-->
        </div>
        <!-- /wp:group -->
    <!-- /wp:post-template -->
</div>
<!-- /wp:query -->

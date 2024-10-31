<?php if($post_type=='video'): ?>
<div class="video-zmyer">
    <div class="video-frame-zmyer">
        <iframe width="640" height="640"
                src="<?php echo esc_url_raw($video_final_url) ?>"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
        </iframe>
    </div>
    <div class="video-description-zmyer">
        <p><?php echo esc_html($description) ?></p>
    </div>
</div>
<?php elseif($post_type=='news'): ?>
<div class="roomvu-news-container">
	<p><?php echo esc_html($article_body) ?></p>
	<a class="roomvu-news-continue" href="<?php echo esc_url_raw($article_url) ?>">
		<div>Continue to full article</div>
	</a>
    
</div>
<?php endif; ?>


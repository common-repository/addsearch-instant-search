<?php

get_header();
?>
<div class="wrap">
	<main id="main" class="site-main" role="main">
		<?php AddSearch::get_instance()->get_script_for_v2( true ); ?>
	</main>
</div>

<?php
get_footer();

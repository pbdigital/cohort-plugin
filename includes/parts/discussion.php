<?php
$group_id = get_field('associated_buddyboss_group');
$group = groups_get_group($group_id);  
$feed_link = bp_get_group_permalink($group) . 'activity?from_cohort=1';

?>
<style>
#page, #content, body {
    background: <?php echo buddyboss_theme_get_option('body_background');?>
}
</style>
<div class="cohort-dicussion-wrapper">
    <iframe src="<?= $feed_link ?>" width="100%" height="1200"></iframe>
</div>
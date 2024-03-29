

<div class="cohorts-body-right">
	<div class="cohorts-body-right-content">
		<h4> Hosts </h4>
		<ul class="hosts">
		<?php while( have_rows('hosts') ): the_row(); ?>
			<?php  ?>

			<?php
				$host = get_sub_field("host");
				$user_link = PBD_Cohorts::is_buddyboss_enabled() ? bp_core_get_user_domain( $host->ID ) : '#';
			?>
			<li>
				<a href="<?= $user_link ?>" target="_blank">
					<div class="hosts-details">
						<div class="hosts-image">
							<img src="<?php echo get_avatar_url($host->ID); ?>">
						</div>
						<div class="hosts-namepos">
							<?php echo $host->display_name . "\n"; ?>
							<p> <?php the_sub_field('description'); ?> </p>
						</div>
					</div>
				</a>
			</li>
		<?php endwhile; ?>
		</ul>
	</div>

	<?php
		//echo do_shortcode('[my_goal]');
	?>
</div>
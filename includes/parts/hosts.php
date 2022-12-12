<div class="cohorts-body-right">
					<div class="cohorts-body-right-content">
						<h1> Hosts </h1>
						<ul class="hosts">
						<?php while( have_rows('hosts') ): the_row(); ?>
							<?php $host = get_sub_field("host"); ?>
							<li>
								<a href="<?php //echo bbp_get_user_profile_url( $host->ID );?>" target="_blank">
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
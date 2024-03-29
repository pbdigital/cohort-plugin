<?php
/**
 * The template for displaying single cohort
 *
 *
 */

get_header();
?>
<div class="cohorts-main-container">
	<div id="cohorts-header">
		<div class="container">
			<div class="cohorts-header-content">
				<?php if( get_field('icon') ): ?>
					<div class="cohorts-header-left">
						<img src="<?php the_field('icon'); ?>" />
					</div>
				<?php endif; ?>
				
				<div class="cohorts-header-right">
					<div class="cohorts-header-right-mid">
						<?php
							// $now = new DateTime();
							// $start = new DateTime(get_field('start_date'));
							// if ($start > $now && get_field('length') && $date_diff->d > 0) {
							// 	$date_diff = $now->diff($start);
							// 	$date_diff = ($date_diff->d > 1) ? $date_diff->d.' Days' : $date_diff->d.' Day';
							// 	echo '<h6>Begins in ' . $date_diff . '</h6>';
							// }

							$now = new DateTime();
							$start_date = new DateTime(get_field('start_date'));
							$difference = $now->diff($start_date);
							$in_days = ($difference->d > 1) ? $difference->d.' Days' : $difference->d.' Day';

							
							echo '<h1>' . (get_field('title') ? get_field('title') : get_the_title()) . '</h1>';
							
							if ($in_days)
								echo '<h6>Begins in ' . $in_days . '</h6>';
						?>

					</div>

					<div class="cohorts-header-right-bot">
						<?php if( get_field('length') ): ?>
							<?php
								$now = new DateTime();
								$start = new DateTime(get_field('start_date'));
								$end = new DateTime(get_field('end_date'));
								$date_diff = $now->diff($start);
								if ( ($start->format('m') == $end->format('m')) && ($start->format('y') == $end->format('y')) ) {
									$schedule = $start->format('F d') . ' - ' . $end->format('d Y');
								} else {
									$schedule = $start->format('F d Y') . ' - ' . $end->format('F d Y');
								}
							?>
							<h5><?php the_field('length'); ?> <span>•</span> <?php echo $schedule; ?> </h5>
						<?php endif; ?>
						<?php
						
						?>
					</div>
					<div class="cohorts-header-right-top">
						<?php if( get_field('description') ): ?>
							<?php
								$now = new DateTime();
								$start_date = new DateTime(get_field('start_date'));
								$difference = $now->diff($start_date);
							?>
							<?php
							$text = get_field('description');
							$text = str_replace('<p>', '',  $text); // Remove <p> tags
							$text = str_replace('</p>', '<br><br>', $text); // Replace </p> with <br>
							?>
							<div class="more" style="display: none"><?php echo $text; ?> <?= $now > $start_date ? null : '' ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="cohorts-body">
		<div class="container">
			<div class="cohorts-body-top">
				<ul>
					<li><a href="#" class="tablinks active" onclick="cohortTabs(event, 'Overview')"> Overview </a></li>

					<?php if (PBD_Cohorts::is_buddyboss_activity_enabled() && get_field('show_discussion_tab')): ?>
						<li><a href="#" class="tablinks" onclick="cohortTabs(event, 'Discussion')"> <?php echo get_field('discussion_tab_label');?> </a></li>
					<?php endif; ?>

					<?php if (PBD_Cohorts::is_buddyboss_group_enabled() && get_field('show_members_tab')): ?>
						<li><a href="#" class="tablinks" onclick="cohortTabs(event, 'Members')"> <?php echo get_field('members_tab_label');?> </a></li>
					<?php endif; ?>
				</ul>
			</div>
			<div id="Overview" class="cohorts-body-content tabcontent">
				<div class="cohorts-body-left">
					<div class="cohorts-body-left-top">
						<?php if( have_rows('steps') ): ?>
							<ul class="cohorts-body-left-steps">
							<?php while( have_rows('steps') ): the_row(); ?>
								<?php
									$now = new DateTime();
									$step_start_date = new DateTime(get_sub_field('start_date'));
									$step_end_date = new DateTime(get_sub_field('end_date'));

									$locked = false;
									if ($now < $step_start_date) {
										$locked = true;
									}

									$total_hours = 0;
									$total_lessons = 0;
									$total_events = 0;
									$total_completed = 0;
									$total_tasks = 0;
									$types = get_sub_field('type');
									// print_r($types);
									foreach($types as $type) {
										if ($type['acf_fc_layout'] == 'course') {
											$total_hours += (int)$type['length'];
											$total_lessons += (function_exists('learndash_get_course_steps_count') ?  learndash_get_course_steps_count($type['course']->ID) : 0);


											$user_progress = (function_exists('learndash_user_get_course_progress') ? learndash_user_get_course_progress( get_current_user_id(),  $type['course']->ID ) : 0);
											$total_completed += $user_progress['completed'];

										}
										else if ($type['acf_fc_layout'] == 'event') {
											$total_events += 1;
										}
										else if ($type['acf_fc_layout'] == 'simple_link ') {
											$total_tasks += 1;
										}
									}

									$percent_total = ($total_completed) ? round($total_completed / $total_lessons * 100, 0) : 0 ;
									
									//Instead of basing hours on courses, admin can specify this value
									if (get_sub_field('hours') > 0){
										$total_hours = get_sub_field('hours');
									}
									// If $percent_total is not already set and $now is greater than $step_end_date,
									$percent_total = (!$percent_total && $now > $step_end_date) ? 100 : null;

								?>
								<li class="<?= ($now > $step_end_date) ? 'ended' : null ?> <?= $locked ? 'locked' : '' ?>" data-progress="<?= $percent_total; ?>">
									<div class="progressbar <?= ($now >= $step_start_date) ? 'started' : null ?>" data-progress="<?= $percent_total; ?>"> </div>
									<h3><?php the_sub_field('section_title'); ?></h3>
									<div class="cohorts-body-left-steps-contents">
										<div class="cohorts-body-left-steps-contents-main">
											<h4> <?php the_sub_field('title'); ?> </h4>
											<p> <?php the_sub_field('description'); ?> </p>
											<ul>
												<?php if (!get_sub_field('hide_hours_indicator')){ ?>
												<li> <img src="<?=  PBD_CO_URL . '/assets/images/timer.png' ?>"> <?= $total_hours ?> hours </li>
												<?php } ?>
												<?php if (!get_sub_field('hide_lessons_indicator')){ ?>
												<li> <img src="<?=  PBD_CO_URL . '/assets/images/player-icon.png' ?>"> <?= $total_lessons ?> lessons </li>
												<?php } ?>
												<?php if (!get_sub_field('hide_events_indicator')){ ?>
												<li> <img src="<?=  PBD_CO_URL . '/assets/images/calendar-icon.png' ?>"> <?= $total_events ?> event </li>
												<?php } ?>
											</ul>
										</div>
										<div class="cohorts-body-left-steps-contents-type">
											<?php if( have_rows('type') ): ?>
												<ul class="cohorts-body-left-steps-contents-typesec">
												<?php while( have_rows('type') ): the_row(); ?>
													<?php
														
														$row_layout = get_row_layout();
														
														$post_object = ( $row_layout == 'course' ) ? get_sub_field('course') : get_sub_field('event');

														if ($row_layout == 'course')
															$post_object = get_sub_field('course');
														else if ($row_layout == 'event')
															$post_object = get_post(get_sub_field('event'));
														else if ($row_layout == 'statement_of_commitment')
															$post_object = get_sub_field('soc');
														else if ($row_layout == 'simple_link')
															$post_object = get_sub_field('simple_link');
														else if ($row_layout == 'document')
															$post_object = get_sub_field('document');
														else if ($row_layout == 'call_recording')
															$post_object = get_sub_field('call_recording');
														else if ($row_layout == 'video')
															$post_object = get_sub_field('video');
														else if ($row_layout == 'introduce_yourself')
														$post_object = get_sub_field('introduce_yourself');
														if ($row_layout == 'simple_link') {
															$post_title = get_sub_field('title');
															$post_sub_title = get_sub_field('description');
														} else {
															$post_title = get_sub_field('title') ? get_sub_field('title') : $post_object->post_title;
															$post_sub_title = get_sub_field('description') ? get_sub_field('description') : $post_object->post_excerpt;
														}
														
													?>
													<li>
														<div class="cohorts-body-contents-type-details">
															<div class="cohorts-body-contents-type-details-img">
																<?php
																	if ($row_layout == 'course') {
																		?> <img src="<?=  PBD_CO_URL . '/assets/images/course-icon.png' ?>"> <?php
																	} 
																	else if ($row_layout == 'statement_of_commitment') {
																		$soc_user = get_user_meta(get_current_user_id(), 'soc_'. $post_object->ID, true);
																		if ($soc_user) {
																			?>
																				<div class="progressbar progressbar-soc started" data-progress="0"> </div>
																			<?php
																		} else {
																			?><img src="<?=  PBD_CO_URL . '/assets/images/soc-icon.png' ?>"><?php
																		}
																	} else if ($row_layout == 'simple_link') {
																		?> <img src="<?=  PBD_CO_URL . '/assets/images/link-icon.png' ?>"> <?php
																	} else if ($row_layout == 'document') {
																		?> <img src="<?=  PBD_CO_URL . '/assets/images/document-icon.png' ?>"> <?php
																	}
																	else if ($row_layout == 'call_recording') {
																		?> <img src="<?=  PBD_CO_URL . '/assets/images/video-icon.png' ?>"> <?php
																	}
																	else if ($row_layout == 'video') {
																		?> <img src="<?=  PBD_CO_URL . '/assets/images/video-icon.png' ?>"> <?php
																	}
																	else if ($row_layout == 'introduce_yourself') {
																		?> <img src="<?=  PBD_CO_URL . '/assets/images/introduce-icon.png' ?>"> <?php
																	}
																	else {
																		$event_month = tribe_get_start_date($post_object->ID, true, 'M');
																		$event_day = tribe_get_start_date($post_object->ID, true, 'd');
																		echo "<p> ". $event_month ." <span> ". $event_day ." </span> </p>";
																		// echo 'here'. $post_object;
																	}
																?>
															</div>
															<div class="cohorts-body-contents-type-details-txt">
																<h4> <?php echo $post_title ?> </h4>
																<p> <?php echo  $post_sub_title ?> </p>
																<div class="cohorts-body-contents-type-details-bot">
																	<div class="cohorts-body-contents-type-details-bot-left">
																		<?php
																			if ($row_layout == 'course') {
																				$user_progress = (function_exists('learndash_user_get_course_progress') ? learndash_user_get_course_progress( get_current_user_id(),  $post_object->ID ) : array('total' => 0, 'completed' => 0));
																				$total_lessons = $user_progress['total'];
																				$completed = $user_progress['completed'];

																				if ($completed) {
																					$percentage = round($completed / $total_lessons * 100, 0);
																				} else {
																					$percentage = 0;
																				}
																				

																				?>
																				<div class="cohorts-body-contents-type-details-bot-left-definition">
																					<ul>
																						<li> <img src="<?=  PBD_CO_URL . '/assets/images/timer.png' ?>"> <?php the_sub_field('length'); ?> Hours </li>
																						<li> <img src="<?=  PBD_CO_URL . '/assets/images/player-icon.png' ?>"> <?= $total_lessons ?> Lessons </li>
																					</ul>
																				</div>
																				<div class="cohorts-body-contents-type-details-bot-left-percentage">
																					<p> <?= $percentage ?> % Completed </p>
																				</div>
																				<div class="cohorts-body-contents-type-details-bot-left-progress">
																					<progress id="file" value="<?= $percentage ?>" max="100"> </progress> 
																				</div>
																				<?php
																			} 
																			else if ($row_layout == 'event') {
																				// Joshua design for event here
																				$event_day = tribe_get_start_date($post_object->ID, true, 'l, F d, Y');
																				$event_time = tribe_get_start_date($post_object->ID, true, 'g:i A');
																				echo "<p> $event_day </p>";
																				echo "<p> $event_time </p>";
																			}
																		?>
																	</div>
																	<div class="cohorts-body-contents-type-details-bot-right">
																		<?php
																			if ($row_layout == 'simple_link') {
																				?> <a href="<?php echo get_sub_field('url'); ?>" <?= (get_sub_field('open_in_new_tab')) ? 'target="_blank"' : '' ?>> <?php echo get_sub_field('button_text'); ?> </a> <?php
																			}
																			else if ($row_layout == 'document') {
																				?> <a href="<?php echo get_sub_field('url'); ?>" <?= (get_sub_field('open_in_new_tab')) ? 'target="_blank"' : '' ?>> <?php echo get_sub_field('button_text'); ?> </a> <?php
																			}  
																			else if ($row_layout == 'introduce_yourself') {
																				?> <a href="<?php echo get_sub_field('url'); ?>" <?= (get_sub_field('open_in_new_tab')) ? 'target="_blank"' : '' ?>> 
																				<img src="<?=  PBD_CO_URL . '/assets/images/hand-icon.png' ?>" />
																				<?php echo get_sub_field('button_text'); ?> </a> <?php
																			}
																			else if ($row_layout == 'video') {
																				?> <a href="<?php echo get_sub_field('url'); ?>" class="video-btn" 
																				data-embed="<?php echo base64_encode(get_sub_field('embed_code'));?>"> 
																				<?php echo get_sub_field('button_text'); ?> </a> <?php
																			} 
																			else if ($row_layout == 'call_recording') {
																				$recording = get_sub_field('call_recording');
																				$url = get_permalink($recording->ID);
																				?> <a href="<?php echo $url; ?>" <?= (get_sub_field('open_in_new_tab')) ? 'target="_blank"' : '' ?>> <?php echo get_sub_field('button_text'); ?> </a>  <?php
																			} 
																			else if ($row_layout == 'event') {

																				$attendee_groups = false;
																				if (class_exists('Tribe__Tickets__Tickets_View')) {
																					$view = Tribe__Tickets__Tickets_View::instance();
																					$attendee_groups = $view->get_event_rsvp_attendees_by_purchaser( $post_object->ID, get_current_user_id() );
																				}
																				

																				if ($attendee_groups) {
																					?> <a href="<?php echo get_permalink($post_object->ID); ?>">
																						<img src="<?=  PBD_CO_URL . '/assets/images/check.png' ?>" />
																					Going 
																					</a> <?php
																				} else {
																					?> <a href="<?php echo get_permalink($post_object->ID); ?>">   <?= tribe_is_past_event($post_object->ID) ? 'Watch Replay' : 'Attend' ?> </a> <?php
																				}	

																				
																			} 
																			else if ($row_layout == 'statement_of_commitment') {
																				$soc_user = get_user_meta(get_current_user_id(), 'soc_'. $post_object->ID, true);
																				if ($soc_user) {
																					?> <a href="#" > SIGNED </a> <?php
																				} else {
																					?> <a href="#" class="sign-commitment" data-id="<?php echo $post_object->ID ?>" data-modal-title="<?= get_sub_field('modal_title') ?>" data-modal-description="<?= get_sub_field('modal_description') ?>"> VIEW + SIGN </a> <?php
																				}
																			}
																			else {
																				?> <a href="<?php echo get_permalink($post_object->ID); ?>"> VIEW </a> <?php
																			}
																		?>
																		
																	</div>
																</div>
															</div>
														</div>
													</li>
												<?php endwhile; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
								</li>
							<?php endwhile; ?>
								<div class="progress-finish"> 
									<h3> FINISH </h3>
								</div>
							</ul>
						<?php endif; ?>
					</div>
				</div>

				<?php 
					if( have_rows('hosts') ) {
						include_once(PBD_CO_INCLUDES_PATH . '/parts/hosts.php');
					}
				?>
				
			</div>
			
			<?php if (PBD_Cohorts::is_buddyboss_activity_enabled() && get_field('show_discussion_tab')): ?>
				<div id="Discussion" class="cohorts-body-content tabcontent discussion-tabcontent" style="display:none">
					<?php include_once(PBD_CO_INCLUDES_PATH . '/parts/discussion.php');  ?>
				</div>
			<?php endif; ?>

			<?php if (PBD_Cohorts::is_buddyboss_group_enabled() && get_field('show_members_tab')): ?>
				<div id="Members" class="cohorts-body-content tabcontent members-tabcontent" style="display:none">
					<div id="buddypress" class="buddypress-wrap bp-single-plain-nav bp-dir-hori-nav">
						<div id="members-group-list" class="group_members dir-list">
							<?php include_once(PBD_CO_INCLUDES_PATH . '/parts/members-v2.php');  ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
		</div>
	</div>
</div>

<!-- The Modal -->
<div id="cohortModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6 6 18M6 6l12 12" stroke="#B3B3B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
    <h3 class="modal-title">Statement of Commitment</h3>
	<p class="modal-description">Please check each item below to indicate how you'll engate with the content & community.</p>
	<form action="" id="soc-form">
		
		<div class="checkbox-section">

		</div>
		<div class="form-group">
			<label for="">Your Signature</label>
			<input type="text" name="signature" val="" require/>
		</div>
		<br/>
		<input type="submit" id="submit-soc" value="Submit" />
	</form>
  </div>

</div>

<!-- Video Modal -->
<div id="videoModal" class="modal">
	<!-- Modal content -->
	<div class="modal-content">
	<span class="close"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6 6 18M6 6l12 12" stroke="#B3B3B3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
		<div class="embed-code">
			<div class="fluid-width-video-wrapper"></div>
		</div>
	</div>
</div>

<style>
	.cohorts-main-container {
		--cohort-body-color: <?= PBD_Cohorts::is_buddyboss_enabled() ? buddyboss_theme_get_option('body_background') : '';?>;
		--cohort-body-bg-color: <?= get_field('single_body_color', 'option') ? : '#FAF9F7' ?>;
		--cohort-container-width: <?= get_field('single_container_max_width', 'option') ? : '1000' ?>px;
		--cohort-progress-color: <?= PBD_Cohorts::is_buddyboss_enabled() ? buddyboss_theme_get_option('header_links_hover') : '#66D697';?>;
	}
</style>

<?php
add_action("wp_footer", function(){ 
    ?>
    
    <script>
		const fillColor = '<?= PBD_Cohorts::is_buddyboss_enabled() ? buddyboss_theme_get_option('header_links_hover') : '#66D697';?>';
		// Get the modal
		var modal = document.getElementById("cohortModal");
		
		// Get the video modal
		var video_modal = document.getElementById("videoModal");
		var embedCodeDiv = video_modal.querySelector('.embed-code .fluid-width-video-wrapper');
		
		// Get the button that opens the modal
		var btn = document.getElementById("myBtn");

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("close")[0];

		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
			modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}

			if (event.target == video_modal) {
				video_modal.style.display = "none";
				//Destory the video so it will stop playing by setting it to blank
				embedCodeDiv.innerHTML = "&nbsp;";
			}
		}

        jQuery(document).ready(function ($) {
			$(document).on('click', '.close', function () {
				$('.modal').fadeOut();
			})
			$('.cohorts-body-left-steps > li').each(function () {
				let percentVal = $('> .progressbar',this).data('progress');
				if (isNaN(percentVal)) {
					percentVal = 0;
				}
				console.log(percentVal);
				$(this,'> .progressbar').circleProgress({
					startAngle: -1.55,
					value: percentVal / 100,
					size: 40,
					lineCap: 'round',
					emptyFill: '#E7E7E8',
					fill: {color: fillColor}
				  });
			})
			
			$(document).on('click', '.video-btn', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				var embed_code = $(this).data('embed');

				$('#videoModal .embed-code .fluid-width-video-wrapper').html(atob(embed_code));
				video_modal.style.display = "block";
			})
			
			
			$(document).on('click', '.sign-commitment', function(e) {
				e.preventDefault()
				var id = $(this).data('id')
				var modal_title = $(this).data('modal-title')
				var modal_description = $(this).data('modal-description')

				console.log('id')
				$.ajax({
		             type : "POST",
		             url : '<?= admin_url( 'admin-ajax.php' ) ?>', 
		             data : {
						action: "get_statement_of_commitment", 
						id: id
					},
		             success: function(response) {
						response = JSON.parse(response)

						let questions = response.questions.map(item => {
							return `<div class="checkbox-label">
									<label><input type="checkbox" name="questions[]"><span><svg width="14" height="10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m12.636 1-8 8L1 5.364" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>${item}</span></label>
								</div>`
						})
						
						$('#soc-form .checkbox-section').html(  questions)
						$('#soc-form .checkbox-section').prepend('<input type="hidden" id="soc-id" value="'+id+'" />')
						$('.modal-title').html(modal_title)
						$('.modal-description').html(modal_description)
						modal.style.display = "block";
		            }
		        }); 
			})

			$('#soc-form').submit(function(e) {
				e.preventDefault();
				var $boxes = $('input[name="questions[]"]');

				var all_checked = true
				$boxes.each(function(){
					if (! $(this).is(":checked") ) {
						all_checked = false
					}
				});

				if (!all_checked || !$('input[name=signature]').val() ) {
					alert('All fields are required');
					return
				}

				$.ajax({
		             type : "POST",
		             url : '<?= admin_url( 'admin-ajax.php' ) ?>', 
		             data : {
						action: "set_user_commitment", 
						id: $('#soc-id').val()
					},
		             success: function(response) {
						location.reload();
		            }
		        }); 
			})
		});

		function cohortTabs(evt, indicator) {
			evt.preventDefault();
			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			tablinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			document.getElementById(indicator).style.display = "grid";
			evt.currentTarget.className += " active";
		}

		jQuery('li.locked a').click(function(e){
			e.preventDefault();
		});
    </script>
    <?php
}, 999 );

get_footer();

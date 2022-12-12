<?php
/**
 * The template for displaying single cohort
 *
 *
 */

get_header();
?>
<div id="cohorts-header">
	<div class="container">
		<div class="cohorts-header-content">
			<div class="cohorts-header-left">
				<?php if( get_field('icon') ): ?>
					<img src="<?php the_field('icon'); ?>" />
				<?php endif; ?>
			</div>
			<div class="cohorts-header-right">
				<div class="cohorts-header-right-top">
					<?php if( get_field('description') ): ?>
						<?php
							$now = new DateTime();
							$start_date = new DateTime(get_field('start_date'));
							$difference = $now->diff($start_date);
						?>
						<h6><?php the_field('description'); ?> <?= $now > $start_date ? null : '<span>•</span>  <span class="range"> Begins in '. $difference->d .' days </span>' ?>  </h6>
					<?php endif; ?>
				</div>
				<div class="cohorts-header-right-mid">
					<h1> <?php echo get_the_title() ?> </h1>
				</div>
				<div class="cohorts-header-right-bot">
					<?php if( get_field('length') ): ?>
						<?php
							$start = new DateTime(get_field('start_date'));
							$end = new DateTime(get_field('end_date'));
							if ( ($start->format('m') == $end->format('m')) && ($start->format('y') == $end->format('y')) ) {
								$schedule = $start->format('F d') . ' - ' . $end->format('d Y');
							} else {
								$schedule = $start->format('F d Y') . ' - ' . $end->format('F d Y');
							}
						?>
						<h6><?php the_field('length'); ?> <span>•</span> <?php echo $schedule; ?> </h6>
					<?php endif; ?>
					<?php
					
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="cohorts-body">
	<div class="container">
		<div class="cohorts-body-top">
			<ul>
				<li> <a href="#"> Overview </a></li>
			</ul>
		</div>
		<div class="cohorts-body-content">
			<div class="cohorts-body-left">
				<div class="cohorts-body-left-top">
					<?php if( have_rows('steps') ): ?>
						<ul class="cohorts-body-left-steps">
						<?php while( have_rows('steps') ): the_row(); ?>
							<?php
								$now = new DateTime();
								$step_start_date = new DateTime(get_sub_field('start_date'));
								$step_end_date = new DateTime(get_sub_field('end_date'));

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
										$total_lessons += learndash_get_course_steps_count($type['course']->ID);


										$user_progress = learndash_user_get_course_progress( get_current_user_id(),  $type['course']->ID );
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
							?>
							<li class="<?= ($now > $step_end_date) ? 'ended' : null ?>">
								<div class="progressbar <?= ($now >= $step_start_date) ? 'started' : null ?>" data-progress="<?= $percent_total; ?>"> </div>
								<h3><?php the_sub_field('section_title'); ?></h3>
								<div class="cohorts-body-left-steps-contents">
									<div class="cohorts-body-left-steps-contents-main">
										<h2> <?php the_sub_field('title'); ?> </h2>
										<p> <?php the_sub_field('description'); ?> </p>
										<ul>
											<li> <img src="<?=  PBD_CO_URL . '/assets/images/timer.png' ?>"> <?= $total_hours ?> hours </li>
											<li> <img src="<?=  PBD_CO_URL . '/assets/images/player-icon.png' ?>"> <?= $total_lessons ?> lessons </li>
											<li> <img src="<?=  PBD_CO_URL . '/assets/images/calendar-icon.png' ?>"> <?= $total_events ?> event </li>
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
														$post_object = get_sub_field('event');
													else if ($row_layout == 'statement_of_commitment')
														$post_object = get_sub_field('soc');
													else if ($row_layout == 'simple_link')
														$post_object = get_sub_field('simple_link');

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
																}
																else {
																	$event_month = tribe_get_start_date($post_object->ID, true, 'M');
																	$event_day = tribe_get_start_date($post_object->ID, true, 'd');
																	echo "<p> ". $event_month ." <span> ". $event_day ." </span> </p>";
																}
															?>
														</div>
														<div class="cohorts-body-contents-type-details-txt">
															<h2> <?php echo $post_title ?> </h2>
															<p> <?php echo  $post_sub_title ?> </p>
															<div class="cohorts-body-contents-type-details-bot">
																<div class="cohorts-body-contents-type-details-bot-left">
																	<?php
																		if ($row_layout == 'course') {
																			$user_progress = learndash_user_get_course_progress( get_current_user_id(),  $post_object->ID );
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
																			?> <a href="<?php echo get_sub_field('url'); ?>" <?= (get_field('open_in_new_tab')) ? 'target="_blank"' : '' ?>> <?php echo get_sub_field('button_text'); ?> </a> <?php
																		} 
																		else if ($row_layout == 'event') {

																			$attendee_groups = false;
																			if (class_exists('Tribe__Tickets__Tickets_View')) {
																				$view = Tribe__Tickets__Tickets_View::instance();
																				$attendee_groups = $view->get_event_rsvp_attendees_by_purchaser( $post_object->ID, get_current_user_id() );
																			}
																			

																			if ($attendee_groups) {
																				?> <a href="<?php echo tribe_get_event_link($post_object->ID); ?>">
																					<img src="<?=  PBD_CO_URL . '/assets/images/check.png' ?>" />
																				 Going 
																				 </a> <?php
																			} else {
																				?> <a href="<?php echo tribe_get_event_link($post_object->ID); ?>"> Attend </a> <?php
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

<style>
	.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 10000; /* Sit on top */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background: rgba(0, 0, 0, 0.5);
	}

	/* Modal Content/Box */
	.modal-content {
		background-color: #fefefe;
		margin: 10% auto 0 !important;
		border: 1px solid #888;
		width: calc(100% - 40px);
		max-width: 740px;
		padding:57px 50px;
		height:auto;
	}
	@media (max-width:601px) {
		.modal-content {
			padding:50px 20px;
		}
	}

	/* The Close Button */
	.close {
		position:absolute;
		cursor:pointer;
		top:20px;
		right:20px;
	
	}
	.close svg {
		width:auto;
	}

	.close:hover,
	.close:focus {
		color: black;
		text-decoration: none;
		cursor: pointer;
	}

	.checkbox-label {
		display: flex;
		flex-direction: row;
		align-items: center;
		padding: 0px;
		gap: 16px;
		font-weight: 400;
		font-size: 14px;
		line-height: 17px;
		/* identical to box height */


		/* dark text */

		color: #393E41;
	}

	.checkbox-label input {
		width: 20px;
		height: 20px;
	}

	.form-group {
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		padding: 0px;
		gap: 10px;

	}
</style>

<?php
add_action("wp_footer", function(){ 
    ?>
    
    <script>
		// Get the modal
		var modal = document.getElementById("cohortModal");

		// Get the button that opens the modal
		var btn = document.getElementById("myBtn");

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("close")[0];

		// When the user clicks on the button, open the modal
		// btn.onclick = function() {
		// modal.style.display = "block";
		// }

		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
			modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
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
					fill: {color: '#66D697'}
				  });
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
		})
    </script>
    <?php
}, 999 );

get_footer();

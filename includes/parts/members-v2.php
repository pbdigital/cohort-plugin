<?php
/**
 * Group Members Loop template
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/single/members-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

$footer_buttons_class = ( bp_is_active( 'friends' ) && bp_is_active( 'messages' ) ) ? 'footer-buttons-on' : '';

$is_follow_active = bp_is_active( 'activity' ) && function_exists( 'bp_is_activity_follow_active' ) && bp_is_activity_follow_active();
$follow_class     = $is_follow_active ? 'follow-active' : '';

// Member directories elements.
$enabled_online_status = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'online-status' );
$enabled_profile_type  = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'profile-type' );
$enabled_followers     = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'followers' );
$enabled_last_active   = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'last-active' );
$enabled_joined_date   = ! function_exists( 'bb_enabled_member_directory_element' ) || bb_enabled_member_directory_element( 'joined-date' );

$members = groups_get_group_members(array(
    'group_id' => get_field('associated_buddyboss_group'),
    'exclude_admins_mods' => 0
));
?>
<?php 
// if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) . '&type=group_role&exclude_admin_mods=0&page=&per_page=-1&group_id=' . get_field('associated_buddyboss_group') ) ) : 

if (bp_group_has_members([
    'group_id' => get_field('associated_buddyboss_group'),
	'exclude_admin_mods' => false,
	'group_role' => 'admin,mod,member'
])) :
?>

	<?php bp_nouveau_group_hook( 'before', 'members_content' ); ?>

	<?php //bp_nouveau_pagination( 'top' ); ?>

	<?php bp_nouveau_group_hook( 'before', 'members_list' ); ?>

	<ul id="members-list" class="item-list members-group-list bp-list grid members-list">

		<?php
		while ( bp_group_members() ) :
            bp_group_the_member();

			bp_group_member_section_title();

			// Check if members_list_item has content.
			ob_start();
			bp_nouveau_member_hook( '', 'members_list_item' );
			$members_list_item_content = ob_get_clean();
			$member_loop_has_content   = ! empty( $members_list_item_content );

			// Get member followers element.
			$followers_count = '';
			if ( $enabled_followers && function_exists( 'bb_get_followers_count' ) ) {
				ob_start();
				bb_get_followers_count( bp_get_member_user_id() );
				$followers_count = ob_get_clean();
			}

			// Get member followers element.
            $followers_count = '';
            $followers = bp_get_followers( array( 'user_id' => bp_get_member_user_id() ) );
            $followers_count = count( $followers );

			// Member joined data.
			$member_joined_date = bp_get_group_member_joined_since();

			// Member last activity.
			$member_last_activity = bp_get_last_activity( bp_get_member_user_id() );

			// Primary and secondary profile action buttons.
			$profile_actions = bb_member_directories_get_profile_actions( bp_get_member_user_id() );

			// Member switch button.
			$member_switch_button = bp_get_add_switch_button( bp_get_member_user_id() );

			// Get Primary action.
			$primary_action_btn = function_exists( 'bb_get_member_directory_primary_action' ) ? bb_get_member_directory_primary_action() : '';
			$moderation_class   = function_exists( 'bp_moderation_is_user_suspended' ) && bp_moderation_is_user_suspended( bp_get_group_member_id() ) ? 'bp-user-suspended' : '';
			$moderation_class   = function_exists( 'bp_moderation_is_user_blocked' ) && bp_moderation_is_user_blocked( bp_get_group_member_id() ) ? $moderation_class . ' bp-user-blocked' : $moderation_class;
			?>
			<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( bp_get_group_member_id() ); ?>" data-bp-item-component="members">
				<div class="list-wrap <?php echo esc_attr( $footer_buttons_class ); ?> <?php echo esc_attr( $follow_class ); ?> <?php echo $member_loop_has_content ? esc_attr( ' has_hook_content' ) : esc_attr( '' ); ?> <?php echo ! empty( $profile_actions['secondary'] ) ? esc_attr( 'secondary-buttons' ) : esc_attr( 'no-secondary-buttons' ); ?> <?php echo ! empty( $primary_action_btn ) ? esc_attr( 'primary-button' ) : esc_attr( 'no-primary-buttons' ); ?>">

					<div class="list-wrap-inner">
						<div class="item-avatar">
							<a href="<?php bp_group_member_domain(); ?>" class="<?php echo esc_attr( $moderation_class ); ?>">
								<?php
								if ( $enabled_online_status ) {
									bb_user_presence_html( bp_get_group_member_id() );
								}
								bp_group_member_avatar();
								if ( groups_is_user_admin( bp_get_member_user_id(),get_field('associated_buddyboss_group') )){
									echo '<p class="item-meta member-type info-'.strip_tags(bp_get_user_member_type(bp_get_member_user_id())).'"><span class="bp-member-type">Coach</span></p>';
								}else {
									echo '<p class="item-meta member-type info-'.strip_tags(bp_get_user_member_type(bp_get_member_user_id())).'">' . wp_kses_post(bp_get_user_member_type(bp_get_member_user_id())) . '</p>';
								}
								
								?>
							</a>
						</div>

						<div class="item">

							<div class="item-block">

								<?php
								if ( $enabled_profile_type && function_exists( 'bp_member_type_enable_disable' ) && true === bp_member_type_enable_disable() && true === bp_member_type_display_on_profile() ) {
									echo '<p class="item-meta member-type only-grid-view">' . wp_kses_post( bp_get_user_member_type( bp_get_member_user_id() ) ) . '</p>';
								}
								?>

								<h2 class="list-title member-name">
									<?php bp_group_member_link(); ?>
								</h2>

								<?php
								if ( $enabled_profile_type && function_exists( 'bp_member_type_enable_disable' ) && true === bp_member_type_enable_disable() && true === bp_member_type_display_on_profile() ) {
									echo '<p class="item-meta member-type only-list-view">' . wp_kses_post( bp_get_user_member_type( bp_get_member_user_id() ) ) . '</p>';
								}
								?>

								<?php if ( ( $enabled_last_active && $member_last_activity ) || ( $enabled_joined_date && $member_joined_date ) ) : ?>
									<p class="item-meta last-activity">

										<?php
										if ( $enabled_joined_date ) :
											echo wp_kses_post( $member_joined_date );
										endif;
										?>

										<?php if ( ( $enabled_last_active && $member_last_activity ) && ( $enabled_joined_date && $member_joined_date ) ) : ?>
											<span class="separator">&bull;</span>
										<?php endif; ?>

										<?php
										if ( $enabled_last_active ) :
											echo wp_kses_post( $member_last_activity );
										endif;
										?>

									</p>
								<?php endif; ?>
							</div>

							<div class="flex align-items-center follow-container justify-center member-details" style="font-size: 14px;color: #737373;">
								<!-- <?php //echo wp_kses_post( $followers_count ); ?> -->
								<b><?=$followers_count?></b>&nbspfollowers
							</div>

							<div class="flex only-grid-view align-items-center primary-action justify-center">
								<?php echo wp_kses_post( $profile_actions['primary'] ); ?>
							</div>
						</div><!-- // .item -->

						<div class="member-buttons-wrap">

							<?php //if ( $profile_actions['secondary'] ) { ?>
								<div class="flex only-grid-view button-wrap member-button-wrap footer-button-wrap">
									<?php echo wp_kses_post( $profile_actions['secondary'] ); ?>
								</div>
							<?php //} ?>

							<?php //if ( $profile_actions['primary'] ) {
                                if (false) { ?>
								<div class="flex only-list-view align-items-center primary-action justify-center">
									<?php echo wp_kses_post( $profile_actions['primary'] ); ?>
								</div>
							<?php } ?>

						</div><!-- .member-buttons-wrap -->

					</div>

					<div class="bp-members-list-hook">
						<?php if ( $member_loop_has_content ) { ?>
							<a class="more-action-button" href="#"><i class="bb-icon-menu-dots-h"></i></a>
						<?php } ?>
						<div class="bp-members-list-hook-inner">
							<?php bp_nouveau_member_hook( '', 'members_list_item' ); ?>
						</div>
					</div>

					<?php //if ( ! empty( $member_switch_button ) ) { 
                        if ( false ) {?>
					<div class="bb_more_options member-dropdown">
						<a href="#" class="bb_more_options_action">
							<i class="bb-icon-menu-dots-h"></i>
						</a>
						<div class="bb_more_options_list">
							<?php echo wp_kses_post( bp_get_add_switch_button( bp_get_member_user_id() ) ); ?>
						</div>
					</div><!-- .bb_more_options -->
					<?php } ?>
				</div>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php bp_nouveau_group_hook( 'after', 'members_list' ); ?>

	<?php //bp_nouveau_pagination( 'bottom' ); ?>

	<?php bp_nouveau_group_hook( 'after', 'members_content' ); ?>

	<?php
else :

	bp_nouveau_user_feedback( 'group-members-none' );

endif;
?>

<!-- Remove Connection confirmation popup -->
<div class="bb-remove-connection bb-action-popup" style="display: none">
	<transition name="modal">
		<div class="modal-mask bb-white bbm-model-wrap">
			<div class="modal-wrapper">
				<div class="modal-container">
					<header class="bb-model-header">
						<h4><span class="target_name"><?php echo esc_html__( 'Remove Connection', 'buddyboss' ); ?></span></h4>
						<a class="bb-close-remove-connection bb-model-close-button" href="#">
							<span class="bb-icon-l bb-icon-times"></span>
						</a>
					</header>
					<div class="bb-remove-connection-content bb-action-popup-content">
						<p>
							<?php
							echo sprintf(
								/* translators: %s: The member name with HTML tags */
								esc_html__( 'Are you sure you want to remove %s from your connections?', 'buddyboss' ),
								'<span class="bb-user-name"></span>'
							);
							?>
						</p>
					</div>
					<footer class="bb-model-footer flex align-items-center">
						<a class="bb-close-remove-connection bb-close-action-popup" href="#"><?php echo esc_html__( 'Cancel', 'buddyboss' ); ?></a>
						<a class="button push-right bb-confirm-remove-connection" href="#"><?php echo esc_html__( 'Confirm', 'buddyboss' ); ?></a>
					</footer>
				</div>
			</div>
		</div>
	</transition>
</div> <!-- .bb-remove-connection -->
<style>
	#members-list.item-list.grid .member-type {
		margin-bottom: 0;
		margin-top: -16px;
	}

	.info-Member span.bp-member-type {
		background-color: #2E6EEA;
		color: #fff;
		font-weight: 600;
		padding: 8px 13px;
		border-radius: 8px;
	}
</style>
<script>
	jQuery(function($){
		var btnClicked = 0

		function changeData() {
			btnClicked = 0
		}

		$(document).on('click', '.friendship-button', function ( event ) {
			if (btnClicked == 1)
				return;

			btnClicked = 1
			event.preventDefault();
			

			var target = $( event.currentTarget )
			var action = target.data( 'bp-btn-action' )
			var item = target.closest( '[data-bp-item-id]' ) 
			var item_id = item.data( 'bp-item-id' )
			var object = item.data( 'bp-item-component' )
			var current_page = 'directory';
			var button_clicked  = 'primary';
			var component = item.data( 'bp-used-to-component' );
			var item_inner = target.closest( '.list-wrap' );

			var urllink = target.attr('href')

			var url = new URL(urllink);
			var nonce = url.searchParams.get("_wpnonce");

			var friends_actions_map = {
				is_friend: 'remove_friend',
				not_friends: 'add_friend',
				pending: 'withdraw_friendship',
				accept_friendship: 'accept_friendship',
				reject_friendship: 'reject_friendship'
			};

			if ( 'members' === object && undefined !== friends_actions_map[ action ] ) {
				action = friends_actions_map[ action ];
				object = 'friends';
			}

			var follow_actions_map = {
				not_following: 'follow',
				following: 'unfollow'
			};

			if ( 'members' === object && undefined !== follow_actions_map[ action ] ) {
				action = follow_actions_map[ action ];
				object = 'follow';
			}

			target.addClass( 'pending loading' );

			var current_page = 'directory';
			if ( ( $( document.body ).hasClass( 'directory' ) && $( document.body ).hasClass( 'members' ) ) || $( document.body ).hasClass( 'group-members' ) ) {
				current_page = 'directory';
			} else if ( $( document.body ).hasClass( 'bp-user' ) ) {
				current_page = 'single';
			}

			var button_clicked  = 'primary';
			var button_activity = ( 'single' === current_page ) ? target.closest( '.header-dropdown' ) : target.closest( '.footer-button-wrap' );

			if ( typeof button_activity.length !== 'undefined' && button_activity.length > 0 ) {
				button_clicked = 'secondary';
			}

			component = 'undefined' === typeof component ? object : component;

			$.ajax({
				type : "post",
				dataType : "json",
				url : '<?= admin_url( 'admin-ajax.php' ) ?>',
				data : {
					nonce: nonce,
					action:  object + '_' + action,
					item_id: item_id,
					current_page: current_page,
					button_clicked: button_clicked,
					component: component,
					
					_wpnonce: nonce
				},
				success: function(response) {
					changeData()
					if ( false === response.success ) {
						item_inner.prepend( response.data.feedback );
						target.removeClass( 'pending loading' );
						if ( item.find( '.bp-feedback' ).length ) {
							item.find( '.bp-feedback' ).show();
							item.find( '.bp-feedback' ).fadeOut( 6000 );
						} else {
							if ( 'groups' === object && 'join_group' === action ) {
								item.append( response.data.feedback );
								item.find( '.bp-feedback' ).fadeOut( 6000 );
							}
						}

					} else {
						target.parent().replaceWith( response.data.contents );
					}
				}
			}) 
		})
	})
</script>

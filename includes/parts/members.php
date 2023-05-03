<?php
// $members = groups_get_group_members(array(
//     'group_id' => get_field('associated_buddyboss_group'),
//     'exclude_admins_mods' => 0
// ));
// echo do_shortcode('[bpps_group_members group_id="'.get_field('associated_buddyboss_group').'" option="avatar-grid"]');
?>
<!-- <pre> -->
<?php
//print_r($members);
?>
<!-- </pre> -->
<?php
$footer_buttons_class = (bp_is_active('friends') && bp_is_active('messages')) ? 'footer-buttons-on' : '';

$is_follow_active = bp_is_active('activity') && function_exists('bp_is_activity_follow_active') && bp_is_activity_follow_active();
$follow_class     = $is_follow_active ? 'follow-active' : '';

// Member directories elements.
$enabled_online_status = !function_exists('bb_enabled_member_directory_element') || bb_enabled_member_directory_element('online-status');
$enabled_profile_type  = !function_exists('bb_enabled_member_directory_element') || bb_enabled_member_directory_element('profile-type');
$enabled_followers     = !function_exists('bb_enabled_member_directory_element') || bb_enabled_member_directory_element('followers');
$enabled_last_active   = !function_exists('bb_enabled_member_directory_element') || bb_enabled_member_directory_element('last-active');
$enabled_joined_date   = !function_exists('bb_enabled_member_directory_element') || bb_enabled_member_directory_element('joined-date');
?>
<?php

if (bp_group_has_members([
    'group_id' => get_field('associated_buddyboss_group'),
    'exclude_admin_mods' => 0
])) :

    // if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) . '&type=group_role&group_id=' . get_field('associated_buddyboss_group') ) ) :

?>

    <?php //bp_nouveau_group_hook('before', 'members_content'); ?>

    <?php //bp_nouveau_pagination('top'); ?>

    <?php //bp_nouveau_group_hook('before', 'members_list'); ?>

    <ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?> members-list cohort-member-list">

        <?php
        while (bp_group_members()) :
            bp_group_the_member();

            bp_group_member_section_title();

            // Check if members_list_item has content.
            ob_start();
            bp_nouveau_member_hook('', 'members_list_item');
            $members_list_item_content = ob_get_clean();
            $member_loop_has_content   = !empty($members_list_item_content);

            // Get member followers element.
            $followers_count = '';
            $followers = bp_get_followers( array( 'user_id' => bp_get_member_user_id() ) );
            $followers_count = count( $followers );

            // Member joined data.
            $member_joined_date = bp_get_group_member_joined_since();

            // Member last activity.
            $member_last_activity = bp_get_last_activity(bp_get_member_user_id());

            // Primary and secondary profile action buttons.
            $profile_actions = bb_member_directories_get_profile_actions(bp_get_member_user_id());

            // Member switch button.
            $member_switch_button = bp_get_add_switch_button(bp_get_member_user_id());

            // Get Primary action.
            $primary_action_btn = function_exists('bb_get_member_directory_primary_action') ? bb_get_member_directory_primary_action() : '';
            $moderation_class   = function_exists('bp_moderation_is_user_suspended') && bp_moderation_is_user_suspended(bp_get_group_member_id()) ? 'bp-user-suspended' : '';
            $moderation_class   = function_exists('bp_moderation_is_user_blocked') && bp_moderation_is_user_blocked(bp_get_group_member_id()) ? $moderation_class . ' bp-user-blocked' : $moderation_class;
        ?>
            <li <?php bp_member_class(array('item-entry')); ?> data-bp-item-id="<?php echo esc_attr(bp_get_group_member_id()); ?>" data-bp-item-component="members">
                <div class="list-member-inner list-wrap <?php echo esc_attr($footer_buttons_class); ?> <?php echo esc_attr($follow_class); ?> <?php echo $member_loop_has_content ? esc_attr(' has_hook_content') : esc_attr(''); ?> <?php echo !empty($profile_actions['secondary']) ? esc_attr('secondary-buttons') : esc_attr('no-secondary-buttons'); ?> <?php echo !empty($primary_action_btn) ? esc_attr('primary-button') : esc_attr('no-primary-buttons'); ?>">

                    <div class="list-wrap-inner">
                        <div class="item-avatar">
                            <a href="<?php bp_group_member_domain(); ?>" class="<?php echo esc_attr($moderation_class); ?>">
                                <?php
                                if ($enabled_online_status) {
                                    bb_user_presence_html(bp_get_group_member_id());
                                }
                                bp_group_member_avatar();
                                echo '<p class="item-meta member-type info-'.strip_tags(bp_get_user_member_type(bp_get_member_user_id())).'">' . wp_kses_post(bp_get_user_member_type(bp_get_member_user_id())) . '</p>';
                                ?>
                            </a>
                        </div>

                        <div class="item">

                            <div class="item-block">

                                <h2 class="list-title member-name">
                                    <?php bp_group_member_link(); ?>
                                </h2>

                            <div class="flex align-items-center follow-container justify-center member-details">
                                <b><?=$followers_count?></b>&nbspfollowers
                            </div>

                            <div class="flex only-grid-view align-items-center primary-action justify-center">
                                <?php echo wp_kses_post($profile_actions['primary']); ?>
                            </div>
                        </div><!-- // .item -->

                        <div class="member-buttons-wrap">

                                <?php if ($profile_actions['secondary']) { ?>
                                    <div class="member-button-wrap footer-button-wrap">
                                        <?php echo wp_kses_post($profile_actions['secondary']); ?>
                                    </div>
                                <?php } ?>

                                
                                
                                     <!-- if (function_exists('bp_is_active') && bp_is_active('friends')) { 
                                         $friendship = BP_Friends_Friendship::check_is_friend( get_current_user_id(), bp_get_member_user_id() );
                                         if($friendship == 'pending'):
                                            <a href="#"><img src="PBD_CO_URL/assets/images/pending-friend-request-icon.png" alt=""></a>
                                        elseif($friendship == 'not_friends'): 
                                            <a href="#"><img src="PBD_CO_URL/assets/images/add-friend-icon.png" alt=""></a>
                                        else : 
                                            <a href="#"><img src="PBD_CO_URL/assets/images/friend-connection-icon.png" alt=""></a>
                                        endif; 
                                    }   -->

                        </div><!-- .member-buttons-wrap -->

                    </div>

                    <div class="bp-members-list-hook">
                        <?php if ($member_loop_has_content) { ?>
                            <a class="more-action-button" href="#"><i class="bb-icon-menu-dots-h"></i></a>
                        <?php } ?>
                        <div class="bp-members-list-hook-inner">
                            <?php bp_nouveau_member_hook('', 'members_list_item'); ?>
                        </div>
                    </div>
                </div>
            </li>

        <?php endwhile; ?>

    </ul>

    <?php //bp_nouveau_group_hook( 'after', 'members_list' ); 
    ?>

    <?php //bp_nouveau_pagination( 'bottom' ); 
    ?>

    <?php //bp_nouveau_group_hook( 'after', 'members_content' ); 
    ?>

<?php
else :

    bp_nouveau_user_feedback('group-members-none');

endif;
?>
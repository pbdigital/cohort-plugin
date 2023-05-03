<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class PBD_Cohorts
{
    private static $instance;

    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new PBD_Cohorts();
        }

        return self::$instance;
    }

    public function __construct()
    {
        add_action('init', array($this, 'pbd_register_cohort_post_type'));
        add_action('init', array($this, 'pbd_register_statement_of_commitment'));
        add_action('init', array($this, 'load_acf_fields'));
        add_action('wp_enqueue_scripts', array($this, 'public_scripts'));
        add_filter('single_template', array($this, 'cohort_single_template'));
        add_action('wp_ajax_nopriv_get_statement_of_commitment', array($this, 'get_statement_of_commitment'));
        add_action('wp_ajax_get_statement_of_commitment', array($this, 'get_statement_of_commitment'));
        add_action('wp_ajax_nopriv_set_user_commitment', array($this, 'set_user_commitment'));
        add_action('wp_ajax_set_user_commitment', array($this, 'set_user_commitment'));

        if (self::is_buddyboss_group_enabled())
            add_action('save_post', array($this, 'create_buddyboss_group'), 10, 3);

        add_action('wp_head', array($this, 'dicussion_setting'), 10, 3);
    }

    public function public_scripts()
    {
        wp_enqueue_style('pbd-co-style', PBD_CO_URL . '/assets/css/main.css', array(), time());
        wp_enqueue_script('pbd-co-script', PBD_CO_URL . '/assets/js/scripts.js', array('jquery'), '1.0');
        wp_enqueue_script('pbd-progress-script', PBD_CO_URL . '/assets/js/circle-progress.min.js', array('jquery'), '1.0');
    }

    public function dicussion_setting(){
        if(isset($_GET['from_cohort'])){
            ?>
            <style>
                header,
                #wpadminbar,
                #item-header,
                #object-nav{
                    display: none;
                }
            </style>
            <link rel="stylesheet" href="<?=PBD_CO_URL?>/assets/css/discussion.css">
            <?php
        }
    }
    public function pbd_register_cohort_post_type()
    {

        $labels = [
            "name" => __("Cohorts", "pbd-co"),
            "singular_name" => __("Cohort", "pbd-co"),
        ];

        $args = [
            "label" => __("Cohorts", "pbd-co"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "rest_namespace" => "wp/v2",
            "has_archive" => false,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => false,
            "rewrite" => ["slug" => "cohort", "with_front" => true],
            "query_var" => true,
            "supports" => ["title", "editor", "thumbnail"],
            "show_in_graphql" => false,
        ];

        register_post_type("cohort", $args);
    }

    public function pbd_register_statement_of_commitment() {
        require_once( PBD_CO_PATH_CLASS.'/class.cpt.builder.php' );

        $builder = new CPTBuilder('soc', 'Statement of Commitment', 'Statements of Commitment');
        $builder->create();
    }

    public function cohort_single_template($single)
    {
        global $post;

        if ($post->post_type == 'cohort') {
            if (file_exists(get_stylesheet_directory() . '/single-cohort.php')) {
                return get_stylesheet_directory() . '/single-cohort.php';
            }
            return PBD_CO_INCLUDES_PATH . '/single-cohort.php';
        }

        return $single;
    }

    public function get_statement_of_commitment()
    {
        $id = $_POST['id'];
        $soc = get_post($id);

        $questions = array();
        $rows = get_field('questions', $id);

        foreach ($rows as $row) {
            $question = $row['question'];
            $questions[] = $question;
        }

        $soc->questions = $questions;
        echo json_encode($soc);
        exit();
    }

    public function set_user_commitment()
    {
        $id = $_POST['id'];
        update_user_meta(get_current_user_id(), 'soc_' . $id, 'completed');

        echo json_encode([
            'user' => get_current_user_id(),
            'soc' => 'soc_' . $id,
            'success' => true
        ]);
        exit;
    }

    public function create_buddyboss_group($post_id, $post, $update)
    {
       

        if ( $post->post_type == 'cohort' && !$update) {
            // Create a BuddyBoss group with the title of the post as the group name
            if (!get_field('associated_buddyboss_group')) {
                $group_id = groups_create_group( array(
                    'name' => $post->post_title,
                    'status' => 'public'
                ) );

                update_field( 'associated_buddyboss_group', $group_id, $post_id );
            }
            
        }
    }

    public static function is_buddyboss_enabled() {
        return function_exists('bp_is_active');
    }

    public static function is_buddyboss_group_enabled() {
        return function_exists('bp_is_active') && bp_is_active('groups');
    }

    public static function is_buddyboss_activity_enabled() {
        return function_exists('bp_is_active') && bp_is_active('activity');
    }

    public function load_acf_fields() {
        require_once( PBD_CO_INCLUDES_PATH.'/acf/fields.php' );

        add_acf_fields();
    }
}

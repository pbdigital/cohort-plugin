<?php


class CPTBuilder {

    public function __construct($slug, $name, $names, $description = '') {
        $this->build_label($name, $names);
        $this->slug = $slug;
        $this->description = $description;
    }

    private function build_label($singular, $plural) {
        $this->labels = array(
            'name'                => _x( $plural, 'Post Type General Name', 'pbd' ),
            'singular_name'       => _x( $singular, 'Post Type Singular Name', 'pbd' ),
            'menu_name'           => __( $plural, 'pbd' ),
            'parent_item_colon'   => __( 'Parent ' . $singular, 'pbd' ),
            'all_items'           => __( 'All ' . $plural, 'pbd' ),
            'view_item'           => __( 'View ' . $singular, 'pbd' ),
            'add_new_item'        => __( 'Add New ' . $singular, 'pbd' ),
            'add_new'             => __( 'Add New', 'pbd' ),
            'edit_item'           => __( 'Edit ' . $singular, 'pbd' ),
            'update_item'         => __( 'Update ' . $singular, 'pbd' ),
            'search_items'        => __( 'Search ' . $singular, 'pbd' ),
            'not_found'           => __( 'Not Found', 'pbd' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'pbd' ),
        );
    }

    public function create() {
        $args = array(
            'label'               => __( $this->slug, 'pbd' ),
            'description'         => __( $this->description, 'pbd' ),
            'labels'              => $this->labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest' => true,
      
        );

        register_post_type($this->slug, $args);
    }
}
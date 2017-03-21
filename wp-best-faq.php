<?php

/**
 * @package WP_Best_FAQ
 * @version 1.0.0
 */
/*
  Plugin Name: Wp Best FAQ
  Plugin URI: http://wordpress.org/plugins/
  Description: This plugin is used for create post faq display accordian shortcode in front side it is easy to modified.
  Author: Pradeep Chaturvedi
  Version: 1.0.0
  License: GPLv2 or later
  Author URI: https://pkcdeepu.wordpress.com/
  Text Domain: wp-best-faq
 */

class Best_FAQ {

    /**
     * Plugin version.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $version    Plugin version
     */
    private $version;

    /**
     * The directory path to the plugin file's includes folder.
     *
     * @since   1.0.0
     * @access  private
     * @var     string      $dir        The directory path to the includes folder
     */
    private $inc;

    // Constructor
    function __construct() {
        $this->version = '1.0.0';
        $this->inc = trailingslashit(plugin_dir_path(__FILE__) . 'includes');
        $this->load_dependencies();
        register_activation_hook(__FILE__, array($this, 'wpb_install'));
        register_deactivation_hook(__FILE__, array($this, 'wpb_uninstall'));


        // --- Add Action code here
        add_action('wp_enqueue_scripts', array($this, 'enq_scripts'));
        add_action('init', array($this, 'content_types'));

        add_shortcode('faq', array($this, 'faq_shortcode'));
    }

    /**
     * Load the required dependencies for the plugin.
     *
     * - Admin loads the backend functionality
     * - Public provides front-end functionality
     * - Dashboard Glancer loads the helper class for the admin dashboard
     *
     * @since   1.0.0
     */
    private function load_dependencies() {
        //require_once( $this->inc . 'class-best-faq-admin.php' );
        require_once( $this->inc . 'best-faq-shortcode.php' );
    }

    /*
     * Actions perform on activation of plugin
     */

    function wpb_install() {
        flush_rewrite_rules();
    }

    /*
     * Actions perform on de-activation of plugin
     */

    function wpb_uninstall() {
        flush_rewrite_rules();
    }

    /**
     * Display FAQs
     *
     * @since   0.9
     * @version 1.2.0
     * @param   array $atts
     */
    function faq_shortcode($atts, $content = null) {
        // Set our JS to load
        wp_enqueue_script('wp-best-faq-script');

        // Translate 'all' to nopaging = true ( for backward compatibility)
        if (isset($atts['showposts'])) {
            if ($atts['showposts'] != "all" and $atts['showposts'] > 0) {
                $atts['posts_per_page'] = $atts['showposts'];
            }
        }

        $f = new Best_FAQ_Display;
        //echo '<pre>';print_r($f);
        return $f->loop($atts);
    }

    function content_types() {
        $defaults = $this->defaults_best_faq_post();
        //print_r($defaults['post_type']['args']); die;
        register_post_type($defaults['post_type']['slug'], $defaults['post_type']['args']);
        // register_taxonomy( $defaults['taxonomy']['slug'], $defaults['post_type']['slug'],  $defaults['taxonomy']['args'] );
    }

    /**
     * Define the defaults used in the registration of the post type and taxonomy
     *
     * @since  1.0.0
     * @return array $defaults
     */
    function defaults_best_faq_post() {
        // Establishes plugin registration defaults for post type and taxonomy
        $defaults = array(
            'post_type' => array(
                'slug' => 'faq',
                'args' => array(
                    'labels' => array(
                        'name' => __('FAQ', 'best-faq'),
                        'singular_name' => __('FAQ', 'best-faq'),
                        'add_new' => __('Add New', 'best-faq'),
                        'add_new_item' => __('Add New Question', 'best-faq'),
                        'edit' => __('Edit', 'best-faq'),
                        'edit_item' => __('Edit Question', 'best-faq'),
                        'new_item' => __('New Question', 'best-faq'),
                        'view' => __('View FAQ', 'best-faq'),
                        'view_item' => __('View Question', 'best-faq'),
                        'search_items' => __('Search FAQ', 'best-faq'),
                        'not_found' => __('No FAQs found', 'best-faq'),
                        'not_found_in_trash' => __('No FAQs found in Trash', 'best-faq')
                    ),
                    'public' => true,
                    'query_var' => true,
                    'menu_position' => 20,
                    'menu_icon' => 'dashicons-editor-help',
                    'has_archive' => false,
                    'supports' => array('title', 'editor', 'revisions', 'page-attributes'),
                    'rewrite' => array('with_front' => false)
                )
            ),
        );

        return apply_filters('best_faq_defaults', $defaults);
    }

    /**
     * Register the necessary Javascript and CSS, which can be overridden in a couple different ways.
     *
     * If you would like to bundle the Javacsript or CSS funtionality into another file and prevent either of the plugin's
     * JS or CSS from loading at all, return false to whichever of the pre_register filters you wish to override
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    function enq_scripts() {
        $inc = trailingslashit(plugin_dir_url(__FILE__));
        // Register the javascript - Check the theme directory first, the parent theme (if applicable) second, otherwise load the plugin file
        /* if ( apply_filters( 'pre_register_best_faq_js', true ) ) {
          if( file_exists( get_stylesheet_directory() . '/best-faq.js' ) )
          wp_register_script( 'best-faq-js', get_stylesheet_directory_uri() . '/best-faq.js', array( 'jquery-ui-accordion' ), $version );
          elseif( file_exists( get_template_directory() . '/best-faq.js' ) )
          wp_register_script( 'best-faq-js', get_template_directory_uri() . '/best-faq.js', array( 'jquery-ui-accordion' ), $version );
          else
          wp_register_script( 'best-faq-js', $inc . 'js/best-faq.js', array( 'jquery-ui-accordion' ) );
          } */

        wp_register_script('wp-best-faq-script', $inc . 'includes/js/best-faq.js', array('jquery'), $this->version);
        //wp_enqueue_script('wp-best-faq-script');
        // Load the CSS - Check the theme directory first, the parent theme (if applicable) second, otherwise load the plugin file
        if (apply_filters('pre_register_best_faq_css', true)) {
            if (file_exists(get_stylesheet_directory() . '/best-faq.css'))
                wp_enqueue_style('best-faq', get_stylesheet_directory_uri() . '/best-faq-front.css', false, $this->version);
            elseif (file_exists(get_template_directory() . '/best-faq.css'))
                wp_enqueue_style('best-faq', get_template_directory_uri() . '/best-faq-front.css', false, $this->version);
            else
                wp_enqueue_style('best-faq', $inc . 'includes/css/best-faq-front.css', false, $this->version);
        }
    }

}

/** Load the plugin */
add_action('plugins_loaded', 'wp_best_faq_run');

function wp_best_faq_run() {
    load_plugin_textdomain('wp-best-faq');
    new Best_FAQ();
}

<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tw_Seo
 * @subpackage Tw_Seo/includes
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tw_Seo {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Tw_Seo_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The human readable name of this plugin
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $human_plugin_name    The String used as Human Readable Name for the plugin.
     */
    protected $human_plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        if ( defined( 'TW_SEO_VERSION' ) ) {
            $this->version = TW_SEO_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        $this->plugin_name = 'tw_seo';
        $this->human_plugin_name = 'ToolWine SEO';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Tw_Seo_Loader. Orchestrates the hooks of the plugin.
     * - Tw_Seo_i18n. Defines internationalization functionality.
     * - Tw_Seo_Admin. Defines all hooks for the admin area.
     * - Tw_Seo_Public. Defines all hooks for the public side of the site.
     * - TKT_Common. Load all TukuToi Common Code.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tw-seo-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tw-seo-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tw-seo-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tw-seo-public.php';

        /**
         * TukuToi Common Code
         */

        if( !defined( 'TKT_COMMON_LOADED' ) ){
            require_once( plugin_dir_path( dirname( __FILE__ ) ).'includes/common/class-tkt-common.php' );

        }
        $this->common = TKT_Common::getInstance();
        
        $this->loader = new Tw_Seo_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Tw_Seo_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Tw_Seo_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin       = new Tw_Seo_Admin( $this->get_plugin_name(), $this->get_human_plugin_name(), $this->get_version() );
        $plugin_settings    = new Tw_Seo_Admin_Settings( $this->get_plugin_name(), $this->get_human_plugin_name(), $this->get_version(), $this->common );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_scripts' );
        $this->loader->add_action( 'publish_post', $plugin_admin, 'create_sitemap' );
        $this->loader->add_action( 'publish_page', $plugin_admin, 'create_sitemap' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'create_sitemap' );
        $this->loader->add_action( 'wp_loaded', $plugin_admin, 'register_fields' );

        $this->loader->add_action( 'admin_menu', $plugin_settings, 'setup_plugin_menu', 11 );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_settings' );

        $this->loader->add_action( 'init', $this->common, 'load' );


    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Tw_Seo_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_head', $plugin_public, 'setup_current_object_data', 1);
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_metatags', 1);
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_schema_org');
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_google_gtag_js');
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_header_html');
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_wp_missing_canonical_archives', 2);
        $this->loader->add_action( 'wp_head', $plugin_public, 'opengraph_fb', 1);
        $this->loader->add_filter( 'language_attributes', $plugin_public, 'opengraph_doctype');
        $this->loader->add_filter( 'get_the_archive_title', $plugin_public, 'remove_archive_titles' );
        $this->loader->add_filter( 'document_title_parts', $plugin_public, 'custom_document_title', 999 );
        $this->loader->add_filter( 'init', $plugin_public, 'remove_default_html_tags' );
           

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The Human name of the plugin used to give the plugin a name
     *
     * @since     1.0.0
     * @return    string    The Human name of the plugin.
     */
    public function get_human_plugin_name() {
        return $this->human_plugin_name;
    }


    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Tw_Seo_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}

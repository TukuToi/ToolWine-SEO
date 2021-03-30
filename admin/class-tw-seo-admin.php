<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, human name, 
 * registers scripts and styles 
 * instantiates Toolset Types fields
 * creates Sitemap XML
 * loads Settings Class dependency
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/admin
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tw_Seo_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The Human Name of the plugin
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $human_plugin_name    The humanly readable plugin name
     */
    private $human_plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;


    /**
     * The SEO Field Slugs
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $field_slugs    Slugs of the Fields to create/use.
     */
    private $field_slugs;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version           The version of this plugin.
     * @param      string    $human_plugin_name The human name of this plugin.
     * @param      array    $fields             The slugs of the Fields to create/use.
     */
    public function __construct( $plugin_name, $human_plugin_name, $version ) {

        $this->plugin_name       = $plugin_name;
        $this->version           = $version;
        $this->human_plugin_name = $human_plugin_name;
        $this->fields            = $this->instantiate_fields();

        $this->load_dependencies();

    }

    /**
     * Include file with Settings Class
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies() {

        /**
         * The Class responsible to create and manage settings in options page for this plugin
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-tw-seo-settings.php';

    }

    /**
     * Build array with Field slugs and definitions
     * @since 1.0.0
     * @access private
     */
    private function instantiate_fields(){

        $frequency_options = array(
            'wpcf-fields-select-option-frequency-always'    => [ 'title' => 'Always', 'value' => 'always'],
            'wpcf-fields-select-option-frequency-hourly'    => [ 'title' => 'Hourly', 'value' => 'hourly'],
            'wpcf-fields-select-option-frequency-daily'     => [ 'title' => 'Daily', 'value' => 'daily'],
            'wpcf-fields-select-option-frequency-weekly'    => [ 'title' => 'Weekly', 'value' => 'weekly'],
            'wpcf-fields-select-option-frequency-monthly'   => [ 'title' => 'Monthly', 'value' => 'monthly'],
            'wpcf-fields-select-option-frequency-yearly'    => [ 'title' => 'Yearly', 'value' => 'yearly'],
            'wpcf-fields-select-option-frequency-never'     => [ 'title' => 'Never', 'value' => 'never'],
            'default'                                       => 'wpcf-fields-select-option-frequency-weekly',
        );

        $conditional_display = array(
            'relation'      => 'AND',
            'conditions'    => array(
                uniqid('condition_')    => array(
                    'field'     => 'Exclude from SiteMap',
                    'operation' => '<>',
                    'value'     => '1',
                    'month' => '03',
                    'date' => '29',
                    'year' => '2021',
                ),
            ),
            'custom'        => null,
            'custom_use'    => 0,
        );

        $fields = array(
            $this->plugin_name .'-title'                    => ['type' => 'textfield', 'name' => 'Custom SEO Title', 'description' => 'Optional dedicated SEO Title (for SEO "title" meta tag)'],
            $this->plugin_name .'-keywords'                 => ['type' => 'textfield', 'name' => 'SEO Keywords', 'description' => 'Keywords for "keywords" meta tag '], 
            $this->plugin_name .'-description'              => ['type' => 'textarea', 'name' => 'SEO Description', 'description' => 'Short Description for "description" meta tag'], 
            $this->plugin_name .'-exclude-from-sitemap'     => ['type' => 'checkbox', 'name' => 'Exclude from SiteMap', 'description' => 'Wether to include or exclude this Post from the Sitemap XML', 'data' => ['set_value' => 1, 'save_empty' => 'no' ]], 
            $this->plugin_name .'-sitemap-priority'         => ['type' => 'numeric', 'name' => 'SiteMap Priority', 'data' => ['user_default_value' => 0.5, 'conditional_display' => $conditional_display], 'description' => 'The Priority of this Post in the Sitemap XML (0.0 to 1.0)'], 
            $this->plugin_name .'-frequency'                => ['type' => 'select', 'name' => 'Update Frequency', 'data' => ['options' => $frequency_options, 'conditional_display' => $conditional_display, 'submit-key' => 'select-frequency-always', 'disabled_by_type' => 0], 'description' => 'Sitemap XML Frequency this Post will be updated with (how often do you plan to update this post)'],
        );

        return $fields;

    }
    
    /**
     * Check if Toolset Types Custom Field Group exist already
     * @since 1.0.0
     * @access private
     */
    private function check_if_types_group_exist( $title ) {

        global $wpdb;

        $db_query = $wpdb->get_row($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title=%s && post_status = 'publish' && post_type = 'wp-types-group' ", $title),'ARRAY_N');

        if( empty( $db_query ) )
            return false;
        
        return true;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function register_styles() {

        wp_register_style( $this->plugin_name . '-styles', plugin_dir_url( __FILE__ ) . 'css/tw-seo-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function register_scripts() {

        wp_register_script( $this->plugin_name . '-scripts', plugin_dir_url( __FILE__ ) . 'js/tw-seo-admin.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Create Toolset Types Custom Fields if not existing
     * @since 1.0.0
     * @access private
     */
    public function register_fields() {

        //Preparation to Types control
        $tw_seo_fields_array        = array();
        $string_wpcf_not_controlled = md5( 'wpcf_not_controlled');

        foreach ( $this->fields  as $key => $value ) {
            $tw_seo_fields_array[] = $key .'_'. $string_wpcf_not_controlled;
        }

        if ( defined('WPCF_INC_ABSPATH') ) {

            //First, check if SEO Meta Tag Fields Group field does not exist
            if ( ! $this->check_if_types_group_exist( 'SEO Fields' ) ) {
                
                require_once WPCF_INC_ABSPATH . '/fields.php';
                
                //Part 1: Assign to Types Control
                //Get Fields
                $fields      = wpcf_admin_fields_get_fields(false, true);
                $fields_bulk = wpcf_types_cf_under_control( 'add', array( 'fields' => $tw_seo_fields_array ) );

                foreach ($fields_bulk as $field_id) {

                    if ( isset( $fields[$field_id] ) ) {
                        $fields[$field_id]['data']['disabled'] = 0;
                    }

                }

                //Save fields
                wpcf_admin_fields_save_fields( $fields );

                //Retrieve updated fields
                $fields = wpcf_admin_fields_get_fields( false, false );

                //Add Readable name to each field
                foreach ( $fields as $key => $value ) {
                    if( array_key_exists( $key, $this->fields ) ){
                        $fields[$key]['name'] = $this->fields[$key]['name'];
                        $fields[$key]['type'] = $this->fields[$key]['type'];
                        if( isset( $this->fields[$key]['data']['user_default_value'] ) )
                            $fields[$key]['data']['user_default_value'] = $this->fields[$key]['data']['user_default_value'];
                        if( isset( $this->fields[$key]['data']['options'] ) )
                            $fields[$key]['data']['options'] = $this->fields[$key]['data']['options'];
                        if( isset( $this->fields[$key]['data']['conditional_display'] ) )
                            $fields[$key]['data']['conditional_display'] = $this->fields[$key]['data']['conditional_display'];
                    }
                }

                //Save fields
                wpcf_admin_fields_save_fields( $fields );

                //Define group
                $group = array(
                    'name'                  => 'SEO Fields',
                    'description'           => 'Custom Fields to specifiy SEO Meta Tag contents and settings.',
                    'filters_association'   => 'any',
                    'conditional_display'   => array( 'relation' => 'AND', 'custom' => '' ),
                    'preview'               =>  'edit_mode',
                    'admin_html_preview'    => '',
                    'admin_styles'          => '',
                    'slug'                  => $this->plugin_name .'-meta-tag-fields'
                );

                //Save group
                $group_id = wpcf_admin_fields_save_group( $group );

                //Save group fields
                wpcf_admin_fields_save_group_fields( $group_id, $fields_bulk );

                error_log( print_r( $fields , true) );

            }
        }
    }
    
    public function create_sitemap() {
        $options = get_option( $this->plugin_name );

        $posts_for_sitemap = get_posts( array(
            'numberposts'   => -1,
            'post_type'     => array_keys( $options['tw_seo_sitemap'] ),
            'meta_key'      => $this->plugin_name .'-sitemap-priority',
            'meta_type'     => 'NUMERIC',
            'orderby'       => array( 
                'meta_value_num'    => 'ASC',
                'modified'          => 'DESC', 
            ), 
            'meta_query'    => array(
                array(
                    'key'       => $this->plugin_name .'-exclude-from-sitemap',
                    'compare'   => 'NOT EXISTS' 
                )
            )
        ));

        //https://www.sitemaps.org/protocol.html
        $sitemap  = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach( $posts_for_sitemap as $post ) {
            
            setup_postdata( $post );

            $postdate   = explode( " ", $post->post_modified );
            $frequency  = get_post_meta( $post->ID, $this->plugin_name .'-frequency', true );
            $priority   = floatval( get_post_meta( $post->ID, $this->plugin_name .'-sitemap-priority', true ) );
 
            $sitemap .= '<url>';
            $sitemap .= '<loc>'. get_permalink( $post->ID ) .'</loc>';
            $sitemap .= '<lastmod>'. $postdate[0] .'</lastmod>';//should be YYYY-MM-DD or YYYY-MM-DDThh:mmTZD
            $sitemap .= '<changefreq>'. $frequency .'</changefreq>';//always hourly daily weekly monthly yearly never
            $sitemap .= '<priority>'. $priority .'</priority>';//0.0 to 1.0
            $sitemap .= '</url>';

        }

        $sitemap .= '</urlset>';

        $fopen = fopen( ABSPATH . 'sitemap.xml', 'w' );

        fwrite( $fopen, $sitemap );
        fclose( $fopen );

    }

}

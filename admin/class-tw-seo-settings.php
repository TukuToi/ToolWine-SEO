<?php

/**
 * The settings of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/admin
 */

/**
 * Class Tw_Seo_Admin_Settings
 *
 */
class Tw_Seo_Admin_Settings {

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
     * TukuToi Common Code
     *
     * @since    1.0.0
     * @access   private
     * @var      TKT_Common    $common    TKT_Common instance.
     */
    private $common;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $human_plugin_name, $version, $common ) {

        $this->plugin_name  = $plugin_name;
        $this->human_plugin_name = $human_plugin_name;
        $this->version      = $version;
        $this->common       = $common;

    }

    /**
     * Enqueue Styles in Settings page
     *
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name . '-styles' );

    }

    /**
     * Add Menu Page of this plugin
     *
     * @since 1.0.0
     * @access public
     */
    public function setup_plugin_menu() {

        $pages[] = add_submenu_page( 
            $this->common->get_common_name(), 
            $this->human_plugin_name, 
            'SEO', 
            'manage_options', 
            $this->plugin_name, 
            array($this,'render_settings_page_content'), 
            2 
        );

        foreach ($pages as $page) {
            add_action( "admin_print_styles-{$page}", array($this->common,'enqueue_styles') );
            add_action( "admin_print_styles-{$page}", array($this,'enqueue_styles') );
        }

    }

    /**
     * Render Settings Page
     *
     * @since 1.0.0
     * @access public
     */
    public function render_settings_page_content( $active_tab = '' ) {
        $this->common->set_render_settings_page_content($active_tab = '', $this->plugin_name, $this->plugin_name, $this->plugin_name);
    }

    /**
     * This Plugins Settings Options.
     *
     * @return array
     * @since 1.0.0
     * @access public
     */
    public function settings_options() {

        $options = array(
            $this->plugin_name .'_gtag'             => "Google Analytics Code",
            $this->plugin_name .'_social_media'     => "Social Media Accounts",
            $this->plugin_name .'_fbappid'          => "Add Facebook App ID",
            $this->plugin_name .'_logo'             => "Logo to use for SEO",
            $this->plugin_name .'_main_description' => "Main Website Description",
            $this->plugin_name .'_header'           => "Header Code",
            $this->plugin_name .'_schema_maps'      => "Post Types Schema Map",
            $this->plugin_name .'_sitemap'          => "Add Posts of these Types to Sitemap",
        );

        return $options;

    }

    /**
     * Prive Defaults for this Plugins Settings Options.
     *
     * @return array
     * @since 1.0.0
     * @access public
     */
    public function settings_options_defaults() {

        $defaults = array(
            $this->plugin_name .'_gtag'             => "AL-382947598234-3",
            $this->plugin_name .'_fbappid'          => '3209572308509872',
            $this->plugin_name .'_social_media'     => "facebook.com/your_page,twitter.com/your_account",
            $this->plugin_name .'_logo'             => has_custom_logo() ? wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ) , 'full' )[0] : '',
            $this->plugin_name .'_main_description' => get_bloginfo( 'description' ),
            $this->plugin_name .'_header'           => "",
            $this->plugin_name .'_schema_maps'      => get_post_types(array('public'=>true)),
            $this->plugin_name .'_sitemap'          => get_post_types(array('public'=>true)),
            //Add meta box by post to exclude specific scriptts or stypes onthere
        );

        return $defaults;

    }

    /**
     * Initialise all Common Settings
     *
     * @since 1.0.0
     * @access public
     */
    public function initialize_settings() {

        // If the options don't exist, create them.
        if( false == get_option( $this->plugin_name ) ) {
            $default_array = $this->settings_options_defaults();
            add_option( $this->plugin_name, $default_array );
        }

     
        // register a new section
        add_settings_section(
            $this->plugin_name,
            __( 'SEO Options', $this->plugin_name ),
            array( $this, 'general_options_callback'),
            $this->plugin_name
        );

        //Why create as many functions as there are options? Just use foreach($settings_array) to create each settings field
        foreach ($this->settings_options() as $option => $name) {
            add_settings_field(
                $option, // as of WP 4.6 this value is used only internally
                 // use $args' label_for to populate the id inside the callback
                __( $name, $this->plugin_name ),
                array($this, $option . '_cb'),
                $this->plugin_name,
                $this->plugin_name,
                [
                    'label_for' => $option,
                    'class' => $this->plugin_name .'_row',
                    $this->plugin_name .'_custom_data' => 'custom',
                ]
            );

        }

        register_setting( $this->plugin_name, $this->plugin_name );

    } // end wppb-demo_initialize_theme_options

    /**
     * General Options Callback API
     * @since 1.0.0
     * @access public
     */
    public function general_options_callback() {
        $this->common->set_general_options_callback('Control the SEO Options of ', get_bloginfo('name'), ' centrally in one place', $this->plugin_name);
    }

    public function tw_seo_main_description_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        // output the field
        ?><span class="description"><?php _e( 'Enter the main SEO description of your Website (<a href="https://schema.org/description" target="_blank"><code>description:</code></a>)', $this->plugin_name ); ?>
            </span>
            <input type="text" class="tkt-option-input" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args[$this->plugin_name .'_custom_data'] ); ?>" name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $options[$this->plugin_name .'_main_description'] ? $options[$this->plugin_name .'_main_description'] : ''?>">
        <?php
    }

    public function tw_seo_fbappid_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        // output the field
        ?><span class="description"><?php _e( 'Add Facebook App ID here', $this->plugin_name ); ?>
            </span>
            <input type="text" class="tkt-option-input" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args[$this->plugin_name .'_custom_data'] ); ?>" name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $options[$this->plugin_name .'_fbappid'] ? $options[$this->plugin_name .'_fbappid'] : ''?>">
        <?php
    }

    public function tw_seo_logo_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        // output the field
        ?><span class="description"><?php _e( 'Add the URL to the fallback Image you want to use in SEO global "logo" options (<a href="https://schema.org/image" target="_blank"><code>image:</code></a>)', $this->plugin_name ); ?>
            </span>
            <input type="text" class="tkt-option-input" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args[$this->plugin_name .'_custom_data'] ); ?>" name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $options[$this->plugin_name .'_logo'] ? $options[$this->plugin_name .'_logo'] : ''?>">   
        <?php
    }

    public function tw_seo_social_media_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        ?><span class="description"><?php _e( 'Add comma-separated URLs to your Social Media Accounts (<a href="https://schema.org/sameAs" target="_blank"><code>sameAs:</code></a>)', $this->plugin_name ); ?>
            </span>
        <textarea class="tkt-option-input" cols='40' rows='3' id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" data-custom="<?php echo esc_attr( $args[$this->plugin_name .'_custom_data'] ); ?>"><?php echo $options[$this->plugin_name .'_social_media'] ? $options[$this->plugin_name .'_social_media'] : ''?></textarea>
        <?php
        // output the field
       
    }

    public function tw_seo_gtag_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        // output the field
        ?><span class="description"><?php _e( 'Enter the Google Search ID like <code>AL-382947598234-3</code>', $this->plugin_name ); ?>
            </span>
            <input type="text" class="tkt-option-input" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args[$this->plugin_name .'_custom_data'] ); ?>" name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $options[$this->plugin_name .'_gtag'] ? $options[$this->plugin_name .'_gtag'] : ''?>">
        <?php
    }

    public function tw_seo_header_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        ?><span class="description"><?php _e( 'Add any HTML that you want in the Header', $this->plugin_name ); ?>
            </span>
        <textarea class="tkt-option-input" cols='40' rows='5' id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" data-custom="<?php echo esc_attr( $args[$this->plugin_name .'_custom_data'] ); ?>"><?php echo $options[$this->plugin_name .'_header'] ? $options[$this->plugin_name .'_header'] : ''?></textarea>
        <?php
    }

    public function tw_seo_schema_maps_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        if($options === false || !is_array($options))
            return;
        $schema = array(
            'no_schema'=>'Don\'t Generate Schema',
            'article'=>'Article',
            'offer'=>'Offer', 
            'softwareapplication'=>'SoftwareApplication', 
            'person'=>'Person',
            'rating'=>'Rating',
            'product'=>'Product',
        );
        $select_options = array();
        $selected = '';
        ?>
        <span class="description"><?php _e( 'Map a <a href="https://schema.org/docs/full.html" target="_blank" >Schema.org</a> Type to each Post Type', $this->plugin_name ); ?></span>
        <ul class="tkt-option-input <?php echo $this->plugin_name ?>-schema-map-list">
        <?php 
        foreach (get_post_types(array('public'=>true)) as $post_type){ 
            if(in_array($post_type, array('attachment')))
               continue;
            echo '<strong>' . ucwords($post_type) . '</strong>';
            ?>
            <li>
                <select name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo $post_type ?>]">
                <?php
                foreach($schema as $select_key => $select_value){
                    if(array_key_exists($this->plugin_name .'_schema_maps', $options))
                        $selected = $options[$this->plugin_name .'_schema_maps'][$post_type] == $select_key ? 'selected' : '';
                    echo '<option value="' . $select_key . '"' . $selected . '>' . $select_value . '</option>';
                }
                ?>
                </select>
            </li>
            <?php
        } 
        ?>
        </ul>
        <?php
    }

    public function tw_seo_sitemap_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( $this->plugin_name );
        if($options === false || !is_array($options))
            return;
        $sitemap = array(
            'no_sitemap'=>'Don\'t add to Sitemap',
            'do_sitemap'=>'Add to Sitemap',
        );
        $select_options = array();
        $selected = '';
        ?>
        <span class="description"><?php _e( 'Wether or not to add posts of type to the <a href="https://www.sitemaps.org/protocol.html" target="_blank">sitemap.xml</a> file', $this->plugin_name ); ?></span>
        <ul class="tkt-option-input <?php echo $this->plugin_name ?>-sitemap-list">
        <?php 
        foreach (get_post_types(array('public'=>true)) as $post_type){ 
            if(in_array($post_type, array('attachment')))
               continue;
            echo '<strong>' . ucwords($post_type) . '</strong>';
            ?>
            <li>
                <select name="<?php echo $this->plugin_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo $post_type ?>]">
                <?php
                foreach($sitemap as $select_key => $select_value){
                    //error_log( print_r( $options[$this->plugin_name .'_schema_maps'] , true) );
                    if(array_key_exists($this->plugin_name .'_sitemap', $options))
                        $selected = $options[$this->plugin_name .'_sitemap'][$post_type] == $post_type . '-' . $select_key ? 'selected' : '';
                    echo '<option value="' . $post_type . '-' . $select_key . '"' . $selected . '>' . $select_value . '</option>';
                }
                ?>
                </select>
            </li>
            <?php
        } 
        ?>
        </ul>
        <?php
    }

}
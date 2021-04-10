<?php

/**
 * The TukuToi Common functionality.
 *
 * Define Common Constants, menus, version and scripts.
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/common
 * @author     TukuToi <hello@tukutoi.com>
 */
class TKT_Common {

    /**
     * The TukuToi Actions Common to all TukuToi plugins
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $common_actions    All actions used by TukuToi Plugins in common.
     */
    protected $common_actions;

    /**
     * The TukuToi Common Version
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $common_version    Defines version of common code loaded.
     */
    protected $common_version;

    /**
     * The TukuToi Common Name
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $common_name    Defines Name of TukuToi Common loaded.
     */
    protected $common_name;

    /**
     * The Vendor Name
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $vendor_name    Defines Vendor of the Company.
     */
    protected $vendor_name;

    /**
     * Load only one instance of this class
     *
     * @since    1.0.0
     * @access   private
     * @var      bool    $instance    Is class instantiated.
     */
    private static $instance = NULL;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $vendor_name       Name of the Vendor.
     * @param      string    $common_name       Name of Common Code loaded.
     * @param      string    $common_version    Version of Common Code loaded.
     * @param      array    $common_actions    Array of Commmon Actions and Filters loaded.
     */
    protected function __construct() {

        $this->vendor_name      = 'TukuToi';
        $this->common_name      = 'tkt_common';
        $this->common_version   = '1.0.0';
        $this->common_actions   = array();
        $this->define_loaded();

    }

    /**
     * Define actions to be loaded
     *
     * @since    1.0.0
     * @access   private
     */
    private function common_actions(){
        $this->common_actions = array(
            'define_menu_icon',
            'define_wpml_active',
            'add_actions',//native WP Actions
            'add_filters',//native WP Filters
        );
    }

    /**
     * All Actions to be added to add_action
     *
     * @since    1.0.0
     * @access   private
     */
    private function add_actions(){
        add_action( 'admin_menu', array($this,'add_main_menu'), 10 );
        add_action( 'admin_init', array($this, 'settings_init') );
        add_action( 'admin_enqueue_scripts', array($this, 'register_styles') );
    }

    /**
     * All Filters to be added to add_filter
     *
     * @since    1.0.0
     * @access   private
     */
    private function add_filters(){
        //non yet
    }

    /**
     * Define Main Admin Menu Icon
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_menu_icon(){
        if(!defined('TKT_ADMIN_MENU_ICON'))
            define('TKT_ADMIN_MENU_ICON', 'data:image/svg+xml;base64,'. base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1008.52 835.08"><path fill="black" d="M1800.48,797.83Q1685.31,683,1570.31,568.06c-9.69-9.69-19-19.79-27.89-30.26C1385.26,352,1100.6,339.05,928.62,511.43c-94.12,94.34-134.82,209.23-121.35,342,9.92,97.68,50.77,181.69,120.65,250.32,87.31,85.75,193.25,126.3,315.91,120.67,102.35-4.69,191.18-42.88,265.63-112.87,26.71-25.1,50.16-53.61,76-79.65q107.16-107.84,215.16-214.84c2.86-2.84,6.6-4.81,12.63-9.1C1806.87,802.93,1803.36,800.7,1800.48,797.83ZM1165.29,1052.3c-113.09-27.12-191.44-125.58-193.76-242.73-2.21-111.42,77.09-214.53,187.27-243.2,112-29.15,218.38,22.64,268.84,99.6l-142.16,141.7,142.86,142.19C1381.17,1024.15,1276.15,1078.88,1165.29,1052.3Zm381.14-171.21c-40.56.18-74.45-33.65-74.41-74.3a74.38,74.38,0,0,1,74.43-74.07c40.86.09,74.06,33.66,73.83,74.64C1620.05,847.6,1586.68,880.91,1546.43,881.09Z" transform="translate(-804.76 -389.88)"/><path d="M1540.45,772.44c-18.93.47-34.43,16.22-34.47,35a35.14,35.14,0,0,0,35.41,35,35,35,0,1,0-.94-70.06Z" transform="translate(-804.76 -389.88)"/></svg>'));
    }

    /**
     * Flag Common Code as loaded
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_loaded(){
        if(!defined('TKT_COMMON_LOADED'))
            define('TKT_COMMON_LOADED', true);
    }
    
    /**
     * Define if WPML is active
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_wpml_active(){
        if(defined( 'TKT_WPML_IS_ACTIVE' ) )
            return;
        if ( function_exists('icl_object_id') ) {
            define( 'TKT_WPML_IS_ACTIVE', true );
        }
        else{
            define( 'TKT_WPML_IS_ACTIVE', false );
        }
    }

    /**
     * Load Common Code
     *
     * @since    1.0.0
     * @access   private
     */
    public function load(){
        $this->common_actions();
        foreach ($this->common_actions as $action) {
            $this->$action();
        }
    }

    /**
     * Render Settings Page Feedback Box
     *
     * @since    1.0.0
     * @access   private
     */
    private function feedback_box(){

        $html  = '<div class="' . $this->get_common_name() . '-feedback">';
        $html .= '<h2>Feedback?</h2>';
        $html .= '<div class="main-dashboard-inner">';
        $html .= '<p>Please <a href="mailto:hello@tukutoi.com">contact TukuToi</a>.</p>';
        $html .= '</div></div>';
        
        return $html;

    }
    
    /**
     * Render General Options Calback
     *
     * @since 1.0.0
     * @access private
     */
    public function set_general_options_callback($pre, $middle, $end, $locale ) {
        echo '<p>' . __( $pre, $locale ) . '<strong>' . $middle . '</strong>' . __( $end, $locale ) . '</p>';
    } 

     /**
     * Render Settings Page
     *
     * @since 1.0.0
     * @access public
     */
    public function set_render_settings_page_content( $active_tab = '', $field = '', $section = '', $locale = '', $label = 'Save Settings', $additional_html = '') {
        
        ?>
        <div class="wrap">

            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php settings_errors(); ?>
            <hr>

            <div>
                <div class="main-dashboard-inner">
                    <?php if(!empty($additional_html))
                        echo $additional_html;?>
                    <form action="options.php" method="post">
                        <?php
                        // output security fields for the registered setting
                        settings_fields( $field );
                        // output setting sections and their fields
                        do_settings_sections( $section );
                        // output save settings button
                        submit_button( __( $label, $locale  ) );
                        ?>
                    </form>
                </div>
            </div>
            
            <hr>
                    
            <?php echo $this->feedback_box();?>

        </div>      
        <?php

    }

    /**
     * Register Common Styles
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_styles() {

        wp_register_style( $this->common_name . '-styles', plugin_dir_url( __FILE__ ) . 'css/tkt-common.css', array(), $this->common_version, 'all' );

    }

    /**
     * Enqueue Common Styles
     *
     * @since    1.0.0
     * @access   public
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->common_name . '-styles' );

    }

    /**
     * Add main menu and one submenu
     *
     * @since 1.0.0
     * @access public
     */
    public function add_main_menu(){

        $pages[] = add_menu_page( 
            $this->vendor_name, 
            $this->vendor_name, 
            'manage_options', 
            $this->common_name, 
            array($this,'render_settings_page_content'), 
            TKT_ADMIN_MENU_ICON
        );

        $pages[] = add_submenu_page( 
            $this->common_name, 
            $this->vendor_name , 
            'Dashboard', 
            'manage_options', 
            $this->common_name, 
            array($this,'render_settings_page_content'), 
            1 
        );

        foreach ($pages as $page) {
            add_action( "admin_print_styles-{$page}", array($this,'enqueue_styles') );
        }
    
    }

    /**
     * Provide Defaults for this Plugins Settings Options.
     *
     * @return array
     * @since 1.0.0
     * @access public
     */
    public function settings_options_defaults() {

        $defaults = array(
            $this->common_name .'_delete_options' => "",
        );

        return $defaults;

    }

    /**
     * Initialise all Common Settings
     *
     * @since 1.0.0
     * @access public
     */
    public function settings_init() {

        if( false == get_option( $this->common_name ) ) {
            $default_array = $this->settings_options_defaults();
            add_option( $this->common_name, $default_array );
        }

        add_settings_section(
            $this->common_name,
            __( $this->vendor_name . ' Global Options', $this->common_name ),
            array( $this, 'general_options_callback'),
            $this->common_name
        );

        $settings_array = array(
            $this->common_name . '_delete_options' => "Delete All Options on Uninstall",
        );

        foreach ($settings_array as $option => $name) {
            add_settings_field(
                $option,
                __( $name, $this->common_name ),
                array($this, $option . '_cb'),
                $this->common_name,
                $this->common_name,
                [
                    'label_for' => $option,
                    'class' => $this->common_name . '_row',
                    $this->common_name . '_custom_data' => 'custom',
                ]
            );

        }

        register_setting( $this->common_name, $this->common_name );

    }

    /**
     * Callback for Delete Options Common Settings
     *
     * @since 1.0.0
     * @access public
     */
    public function tkt_common_delete_options_cb( $args ) {

        $options = get_option( $this->common_name );
        
        ?>
        <span class="description">
            <?php esc_html_e( 'If checked, ' . $this->vendor_name .' Plugins will delete their saved options on Uninstall.', $this->common_name ); ?>
        </span>
        
        <input 
            type="checkbox" 
            class="tkt-option-input" 
            id="<?php echo esc_attr( $args['label_for'] ); ?>" 
            data-custom="<?php echo esc_attr( $args[$this->common_name . '_custom_data'] ); ?>" 
            name="<?php echo $this->common_name ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" 
            <?php echo isset($options[$this->common_name . '_delete_options']) ? 'value="1"' :'value="0"'; ?> 
            <?php echo isset($options[$this->common_name . '_delete_options']) ? 'checked="checked"' : ''; ?> 
        />
        <?php

    }

    /**
     * General Options Callback
     *
     * @since 1.0.0
     * @access public
     */
    public function general_options_callback() {
        $this->set_general_options_callback('Control the Global Options of all ', $this->vendor_name, ' Plugins centrally in one place', $this->common_name);
    }

    /**
     * Render Settings Page
     *
     * @since 1.0.0
     * @access public
     */
    public function render_settings_page_content( $active_tab = '' ) {
        
        $this->set_render_settings_page_content( $active_tab = '', $field = $this->common_name, $section = $this->common_name, $locale = $this->common_name );

    }

    /**
     * Get Common Name
     *
     * @since    1.0.0
     * @access   public
     */
    public function get_common_name(){
        return $this->common_name;
    }

    /**
     * Instantiate class
     *
     * @since    1.0.0
     * @access   public
     * @paramt      $instance    Is class instantiated.
     */
    static public function getInstance(){
        if (self::$instance === NULL)
            self::$instance = new TKT_Common();
        return self::$instance;
    }
    
}

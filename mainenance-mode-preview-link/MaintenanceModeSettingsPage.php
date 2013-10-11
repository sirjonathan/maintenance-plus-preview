<?php
class MaintenanceModeSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Maintenace Mode Settings Admin', 
            'Maintenance Mode', 
            'manage_options', 
            'maintenance_mode-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'maint_display_text' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Maintenance Mode Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'maint_display_settings' );  //option group 
                do_settings_sections( 'maintenance_mode-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'maint_display_settings', // Option group
            'maint_display_text', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Maintenance Mode Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'maintenance_mode-setting-admin' // Page
        );  

        add_settings_field(
            'maint_title', 
            'Page Title', 
            array( $this, 'maint_title_callback' ), 
            'maintenance_mode-setting-admin', 
            'setting_section_id'
        );      

        add_settings_field(
            'maint_text', // ID
            'Display Message', // Title 
            array( $this, 'maint_text_callback' ), // Callback
            'maintenance_mode-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        if( !empty( $input['maint_text'] ) )
            $input['maint_text'] = wp_kses_post( $input['maint_text'] );
       if( !empty( $input['maint_title'] ) )
            $input['maint_title'] = wp_kses_post( $input['maint_title'] );

        return $input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function maint_text_callback()
    {

        echo '<textarea id="maint_text" name="maint_display_text[maint_text]" cols="100">'. $this->options['maint_text'] . '</textarea>';
        /*'<input type="text" id="maint_text" name="maint_display_text[maint_text]" value="'. $this->options['maint_text']. '" />';*/
    }
    public function maint_title_callback()
    {

        echo '<input type="text" id="maint_title" name="maint_display_text[maint_title]" value="'. $this->options['maint_title']. '" size=100/>';
    }
    // Define default option settings
}

if( is_admin() )
    $maintmode_settings_page = new MaintenanceModeSettingsPage();
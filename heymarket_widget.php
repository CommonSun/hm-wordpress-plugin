<?php
/**
 * Plugin Name:     Heymarket Wordpress Widget
 * Plugin URI:      https://www.heymarket.com
 * Description:     This plugin allows you to insert the Heymarket web widget into your Wordpress site.
 * Author:          Anthony Pelot
 * Author URI:      https://www.heymarket.com
 * Text Domain:     heymk_widget
 * Domain Path:     /languages
 * Version:         0.1.1
 *
 * @package         Heymarket_Widget
 */

function heymk_widget() {
  $options = get_option( 'heymk_options' );
  ?>
  <script type='text/javascript'>
  (function(_a,id,a,_) {
    function Modal(){
      var h = a.createElement('script'); h.type = 'text/javascript'; h.async = true;
      var e = id; h.src = e+(e.indexOf("?")>=0?"&":"?")+'ref='+_;
      var y = a.getElementsByTagName('script')[0]; y.parentNode.insertBefore(h, y);
      h.onload = h.onreadystatechange = function() {
        var r = this.readyState; if (r && r != 'complete' && r != 'loaded') return;
        try { HeymarketWidget.construct(_); } catch (e) {}
      };
    };
    (_a.attachEvent ? _a.attachEvent('onload', Modal) : _a.addEventListener('load', Modal, false));
  })(window,'https://widget.heymarket.com/heymk-widget.bundle.js',document,{
    CLIENT_ID: "<?php echo $options['heymk_field_cid'] ?>"
  });
  </script>
  <?php
}
add_action( 'wp_head', 'heymk_widget', 10 );

function heymk_settings_init() {
  // register a new setting for "heymk" page
  register_setting( 'heymk', 'heymk_options' );
  
  // register a new section in the "heymk" page
  add_settings_section(
    'heymk_section_developers',
    __( 'Heymarket Web Widget', 'heymk' ),
    'heymk_section_developers_cb',
    'heymk'
  );
  
  // register a new field in the "heymk_section_developers" section, inside the "heymk" page
  add_settings_field(
    'heymk_field_cid', // as of WP 4.6 this value is used only internally
    // use $args' label_for to populate the id inside the callback
    __( 'Client ID', 'heymk' ),
    'heymk_field_cid_cb',
    'heymk',
    'heymk_section_developers',
    [
      'label_for' => 'heymk_field_cid',
      'class' => 'heymk_row',
      'heymk_custom_data' => 'custom',
    ]
  );
}
  
/**
* register our heymk_settings_init to the admin_init action hook
*/
add_action( 'admin_init', 'heymk_settings_init' );

/**
* custom option and settings:
* callback functions
*/

// developers section cb

// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function heymk_section_developers_cb( $args ) {
  ?>
  <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Please enter the Heymarket Widget Client ID. You can find this on the Widget setup page in the Heymarket app.', 'heymk' ); ?></p>
  <?php
}
  
// cid field cb

// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function heymk_field_cid_cb( $args ) {
  // get the value of the setting we've registered with register_setting()
  $options = get_option( 'heymk_options' );
  // output the field
  ?>
  <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" 
  data-custom="<?php echo esc_attr( $args['heymk_custom_data'] ); ?>" 
  name="heymk_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
  value="<?php echo esc_attr($options['heymk_field_cid']) ?>"
  />
  <?php
}
  
/**
* top level menu
*/
function heymk_options_page() {
  // add top level menu page
  add_menu_page(
  'Heymarket',
  'Heymarket Options',
  'manage_options',
  'heymk',
  'heymk_options_page_html'
  );
}
  
/**
* register our heymk_options_page to the admin_menu action hook
*/
add_action( 'admin_menu', 'heymk_options_page' );

/**
* top level menu:
* callback functions
*/
function heymk_options_page_html() {
  // check user capabilities
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }
  
  // add error/update messages
  
  // check if the user have submitted the settings
  // wordpress will add the "settings-updated" $_GET parameter to the url
  if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'heymk_messages', 'heymk_message', __( 'Settings Saved', 'heymk' ), 'updated' );
  }
  
  // show error/update messages
  settings_errors( 'heymk_messages' );
  ?>
  <div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <form action="options.php" method="post">
  <?php
  // output security fields for the registered setting "heymk"
  settings_fields( 'heymk' );
  // output setting sections and their fields
  // (sections are registered for "heymk", each field is registered to a specific section)
  do_settings_sections( 'heymk' );
  // output save settings button
  submit_button( 'Save Settings' );
  ?>
  </form>
  </div>
  <?php
}
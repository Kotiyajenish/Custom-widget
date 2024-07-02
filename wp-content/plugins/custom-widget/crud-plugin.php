<?php
/*
  Plugin Name: custom-widget
  Description: Plugin for testing purposes
  Version: 1.0
  Author: Sahil Gulati
  Author URI: http://sahilgulati.com
*/

global $jal_db_version;
$jal_db_version = '1.0';

function jal_install()
{
    global $wpdb;
    global $jal_db_version;

    $table_name = $wpdb->prefix . 'employee_list';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        address text NOT NULL,
        role text NOT NULL,
        contact bigint(12),
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('jal_db_version', $jal_db_version);
}

register_activation_hook(__FILE__, 'jal_install');

function at_try_menu()
{
    add_menu_page(
        'WP Setting & Widget Page', // Page title
        'WP Setting & Widget Page', // Menu title
        'manage_options', // Capabilities
        'employee_listing', // Menu slug
        'employee_listing' // Function name
    );
}

add_action('admin_menu', 'at_try_menu');

function hstngr_register_widget()
{
    register_widget('hstngr_widget');
}
add_action('widgets_init', 'hstngr_register_widget');

class hstngr_widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'hstngr_widget',
            __('Wpd Ws Example Widget', 'hstngr_widget_domain'),
            array('description' => __('Wpd Ws Example Widget', 'hstngr_widget_domain'))
        );
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        $firstname = $instance['firstname'];
        $lastname = $instance['lastname'];
        $sex = $instance['sex'];
        $display_sex_publicly = isset($instance['display_sex_publicly']) ? $instance['display_sex_publicly'] : false;

        echo '<h1>Demo Widget</h1>';
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        if (!empty($firstname)) {
            echo '<p>' . __('First Name: ', 'hstngr_widget_domain') . $firstname . '</p>';
        }
        if (!empty($lastname)) {
            echo '<p>' . __('Last Name: ', 'hstngr_widget_domain') . $lastname . '</p>';
        }
        if ($display_sex_publicly && !empty($sex)) {
            echo '<p>' . __('Sex: ', 'hstngr_widget_domain') . $sex . '</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : __('Default Title', 'hstngr_widget_domain');
        $firstname = isset($instance['firstname']) ? $instance['firstname'] : '';
        $lastname = isset($instance['lastname']) ? $instance['lastname'] : '';
        $sex = isset($instance['sex']) ? $instance['sex'] : '';
        $display_sex_publicly = isset($instance['display_sex_publicly']) ? (bool) $instance['display_sex_publicly'] : false;
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'hstngr_widget_domain'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('firstname'); ?>"><?php _e('First Name:', 'hstngr_widget_domain'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('firstname'); ?>" name="<?php echo $this->get_field_name('firstname'); ?>" type="text" value="<?php echo esc_attr($firstname); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('lastname'); ?>"><?php _e('Last Name:', 'hstngr_widget_domain'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('lastname'); ?>" name="<?php echo $this->get_field_name('lastname'); ?>" type="text" value="<?php echo esc_attr($lastname); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('sex'); ?>"><?php _e('Sex:', 'hstngr_widget_domain'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('sex'); ?>" name="<?php echo $this->get_field_name('sex'); ?>">
                <option value="male" <?php selected($sex, 'male'); ?>><?php _e('Male', 'hstngr_widget_domain'); ?></option>
                <option value="female" <?php selected($sex, 'female'); ?>><?php _e('Female', 'hstngr_widget_domain'); ?></option>
                <option value="other" <?php selected($sex, 'other'); ?>><?php _e('Other', 'hstngr_widget_domain'); ?></option>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($display_sex_publicly); ?> id="<?php echo $this->get_field_id('display_sex_publicly'); ?>" name="<?php echo $this->get_field_name('display_sex_publicly'); ?>" />
            <label for="<?php echo $this->get_field_id('display_sex_publicly'); ?>"><?php _e('Display sex publicly?', 'hstngr_widget_domain'); ?></label>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['firstname'] = (!empty($new_instance['firstname'])) ? strip_tags($new_instance['firstname']) : '';
        $instance['lastname'] = (!empty($new_instance['lastname'])) ? strip_tags($new_instance['lastname']) : '';
        $instance['sex'] = (!empty($new_instance['sex'])) ? strip_tags($new_instance['sex']) : '';
        $instance['display_sex_publicly'] = !empty($new_instance['display_sex_publicly']) ? (bool) $new_instance['display_sex_publicly'] : false;
        return $instance;
    }
}

function employee_listing() { ?>
    <div class="wrap">
        <h1><?php echo esc_html__('WP Settings & Widget Page', 'textdomain'); ?></h1>
        <style>
            .reset-setting {
                margin: 20px 0;
            }
            .reset-form {
                padding: 10px 20px;
                background-color: #0073aa;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            .reset-form:hover {
                background-color: #005177;
            }
        </style>
        <div class="reset-setting">
            <input type="button" class="reset-form" onclick="myFunction()" value="Reset All Settings">
        </div>
        <form method="post" action="options.php" id="frm" name="frm" enctype="multipart/form-data">
            <?php
                settings_fields('employee_list_settings');
                do_settings_sections('employee_list');
                submit_button();
                ?>
        </form>
        <script>
            function myFunction() {
                document.getElementById("frm").reset();
            }
        </script>
    </div>
<?php
}

function employee_list_settings_init(){

    register_setting('employee_list_settings', 'employee_list_options', 'employee_list_sanitize');

    add_settings_section(
        'employee_list_section',
        __('WP Settings & Widget Page', 'textdomain'),
        'employee_list_section_callback',
        'employee_list'
    );

    add_settings_field(
        'employee_list_title',
        __('Title:', 'textdomain'),
        'employee_list_title_render',
        'employee_list',
        'employee_list_section'
    );

    add_settings_field(
        'employee_list_email',
        __('Email:', 'textdomain'),
        'employee_list_email_render',
        'employee_list',
        'employee_list_section'
    );


    add_settings_field(
        'employee_list_description',
        __('Description:', 'textdomain'),
        'employee_list_description_render',
        'employee_list',
        'employee_list_section'
    );

    add_settings_field(
        'employee_list_editor_content',
        __('Editor Content:', 'textdomain'),
        'employee_list_editor_content_render',
        'employee_list',
        'employee_list_section'
    );

    add_settings_field(
        'employee_list_upload_image',
        __('Upload Image:', 'textdomain'),
        'employee_list_upload_image_render',
        'employee_list',
        'employee_list_section'
    );

    add_settings_field(
        'employee_list_color',
        __('Color Picker:', 'textdomain'),
        'employee_list_color_render',
        'employee_list',
        'employee_list_section'
    );


}
add_action('admin_init', 'employee_list_settings_init');

function employee_list_section_callback(){
    echo __('', 'textdomain');
}

function employee_list_title_render(){
    $options = get_option('employee_list_options');
    ?>
    <input type="text" name="employee_list_options[employee_list_title]" value=""  />
<?php
}

function employee_list_email_render(){
    $options = get_option('employee_list_options');
    ?>
    <input type="text" name="employee_list_options[employee_list_email]" value="" />
    <?php
}

function employee_list_description_render(){
    $options = get_option('employee_list_options');
    ?>
    <textarea cols="40" rows="5" name="employee_list_options[employee_list_description]" value="" ></textarea>
<?php
}

function employee_list_editor_content_render(){
    $options = get_option('employee_list_options');
    // $content = isset($options['employee_list_editor_content']) ? esc_attr($options['employee_list_editor_content']) : '';
    wp_editor($content, 'employee_list_editor_content', array(
        'textarea_name' => 'employee_list_options[employee_list_editor_content]',
        'media_buttons' => true,
        'teeny' => true
    ));
}

function employee_list_upload_image_render(){
    $options = get_option('employee_list_options');
    ?>
    <input type="file" name="employee_list_image" value="" />
    <?php if ($image_url) : ?>
        <br><img src="<?php echo $image_url; ?>" style="max-width: 300px;"/>
    <?php endif; ?>
<?php
}


function employee_list_color_render() {
    $options = get_option('employee_list_options');
    ?>
    <input type="color" class="color-picker" name="employee_list_options[color]" value="" data-default-color="#000000" />
    <?php
}

function employee_list_sanitize($input){
    $new_input = array();

    if(isset($_POST['employee_list_options']['employee_list_title'])){
        $new_input['employee_list_title'] = sanitize_text_field($_POST['employee_list_options']['employee_list_title']);
    }

    if(isset($input['employee_list_email'])){
        $new_input['employee_list_email'] = sanitize_text_field($input['employee_list_email']);
    }

    if(isset($_POST['employee_list_options']['employee_list_description'])){
        $new_input['employee_list_description'] = sanitize_textarea_field($_POST['employee_list_options']['employee_list_description']);
    }

    if(isset($_POST['employee_list_options']['employee_list_editor_content'])){
        $new_input['employee_list_editor_content'] = wp_kses_post($_POST['employee_list_options']['employee_list_editor_content']);
    }

    if(!empty($_FILES['employee_list_image']['tmp_name'])){
        $upload = wp_upload_bits($_FILES['employee_list_image']['name'], null, file_get_contents($_FILES['employee_list_image']['tmp_name']));
        if(!$upload['error']){
            $new_input['employee_list_image'] = $upload['url'];
        }
    } else {
        $options = get_option('employee_list_options');
        $new_input['employee_list_image'] = isset($options['employee_list_image']) ? esc_url($options['employee_list_image']) : '';
    }

    if (isset($_POST['employee_list_options']['color'])) {
        $new_input['color'] = sanitize_hex_color($_POST['employee_list_options']['color']);
    }

    return $new_input;
}

function employee_list_enqueue_color_picker($hook_suffix) {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('my-script-handle', plugins_url('custom-script.js', __FILE__), array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'employee_list_enqueue_color_picker');


function employee_list_enqueue_scripts($hook_suffix){
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-validation', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js', array('jquery'), null, true);
    wp_enqueue_script('employee-list-custom-script', plugins_url('custom-script.js', __FILE__), array('jquery', 'jquery-validation'), null, true);
}
add_action('admin_enqueue_scripts', 'employee_list_enqueue_scripts');
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
    jQuery(document).ready(function($) {
        jQuery('.color-picker').wpColorPicker();
        jQuery('#frm').validate({
            rules: {
                'employee_list_options[title]': {
                    required: true,
                    minlength: 2
                },
                'employee_list_options[description]': {
                    required: true,
                    minlength: 5
                },
                'employee_list_options[editor_content]': {
                    required: true
                },
                'employee_list_options[date]': {
                    required: true,
                    date: true
                },
                'employee_list_image[image]': {
                    required: true,
                    accept: "image/"
                },
                'employee_list_options[color]': {
                    required: true
                }
            },
            messages: {
                'employee_list_options[title]': {
                    required: "Enter a title.",
                    minlength: "Title must be at least 2 characters long"
                },
                'employee_list_options[description]': {
                    required: "Enter a description",
                    minlength: "Description must be at least 5 characters long"
                },
                'employee_list_options[editor_content]': {
                    required: "Enter the editor content."
                },
                'employee_list_options[date]': {
                    required: "Enter a date.",
                    date: "Please enter a valid date"
                },
                'employee_list_image[image]': {
                    required: "Choose image",
                    accept: "Only image files are allowed"
                },
                'employee_list_options[color]': {
                    required: "Choose color"
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
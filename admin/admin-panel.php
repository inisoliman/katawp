<?php
if (!defined('ABSPATH')) exit;

class KataWP_Admin_Panel {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'القراءات اليومية',
            'قراءات',
            'manage_options',
            'katawp-settings',
            [$this, 'display_settings_page'],
            'dashicons-calendar',
            20
        );
    }
    
    public function register_settings() {
        register_setting('katawp-settings', 'katawp_settings');
    }
    
    public function display_settings_page() {
        ?>
        <div class="wrap">
            <h1>إعدادات القراءات اليومية</h1>
            <form method="post" action="options.php">
                <?php settings_fields('katawp-settings'); ?>
                <?php do_settings_sections('katawp-settings'); ?>
                <table class="form-table">
                    <tr>
                        <th>اللغة الافتراضية:</th>
                        <td>
                            <select name="katawp_language">
                                <option value="ar" selected>عربي</option>
                                <option value="en">انجليزي</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new KataWP_Admin_Panel();

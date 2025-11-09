<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Admin Settings Page
 * Handles all plugin settings and configuration
 */

class KataWP_Settings {
    
    const OPTION_NAME = 'katawp_settings';
    
    public static function register_admin_menu() {
        add_menu_page(
            __('KataWP Settings', 'katawp'),
            __('KataWP', 'katawp'),
            'manage_options',
            'katawp-settings',
            array(__CLASS__, 'render_settings_page'),
            'dashicons-book-alt',
            26
        );
    }
    
    public static function register_settings() {
        register_setting('katawp-settings-group', self::OPTION_NAME);
    }
    
    public static function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'katawp'));
        }
        
        $settings = get_option(self::OPTION_NAME, array());
        ?>
        <div class="wrap">
            <h1><?php _e('KataWP Settings', 'katawp'); ?></h1>
            
            <form method="post" action="options.php" class="katawp-settings-form">
                <?php settings_fields('katawp-settings-group'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="katawp_enable_cache">
                                <?php _e('Enable Cache', 'katawp'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="katawp_enable_cache" 
                                   name="<?php echo esc_attr(self::OPTION_NAME); ?>[enable_cache]" 
                                   value="1" <?php checked($settings['enable_cache'] ?? 0, 1); ?> />
                            <p class="description"><?php _e('Enable caching for better performance', 'katawp'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="katawp_cache_duration">
                                <?php _e('Cache Duration (Hours)', 'katawp'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" id="katawp_cache_duration" 
                                   name="<?php echo esc_attr(self::OPTION_NAME); ?>[cache_duration]" 
                                   value="<?php echo esc_attr($settings['cache_duration'] ?? 24); ?>" 
                                   min="1" max="720" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="katawp_default_language">
                                <?php _e('Default Language', 'katawp'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="katawp_default_language" 
                                    name="<?php echo esc_attr(self::OPTION_NAME); ?>[language]">
                                <option value="ar" <?php selected($settings['language'] ?? 'ar', 'ar'); ?>>العربية</option>
                                <option value="en" <?php selected($settings['language'] ?? 'ar', 'en'); ?>>English</option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="katawp-info">
                <h3><?php _e('Plugin Information', 'katawp'); ?></h3>
                <p><?php printf(__('Version: %s', 'katawp'), KATAWP_VERSION); ?></p>
                <p><?php printf(__('Database Version: %s', 'katawp'), KATAWP_DB_VERSION); ?></p>
            </div>
        </div>
        <?php
    }
}

// Hooks
add_action('admin_menu', array('KataWP_Settings', 'register_admin_menu'));
add_action('admin_init', array('KataWP_Settings', 'register_settings'));

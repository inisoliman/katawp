<?php
if (!defined('ABSPATH')) exit;

/**
 * KataWP Frontend Display Functions
 * Handles all frontend rendering
 */

class KataWP_Frontend {
    
    public static function enqueue_styles() {
        wp_enqueue_style('katawp-style', KATAWP_PLUGIN_URL . 'assets/css/katawp.css');
    }
    
    public static function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('katawp-script', KATAWP_PLUGIN_URL . 'assets/js/katawp.js', array('jquery'));
        
        wp_localize_script('katawp-script', 'katawpData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('katawp_nonce')
        ));
    }
    
    /**
     * Render daily readings
     */
    public static function render_readings($args = array()) {
        global $wpdb;
        
        $date = isset($args['date']) ? $args['date'] : current_time('Y-m-d');
        
        // Try cache first
        $cached = KataWP_Cache::get_reading($date);
        if ($cached !== false) {
            return $cached;
        }
        
        $readings = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}daily_readings WHERE reading_date = %s",
                $date
            )
        );
        
        ob_start();
        ?>
        <div class="kata-readings-container">
            <?php foreach ($readings as $reading): ?>
                <div class="kata-reading-card">
                    <h3><?php echo esc_html($reading->title); ?></h3>
                    <div class="kata-reading-content">
                        <?php echo wp_kses_post($reading->content); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $output = ob_get_clean();
        KataWP_Cache::set_reading($date, $output);
        return $output;
    }
    
    /**
     * Render synaxarium
     */
    public static function render_synaxarium($args = array()) {
        global $wpdb;
        
        $date = isset($args['date']) ? $args['date'] : current_time('Y-m-d');
        
        $cached = KataWP_Cache::get_synaxarium($date);
        if ($cached !== false) {
            return $cached;
        }
        
        $synaxarium = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}synaxarium WHERE synax_date = %s",
                $date
            )
        );
        
        ob_start();
        ?>
        <div class="kata-synaxarium">
            <?php foreach ($synaxarium as $item): ?>
                <div class="synax-item">
                    <h4><?php echo esc_html($item->saint_name); ?></h4>
                    <p><?php echo wp_kses_post($item->description); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $output = ob_get_clean();
        KataWP_Cache::set_synaxarium($date, $output);
        return $output;
    }
    
    /**
     * Render search form
     */
    public static function render_search_form() {
        ob_start();
        ?>
        <div class="kata-search-wrapper">
            <form class="kata-search-form" method="post">
                <input type="text" class="kata-search-input" placeholder="<?php _e('Search readings...', 'katawp'); ?>" />
                <button type="submit" class="kata-search-btn"><?php _e('Search', 'katawp'); ?></button>
            </form>
            <div class="kata-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Hooks
add_action('wp_enqueue_scripts', array('KataWP_Frontend', 'enqueue_styles'));
add_action('wp_enqueue_scripts', array('KataWP_Frontend', 'enqueue_scripts'));

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
        $date = isset($args['date']) ? $args['date'] : current_time('Y-m-d');
        $reading = katawp_get_reading_by_date($date);
        
        ob_start();

        if ($reading && isset($reading->synaxarium)) {
            ?>
            <div class="kata-readings-container">
                <div class="kata-reading-card">
                    <h3><?php echo esc_html($reading->synaxarium->DayName); ?></h3>
                    <div class="kata-reading-content">
                        <h4>Gospel Reading</h4>
                        <?php if (!empty($reading->gospel_reading)): ?>
                            <?php foreach ($reading->gospel_reading as $verse): ?>
                                <p><?php echo esc_html($verse->Text); ?></p>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p><?php _e('Reading not available.', 'katawp'); ?></p>
                        <?php endif; ?>

                        <h4>Pauline Reading</h4>
                        <?php if (!empty($reading->pauline_reading)): ?>
                            <?php foreach ($reading->pauline_reading as $verse): ?>
                                <p><?php echo esc_html($verse->Text); ?></p>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p><?php _e('Reading not available.', 'katawp'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="kata-readings-container">
                <p><?php _e('No readings found for this date.', 'katawp'); ?></p>
            </div>
            <?php
        }

        return ob_get_clean();
    }
    
    /**
     * Render synaxarium
     */
    public static function render_synaxarium($args = array()) {
        $date = isset($args['date']) ? $args['date'] : current_time('Y-m-d');
        $reading = katawp_get_reading_by_date($date);
        
        ob_start();

        if ($reading && isset($reading->synaxarium)) {
            ?>
            <div class="kata-synaxarium">
                <div class="synax-item">
                    <h4><?php echo esc_html($reading->synaxarium->DayName); ?></h4>
                    <p><strong>Seasonal Tune:</strong> <?php echo esc_html($reading->synaxarium->Seasonal_Tune); ?></p>
                    <p><strong>Weather Prayers:</strong> <?php echo esc_html($reading->synaxarium->Weather_Prayers); ?></p>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="kata-synaxarium">
                <p><?php _e('No synaxarium found for this date.', 'katawp'); ?></p>
            </div>
            <?php
        }

        return ob_get_clean();
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

<?php
// كود شورت كود لعرض القراءة اليومية
function katawp_readings_shortcode($atts) {
    $atts = shortcode_atts([
        'date' => date('Y-m-d')
    ], $atts);
    $reading = katawp_get_reading($atts['date']);
    $synax = katawp_get_synax($atts['date']);
    ob_start();
    echo '<div class="katawp-reading">';
    if ($reading) {
        echo '<h3>القراءات اليومية:</h3>';
        echo '<div>' . esc_html($reading->read_content) . '</div>';
    }
    if ($synax) {
        echo '<h4>السنكسار:</h4>';
        echo '<div>' . esc_html($synax->synax_content) . '</div>';
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('katawp_readings', 'katawp_readings_shortcode');

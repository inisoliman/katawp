<?php
if (!defined('ABSPATH')) exit;
?>
<div class="katawp-container">
    <div class="katawp-header">
        <h2><?php echo esc_html($reading->holiday_name ?? ''); ?></h2>
        <p class="katawp-date">📅 <?php echo esc_html(katawp_format_date($reading->gregorian_date ?? '')); ?></p>
    </div>
    <div class="katawp-content">
        <div class="katawp-description">
            <?php echo wp_kses_post($reading->holiday_description ?? ''); ?>
        </div>
        <div class="katawp-actions">
            <button class="btn btn-share">📤 مشاركة</button>
            <button class="btn btn-print" onclick="window.print()">🖨️ طباعة</button>
        </div>
    </div>
</div>

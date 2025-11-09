<?php

if (!defined('ABSPATH')) exit;

class KataWP_Widget_Readings extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'katawp_readings_widget',
            'القراءات اليومية - KataWP Readings',
            ['description' => 'عرض القراءات اليومية في الويدجت']
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $db = new KataWP_Database();
        $reading = $db->get_today_reading();
        
        if ($reading) {
            echo '<div class="katawp-widget-content">';
            echo '<h4>' . esc_html($reading->holiday_name) . '</h4>';
            echo '<p class="katawp-date">📅 ' . esc_html(katawp_format_date($reading->gregorian_date)) . '</p>';
            echo '<p>' . wp_kses_post(wp_trim_words($reading->holiday_description, 20)) . '</p>';
            echo '<a href="#" class="btn btn-small">اقرأ المزيد</a>';
            echo '</div>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : 'القراءات اليومية';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">العنوان:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

class KataWP_Widget_Search extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'katawp_search_widget',
            'بحث القراءات - KataWP Search',
            ['description' => 'بحث عن القراءات والسنكسار']
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        ?>
        <form class="katawp-search-form" method="GET" action="#">
            <input type="text" name="katawp_search" placeholder="ابحث عن قراءة..." class="katawp-search-input" />
            <button type="submit" class="katawp-search-btn">🔍 ابحث</button>
        </form>
        <?php
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : 'بحث القراءات';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">العنوان:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

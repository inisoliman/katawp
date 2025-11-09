/**
 * KataWP Admin JavaScript
 * Handles admin dashboard functionality
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeAdmin();
        handleMetaboxTabs();
        handleColorPicker();
        handleMultiselect();
    });

    /**
     * Initialize admin features
     */
    function initializeAdmin() {
        // Add admin styles and classes
        $('body').addClass('katawp-admin');
        
        // Initialize tooltips
        $('.katawp-tooltip').tooltip();
        
        // Initialize form validation
        $('.katawp-form').validate();
    }

    /**
     * Handle metabox tabs
     */
    function handleMetaboxTabs() {
        $('.katawp-tabs ul li a').on('click', function(e) {
            e.preventDefault();
            var tab = $(this).attr('href');
            
            $('.katawp-tabs ul li a').removeClass('active');
            $(this).addClass('active');
            
            $('.katawp-tab-content').hide();
            $(tab).show();
        });
    }

    /**
     * Handle color picker
     */
    function handleColorPicker() {
        $('.katawp-color-picker').spectrum({
            preferredFormat: 'hex',
            allowEmpty: true,
            change: function(color) {
                console.log('Color changed:', color.toHexString());
            }
        });
    }

    /**
     * Handle multiselect functionality
     */
    function handleMultiselect() {
        $('.katawp-multiselect').multiselect({
            columns: 2,
            placeholder: 'Select options',
            search: true
        });
    }

    /**
     * Handle AJAX settings save
     */
    $(document).on('click', '.katawp-save-settings', function() {
        var data = $('.katawp-settings-form').serialize();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'katawp_save_settings',
                settings: data,
                nonce: $('#katawp_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    showNotice('Settings saved successfully', 'success');
                } else {
                    showNotice('Error saving settings', 'error');
                }
            }
        });
    });

    /**
     * Show admin notice
     */
    function showNotice(message, type) {
        var noticeClass = 'notice-' + type;
        var notice = '<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>';
        $('.wrap').prepend(notice);
        
        setTimeout(function() {
            $('.notice').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

})(jQuery);

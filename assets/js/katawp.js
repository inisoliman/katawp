/**
 * KataWP - Frontend JavaScript
 * Handles search, print, and social share functionality
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        handleSearch();
        handlePrint();
        handleShare();
        initializeReadingCard();
    });

    /**
     * Handle search functionality with AJAX
     */
    function handleSearch() {
        $('.kata-search-form').on('submit', function(e) {
            e.preventDefault();
            var searchQuery = $('.kata-search-input').val();
            
            if (searchQuery.trim() === '') {
                return false;
            }

            $.ajax({
                url: katawpData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'katawp_search',
                    query: searchQuery,
                    nonce: katawpData.nonce
                },
                success: function(response) {
                    $('.kata-results').html(response);
                    console.log('Search results:', response);
                },
                error: function(error) {
                    console.error('Search error:', error);
                    $('.kata-results').html('<p class="error">Error performing search</p>');
                }
            });
        });
    }

    /**
     * Handle print functionality
     */
    function handlePrint() {
        $('.btn-print').on('click', function() {
            window.print();
        });
    }

    /**
     * Handle social share functionality
     */
    function handleShare() {
        $('.btn-share').on('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Check out this reading',
                    url: window.location.href
                });
            } else {
                copyToClipboard(window.location.href);
            }
        });
    }

    /**
     * Copy text to clipboard
     */
    function copyToClipboard(text) {
        var temp = $('<textarea>');
        $('body').append(temp);
        temp.val(text).select();
        document.execCommand('copy');
        temp.remove();
        alert('Link copied to clipboard!');
    }

    /**
     * Initialize reading card interactions
     */
    function initializeReadingCard() {
        $('.kata-reading-card').on('mouseover', function() {
            $(this).addClass('active');
        }).on('mouseout', function() {
            $(this).removeClass('active');
        });
    }

})(jQuery);

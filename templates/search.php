<?php
if (!defined('ABSPATH')) exit;
?>
<div class="katawp-search-container">
    <h3>🔍 بحث عن القراءات</h3>
    <form class="katawp-search-form" method="GET" action="#">
        <input type="text" id="katawp-search-input" name="katawp_search" 
               placeholder="ابحث عن قراءة أو حدث..." 
               class="katawp-search-input" required />
        <button type="submit" class="katawp-search-btn">بحث</button>
    </form>
    
    <?php if (!empty($results)): ?>
        <div class="katawp-results">
            <h4>نتائج البحث</h4>
            <ul>
                <?php foreach ($results as $result): ?>
                    <li>
                        <strong><?php echo esc_html($result->holiday_name ?? ''); ?></strong><br>
                        <small><?php echo esc_html($result->gregorian_date ?? ''); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p class="no-results">لم يتم العثور على نتائج</p>
    <?php endif; ?>
</div>

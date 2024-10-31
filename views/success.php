
<?php
/**
 * setting.php
 * Load the plugin setting page
 * @package Views
 * @version 1.0
 */
?>
<div class="wrap settings_wrap">
    <div class="clear_both"></div>
    <div class="title_line">
        <div id="icon-options-general" class="icon32"></div>
        <h2>Roomvu Marketing </h2>
        <?php if (isset($success) && $success != ''): ?>
            <div class='updated'>
                <p><strong><?php echo esc_html($success); ?></strong></p>
            </div>
        <?php endif; ?>
        <?php if (isset($error) && $error != ''): ?>
            <div class='error'>
                <p><strong><?php echo esc_html($error); ?></strong></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="settings_wrapper unite_settings_wide">
        <?php echo esc_html($count); ?> Posts are imported successfully

    </div><!--settings_wrapper-->
</div><!--wrap-->


<?php
/**
 * setting.php
 * Load the plugin setting page
 * @package Views
 * @version 1.0
 */
?>
<div id="rvm_container" class="wrap">

    <div class="clear_both"></div>
    <?php if (isset($success) && $success != ''): ?>
        <div class='updated pn_message pn-success'>
            <p><strong><?php echo esc_html($success); ?></strong></p>
        </div>
    <?php endif; ?>
    <?php if (isset($error) && $error != ''): ?>
        <div class='error pn_message pn-error'>
            <p><strong><?php echo esc_html($error); ?></strong></p>
        </div>
    <?php endif; ?>

    <div id="header">
        <div class="logo">
            <h2><?php echo esc_html($data['plugin_title']); ?></h2>
        </div>
        <a target="_new" href="https://www.roomvu.com/">
            <div class="icon-option"></div>
        </a>
        <div class="clear"></div>
    </div>

    <div id="main" class="">
        <div id="authentication" class="group">

            <div>
                <a href="<?php echo esc_url('edit.php?&page=' . $data['plugin_name'] . '&action=import'); ?>"
                   class="button button-primary"> <span class="dashicons dashicons-calendar-alt"></span> Import from
                    calendar</a>

                <div style="float: right">
                    Service Status :
                    <?php echo ($data['activeCron']) ? ' <span class="correct-data"> <span class="dashicons dashicons-saved"></span> Service enable </span> ' : ' <span class="invalid-data"> <span class="dashicons dashicons-no"></span> Disabled </span> '; ?>
                    <?php

                    if (!$data['activeCron']) {
                        echo '<i>Please fill required data and save setting form</i>';
                    }
                    ?>
                    <?php echo ($data['correctApi']) ? ' <span class="correct-data"> <span class="dashicons dashicons-saved"></span> The Credentials is ok </span> ' : ' <span class="invalid-data"> <span class="dashicons dashicons-no"></span> The Credentials is incorrect </span>'; ?>
                    <?php
                    if (!$data['correctApi']) {
                        echo '<i>System is not able to communicate with Api</i>';
                    }
                    ?>
                </div>
            </div>

            <form name="subscription_update_form" method="POST" action="<?php esc_attr($_SERVER['PHP_SELF']) ?>"
                  id="setting-update-form">
                <input type="hidden" class="large-text" name="profile_name"
                       value="<?php echo esc_attr((isset($settings['profile_name'])) ? $settings['profile_name'] : ''); ?>"/>
                <input type="hidden" class="large-text" name="name"
                       value="<?php echo esc_attr((isset($settings['name'])) ? $settings['name'] : ''); ?>"/>

                <table class="form-table">

                    <tr valign="top">
                        <th scope="row">
                            Email
                        </th>
                        <td>
                            <div class="controls">
                                <input type="email" required class="large-text" name="email"
                                       value="<?php echo esc_attr((isset($settings['email'])) ? $settings['email'] : ''); ?>"/>
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            Api key
                        </th>
                        <td>
                            <div class="controls">
                                <input type="text" required class="large-text" name="api_key"
                                       value="<?php echo esc_attr((isset($settings['api_key'])) ? $settings['api_key'] : ''); ?>"/>
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            Default Category
                        </th>
                        <td>
                            <div class="controls">
                                <?php wp_dropdown_categories([
                                    'name' => 'default_category',
                                    'hide_if_empty' => 0,
                                    'hide_empty' => 0,
                                    'selected' => (isset($settings['default_category'])) ? $settings['default_category'] : '',
                                ]); ?>
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            Default Post Status
                        </th>
                        <td>
                            <div class="controls">
                                <?php $statuses = get_post_statuses(); ?>
                                <select name="default_status" ?>
                                    <?php foreach ($statuses as $status => $statusText):
                                        $default = (isset($settings['default_status']) && $settings['default_status']) ? $settings['default_status'] : 'publish';
                                        ?>
                                        <option value="<?php echo esc_attr($status) ?>" <?php echo ($status == $default) ? 'selected="selected"' : ''; ?> ><?php echo esc_html($statusText) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">

                        </th>
                        <td>
                            <div class="controls">
                                <input type="submit" name="submit" id="submit" class="button button-primary"
                                       value="Save Changes">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div><!--settings_wrapper-->
</div><!--wrap-->



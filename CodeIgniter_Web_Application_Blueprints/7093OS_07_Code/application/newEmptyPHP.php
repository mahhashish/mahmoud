<?php

function scl_simple_options_page() {
    ?>
    <div class="wrap">
        <form method="post" id="scl_simple_options" action="options.php">
            <?php
            settings_fields('scl_simple_options');
            $options = get_option('scl_simple_options');
            ?>
            <h2><?php _e('Sample Options'); ?></h2>
            <table class = "form-table">
                <tr>
                    <th scope = "row"><?php _e('Short Links');
            ?></th>
                    <td colspan="3">
                        <p> <label>
                                <input name="scl_simple_options[shortlink]"
                                       type="checkbox" value="1" <?php checked($options['shortlink'], 1); ?>/>
                                       <?php _e('Display a short URL on all posts and
pages'); ?>
                            </label></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Google Verification'); ?></th>
                    <td colspan="3">
                        <input type="text" id="google_meta_key"
                               name="scl_simple_options[google_meta_key]" value="<?php echo esc_attr($options['google_meta_key']); ?>" />
                        <br /><span class="description"><?php _e('Enter the 
verification key for the Google meta tag.'); ?></span>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" value="<?php echo esc_attr_e('Update Options'); ?>"
                       class="button-primary" />
            </p>
        </form>
    </div>
    <?php
}

<?php
/*
 * Function for duplicating post
 */
include __DIR__.'/inc/acf-filter.php';
/**
 * Create the options page that we will later set "global"
 */
if(function_exists('acf_add_options_page')){
    acf_add_options_page([
        'page_title'  => 'Duplicate Content',
        'menu_title'  => 'Duplicate Content'
    ]);
}

/*
 * Function for duplicating post
 */

add_action('save_post', 'wpml_duplicate_on_publish');
function wpml_duplicate_on_publish ( $post_id ){
    $deActivate = get_field('deactivate', 'option');

    if($deActivate) return '';

    global $sitepress, $iclTranslationManagement;

    // don't save for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // don't save for revisions
    if (isset($post->post_type) && $post->post_type == 'revision') {
        return $post_id;
    }

    $master_post_id = $post_id;
    $master_post = get_post($master_post_id);
    $master_post_type = $master_post->post_type;

    $language_codes = get_field('language_code', 'option');
    $selected_post_types = get_field('content_type', 'option');

    if(in_array($master_post_type,$selected_post_types) && !empty($language_codes)) {
        $language_details_original = $sitepress->get_element_language_details($master_post_id, 'post_' . $master_post_type);
        foreach ($language_codes as $language_code) {
            $check_translation_exist = wpml_object_id_filter($master_post_id, $master_post_type, false, $language_code);
            if (!$check_translation_exist) {
                // unhook this function so it doesn't loop infinitely
                remove_action('save_post', 'wpml_duplicate_on_publish');
                foreach ($sitepress->get_active_languages() as $lang => $details) {
                    if ($lang != $language_details_original->language_code && ($lang == $language_code)) {
                        $iclTranslationManagement->make_duplicate($master_post_id, $lang);
                    }
                }
                // re-hook this function
                add_action('save_post', 'wpml_duplicate_on_publish');
            }
        }
    }
}
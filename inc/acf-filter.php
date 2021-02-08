<?php
/*
 * Load the languages in acf
 */
function app_acf_load_languages($field){
    $langArray = [];
    $languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0 ) );
    if($languages) {
        foreach( $languages as $language ){
            if( !$language['active'] ) :
                $langArray[$language['language_code']]=$language['translated_name'];
            endif;
        }
    }
    // $field['required'] = true;
    $field['choices'] = $langArray;
    return $field;
}
// Apply to fields named "language_code".
add_filter('acf/load_field/name=language_code', 'app_acf_load_languages');

/*
 * Load all the active languages in acf
 */
function app_acf_load_content_type($field){
    $typesArray =[];
    $args = [
        'public'   => true,
        '_builtin' => false
    ];

    $typesArray['page']='Page';
    $typesArray['post']='Post';
    $types = get_post_types( $args, 'objects' );
    if($types) {
        foreach ($types as $type) {
            $typesArray[$type->name]=$type->labels->singular_name;
        }
    }
    $field['choices'] = $typesArray;
    return $field;
}
// Apply to fields named "content_type".
add_filter('acf/load_field/name=content_type', 'app_acf_load_content_type');

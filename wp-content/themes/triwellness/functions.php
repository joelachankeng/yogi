<?php
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
function theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', null, '3');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/dist/styles.min.css', false, null);
    wp_enqueue_script('child-js', get_stylesheet_directory_uri() . '/dist/app.min.js', [], null, true);
}
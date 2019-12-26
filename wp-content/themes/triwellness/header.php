<?php 
/****
 * 
 * This is the template for the header
 * 
 * Displays all of the head element and everything up to the 'site-content' <div class=""></div>
 * 
 * 
 */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width; initial-scale=1">

        <?php wp_head(); ?>
    </head>

    <body>
        <div class="site-content" id="content">
    

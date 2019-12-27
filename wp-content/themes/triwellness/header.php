<?php 
/****
 * 
 * This is the template for the header
 * 
 * Displays all of the head element and everything up to the 'site-content' <div class=""></div>
 * 
 * 
 */

//This is function to get the menu items and divide in half.
$main_menu = get_term(get_nav_menu_locations()['main-menu'], 'nav_menu')->name;
$menu_items = wp_get_nav_menu_items($main_menu);
$num_of_menu_items_half = round(count($menu_items) / 2);

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
            <header class="header flex flex-col">
                <div class="notification-bar">
                    <a href='#' class='notification hover-secondary'>notification 1</a>
                    <a href='#' class='notification hover-secondary'>notification 2</a>
                    <a href='#' class='notification hover-secondary'>notification 3</a>
                </div>
                <div class="main-menu flex flex-row w-ful items-center justify-center">
                    <ul class="menu-left flex">
                    <?php
                        $x = 1;
                        foreach($menu_items as $menu_item) {
                            if($x <= $num_of_menu_items_half) { ?>
                                <li class="menu-items">
                                    <a href="<?php echo $menu_item->url; ?>"
                                        class="color-primary hover uppercase font-bold"
                                    >
                                        <?php echo $menu_item->title; ?>
                                    </a>                                
                                </li>
                    <?php 
                            }           
                            $x++;
                        }
                    ?>
                    </ul>
                    <a href="<?php echo get_bloginfo('url'); ?>" class="logo">
                        <img src="
                            <?php 
								$custom_logo_id = get_theme_mod( 'custom_logo' );
								$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
								echo $image[0];
							?>
                        " alt="" class="logo-img">
                    </a>
                    <ul class="menu-right flex">
                    <?php
                        $x = 1;
                        foreach($menu_items as $menu_item) {
                            if($x > $num_of_menu_items_half) { ?>
                                <li class="menu-items">
                                    <a href="<?php echo $menu_item->url; ?>"
                                        class="color-primary hover uppercase font-bold"
                                    >
                                        <?php echo $menu_item->title; ?>
                                    </a>                                
                                </li>
                    <?php 
                            }           
                            $x++;
                        }
                    ?>
                    </ul>
                </div>
            </header>


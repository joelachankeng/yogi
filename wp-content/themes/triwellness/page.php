<?php 
/****
 * 
 * This is the template for displaying pages
 * 
 * This is the template that displays all pages by defualt
 * 
 * 
 * 
 */

    get_header();
?>

<div id="primary">
    <main id="main" role="main">
        <?php 
            //start the loop.
            while( have_posts() ) :
                the_post();

                //end of the loop
            endwhile;
        ?>
    </main> <!---- .site-main --->
</div><!--- .content.area --->while
<?php get_footer(); ?>
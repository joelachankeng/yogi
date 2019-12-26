<?php

$blog_name		= get_bloginfo( 'name' );
$blog_desc		= get_bloginfo( 'description' );
$image_logo		= '';
$retina_logo	= '';
$page_logo		= '';
$class_center_vertical = '';
if ( noo_get_option( 'noo_header_use_image_logo', false ) ) {
	$image_logo		= 'noo-logo-normal';
	if ( noo_get_image_option( 'noo_header_logo_image', '' ) !=  '' ) {
		$image_logo = noo_get_image_option( 'noo_header_logo_image', '' );
		$retina_logo = noo_get_image_option( 'noo_header_logo_retina_image', $image_logo );
	}
}
?>
<div class="navbar-wrapper">
	<div class="navbar navbar-default <?php echo noo_navbar_class(); ?>" role="navigation">
		<div class="container-boxed max">
			<div class="navbar-header">
				<?php if ( is_front_page() ) : echo '<h1 class="sr-only">' . $blog_name . '</h1>'; endif; ?>
				<a class="navbar-toggle collapsed" data-toggle="collapse" data-target=".noo-navbar-collapse">
					<span class="sr-only"><?php echo __( 'Navigation', 'noo' ); ?></span>
					<i class="fa fa-bars"></i>
				</a>
				<?php if( noo_get_option( 'noo_header_member', true ) ) :?>
					<a class="navbar-toggle member-navbar-toggle collapsed" data-toggle="collapse" data-target=".noo-user-navbar-collapse">
						<i class="fa fa-user"></i>
					</a>
				<?php elseif( defined('WOOCOMMERCE_VERSION') && noo_get_option( 'noo_header_minicart', true ) ) : 
					echo noo_minicart_mobile();
				endif;?>
				<a href="<?php echo home_url( '/' ); ?>" class="navbar-brand" title="<?php echo esc_attr($blog_desc); ?>">
				<?php echo ( $image_logo == '' ) ? $blog_name : '<img class="noo-logo-img noo-logo-normal" src="' . $image_logo . '" alt="' . $blog_desc . '">'; ?>
				<?php echo ( $retina_logo == '' ) ? '' : '<img class="noo-logo-retina-img noo-logo-normal" src="' . $retina_logo . '" alt="' . $blog_desc . '">'; ?>
				<?php echo ( $page_logo == '' ) ? '' : '<img class="noo-logo-img noo-logo-floating" src="' . $page_logo . '" alt="' . $blog_desc . '">'; ?>
				</a>
			</div> <!-- / .nav-header -->
			<?php if(noo_get_option( 'noo_header_member', true )) :?>
				<nav class="collapse navbar-collapse noo-user-navbar-collapse" role="navigation">
					<ul class="navbar-nav sf-menu">
						<?php if( !is_user_logged_in() ) : ?>
							<?php if(defined('WOOCOMMERCE_VERSION') && noo_get_option( 'noo_header_minicart', true )) :
								global $woocommerce;
							?>
								<li class="menu-item fly-right"><a  href="<?php echo wc_get_cart_url();?>" class="member-cart-link"><?php _e('My Cart','noo')?></a></li>
							<?php endif; ?>
							<li class="menu-item fly-right"><a  href="<?php echo Noo_Member::get_login_url();?>" class="member-login-link"><?php _e('Login','noo')?></a></li>
							<li class="menu-item fly-right"><a href="<?php echo Noo_Member::get_register_url();?>" class="member-register-link"><?php _e('Register','noo')?></a></li>									
						<?php else : ?>
							<?php if(defined('WOOCOMMERCE_VERSION') && noo_get_option( 'noo_header_minicart', true )) :
								global $woocommerce;
							?>
								<li class="menu-item fly-right"><a  href="<?php echo wc_get_cart_url();?>" class="member-cart-link"><?php _e('My Cart','noo')?></a></li>
							<?php endif; ?>
							<?php if(defined('WOOCOMMERCE_VERSION')) : ?>
								<li class="menu-item fly-right"><a href="<?php echo Noo_Member::get_manage_page_url()?>"><?php _e('Manage Membership','noo')?></a></li>
							<?php endif; ?>
							<li class="menu-item fly-right"><a href="<?php echo Noo_Member::get_logout_url()?>"><?php _e('Log out','noo')?></a></li>
						<?php endif;?>
					</ul>
				</nav>
			<?php endif;?>

			<?php if( noo_get_option( 'noo_header_member', true ) || ( defined('WOOCOMMERCE_VERSION') && noo_get_option( 'noo_header_minicart', true )) ) :?>
				<nav>
					<ul class="navbar-nav sf-menu  member-links header-right">
						<?php if(noo_get_option( 'noo_header_member', true )) :?>
							<li class="member-info">
								<a href="#">
									<span class="user-name"><i class="fa fa-user"></i></span>
								</a>
							<?php if( !is_user_logged_in() ) : ?>
							<ul class="sub-menu">
								<li><a  href="<?php echo Noo_Member::get_login_url();?>" class="member-login-link"><i class="fa fa-sign-in"></i> <?php _e('Login','noo')?></a></li>
								<li><a href="<?php echo Noo_Member::get_register_url();?>" class="member-register-link"><i class="fa fa-key"></i> <?php _e('Register','noo')?></a></li>
							</ul>
							<?php else : ?>
								<?php global $current_user;
								    wp_get_current_user();
								?>
								<ul class="sub-menu">
									<?php if(defined('WOOCOMMERCE_VERSION')) : ?>
										<li><a href="<?php echo Noo_Member::get_manage_page_url()?>"><i class="fa fa-sign-out"></i> <?php _e('Manage Membership','noo')?></a></li>
									<?php endif;?>
									<li><a href="<?php echo Noo_Member::get_logout_url()?>"><i class="fa fa-sign-out"></i> <?php _e('Log out','noo')?></a></li>
								</ul>
							<?php endif;?>
							</li>
						<?php endif ?>
						<?php 
						if(defined('WOOCOMMERCE_VERSION') && noo_get_option( 'noo_header_minicart', true )){
							echo noo_minicart();
						}
						?>
					</ul>
				</nav>
			<?php endif ?>

			<nav class="collapse navbar-collapse noo-navbar-collapse<?php echo (is_one_page_enabled() ? ' navbar-scrollspy':'')?>" role="navigation">
	        <?php
				if ( has_nav_menu( 'primary' ) ) :
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav sf-menu'
						) );
				else :
					echo '<ul class="navbar-nav nav"><li><a href="' . home_url( '/' ) . 'wp-admin/nav-menus.php">' . __( 'No menu assigned!', 'noo' ) . '</a></li></ul>';
				endif;
			?>
			</nav> <!-- /.navbar-collapse -->


		</div> <!-- /.container-fluid -->
	</div> <!-- / .navbar -->
</div>

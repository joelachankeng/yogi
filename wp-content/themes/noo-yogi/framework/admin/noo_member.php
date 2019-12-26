<?php
class Noo_Member {
	
	protected static $_instance = null;
	public $settings_screen = null;
	public static $option_key = 'noo_member';
	
	public function __construct(){
		add_action( 'wp_ajax_nopriv_noo_ajax_login', array(__CLASS__, 'ajax_login') );
		add_action( 'wp_ajax_noo_ajax_login', array(__CLASS__, 'ajax_login_priv') );
		add_action( 'wp_ajax_nopriv_noo_ajax_register', array(__CLASS__, 'ajax_register') );
		add_action( 'wp_ajax_noo_ajax_register', array(__CLASS__, 'ajax_register') );
		
		add_filter('login_url', array(&$this,'login_url'),99999);
		add_filter('logout_url', array(&$this,'logout_url'),99999);
		add_filter('register_url', array(&$this,'register_url'),99999);
		add_filter('lostpassword_url', array(&$this,'lostpassword_url'),99999);
		
		add_action( 'init', array( __CLASS__, 'lost_password_action' ) );
		
		add_shortcode('noo_login_form', array(__CLASS__,'noo_login_form_shortcode'));
		add_shortcode('noo_register_form', array(__CLASS__,'noo_register_form_shortcode'));
		add_shortcode('noo_forgotpassword_form',array(__CLASS__,'noo_forgotpassword_form_shortcode'));
		
		add_shortcode('noo_member_manage', array(__CLASS__,'member_manage_shortcode'));
		
		
		add_shortcode('noo_membership_package', array(__CLASS__,'membership_package_shortcode'));
		
		add_action( 'wp_enqueue_scripts', array(&$this,'enqueue_scripts') );
		if(is_admin()){
			add_action( 'admin_menu', array( $this, 'settings_menu' ) );
			add_action( 'admin_init', array( $this, 'plugin_settings' ) );
		}
		add_action( 'template_redirect', array( &$this, 'template_redirect' ) );
	}
	
	public function template_redirect(){
		global $member_manage_id;
		if(empty($member_manage_id)){
			$member_manage_id = get_the_ID();
		}
	}
	
	/**
	 * Retrieve get Instance
	 * 
	 * @return Noo_Member
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function get_setting( $id, $default = null ){
		$options = get_option(self::$option_key);
		if (isset($options[$id])) {
			return $options[$id];
		}
		return $default;
	}
	
	public function settings_menu(){
		$this->settings_screen = add_options_page(
			__( 'Member Settings', 'noo' ),
			__( 'Member Settings', 'noo' ),
			'manage_options',
			'noo-member-settings',
			array( $this, 'settings_page' )
		);
	}
	
	public function settings_page() {
		$screen = get_current_screen();
	
		if ( ! $this->settings_screen || $screen->id !== $this->settings_screen ) {
			return;
		}
		// Load the plugin options.
		$settings = get_option( self::$option_key );
	
		?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper"><?php _e( 'Member Settings', 'noo' ); ?></h2>
			<form method="post" action="options.php">
				<?php
					settings_fields( self::$option_key );
					do_settings_sections( self::$option_key );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function plugin_settings() {

		// Process the settings.
		foreach ( self::options_list() as $settings_id => $sections ) {

			// Create the sections.
			foreach ( $sections as $section_id => $section ) {
				add_settings_section(
					$section_id,
					$section['title'],
					'__return_false',
					$settings_id
				);

				// Create the fields.
				foreach ( $section['fields'] as $field_id => $field ) {
					switch ( $field['type'] ) {
						case 'text':
							add_settings_field(
								$field_id,
								$field['title'],
								array( $this, 'text_element_callback' ),
								$settings_id,
								$section_id,
								array(
									'tab'         => $settings_id,
									'id'          => $field_id,
									'class'       => 'regular-text',
									'default' => isset( $field['default'] ) ? $field['default'] : '',
									'description' => isset( $field['description'] ) ? $field['description'] : ''
								)
							);
							break;
						case 'checkbox':
							add_settings_field(
								$field_id,
								$field['title'],
								array( $this, 'checkbox_element_callback' ),
								$settings_id,
								$section_id,
								array(
									'tab'         => $settings_id,
									'id'          => $field_id,
									'default' => isset( $field['default'] ) ? $field['default'] : '',
									'description' => isset( $field['description'] ) ? $field['description'] : ''
								)
							);
							break;
						case 'radio':
							add_settings_field(
								$field_id,
								$field['title'],
								array( $this, 'radio_element_callback' ),
								$settings_id,
								$section_id,
								array(
								'tab'         => $settings_id,
								'id'          => $field_id,
								'title'       => $field['title'],
								'default'     => isset( $field['default'] ) ? $field['default'] : '',
								'description' => isset( $field['description'] ) ? $field['description'] : '',
								'options'	  => isset( $field['options'] ) ? $field['options'] : array(),
								)
							);
							break;
						case 'select':
							add_settings_field(
								$field_id,
								$field['title'],
								array( $this, 'select_element_callback' ),
								$settings_id,
								$section_id,
								array(
								'tab'         => $settings_id,
								'id'          => $field_id,
								'title'       => $field['title'],
								'default'     => isset( $field['default'] ) ? $field['default'] : '',
								'description' => isset( $field['description'] ) ? $field['description'] : '',
								'options'	  => isset( $field['options'] ) ? $field['options'] : array(),
								)
							);
							break;
						case 'dropdown_pages':
							add_settings_field(
								$field_id,
								$field['title'],
								array( $this, 'dropdown_pages_element_callback' ),
								$settings_id,
								$section_id,
								array(
								'tab'         => $settings_id,
								'id'          => $field_id,
								'default' => isset( $field['default'] ) ? $field['default'] : '',
								'description' => isset( $field['description'] ) ? $field['description'] : ''
								)
							);
							break;
						default:
							break;
					}
				}
			}
			register_setting( $settings_id, $settings_id );
		}
	}

	public function text_element_callback( $args ) {
		$tab     = $args['tab'];
		$id      = $args['id'];
		$class   = isset( $args['class'] ) ? $args['class'] : 'small-text';
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$current = self::get_setting( $id, $default );
		$html    = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="%4$s" />', $id, $tab, $current, $class );

		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		echo $html;
	}
	
	public function dropdown_pages_element_callback($args){
		$tab     = $args['tab'];
		$id      = $args['id'];
		$class   = isset( $args['class'] ) ? $args['class'] : 'small-text';
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$current = self::get_setting( $id, $default );
		$dropdown_args = array(
			'name'             => sprintf('%2$s[%1$s]',$id,$tab),
			'id'               => $id,
			'sort_column'      => 'menu_order',
			'sort_order'       => 'ASC',
			'show_option_none' => ' ',
			'class'            => '',
			'echo'             => false,
			'selected'         => $current
		);
		echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'noo' ) .  "' id=", wp_dropdown_pages( $dropdown_args ) );
		
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			echo sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	}

	public function checkbox_element_callback( $args ) {
		$tab     = $args['tab'];
		$id      = $args['id'];
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$current = self::get_setting( $id, $default );
		$html    = sprintf( '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1"%3$s />', $id, $tab, checked( 1, $current, false ) );
		$html   .= sprintf( '<label for="%s"> %s</label></br>', $id, __( 'Activate/Deactivate', 'noo' ) );

		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		echo $html;
	}

	public function select_element_callback( $args ) {
		$tab     = $args['tab'];
		$id      = $args['id'];
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$current = self::get_setting( $id, $default );

		$html = sprintf( '<select id="%1$s" name="%2$s[%1$s]" >', $id, $tab );
		if( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			foreach ($args['options'] as $value => $label) {
				$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', $value, selected( $current, $value, false ), $label );
			}
		}

		$html .= '</select>';

		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		echo $html;
	}

	public function radio_element_callback( $args ) {
		$tab     = $args['tab'];
		$id      = $args['id'];
		$default = isset( $args['default'] ) ? $args['default'] : '';
		$current = self::get_setting( $id, $default );

		$html = sprintf( '<fieldset id="%1$s">', $id );
		$html .= '<legend class="screen-reader-text">' . esc_html($args['title']) . '</legend>';
		if( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			foreach ($args['options'] as $value => $label) {
				$html .= '<label title="none">';
				$html .= 	sprintf( '<input type="radio" name="%1$s[%2$s]" value="%3$s" %4$s />', $tab, $id, $value, checked( $current, $value, false ) );
				$html .= 	sprintf( '<span>%1$s</span>', $label );
				$html .= '</label>';
				$html .= '<br/>';
			}
		}

		$html .= '</fieldset>';

		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		echo $html;
	}

	/**
	 *  Options list.
	 *
	 * @return array
	 */
	protected static function options_list() {		
		$settings = array(
			self::$option_key => array(
				'pages' => array(
					'title'  => __( 'Member Pages', 'noo' ),
					'fields' => array(
						'manage_page' => array(
							'title'   => __( 'Manage Page', 'noo' ),
							'type'    => 'dropdown_pages',
							'description' => sprintf(__( 'Page content %s', 'noo' ),'[noo_member_manage]'),
						),
						'login_page' => array(
							'title'   => __( 'Login Page', 'noo' ),
							'type'    => 'dropdown_pages',
							'description' => sprintf(__( 'Page content %s', 'noo' ),'[noo_login_form]'),
						),
						'register_page' => array(
							'title'   => __( 'Register Page', 'noo' ),
							'type'    => 'dropdown_pages',
							'description' => sprintf(__( 'Page content %s', 'noo' ),'[noo_register_form]'),
						),
						'forgotpassword_page' => array(
							'title'   => __( 'Forgot Password Page', 'noo' ),
							'type'    => 'dropdown_pages',
							'description' => sprintf(__( 'Page content %s', 'noo' ),'[noo_register_form]'),
						),
					)
				),
			),
		);

		return $settings;
	}
	
	public function enqueue_scripts(){
		wp_register_script('noo-member', NOO_ASSETS_URI . '/js/member.js',array('jquery'),null,true);
		$nooMemberL10n = array(
			'ajax_security' =>wp_create_nonce( 'noo-member-security' ),
			'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
			'is_manage_page'=>get_the_ID() == self::get_setting('manage_page'),
			'loadingmessage'=>'<i class="fa fa-spinner fa-spin"></i> '.__('Sending info, please wait...','noo'),
		);
		wp_localize_script('noo-member', 'nooMemberL10n', $nooMemberL10n);
		wp_enqueue_script('noo-member');
	}
	
	
	public static function get_login_url(){
		return wp_login_url();
	}
	
	
	public static function get_register_url(){
		return wp_registration_url();
	}
	
	public static function get_logout_url(){
		return wp_logout_url();
	}
	
	public function login_url($login_url){
		$basename = basename($_SERVER['REQUEST_URI']);
		if($basename != 'wp-login.php' && $login_page =  self::get_setting('login_page')){
			$new_login_url = get_permalink($login_page);
			if( $var_pos = strpos($login_url, '?') ) {
				$login_args = wp_parse_args( substr($login_url, $var_pos + 1), array() );
				if( isset( $login_args['redirect_to'] ) ) $login_args['redirect_to'] = urlencode($login_args['redirect_to']);
				
				$new_login_url = esc_url( add_query_arg($login_args, $new_login_url ) );
			}

			return $new_login_url;
		}
		return $login_url;
	}
	
	public function register_url($register_url){
		if($register_page =  self::get_setting('register_page')){
			$register_url = get_permalink($register_page);
		}
		return $register_url;
	}
	
	public function logout_url($logout_url,$redirect=''){
		if($login_page =  self::get_setting('login_page')){
			$login_url = get_permalink($login_page);
			$args['redirect_to'] = urlencode( $login_url );
			return  esc_url(add_query_arg($args, $logout_url));
		}
		return $logout_url;
	}
	
	public function lostpassword_url($lostpassword_url){
		if($forgotpassword_page =  self::get_setting('forgotpassword_page')){
			$lostpassword_url = get_permalink($forgotpassword_page);
		}
		return $lostpassword_url;
	}
	
	public static function get_manage_page_url(){
		$url = '';
		if($manage_page = self::get_setting('manage_page'))
			$url = get_permalink($manage_page);
		return $url;
	}
	
	public static function member_manage_shortcode($atts, $content = null){
		extract( shortcode_atts( array(
			'class'                 => '',
			'id'                    => '',
		), $atts));
		if(!is_user_logged_in())
			wp_safe_redirect(self::get_login_url());
		$page = isset($_GET['page']) ? $_GET['page']: '';
		$user_id = get_current_user_id();
		$user_info = get_userdata($user_id);
		if( get_the_ID() == self::get_setting('manage_page')){
			switch ($page){
				default:
					if($user_package = get_user_meta($user_id,'_membership_package',true)){
						ob_start();
						$order_id = isset($user_package['order_id']) ? $user_package['order_id'] : 0;
						?>
						<div class="user-membership-package">
							<h1><?php esc_html_e('Your package','noo')?></h1>
							<div class="user-membership-package-content">
								<div class="row">
									<div class="col-xs-6"><strong><?php _e('Name','noo')?></strong></div>
									<div class="col-xs-6"><?php echo esc_html($user_info->display_name) ?></div>
								</div>
								<div class="row">
									<div class="col-xs-6"><strong><?php _e('Email','noo')?></strong></div>
									<div class="col-xs-6"><?php echo esc_html($user_info->user_email) ?></div>
								</div>
								<div class="row">
									<div class="col-xs-6"><strong><?php _e('Order ID','noo')?></strong></div>
									<div class="col-xs-6"><?php echo absint($order_id); ?></div>
								</div>
								<div class="row">
									<div class="col-xs-6"><strong><?php _e('Package','noo')?></strong></div>
									<div class="col-xs-6"><?php echo get_the_title($user_package['product_id']) ?></div>
								</div>
								<div class="row">
									<div class="col-xs-6"><strong><?php _e('Created','noo')?></strong></div>
									<div class="col-xs-6"><?php echo mysql2date('d/m/Y', $user_package['created']); ?></div>
								</div>
							</div>
						</div>
						<?php
						return ob_get_clean();
					}else{
						return self::membership_package_shortcode();
					}
				break;
			}
		}
	}
	
	public static function is_membership(){
		return true;
	}
	
	public static function membership_package_shortcode(){
		global $noo_view_membership_package;
		$noo_view_membership_package = true;
		ob_start();
		?>
<div class="membership-package clearfix">
		<?php 
		$packages = get_posts( array(
			'post_type'      => 'product',
			'posts_per_page' => 6,
			// 'orderby'=> 'ID',
   //  		'order' => 'asc',
			'post_status'=>array('publish'),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'membership_package' )
				)
			)
		) );
		
		?>
		<?php if($packages):?>
			<div class="noo-pricing-table classic pricing-<?php  echo esc_attr(count($packages))?>-col package-pricing">
			<?php foreach ($packages as $package):?>
				<?php $product = wc_get_product($package->ID);?>
				<div class="noo-pricing-column <?php if($product->is_featured()):?>featured<?php endif;?>">
				    <div class="pricing-content">
				        <div class="pricing-header">
				        	<?php if($_package_sub_title = $product->get_sub_title()):?>
				        	<span><?php echo esc_html($_package_sub_title)?></span>
				        	<?php endif;?>
				            <h2 class="pricing-title"><?php echo esc_html($product->get_title())?></h2>
				            <span class="arrow">&nbsp;</span>
				        </div>
				        <div class="pricing-price">
				        	  <h3 class="pricing-value"><span class="noo-price"><?php echo $product->get_price_html()?></span></h3>
				        </div>
				        <?php if($featureds = $product->get_featureds()):?>
				        <?php 
				        $featured_arr = explode("\n", $featureds);
				        ?>
				        <div class="pricing-info">
				            <ul class="noo-ul-icon fa-ul">
				            	<?php foreach ((array)$featured_arr as $featured):?>
				            	<li>
				            		<?php echo ($featured)?>
				            	</li>
				            	<?php endforeach;?>
				            </ul>
				        </div>
				        <?php endif;?>
				        <div class="pricing-footer">
				        	<a class="btn" href="<?php echo esc_url($product->add_to_cart_url())?>" data-package="<?php the_ID() ?>"><?php echo wp_kses_post($product->add_to_cart_text())?></a>
				        </div>
				    </div>
				</div>
			<?php endforeach;?>
		</div>
	<?php endif;?>
</div>
		<?php
		wp_reset_query();
		return ob_get_clean();
	}
	
	public static function noo_login_form_shortcode(){
		ob_start();
		?>
		<div class="login-form">
			<?php self::ajax_login_form(__('Login','noo'));?>
		</div>
		<?php
		return ob_get_clean();
	}
	
	public static  function  noo_register_form_shortcode(){
		ob_start();
		?>
		<div class="noo-register-form">
			<?php self::ajax_register_form(__('Register','noo'));?>		
		</div>
		<?php
		return ob_get_clean();	
	}
	
	public static function noo_forgotpassword_form_shortcode(){
		ob_start();
		do_action('noo_lost_password_form_before');
		?>
<div class="account-form">
    <div class="account-lost-password-form row">
    	<div class="col-sm-8">
	        <form class="form-horizontal" id="noo-lost-password-form" method="post">
	        	<div style="display: none">
	        		<input type="hidden" name="action" value="lost_password"> 
					<?php wp_nonce_field('lost-password')?>
	        	</div>
	        	<p class="lost-pass-desc"><?php _e('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.','noo')?></p>
	            <div class="form-group row">
	                <label class="col-sm-3 control-label" for="user_login"><?php _e('Username or email','noo')?></label>
	                <div class="col-sm-9">
	                    <input type="text" placeholder="Username" required autofocus name="user_login" id="user_login" class="form-control">
	                </div>
	            </div>
	            <div class="form-actions form-group text-center">
	                <button class="btn btn-primary" type="submit"><?php esc_html_e('Reset Password','noo')?></button>
	            </div>
	        </form>
		</div>
    </div>
</div>
		<?php
		do_action('noo_lost_password_form_after');
		return ob_get_clean();
	}
	
	public static function ajax_login_form($submit_label='Sign In'){
		?>
		<form id="noo-ajax-login-form" action="<?php echo wp_login_url(apply_filters('noo_login_redirect','')); ?>" class="form-horizontal row">
			<div class="col-sm-8">
				<div style="display: none">
					<input type="hidden" name="action" value="noo_ajax_login">
					<?php wp_nonce_field('noo-ajax-login','security')?>
				</div>
				<div class="form-group text-center noo-ajax-result" style="display: none"></div>
				<?php do_action( 'noo_login_form_start' ); ?>
				<div class="form-group row">
				    <label for="log" class="col-sm-3 control-label"><?php _e('Username','noo')?></label>
				    <div class="col-sm-9">
				      <input type="text" class="form-control" id="log"  name="log" autofocus required placeholder="<?php echo esc_attr__('Username','noo')?>">
				    </div>
				 </div>
				 <div class="form-group row">
				    <label for="pwd" class="col-sm-3 control-label"><?php _e('Password','noo')?></label>
				    <div class="col-sm-9">
				      <input type="password" id="pwd" required value="" name="pwd"  class="form-control" placeholder="<?php echo esc_attr__('Password','noo')?>">
				    </div>
				 </div>
				 <div class="form-group row">
				    <div class="col-sm-9 col-sm-offset-3">
				    	<div>
				    		<div class="form-control-flat"><label><input type="checkbox"  id="rememberme" name="rememberme" value="forever"><i></i> <?php _e('Keep me logged in','noo'); ?></label></div>
					    </div>
					</div>
				</div>

				<?php do_action( 'noo_login_form' ); ?>
				<div class="form-group row">
					<div class="form-actions col-sm-9 col-sm-offset-3">
					 	<button type="submit" class="btn btn-primary"><?php echo esc_html($submit_label)?></button>
					 	<span><a class="forgot-password" href="<?php echo wp_lostpassword_url()?>"><?php _e('Forgot Password?','noo')?></a></span>
					</div>
				</div>
				<?php do_action( 'noo_login_form_end' ); ?>
			</div>

			<div class="col-sm-4 noo-register-now">
				<?php if( isset($_GET['redirect_to']) && !empty($_GET['redirect_to']) ) : ?>
			 		<input type="hidden" id="redirect_to" name="redirect_to" value="<?php echo esc_url( $_GET['redirect_to'] ); ?>" />
			 	<?php endif; ?>
				<?php if(get_option('users_can_register')):?>
				 	<p class="title"><b><?php echo sprintf(__('I don\'t have acount','noo')); ?></b></p>
				 	<?php echo sprintf(__('<a href="%s" class="btn-primary" rel="nofollow">Register Now</a>','noo'),self::get_register_url())?>
				<?php endif;?>
			</div>

		</form>
		<?php	
	}
	
	public static function ajax_register_form($submit_label='Sign Up'){
		?>
		<form id="noo-ajax-register-form" action="<?php echo esc_url( wp_registration_url() ); ?>" class="form-horizontal">
			<div style="display: none">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url(apply_filters('noo_register_redirect','')); ?>" />
				<input type="hidden" name="action" value="noo_ajax_register">
				<?php wp_nonce_field('noo-ajax-register','security')?>
			</div>
			<div class="form-group text-center noo-ajax-result" style="display: none"></div>
			<?php do_action( 'noo_register_form_start' ); ?>
			<div class="form-group row">
			    <label for="user_login" class="col-sm-4 control-label"><?php _e('Username','noo')?></label>
			    <div class="col-sm-8">
			      <input type="text" class="form-control" id="user_login"  name="user_login" autofocus required placeholder="<?php echo esc_attr__('Username','noo')?>">
			    </div>
			 </div>
			 <div class="form-group row">
			    <label for="user_email" class="col-sm-4 control-label"><?php _e('Email','noo')?></label>
			    <div class="col-sm-8">
			      <input type="email" class="form-control" id="user_email"  name="user_email" required placeholder="<?php echo esc_attr__('Email','noo')?>">
			    </div>
			 </div>
			 <div class="form-group">
			    <label for="user_password" class="col-sm-4 control-label"><?php _e('Password','noo')?></label>
			    <div class="col-sm-8">
			      <input type="password" id="user_password" required value="" name="user_password"  class="form-control" placeholder="<?php echo esc_attr__('Password','noo')?>">
			    </div>
			 </div>
			 <div class="form-group row">
			    <label for="cuser_password" class="col-sm-4 control-label"><?php _e('Retype your password','noo')?></label>
			    <div class="col-sm-8">
			      <input type="password" id="cuser_password" required value="" name="cuser_password"  class="form-control" placeholder="<?php echo esc_attr__('Repeat password','noo')?>">
			    </div>
			 </div>
			 <?php do_action( 'noo_register_form' ); ?>
			 <div class="form-actions row">
				 <div class="col-md-8 col-md-offset-4">
				 		<?php if( $terms_page = self::get_setting('terms_page', '') ) : ?>
				 	  <div class="checkbox account-reg-term">
				 	  	<div class="form-control-flat"><label class="checkbox"><input type="checkbox"  title="<?php esc_attr_e('Please agree with the term','noo')?>" id="account_reg_term"><i></i> <?php _e('I agree with the','noo')?> <a href="<?php echo esc_url(apply_filters('noo_term_url', get_permalink( $terms_page )));?>"><?php _e('Terms of use','noo')?></a></label></div>
					  </div>
					<?php endif; ?>
				 	 <button type="submit" class="btn btn-primary"><?php echo esc_html($submit_label)?></button>
				 </div>
			 </div>
			 <?php do_action( 'noo_register_form_end' ); ?>
		</form>
		<?php
	}
	
	public static function lost_password_action(){
		global $wpdb, $wp_hasher;
	
		if(is_user_logged_in())
			return;
	
		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) )
			return;
	
		if ( empty( $_POST[ 'action' ] ) || 'lost_password' !== $_POST[ 'action' ] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'lost-password' ) )
			return;
	
		if ( empty( $_POST['user_login'] ) ) {
			noo_message_add( __( 'Enter a username or e-mail address.', 'noo' ), 'error' );
			return false;
	
		} else {
			// Check on username first, as customers can use emails as usernames.
			$login = trim( $_POST['user_login'] );
			$user_data = get_user_by( 'login', $login );
		}
	
		// If no user found, check if it login is email and lookup user based on email.
		if ( ! $user_data && is_email( $_POST['user_login'] ) ) {
			$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
		}
	
		do_action( 'lostpassword_post' );
	
		if ( ! $user_data ) {
			noo_message_add( __( 'Invalid username or e-mail.', 'noo' ), 'error' );
			return false;
		}
	
		if ( is_multisite() && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
			noo_message_add( __( 'Invalid username or e-mail.', 'noo' ), 'error' );
			return false;
		}
	
		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
	
		do_action( 'retrieve_password', $user_login );
	
		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );
	
		if ( ! $allow ) {
	
			noo_message_add( __( 'Password reset is not allowed for this user', 'noo' ), 'error' );
	
			return false;
	
		} elseif ( is_wp_error( $allow ) ) {
	
			noo_message_add( $allow->get_error_message(), 'error' );
	
			return false;
		}
	
		$key = wp_generate_password( 20, false );
	
		do_action( 'retrieve_password_key', $user_login, $key );
	
		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
	
		$hashed = $wp_hasher->HashPassword( $key );
	
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
	
		// Send email notification
		$message = __('Someone requested that the password be reset for the following account:', 'noo') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s', 'noo'), $user_login) . "\r\n\r\n";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'noo') . "\r\n\r\n";
		$message .= __('To reset your password, visit the following address:', 'noo') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
	
		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			/*
			 * The blogname option is escaped with esc_html on the way into the database
		* in sanitize_option we want to reverse this for the plain text arena of emails.
		*/
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
		$title = sprintf( __('[%s] Password Reset', 'noo'), $blogname );
	
		/**
		 * Filter the subject of the password reset email.
		 *
		 * @since 2.8.0
		 *
		 * @param string $title Default email title.
		*/
		$title = apply_filters( 'retrieve_password_title', $title );
		/**
		 * Filter the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 *
		 * @param string $message Default mail message.
		 * @param string $key     The activation key.
		*/
		$message = apply_filters( 'retrieve_password_message', $message, $key );
	
	
		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
			noo_message_add( __( 'The e-mail could not be sent', 'noo' ), 'error' );
		} else {
			noo_message_add( __( 'Check your e-mail for the confirmation link.', 'noo' ) );
		}
		return true;
	}
	
	public static  function ajax_login(){
		check_ajax_referer('noo-ajax-login','security');
		$info = array();
		$info['user_login'] = $_POST['log'];
		$info['user_password'] = $_POST['pwd'];
		$info['remember'] = (isset( $_POST['remember'] ) && $_POST['remember'] === true) ? true : false ;
		$info = apply_filters('noo_ajax_login_info', $info);
			
		$user_signon = wp_signon( $info, is_ssl() );
		if ( is_wp_error( $user_signon ) ){
			$error_msg = $user_signon->get_error_message();
			wp_send_json(array( 'loggedin' => false, 'message' => '<span class="error-response">' . $error_msg . '</span>' ));
		} else {
			$redirecturl = ( isset( $_POST['redirect_to'] ) && !empty($_POST['redirect_to']) ) ? $_POST['redirect_to'] : home_url('/');
			$redirecturl = apply_filters( 'login_redirect', $redirecturl, $redirecturl, $user_signon ); // enable redirect from some plugin
			$redirecturl = apply_filters( 'noo_login_redirect', add_query_arg( array( 'logged_in' => 1 ), $redirecturl ), '', $user_signon );
			wp_send_json(array('loggedin'=>true, 'redirecturl' => esc_url( $redirecturl ), 'message'=> '<span class="success-response">' . __( 'Login successful, redirecting...','noo' ) . '</span>' ));
		}
		die;
	}
	
	public static  function ajax_login_priv(){
		$link = "javascript:window.location.reload();return false;";
		wp_send_json(array('loggedin'=>false, 'message'=> sprintf(__('You are already logged in. Please <a href="#" onclick="%s">refresh</a> page','noo'),$link)));
		die();
	}
	
	public static function register_new_user( $user_login, $user_email, $user_password='', $cuser_password='') {
		$errors = new WP_Error();
		$sanitized_user_login = sanitize_user( $user_login );
		$user_email = apply_filters( 'user_registration_email', $user_email );
	
		// Check the username was sanitized
		if ( $sanitized_user_login == '' ) {
			$errors->add( 'empty_username', __( 'Please enter a username.', 'noo' ) );
		} elseif ( ! validate_username( $user_login ) ) {
			$errors->add( 'invalid_username', __( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'noo' ) );
			$sanitized_user_login = '';
		} elseif ( username_exists( $sanitized_user_login ) ) {
			$errors->add( 'username_exists', __( 'This username is already registered. Please choose another one.', 'noo' ) );
		}
	
		// Check the email address
		if ( $user_email == '' ) {
			$errors->add( 'empty_email', __( 'Please type your email address.', 'noo' ) );
		} elseif ( ! is_email( $user_email ) ) {
			$errors->add( 'invalid_email', __( 'The email address isn\'t correct.', 'noo' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			$errors->add( 'email_exists', __( 'This email is already registered, please choose another one.', 'noo' ) );
		}
		//Check the password
	
		if(empty($user_password)){
			$user_password = wp_generate_password( 12, false );
		}else{
			if(strlen($user_password) < 6){
				$errors->add( 'minlength_password', __( 'Password must be 6 character long.', 'noo' ) );
			}elseif (empty($cuser_password)){
				$errors->add( 'not_cpassword', __( 'Not see password confirmation field.', 'noo' ) );
			}elseif ($user_password != $cuser_password){
				$errors->add( 'unequal_password', __( 'Passwords do not match.', 'noo' ) );
			}
		}
	
		$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
	
		if ( $errors->get_error_code() )
			return $errors;
	
		$user_pass = $user_password;
		$new_user = array(
			'user_login' => $sanitized_user_login,
			'user_pass'  => $user_pass,
			'user_email' => $user_email,
		);
		$user_id = wp_insert_user( apply_filters( 'noo_create_user_data', $new_user ) );
		//$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	
		if ( ! $user_id ) {
			$errors->add( 'registerfail', __( 'Couldn\'t register you... please contact the site administrator', 'noo' ) );
			return $errors;
		}
	
		update_user_option( $user_id, 'default_password_nag', true, true ); // Set up the Password change nag.
	
		$user = get_userdata( $user_id );
	
		if(!empty($user_password)){
			wp_new_user_notification( $user_id );
				
			$data_login['user_login']             = $user->user_login;
			$data_login['user_password']          = $user_password;
			$user_login                           = wp_signon( $data_login, is_ssl() );
		}
	
		add_filter( 'wp_mail_content_type',array(__CLASS__,'set_html_content_type' ),100);

		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$headers = array();
		$from = self::do_not_reply_address();
		$headers[] = 'From: ' . $blogname . ' ' . $from;

		// user email
		$subject = sprintf(__('Welcome to [%1$s]','noo'),$blogname);
		$to = $user->email;
		
		$message = __('Hi %1$s,<br/><br/>
You\'ve just successfully registered an account on %2$s.<br/>
Start reading now! %3$s
<br/><br/>
Best regards,<br/>
%4$s','noo');
		@wp_mail($to, $subject, sprintf($message,$user->display_name,$blogname,home_url(),$blogname),$headers);

		remove_filter( 'wp_mail_content_type',array(__CLASS__,'set_html_content_type' ),100);
	
		//wp_set_auth_cookie($user_id);
		return $user_id;
	}
	
	public static  function ajax_register(){
		if( !check_ajax_referer('noo-ajax-register', 'security', false) ) {
			$result = array(
				'success' => false,
				'message' => '<span class="error-response">'.__( 'Your session is expired or you submitted an invalid form.', 'noo' ).'</span>',
			);
		}
		if(get_option( 'users_can_register' )){
			$user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
			$user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
			$user_password  = isset($_POST['user_password']) ? $_POST['user_password'] : '';
			$cuser_password = isset($_POST['cuser_password']) ? $_POST['cuser_password'] : '';
			$errors = self::register_new_user($user_login, $user_email,$user_password,$cuser_password);
			$result = array();
			if ( is_wp_error( $errors ) ) {
				$result = array(
					'success' => false,
					'message'   => '<span class="error-response">'.$errors->get_error_message().'</span>',
				);
				
			} else {
				$result = array(
					'success'     => true,
					'message'	=> '<span class="success-response">'.__( 'Registration complete.', 'noo' ).'</span>',
					'redirecturl'=>apply_filters('noo_register_redirect', home_url('/')),
				);
			}
		}else {
			$result = array(
				'success' => false,
				'message'   =>__( 'Not allow register in site.', 'noo' ),
			);
		}
		wp_send_json($result);
	}

	public static function set_html_content_type() {
		return 'text/html';
	}

	public static function do_not_reply_address(){
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) === 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		return apply_filters( 'do_not_reply_address', 'noreply@' . $sitename );
	}
}
new Noo_Member();
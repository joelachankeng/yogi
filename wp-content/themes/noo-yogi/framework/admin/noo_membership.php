<?php

class WC_Product_Membership_Package extends WC_Product {
	
	public function __construct( $product ) {
		$this->product_type = 'membership_package';
		parent::__construct( $product );
	}
	
	public function is_purchasable() {
		return true;
	}
	
	public function is_sold_individually() {
		return true;
	}
	
	public function is_virtual() {
		return true;
	}

	public function is_downloadable()
	{
		return true;
	}

	public function has_file($download_id = '')
	{
		return false;
	}
	
	public function get_sub_title(){
		$value = get_post_meta( $this->id, '_package_sub_title', true );
		if ( ! empty( $value ) ) {
				return $value;
		}
		return '';
	}
	public function get_duration(){
		return absint($this->package_duration);
	}
	public function get_featureds(){
		return $value = get_post_meta( $this->id, '_package_featureds', true );
	}
	public function add_to_cart_url() {
		$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}
	public function is_package_single(){
		return ($this->package_single === 'yes') ? true : false;
	}
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Sign up now', 'noo' ) : __( 'Read More', 'noo' );
		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}
	
	protected function format_price( $price, $args = array() ) {
		extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
			'ex_tax_label'       => false,
			'currency'           => '',
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => 0,
			'price_format'       => get_woocommerce_price_format()
		) ) ) );
	
		$negative        = $price < 0;
		$price           = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
		if ( get_theme_mod('noo_woocommerce_membership_price_rounding', true) ) {
			$price           = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
		}
	
		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}
	
		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<i class="currency">'.get_woocommerce_currency_symbol( $currency ).'</i>', $price );
		$return          = '<span class="amount">' . $formatted_price . '</span>';
	
		if ( $ex_tax_label && wc_tax_enabled() ) {
			$return .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
		}
	
		return apply_filters( 'wc_price', $return, $price, $args );
	}
	
	public function get_price_html( $price = '' ) {
	
		$display_price         = wc_get_price_to_display($this);
		$display_regular_price = wc_get_price_to_display($this, $this->get_regular_price() );
	
		if ( $this->get_price() > 0 ) {
	
			if ( $this->is_on_sale() && $this->get_regular_price() ) {
	
				$price .= $this->get_price_html_from_to( $display_regular_price, $display_price ) . $this->get_price_suffix();
	
				$price = apply_filters( 'woocommerce_sale_price_html', $price, $this );
	
			} else {
	
				$price .= $this->format_price( $display_price ) . $this->get_price_suffix();
	
				$price = apply_filters( 'woocommerce_price_html', $price, $this );
	
			}
	
		} elseif ( $this->get_price() === '' ) {
	
			$price = apply_filters( 'woocommerce_empty_price_html', '', $this );
	
		} elseif ( $this->get_price() == 0 ) {
	
			if ( $this->is_on_sale() && $this->get_regular_price() ) {
	
				$price .= $this->get_price_html_from_to( $display_regular_price, __( 'Free!', 'noo' ) );
	
				$price = apply_filters( 'woocommerce_free_sale_price_html', $price, $this );
	
			} else {
	
				$price = __( 'Free!', 'noo' );
	
				$price = apply_filters( 'woocommerce_free_price_html', $price, $this );
	
			}
		}
	
		return apply_filters( 'woocommerce_get_price_html', $price, $this );
	}
	
	public function get_price_html_from_to( $from, $to ) {
		$price = '<del>' . ( ( is_numeric( $from ) ) ? $this->format_price( $from ) : $from ) . '</del> <ins>' . ( ( is_numeric( $to ) ) ? $this->format_price( $to ) : $to ) . '</ins>';
	
		return apply_filters( 'woocommerce_get_price_html_from_to', $price, $from, $to, $this );
	}
	
}

class Noo_Membership {
	
	public function __construct(){
		add_action('init', array(&$this,'init'));
		add_action('woocommerce_add_to_cart_handler_membership_package', array(&$this,'woocommerce_add_to_cart_handler'),100);
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_paid' ) );
		//add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
		if(is_admin()){
			add_action('admin_init', array(&$this,'admin_init'));
			//add_filter( 'request', array( $this, 'request_query' ) );
		}else{
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ),100);
		}
	}
	
	
	public function restrict_manage_posts(){
		global $typenow, $wp_query;
		if(function_exists('wc_get_order_types')){
			if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
				?>
				
				<?php
			}
		}
	}
	
	public function request_query($vars){
		global $typenow, $wp_query, $wp_post_statuses;
		if(!function_exists('wc_get_order_types')){
			return $vars;
		}
		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			
		}
		return $vars;
	}
	
	public function pre_get_posts($q){
		global $noo_view_membership_package;
		
		if(!defined('WOOCOMMERCE_VERSION'))
			return ;
		
		if(empty($noo_view_membership_package) && $this->is_noo_property_query($q))
		{
			$tax_query = array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'membership_package' ),
					'operator' => 'NOT IN',
			);
			$q->tax_query->queries[] = $tax_query;
			$q->query_vars['tax_query'] = $q->tax_query->queries;
		}
		$noo_view_membership_package = false;
		
	}
	protected function is_noo_property_query($query = null){
		if( empty( $query ) ) return false;
		if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'product' )
			return true;
		if(is_post_type_archive( 'product' ) || is_product_taxonomy() )
			return true;
		return false;
		
	}
	
	public function init(){
		if(!defined('WOOCOMMERCE_VERSION'))
			return ;
		
		add_action( 'after_switch_theme', array(&$this,'switch_theme_hook'), 10 , 2);
		
		if(is_admin()){
			add_filter( 'product_type_selector' , array(&$this, 'product_type_selector'));
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'membership_package_product_data' ) );
			add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ) );
		}
	}
	
	public function order_paid($order_id){
		if(!is_user_logged_in()){
			return;
		}
		$order = new WC_Order( $order_id );
		if ( get_post_meta( $order_id, 'membership_package_processed', true ) ) {
			return;
		}
		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );
		
			if ($product->is_type( 'membership_package' ) && $order->customer_user ) {
				$user_id = $order->customer_user;
				
				$package_data = array(
					'order_id'   		=> $order_id,
					'product_id'   		=> $product->get_id(),
					'created'      		=> current_time('mysql'),
					'package_single' 	=> ($product->is_package_single() ? 'yes' : 'no'),
					'duration' 			=> absint($product->get_duration()),
				);
				if(update_user_meta( $user_id,'_membership_package', $package_data )){
					
				}
			}
		}
		update_post_meta( $order_id, '_membership_package_processed', true );
		
	}
	
	public function woocommerce_add_to_cart_handler(){
		global $woocommerce;
		$product_id          = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );
		$product 			= wc_get_product( absint($product_id) );
		$quantity 			= empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		if ( $product->is_type( 'membership_package' ) && $passed_validation ) {
			// Add the product to the cart
			// $woocommerce->cart->empty_cart();
			foreach ($woocommerce->cart->cart_contents as $k => $v) {
				// Remove membership if available
				if ( 'membership_package' === $v['data']->product_type ) {
					$woocommerce->cart->remove_cart_item( $k );
				}
			}
			if (  $woocommerce->cart->add_to_cart( $product_id, $quantity ) ) {
				//woocommerce_add_to_cart_message( $product_id );
				wp_safe_redirect(wc_get_checkout_url());
				die;
			}
		}
		
	}
	
	public function admin_init(){
		
	}
	
	public function switch_theme_hook($newname, $newtheme){
		if(defined('WOOCOMMERCE_VERSION')){
			if ( ! get_term_by( 'slug', sanitize_title( 'membership_package' ), 'product_type' ) ) {
				wp_insert_term( 'membership_package', 'product_type' );
			}
		}
	}
	
	public function product_type_selector($types){
		$types[ 'membership_package' ] = __( 'Membership Package', 'noo' );
		return $types;
	}
	
	public function membership_package_product_data(){
		global $post;
		?>
		<div class="options_group show_if_membership_package">
			<?php 
			woocommerce_wp_checkbox( 
				array( 
					'id' => '_featured', 
					'label' => __( 'Membership package is featured', 'noo' ), 
					'description' => __( 'Membership package is featured.', 'noo' ), 
					'value' => get_post_meta($post->ID,'_featured',true), 
					'placeholder' => __( 'Membership package is featured.', 'noo' ), 
					'type' => 'text', 
					'desc_tip' => true,
				) );
			woocommerce_wp_checkbox(
				array(
				'id' => '_package_single',
				'label' => __( 'Membership package is single', 'noo' ),
				'description' => __( 'Membership package is single.', 'noo' ),
				'value' => get_post_meta($post->ID,'_package_single',true),
				'placeholder' => __( 'Membership package is single.', 'noo' ),
				'type' => 'text',
				'desc_tip' => true,
			) );
			woocommerce_wp_text_input(
				array(
					'id' => '_package_duration',
					'label' => __( 'Membership package duration', 'noo' ),
					'description' => __( 'Membership package duration.', 'noo' ),
					'value' => max(absint(get_post_meta($post->ID,'_package_duration',true)),1),
					'placeholder' => __( 'Membership package duration.', 'noo' ),
					'type' => 'number',
					'desc_tip' => true,
				) );
			woocommerce_wp_text_input( 
				array( 
					'id' => '_package_sub_title', 
					'label' => __( 'Membership package sub title', 'noo' ), 
					'description' => __( 'Membership package sub title.', 'noo' ), 
					'value' => get_post_meta($post->ID,'_package_sub_title',true), 
					'placeholder' => __( 'Membership package sub title.', 'noo' ), 
					'type' => 'text', 
					'desc_tip' => true,
				) );
			woocommerce_wp_textarea_input( 
				array( 
					'id' => '_package_featureds', 
					'label' => __( 'Membership package featured', 'noo' ), 
					'description' => __( 'Membership package featured. Divide values with linebreaks (Enter).', 'noo' ), 
					'value' => get_post_meta($post->ID,'_package_featureds',true), 
					'placeholder' => __( 'Membership package featured.', 'noo' ), 
					'type' => 'textarea', 
					'desc_tip' => true,
				) );
			?>
		
			<script type="text/javascript">
				jQuery('.pricing').addClass( 'show_if_membership_package' );
			</script>
			<?php 
			do_action('noo_membership_package_data')
			?>
		</div>
		<?php
	}
	
	public function save_product_data($post_id){
		// Save meta
		$fields = array(
			'_package_sub_title'  => '',
			'_package_featureds' => '',
			'_featured'=>'',
			'_package_duration'=>'',
			'_package_single'=>'',
		);
		foreach ( $fields as $key => $value ) {
			$value = ! empty( $_POST[ $key ] ) ? $_POST[ $key ] : '';
			update_post_meta( $post_id, $key, ($value) );
		}
		
		do_action('noo_membership_package_save_data');
	}
}
new Noo_Membership();
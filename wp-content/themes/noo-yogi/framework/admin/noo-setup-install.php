<?php
/**
 * Display notices in admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------
 * Create functions notice_html_install
 * ------------------------------------------------------- */
class Noo_Notice_Install {

	private $product_id = 'noo-yogi';

	private $install_option_group = 'noo_option_install_group';

	private $install_option_name = 'noo_option_install_name';

	private $install_section_id = 'noo_option_section_id';

	private $option_metabox = array();

	public function __construct() {

		if ( current_user_can( 'manage_options' ) ) {

			// add_action( 'admin_notices', array( $this, 'notice_html_install' ) );
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );

			// -- Call setting fields
				add_action('admin_init', array( $this, 'noo_settings_fields'));
			// -- set ajax
				add_action( 'wp_ajax_noo_setup', array( $this, 'noo_setup' ) );
			if ( isset( $_GET['page'] ) == 'noo-setup' ) :

				add_action( 'admin_head', array( $this, 'load_enqueue_style_setup' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue_script_setup' ) );

			endif;

            require NOO_FRAMEWORK_ADMIN . '/import-demo/noo-import.php';

		}
	}

	public function load_enqueue_style_setup() {

		?>
		<style type="text/css" media="screen">
			mark.yes {
    			background-color: transparent;
    			color: #7ad03a;
			}
			mark.error {
			    background-color: transparent;
			    color: #a00;
			}
			mark.error span:hover {
				cursor: pointer;
			}
			table.widefat {
				margin-bottom: 20px;
			}
		</style>
		<?php

	}

	public function load_enqueue_script_setup() {

		if ( isset($_GET['page']) &&  $_GET['page'] == 'noo-setup' ) : 
			wp_register_script( 'setup-install', NOO_FRAMEWORK_URI . '/admin/assets/js/noo.setup.install.js', array( 'jquery', 'jquery-ui-tooltip' ), null, true );
			wp_enqueue_script('setup-install');

			wp_register_script( 'setup-install-demo', NOO_FRAMEWORK_URI . '/admin/assets/js/noo.setup.install.demo.js' );
			wp_enqueue_script('setup-install-demo');

			wp_register_style( 'jquery-ui', NOO_FRAMEWORK_URI . '/admin/assets/css/jquery-ui.tooltip.css' );
			wp_register_style( 'setup-style', NOO_FRAMEWORK_URI . '/admin/assets/css/noo-setup.css', array( 'jquery-ui' ) );
			wp_enqueue_style( 'setup-style' );


			wp_localize_script( 'setup-install', 'nooSetup', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			wp_localize_script( 'setup-install-demo', 'nooSetupDemo', 
				array( 
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'notice'   => esc_html__( 'Do you want to continue this action?', 'noo' ),
					'warning'  => esc_html__( 'Please waiting, not exit page.', 'noo' ),
					'ajax_nonce' => wp_create_nonce( 'install-demo' ),
					'img_ajax_load' => NOO_FRAMEWORK_URI . '/admin/assets/images/ajax-loader.gif'
				) 
			);
		endif;
	}
	
	// -- Notice html
	
	public function notice_html_install() {
		if ( !$this->noo_get_option('disable_notice_install') && !isset($_GET['page']) == 'noo-setup' ) :

		?>
		<div id="message" class="updated notice is-dismissible">
			<p><strong><?php echo sprintf( __('Welcome to %s,', 'noo'), wp_get_theme($this->product_id)->get('Name') ); ?></strong></p>
			<p><?php _e('If it is the first time you install this theme, you should go and check the basic setting.', 'noo'); ?></p>
			<p class="submit">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=noo-setup' ) ); ?>" class="button-primary">
					<?php _e( 'Go to Quick Setup', 'noo' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=noo-setup&action=skip' ) ); ?>" class="button">
					<?php _e( 'Skip Setup', 'noo' ); ?>
				</a>
			</p>
		</div>
		<?php

		endif;
	}

	// -- Create page
		public function admin_menus() {
			add_menu_page ( 
				__( 'Noo Settings', 'noo' ), 
				__( 'Noo Settings', 'noo' ), 
				'manage_options', 
				'noo-setup', 
				array( $this, 'noo_page_setup' ),
				'dashicons-admin-generic'
			);
		}

	// -- Processsing setup
		public function noo_setup() {
			global $wpdb;

			// ----
			
				$page['title'] = $_POST['title'];
				$page['content'] = isset( $_POST['content'] ) && !empty( $_POST['content'] ) ? $_POST['content'] : '';
				$page['page_template'] = isset( $_POST['page_template'] ) && !empty( $_POST['page_template'] ) ? $_POST['page_template'] : 'default';
				$page['setting_group'] = isset( $_POST['setting_group'] ) && !empty( $_POST['setting_group'] ) ? $_POST['setting_group'] : '';
				$page['setting_key'] = isset( $_POST['setting_key'] ) && !empty( $_POST['setting_key'] ) ? $_POST['setting_key'] : '';

			// -----

				$post_data = array(
					'post_title' => $page['title'],
					'post_content'  => $page['content'],
					'post_status' => 'publish',
					'post_type'   => 'page',
					'post_author' => get_current_user_id()
				);

				$id_page = wp_insert_post( $post_data ); // -- Insert post
			
				if( !is_wp_error( $id_page ) ) {
					update_post_meta($id_page, '_wp_page_template',  $page['page_template']); //- Set page template
					if( !empty( $page['setting_key'] ) ) {
						if( !empty( $page['setting_group'] ) ) {
							$setting_group = get_option( $page['setting_group'] );
							$setting_group[ $page['setting_key'] ] = $id_page;
							update_option( $page['setting_group'], $setting_group );
						} else {
							$setting_value = get_option( $page['setting_key'] );
							update_option( $page['setting_key'], $id_page );
						}
					}

					$post = get_post($id_page);
					echo json_encode( array( 'id' => __('Done', 'noo'), 'slug' => $post->post_name ) );
				}
				exit;
		}

	// -- Add option
		public function noo_add_option( $name ) {
			$options = array_merge( get_option( 'noo_setup', array() ), $name );
			update_option( 'noo_setup', $options );
		}

	// -- Get option
		public function noo_get_option( $name ) {
			$options = get_option( 'noo_setup' );
			return $options[$name];
		}

	// -- Tab menu
		public function noo_tab_menu( $current = 'general' ) {

			$tabs = array(
		        'general'   => __("Quick Setup", 'noo'), 
		        'import_demo'  => __("Import Demo", 'noo')
		    );
		    $html =  '<h2 class="nav-tab-wrapper">';
		    foreach( $tabs as $tab => $name ) :
		        
		        $class = ($tab == $current) ? 'nav-tab-active' : '';
		        $html .=  '<a class="nav-tab ' . $class . '" href="?page=noo-setup&tab=' . $tab . '">' . $name . '</a>';
		    
		    endforeach;
		    $html .= '</h2>';
		    echo $html;

		} 

	// -- Page setup
		public function noo_page_setup() {
		    
		    if ( isset( $_GET['page'] ) == 'noo-setup' ) :

		    	if ( isset( $_GET['tab'] ) ) :

		    		$tab = $_GET['tab'];

		    	else :

		    		$tab = 'general';

		    	endif;

		    	$this->noo_tab_menu( $tab );

		    	switch ( $tab ) {
		    		case 'general':
		    			$this->noo_general_options();
		    			break;
		    		case 'import_demo':
		    			$this->noo_import_demo_options();
		    			break;
		    		default:
		    			$this->noo_general_options();
		    			break;
		    	}

		    endif;
		}

	// -- OPTION
	
		// -- General options
		public function noo_general_options() {
			if ( isset( $_GET['action'] ) == 'skip' ) :
				$this->noo_add_option( array( 'disable_notice_install' => true ) );
				wp_redirect( admin_url() );
				die;
			endif;
			if ( isset( $_POST['license_key'] ) && isset( $_POST['email'] ) ) :
				$value_license = array(
					'license_key' => $_POST['license_key'],
					'email' => $_POST['email'],
				);
				update_option( $this->product_id . '-license-settings', $value_license );

				echo '
					<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
						<p><strong>Settings saved.</strong></p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text">Dismiss this notice.</span>
						</button>
					</div>';

				$this->noo_add_option( array( 'disable_notice_install' => true ) );

			endif;
			echo '<div class="wrap">';
	        echo '<form method="post">';

			$this->noo_option_client();
			// $this->noo_option_page();

			echo '<p>';
			submit_button('', 'primary', '', false);
			echo '&nbsp;&nbsp;';
			echo '<a href="' . esc_url( admin_url( 'admin.php?page=noo-setup&action=skip' ) ) . '" class="button">';
			_e( 'Cancel', 'noo' );
			echo '</a>';
			echo '</p>';

			echo '</form>';
			echo '</div>';

		}

			// -- Option client
			public function noo_option_client() {
				$settings_field_name = $this->get_product_field_name();
				$settings_group_id = $this->product_id . '-license-settings-group';
            	$options = get_option( $settings_field_name );
            	if( empty( $options ) ) $options = array( 'license_key' => '', 'email' => '' );
				?>
					<table class="widefat" cellspacing="0" id="client">
						<thead>
							<tr>
								<th colspan="3" data-export-label="<?php _e( 'License', 'noo' ); ?>">
									<label>
										<?php _e( 'License', 'noo' ); ?>
									</label>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td data-export-label="<?php _e( 'License key', 'noo' ); ?>"><?php _e( 'License key:', 'noo' ); ?></td>
								<td class="help">
									<a href="#" title="<?php _e( 'The purchase code you get from ThemeForest', 'noo' ); ?>" class="help_tip"><span class="dashicons dashicons-editor-help"></span></a>
								</td>
								<td>
									<input type='text' name='license_key' value='<?php echo $options['license_key']; ?>' class='regular-text'>
									<input type='hidden' name='email' value='<?php echo str_replace( 'http://', '', home_url( )); ?>' class='regular-text'>
									<span><?php echo sprintf( __( '<a target="_blank" href="%s">How to get License key?</a>', 'noo' ), 'http://support.nootheme.com/wp-content/uploads/2015/07/HowToGetPurchaseCode.png' ) ?></span>
								</td>
							</tr>
						</tbody>
					</table>
				<?php
			}

			// -- Option page
			public function noo_option_page() {
			}

		// -- Tools options
		public function noo_import_demo_options() {

			$list_demo = array(
				array(
					'name' => esc_html__( 'Yogi Demo', 'noo' ),
					'img'  => NOO_FRAMEWORK_ADMIN_URI . '/import-demo/data/demo-1/screenshot.png',
					'file' => 'demo-1'
				)
			);

			?>
				<table class="widefat" cellspacing="0" style="width: 99%;">
					<thead>
						<tr>
							<th colspan="1" data-export-label="<?php esc_html_e( 'Settings', 'noo' ); ?>">
								<label class="hide_main">
									<?php esc_html_e( 'Settings', 'noo' ); ?>
								</label>
							</th>
						</tr>
					</thead>
					<tbody id="noo_main_select">
						<tr>
							<td>
								<input type='checkbox' data-id='import_post' id='import_post' value='1' checked /> <?php esc_html_e( 'Import Post', 'noo' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<input type='checkbox' data-id='import_nav' id='import_nav' value='1' checked /> <?php esc_html_e( 'Import Nav Menu', 'noo' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<input type='checkbox' data-id='import_comment' id='import_comment' value='1' checked /> <?php esc_html_e( 'Import Comment', 'noo' ); ?>
							</td>
						</tr>
					</tbody>
				</table>
				<div id="noo_tools">

					<!-- [ MAIN ] -->
					<div id="process_import"></div>
					<div class="theme-browser rendered" style="margin-top: 20px;">
						<div class="themes">
							<?php foreach ($list_demo as $id => $demo) : ?>
								<div class="theme" tabindex="0">
									<div class="theme-screenshot">
										<img src="<?php echo esc_attr( $demo['img'] ); ?>" alt="" />
									</div>
									<span class="more-details" id="install_<?php echo esc_attr( $demo['file'] ); ?>"><?php esc_html_e( 'Install ' .$demo['name'], 'noo' ); ?></span>
									<h3 class="theme-name" id="noo-<?php echo esc_attr( $demo['file'] ); ?>-name"><?php echo esc_html( $demo['name'] ); ?></h3>
                            		<div class="noo-load-ajax"></div>
									<div class="theme-actions">
										<button class="install-demo button button-primary activate" 
											data-name="<?php echo esc_attr( $demo['file'] ); ?>"
											data-import-post="true"
											data-import-nav="true"
											data-import-comment="true"
										><?php esc_html_e( 'Install Demo', 'noo' ); ?></button>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<br class="clear">
					</div>
				
				</div><!-- /#noo_tools -->

			<?php

		}

	// -- Setting fields
		
		public function get_product_field_name() {
            return $this->product_id . '-license-settings';
        }
		
		public function noo_settings_fields() {

			register_setting( $this->install_option_group, $this->install_option_name );

		    add_settings_section( 
		    	$this->install_section_id, 
		    	'Title Section', 
		    	'',
		    	$this->install_option_group 
		    );
		    add_settings_field( 
		    	$this->product_id . '-page',
		    	'Page', 
		    	array( $this, 'render_settings_section' ),
		    	$this->install_option_group,
		    	$this->install_section_id 
		    );


		}

		/**
         * Renders the description for the settings section.
         */
        public function render_settings_section() {
        }

	// -- Options fields
		public function noo_options_fields() {

			$this->option_metabox[] = array(

				'id' => 'general_options',
				'title' => _e( 'General Options', 'noo' )

			);

			return $this->option_metabox;			

		}

}
new Noo_Notice_Install;

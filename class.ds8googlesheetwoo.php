<?php

if (!defined('ABSPATH')) exit;

class DS8GoogleSheetWOO {
  
        private static $instance = null;
        private $data = array();
        
        /**
         * Function constructor
         */
        function __construct() {
            $this->load_dependencies();
            $this->define_admin_hooks();
            
            //add_action('widgets_init', array($this, 'ds8_googlesheetwoo_register_widget'));
            
            add_action('wp_enqueue_scripts', array($this, 'ds8_googlesheetwoo_javascript'), 10);
            add_shortcode('ds8googlesheetwoo', array($this, 'ds8googlesheetwoo_shortcode_fn'));
            add_action('event_check_google_sheet',  array($this,'connect_to_google_sheet'));
            add_action('admin_init',array($this,'manual_sync_google'));
            add_filter( 'cron_schedules', function ( $schedules ) {
                $schedules['per_minute'] = array(
                    'interval' => 60,
                    'display' => __( 'One Minute' )
                );
                return $schedules;
             } );
        }
        
        public function manual_sync_google(){
          global $pagenow;
          if (!is_admin())
                  return;
            
          if ($pagenow == 'options-general.php' && !empty($_POST['conectar']) && $_POST['conectar'] === "Sincronizar") {
            $this->connect_to_google_sheet();
          }
          
        }
        
        public function connect_to_google_sheet() {
          
          $logger = wc_get_logger();
          
          include_once __DIR__ . '/vendor/autoload.php';
          include_once __DIR__ . "/includes/base.php";

          /*************************************************
          * Ensure you've downloaded your oauth credentials
          ************************************************/
          if (!$oauth_credentials = getOAuthCredentialsFile()) {
             echo missingOAuth2CredentialsWarning();
             return;
          }

          /************************************************
          * The redirect URI is to the current page, e.g:
          * http://localhost:8080/large-file-upload.php
          ************************************************/
          $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

          $developer_key = get_option('ds8_google_api_page');
          $spreadsheetId = get_option('ds8_google_sheet_id_page');

          $client = new Google\Client();
          $client->setAuthConfig($oauth_credentials);
          //$client->setRedirectUri($redirect_uri);
          $client->setApplicationName("Client_Library_Examples");
          $client->setDeveloperKey($developer_key);

          $drive = new Google\Service\Drive($client);
          $file = $drive->files->get($spreadsheetId);
          $fecha = $file->getModifiedTime();

          $service = new Google\Service\Sheets($client);
          error_log('Iniciada sincronización con Google Sheet API');
          $logger->info('Iniciada sincronización con Google Sheet API', array( 'source' => 'sync-google' ) );
          // Prints the names and majors of students in a sample spreadsheet:
          // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
          $range = 'A2:D';
          $response = $service->spreadsheets_values->get($spreadsheetId, $range);
          $values = $response->getValues();

          error_log('Google Service API :: Total productos a sincronizar='.count($values));
          $logger->info('Google Service API :: Total productos a sincronizar='.count($values), array( 'source' => 'sync-google' ) );

          if (empty($values)) {
              error_log('No se encontraron datos');
              $logger->info('No se encontraron datos', array( 'source' => 'sync-google' ) );
          } else {
              $data = array();
              foreach ($values as $row) {
                  //printf("%s, %s\n", $row[0], $row[1]);
                  if ( !empty( trim( $row[1] ) ) ){

                      $data[] = array('name' => strtoupper($row[0]),
                                      'sku' => strtoupper($row[1]),
                                      'regular_price' => $row[2],
                                      'price' => (isset($row[3]) ? $row[3] : ''),
                                      'fecha_de_actualizacion' => ''
                              );
                  }
              }

              if (count($data) > 1) :
                foreach ($data as &$row) :
                    $idproduct = wc_get_product_id_by_sku( $row['sku'] );
                    $product = wc_get_product($idproduct);

                    if (!is_bool($product)){
                      if ( /*!empty($product->get_regular_price()) &&*/ !empty($row['regular_price']) ){
                        $product->set_regular_price($row['regular_price']);
                      }
                      if ( /*!empty($product->get_sale_price()) &&*/ !empty($row['price'] && !$product->is_type( 'simple' )) ){
                        $product->set_sale_price($row['price']);
                      }
                      $retorna = $product->save();

                      if ($retorna != 0){
                        error_log('Product SKU:'.$row['sku'].' Actualizado');
                        $logger->info('Product SKU:'.$row['sku'].' Actualizado', array( 'source' => 'sync-google' ) );
                        $row['fecha_de_actualizacion'] = ucfirst(wp_date ("F d Y H:i:s"));
                      }
                    }else{
                        $row['fecha_de_actualizacion'] = 'No se encuentra SKU';
                    }
                endforeach;
                error_log('Finalizada sincronización productos Woocommerce con Google Sheet API.');
                $logger->info('Finalizada sincronización productos Woocommerce con Google Sheet API.', array( 'source' => 'sync-google' ) );
              else:
                error_log('Google Service API :: No hay data para actualizar');
                $logger->info('Google Service API :: No hay data para actualizar', array( 'source' => 'sync-google' ) );
              endif;

              $GLOBALS['data'] = $data;            
          }
        }
        
        /**
        * Singleton pattern
        *
        * @return void
        */
        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }
        
        private function load_dependencies() {
        }
        
        /**
          * Admin hooks
          *
          * @return void
          */
        private function define_admin_hooks() {
        }
        
        public function ds8_googlesheetwoo_register_widget() {
        }
        
        public function ds8googlesheetwoo_shortcode_fn($atts) {
          
          
          echo '</div></div>';
        }
        
        /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0
	 */
	private static function set_locale() {
		load_plugin_textdomain( 'ds8googlesheetwoo', false, plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' );

	}
        
        public static function ds8relatedposts_textdomain( $mofile, $domain ) {
                if ( 'ds8relatedposts' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
                        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
                        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
                }
                return $mofile;
        }
        
        
        /**
	 * Check if plugin is active
	 *
	 * @since    1.0
	 */
	private static function is_plugin_active( $plugin_file ) {
		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

        public function ds8_googlesheetwoo_javascript(){
          
            if (!is_front_page()) {
              wp_enqueue_style('ds8googlesheetwoo-css', plugin_dir_url( __FILE__ ) . 'assets/css/ds8googlesheetwoo.css', array(), DS8GOOGLE_SHEETS_WOO_VERSION);
            }
        }
        
        public static function view( $name, array $args = array() ) {
                $args = apply_filters( 'ds8googlesheetwoo_view_arguments', $args, $name );

                foreach ( $args AS $key => $val ) {
                        $$key = $val;
                }

                load_plugin_textdomain( 'ds8googlesheetwoo' );

                $file = DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . 'views/'. $name . '.php';

                include( $file );
	}
        
        public static function plugin_deactivation( ) {
            wp_clear_scheduled_hook( 'event_check_google_sheet' );
        }

        /**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], DS8GOOGLE_SHEETS_WOO_MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'ds8googlesheetwoo' );
                        
			$message = '<strong>'.sprintf(esc_html__( 'DS8 Google Sheet %s requires WordPress %s or higher.' , 'ds8googlesheetwoo'), DS8GOOGLE_SHEETS_WOO_VERSION, DS8GOOGLE_SHEETS_WOO_MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version.', 'ds8googlesheetwoo'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/ds8googlesheetwoo/download/');

			DS8GoogleSheetWOO::bail_on_activation( $message );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
                        flush_rewrite_rules();
			add_option( 'Activated_DS8GoogleSheetWOO', true );
		}
                if (! wp_next_scheduled ( 'event_check_google_sheet' )) {
                  wp_schedule_event( time() + 60, 'per_minute','event_check_google_sheet' );
                }
	}

        private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
</head>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$ds8googlesheet = plugin_basename( DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . 'ds8google-sheets-woo.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $ds8googlesheet ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

}
<?php

class DS8GoogleSheetWoo_Admin {
  
	private static $initiated = false;
	private static $notices   = array();

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	public static function init_hooks() {
		// The standalone stats page was removed in 3.0 for an all-in-one config and stats page.
		// Redirect any links that might have been bookmarked or in browser history.
		if ( isset( $_GET['page'] ) && 'googlesheet-woo-display' == $_GET['page'] ) {
			wp_safe_redirect( esc_url_raw( self::get_page_url( 'googlesheet' ) ), 301 );
			die;
		}

		self::$initiated = true;
                self::ds8_on_init();

		add_action( 'admin_init', array( 'DS8GoogleSheetWoo_Admin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'DS8GoogleSheetWoo_Admin', 'admin_menu' ), 5 );
		//add_action( 'admin_notices', array( 'DS8GoogleSheetWoo_Admin', 'display_notice' ) );
		//add_action( 'admin_enqueue_scripts', array( 'DS8GoogleSheetWoo_Admin', 'load_resources' ) );
		add_filter( 'plugin_action_links', array( 'DS8GoogleSheetWoo_Admin', 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'ds8google-sheets-woo.php'), array( 'DS8GoogleSheetWoo_Admin', 'admin_plugin_settings_link' ) );
		//add_filter( 'all_plugins', array( 'DS8GoogleSheetWoo_Admin', 'modify_plugin_description' ) );
                
                add_action('admin_notices', array( 'DS8GoogleSheetWoo_Admin','general_admin_notice'));
	}
        
        public static function general_admin_notice(){
            
            if (isset($GLOBALS['upload_result'])){
               echo '<div class="notice notice-success is-dismissible">
                      <p>Se ha cargado exitosamente el archivo</p>
                  </div>';
            }
        }

	public static function admin_init() {
		if ( get_option( 'Activated_GoogleSheetWoo' ) ) {
			delete_option( 'Activated_GoogleSheetWoo' );
			if ( ! headers_sent() ) {
				wp_redirect( add_query_arg( array( 'page' => 'googlesheetwoo-key-config', 'view' => 'start' ), class_exists( 'Jetpack' ) ? admin_url( 'admin.php' ) : admin_url( 'options-general.php' ) ) );
			}
		}
                
                // JLMA - FEATURE 01-09-2022
                if(isset($_POST) && isset($_POST['option_page']) &&  $_POST['option_page'] === 'ds8-settings-group') {
                    update_option('plugin_permalinks_flushed', 0);
                }
                
                register_setting('ds8-settings-group', 'ds8_google_api_page');
                register_setting('ds8-settings-group', 'ds8_google_sheet_id_page');
		load_plugin_textdomain( 'ds8googlesheetwoo' );
	}

	public static function admin_menu() {
			self::load_menu();
	}

	public static function admin_head() {
		if ( !current_user_can( 'manage_options' ) )
			return;
	}
	
	public static function admin_plugin_settings_link( $links ) { 
  		$settings_link = '<a href="'.esc_url( self::get_page_url() ).'">'.__('Settings', 'ds8googlesheetwoo').'</a>';
  		array_unshift( $links, $settings_link ); 
  		return $links; 
	}

	public static function load_menu() {
		
                $hook = add_options_page( __('Google Sheet WOO', 'ds8googlesheetwoo'), __('Google Sheet WOO', 'ds8googlesheetwoo'), 'manage_options', 'googlesheetwoo-key-config', array( 'DS8GoogleSheetWoo_Admin', 'display_page' ) );
	}
        
        // Hook into WordPress init; this function performs report generation when the admin form is submitted
        public static function ds8_on_init() {
                global $pagenow;
                // Check if we are in admin and on the report page
                if (!is_admin())
                        return;
                if ( ! function_exists( 'wp_handle_upload' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                //admin.php when is options
                if ($pagenow == 'options-general.php' && !empty($_POST['uploadds8']) && ($_POST['uploadds8'] === "Upload" || $_POST['uploadds8'] === "Cargar") ) {
                    // Upload file
                    if(isset($_POST['uploadds8'])){
                        if($_FILES['file']['name'] != ''){
                            $uploadedfile = $_FILES['file'];
                            $upload_overrides = array( 'test_form' => false );
                            //$uploaded = wp_handle_upload( $uploadedfile, $upload_overrides );
                            $success = move_uploaded_file($uploadedfile['tmp_name'],DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . $uploadedfile['name']);
                            if ($success === FALSE) { //if (is_wp_error($uploaded)) {
                                //echo "Error uploading file: " . $uploaded->get_error_message();
                                $GLOBALS['upload_result'] = $success;
                            } else {
                                //echo "File upload successful!";
                                //self::get_parsed_excel($uploaded);
                                $GLOBALS['upload_result'] = $success;
                            }
                        }
                    }
                }
        }
        
        public static function display_page() {
          
		if ( ( isset( $_GET['view'] ) && $_GET['view'] == 'start'  ) || $_GET['page'] == 'googlesheetwoo-key-config' ){
                  
                  require_once( DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . 'includes/class.sheetgoogle.php' );
                  $data = isset($GLOBALS['data']) ? $GLOBALS['data'] : array();
                  if (count($data)>1) {
                    
                  }
			//self::display_start_page();
                        //DS8GoogleSheetWOO::view( 'start' );
                        // FEATURE JLMA 29-08-2022
                        $options = array(
                            array("name" => "Developer API Key",
                                "desc" => "Para conectar con Google API",
                                "id" => "ds8_google_api_page",
                                "type" => "text",
                                "std" => "",
                                "class" => "regular-text"
                            ),
                            array("name" => "Identificador de hoja de excel.",
                                "desc" => 'https://docs.google.com/spreadsheets/d/<b style="text-decoration: underline">1WDf0rXzlMptdzydEWZ-gZ4h9RUOCxH6lvRuypaahxXs</b>/edit#gid=0',
                                "id" => "ds8_google_sheet_id_page",
                                "type" => "text",
                                "std" => "",
                                "class" => "regular-text"
                            )
                        );
                        DS8GoogleSheetWOO::view( 'start', array(
                                'front_page_elements' => null,
                                'options' => $options
                        ) );
                }
	}

	public static function load_resources() {
		global $hook_suffix;

		if ( in_array( $hook_suffix, apply_filters( 'googlesheetwoo_admin_page_hook_suffixes', array(
			'index.php', # dashboard
			'post.php',
			'plugins.php',
		) ) ) ) {
			wp_register_style( 'googlesheetwoo.css', plugin_dir_url( __FILE__ ) . '_inc/googlesheetwoo.css', array(), DS8GOOGLE_SHEETS_WOO_VERSION );
			wp_enqueue_style( 'googlesheetwoo.css');

			wp_register_script( 'googlesheetwoo.js', plugin_dir_url( __FILE__ ) . '_inc/googlesheetwoo.js', array('jquery'), DS8GOOGLE_SHEETS_WOO_VERSION );
			wp_enqueue_script( 'googlesheetwoo.js' );
		
			$inline_js = array(
				'comment_author_url_nonce' => wp_create_nonce( 'comment_author_url_nonce' ),
				'strings' => array(
					'Remove this URL' => __( 'Remove this URL' , 'ds8googlesheetwoo'),
					'Removing...'     => __( 'Removing...' , 'ds8googlesheetwoo'),
					'URL removed'     => __( 'URL removed' , 'ds8googlesheetwoo'),
					'(undo)'          => __( '(undo)' , 'ds8googlesheetwoo'),
					'Re-adding...'    => __( 'Re-adding...' , 'ds8googlesheetwoo'),
				)
			);

			if ( isset( $_GET['ds8googlesheetwoo_recheck'] ) && wp_verify_nonce( $_GET['ds8googlesheetwoo_recheck'], 'ds8googlesheetwoo_recheck' ) ) {
				$inline_js['start_recheck'] = true;
			}

			if ( apply_filters( 'ds8googlesheetwoo_enable_mshots', true ) ) {
				$inline_js['enable_mshots'] = true;
			}

			wp_localize_script( 'googlesheetwoo.js', 'WP_DS8GoogleSheetWOO', $inline_js );
		}
	}	

	public static function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( plugin_dir_url( __FILE__ ) . '/ds8google-sheets-woo.php' ) ) {
			$links[] = '<a href="' . esc_url( self::get_page_url() ) . '">'.esc_html__( 'Settings' , 'ds8googlesheetwoo_recheck').'</a>';
		}

		return $links;
	}

	public static function display_alert() {
		DS8GoogleSheetWOO::view( 'notice', array(
			'type' => 'alert',
			'code' => (int) get_option( 'ds8googlesheetwoo_alert_code' ),
			'msg'  => get_option( 'ds8googlesheetwoo_alert_msg' )
		) );
	}
        
        public static function get_page_url( $page = 'config' ) {

		$args = array( 'page' => 'googlesheetwoo-key-config' );

		$url = add_query_arg( $args,  admin_url( 'options-general.php' ) );

		return $url;
	}
        
        public static function plugin_deactivation( ) {
          
        }
        
        // FEATURE JLMA 29-08-2022
        public static function create_form($options) {
            foreach ($options as $value) {
                switch ($value['type']) {
                    case "textarea";
                        self::create_section_for_textarea($value);
                        break;
                    case "text";
                        self::create_section_for_text($value);
                        break;
                    case "select":
                        self::create_section_for_taxonomy_select($value);
                        break;
                    case "select-page":
                        self::combo_select_page_callback($value);
                        break;
                }
            }
        }
        
        public static function ds8_get_formatted_page_array() {

            $ret = array();
            $pages = get_pages();
            if ($pages != null) {
                foreach ($pages as $page) {
                    $ret[$page->ID] = array("name" => $page->post_title, "id" => $page->ID);
                }
            }

            return $ret;
        }

        public static function combo_select_page_callback($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';
            echo '<td>';

            echo "<select id='" . $value['id'] . "' class='post_form' name='" . $value['id'] . "'>\n";
            echo "<option value='0'>-- Select page --</option>";

            $pages = get_pages();

            foreach ($pages as $page) {
                $checked = ' ';

                if (get_option($value['id']) == $page->ID) {
                    $checked = ' selected="selected" ';
                } else if (get_option($value['id']) === FALSE && $value['std'] == $page->ID) {
                    $checked = ' selected="selected" ';
                } else {
                    $checked = '';
                }

                echo '<option value="' . $page->ID . '" ' . $checked . '/>' . $page->post_title . "</option>\n";
            }
            echo "</select>";
            echo "</td>";
            echo '</tr>';
        }

        public static function create_section_for_taxonomy_select($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';
            echo '<td>';

            echo "<select id='" . $value['id'] . "' class='post_form' name='" . $value['id'] . "'>\n";
            echo "<option value='0'>-- Seleccione --</option>";

            foreach ($value['options'] as $option_value => $option_list) {
                $checked = ' ';

                if (get_option($value['id']) == $option_value) {
                    $checked = ' selected="selected" ';
                } else if (get_option($value['id']) === FALSE && $value['std'] == $option_list) {
                    $checked = ' selected="selected" ';
                } else {
                    $checked = '';
                }

                echo '<option value="' . $option_value . '" ' . $checked . '/>' . $option_list . "</option>\n";
            }
            echo "</select>";
            echo "</td>";
            echo '</tr>';
        }

        public static function create_section_for_textarea($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';

            $text = "";
            if (get_option($value['id']) === FALSE) {
                $text = $value['std'];
            } else {
                $text = get_option($value['id']);
            }

            echo '<td><textarea rows="6" cols="80" id="' . $value['id'] . '" name="' . $value['id'] . '">'.strip_tags($text).'</textarea></td>';
            echo '</tr>';
        }

        public static function create_section_for_text($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';

            $text = "";
            if (get_option($value['id']) === FALSE) {
                $text = $value['std'];
            } else {
                $text = get_option($value['id']);
            }

            echo '<td>';
            echo '<input type="text" id="' . $value['id'] . '" name="' . $value['id'] . '" value="' . $text . '" class="' . $value['class'] . '" />';
            if (!empty($value['desc'])){
              echo '<p class="description">'.$value['desc'].'</p>';
            }
            echo '</td>';
            echo '</tr>';
        }
	
}

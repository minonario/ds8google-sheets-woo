<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

?>
<div id="ds8googlesheetwoo-plugin-container">

	<div class="ds8googlesheetwoo-lower">

		<div class="ds8googlesheetwoo-boxes">
                  
                  <div class="wrap">

                    <h2><?php _e('DS8 Sync Up Woocommerce with Google Sheet - Config API') ?></h2>

                    <form class="ds8-form" method="post" action="options.php">
                    <?php settings_fields('ds8-settings-group'); ?>
                    <?php do_settings_sections('ds8-settings-page') ?>

                        <table class="form-table">
                        <?php DS8GoogleSheetWoo_Admin::create_form($options); ?>
                          <tr valign="top">
                            <th scope="row">Ruta credenciales OAuth</th>
                            <td> <?php  echo DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . 'oauth-credentials.json' ?></td>
                          </tr>
                        </table>

                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>

                    </form>
                  </div>
                  
                  <div class="wrap">
                    <h2><?php _e('Cargar de archivo OAUTH en formato JSON') ?></h2>
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype='multipart/form-data'>

                      <table class="form-table">
                        <tr valign="top">
                          <th scope="row">Cargar archivo</th>
                          <td>
                            <input type='file' name='file' onchange="ValidateSingleInput(this)">
                            <p class="description">Se va a reemplzar en caso de existir. Tiene que tener el nombre <b>oauth-credentials.json</b></p>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>
                            <p class="submit">
                              <?php submit_button(__('Cargar', 'ds8googlesheetwoo'), '', 'uploadds8', false); ?>
                            </p>
                          </td>
                        </tr>
                      </table>
                    </form>
                  </div>
                    
                  <div class="wrap">
                    <h2><?php _e('Conexión y ejecución de servicio Google Sheet API') ?></h2>
                    <form class="ds8-form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                          <table class="form-table">
                            <tr valign="top">
                              <th scope="row">Google Sheet API</th>
                              <td> Ejecutar proceso sincronización hoja de Google con productos en Woocommerce</td>
                            </tr>
                            <tr>
                                    <td>&nbsp;</td>
                                    <td><?php submit_button('Sincronizar', '', 'conectar', false); ?></td>
                            </tr>
                          </table>
                          <div class="resultados">
                            <?php
                            
                            $data = isset($GLOBALS['data']) ? $GLOBALS['data'] : array();
                            
                            if ( count($data) > 1 ) {
                              $wp_list_table = new SheetGoogle();
                              $wp_list_table->prepare_items();
                              $wp_list_table->display();
                            }
                            
                            //$data = isset($GLOBALS['data']) ? $GLOBALS['data'] : array();
//                            if (count($data)>1) :
//                              echo 'process';
//                              foreach ($data as $row) {
//                                  
//                                  printf("%s, %s\n", $row['sku'], $row['regular_price']);
//                                  $idproduct = wc_get_product_id_by_sku( $row['sku'] );
//                                  $product = wc_get_product($idproduct);
//                                  
//                                  
//                                  if ( !empty($product->get_regular_price()) && !empty($row['regular_price']) ){
//                                    $product->set_regular_price($row['regular_price']);
//                                  }
//                                  if ( !empty($product->get_sale_price()) && !empty($row['price']) ){
//                                    $product->set_sale_price($row['price']);
//                                  }
//                                  $ret = $product->save();
//                                  echo 'result='.$ret;
//                                  
//                                  //var_dump($product);
//                              }
//                            else:
//                              echo 'no hay data para actualizar';
//                            endif;
                            ?>
                          </div>
                    </form>
                  </div>
                  
		</div>
	</div>
</div>
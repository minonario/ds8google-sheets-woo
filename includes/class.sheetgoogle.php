<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class SheetGoogle extends WP_List_Table {

  function __construct() {
    parent::__construct(array(
        'singular' => 'wp_list_text_link', //Singular label
        'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
        'ajax' => false //We won't support Ajax for this table
    ));
  }

  function extra_tablenav($which) {
    if ($which == "top") {
      //The code that goes before the table is here
      echo "Resultado de sincronizar los productos con la hoja excel de Google.";
    }
    /*if ($which == "bottom") {
      //The code that goes after the table is there
      echo"Hi, I'm after the table";
    }*/
  }

  function get_columns() {
    $columns['col_sku'] = __('SKU');
    $columns['col_price'] = __('Price');
    $columns['col_regular_price'] = __('Regular Price');
    $columns['col_date_update'] = __('Fecha de actualización');
    return $columns;
  }
  
  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
      case 'col_sku':
      case 'col_price':
      case 'col_regular_price':
      case 'col_date_update':
        return isset($item[ $column_name ]) ? $item[ $column_name ] : '';
      default:
        return print_r( $item, true );
    }
  }

  function prepare_items() {
    global $wpdb, $_wp_column_headers;
    $screen = get_current_screen();
    
    $columns = $this -> get_columns(); 
    $hidden = array(); 
    $sortable = array(); 
    $this->_column_headers = array( $columns ,$hidden , $sortable ); 
    
    $items = array();
    
    $data = isset($GLOBALS['data']) ? $GLOBALS['data'] : array();
    
    foreach($data as $row) {
     //if (value[0] != null || $matches[0] != false) {
        $items[]= array('col_sku'   => $row['sku'], 
                        'col_price'   => $row['price'],
                        'col_regular_price'   => $row['regular_price'],
                        'col_date_update'   => $row['fecha_de_actualizacion']
            );
      //}
      
    }
    $totalitems = sizeof($data);//$wpdb->query($query);
    $this->items = $items; //$wpdb->get_results($query);
  }
  
}
<?php
function lili_ad_listing_cache($listing_node, $field) {
  $listing = &drupal_static(__FUNCTION__);
  // to-do translation
  // $_language = $language->language;
  $_w = entity_metadata_wrapper('node', $listing_node);
  //dpm($listing_node);
  $tid_type = array(
      TNT_INDUSTRY_TRUCK_TID   => array(
          'category' => 'field_truck_category',
          'make'     => 'field_truck_make',
      ),
      TNT_INDUSTRY_TRAILER_TID => array(
          'category' => 'field_trailer_category',
          'make'     => 'field_trailer_make',
      ),
      TNT_INDUSTRY_PART_TID    => array(
          'category' => 'field_part_category',
          'make'     => '', // part has no make..
      ),
  );

  if (!isset($listing)) {
    // Industry taxonomy id (multi values are allowed in this field..)
    $listing['type'] = $_w->field_industry[0]->raw();
  }

  if (!isset($listing[$field])) {
    // text fields
    $text = array('field_ad_item_year','field_ad_item_model');
    if (in_array($field, $text)) {
      $v = $_w->$field->value();
      $listing[$field] = isset($v) ? trim($v) : '';
    }

    // Radio fields
    $radios = array('field_ad_item_condition');
    if (in_array($field, $radios)) {
      $v = $_w->$field->value(); // on = 1, off = 0
      $listing[$field] = '';
      if (isset($v)) {
        $info = field_info_field($field); // on = New, off = Used
        $allowed_values = $info['settings']['allowed_values'];
        $listing[$field] = $allowed_values[$v]; // on = New, off = Used
      }
    }

    // custom fields
    if ($field == 'industry') {
      // tax field in multi value list
      $listing[$field] = '';
      if (isset($listing['type'])) {
        $v                 = $_w->field_industry[0]->value();
        $listing[ $field ] = ( isset( $v ) ) ? $v->name : '';
      }
    }

    if ($field == 'application') {
      $listing[$field] = '';
      if (isset($listing['type'])) {
        $_f = $tid_type[ $listing['type'] ]['category'];
        // tax field in single value list
        $v                 = $_w->$_f->raw();
        $listing[ $field ] = ( isset( $v ) ) ? $_w->$_f->name->value() : '';
      }
    }

    if ($field == 'make') {
      $listing[$field] = '';
      if (isset($listing['type']) && $_f = $tid_type[$listing['type']]['make']) {
        // tax field in single value list
        $v = $_w->$_f->raw();
        $listing[$field] = (isset($v)) ? $_w->$_f->name->value() : '';
      }
    }

    if ($field == 'dealer_name') {
      // entity reference in single value list
      $v = $_w->field_dealer->raw();
      $listing[$field] = (isset($v)) ? $_w->field_dealer->title->value() : '';
    }

    // custom fields.

  }
  return trim($listing[$field]);
}

/*
  $_ad_title = array(
      tnt_ad_listing_cache($ad_listing_node, 'field_ad_item_year'),
      tnt_ad_listing_cache($ad_listing_node, 'make'),
      tnt_ad_listing_cache($ad_listing_node, 'field_ad_item_model'),
      tnt_ad_listing_cache($ad_listing_node, 'application'),
  );
  $_ad_title = implode(" ", array_filter($_ad_title, 'strlen'));
*/

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  function getShop_accessToken()
  {
    $ci =& get_instance();
    $query=$ci->db->query("SELECT * FROM `shopify_stores` limit  0,1");
    $rowdata=$query->row();
    if(count($rowdata)>0)
    {
      $data = array(
        'API_KEY' => $ci->config->item('shopify_api_key'),
        'API_SECRET' => $ci->config->item('shopify_secret'),
        'SHOP_DOMAIN' => $rowdata->domain,
        'ACCESS_TOKEN' => $rowdata->token
      );
    return $data;
    }
  }

  function getShopIdby_shop($shop)
  {
    $ci =& get_instance();
    $query=$ci->db->query("SELECT * FROM `shopify_stores` where domain='".$shop."' limit  0,1");
    $rowdata=$query->row();
    if($rowdata)
    {
      return $rowdata->id;
    }
  }

  function getShop_accessToken_byShop($shop=NULL)
  {
         $ci =& get_instance();
    $query=$ci->db->query("SELECT * FROM `shopify_stores` where domain='".$shop."' limit  0,1");
    $rowdata=$query->row();

    if($rowdata)
    {
      $data = array(
        'API_KEY' => $ci->config->item('shopify_api_key'),
        'API_SECRET' => $ci->config->item('shopify_secret'),
        'SHOP_DOMAIN' => $rowdata->domain,
        'ACCESS_TOKEN' => $rowdata->token
      );
      return $data;
    }
  }

  function asset_url($asset_name='',$asset_type = NULL)
  {
    return ASSETS.$asset_type.'/'.$asset_name;
  }

  function css_asset($asset_name,$attributes = array()) {
    $attribute_str = _parse_asset_html($attributes);
    return '<link href="' . asset_url($asset_name,'css') . '" rel="stylesheet" type="text/css"' . $attribute_str . ' />';
  }

  function js_asset($asset_name) {
    return '<script type="text/javascript" src="' . asset_url($asset_name,'js') . '"></script>';
  }

  function image_asset($asset_name, $module_name = '', $attributes = array()) {
    $attribute_str = _parse_asset_html($attributes);
    return '<img src="' . asset_url($asset_name,'images') . '"' . $attribute_str . ' />';
  }

  function _parse_asset_html($attributes = NULL)
  {
    if (is_array($attributes)){
      $attribute_str = '';
      foreach ($attributes as $key => $value){
        $attribute_str .= ' ' . $key . '="' . $value . '"';
      }
      return $attribute_str;
    }else{
      return '';
    }
  }

  function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
  }

  function FileSizeConvert($bytes)
  {
      $bytes = floatval($bytes);
          $arBytes = array(
              0 => array(
                  "UNIT" => "TB",
                  "VALUE" => pow(1024, 4)
              ),
              1 => array(
                  "UNIT" => "GB",
                  "VALUE" => pow(1024, 3)
              ),
              2 => array(
                  "UNIT" => "MB",
                  "VALUE" => pow(1024, 2)
              ),
              3 => array(
                  "UNIT" => "KB",
                  "VALUE" => 1024
              ),
              4 => array(
                  "UNIT" => "B",
                  "VALUE" => 1
              ),
          );

      foreach($arBytes as $arItem)
      {
          if($bytes >= $arItem["VALUE"])
          {
              $result = $bytes / $arItem["VALUE"];
              $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
              break;
          }
      }
      return $result;
  }

  function json_send($array)
  {
    echo json_encode($array);
  }


  function _debug($value)
  {
    echo "<pre>";
    print_r($value);
    echo "</pre>";
  }

  function app_serialize($array)
  {
    return base64_encode(serialize($array));
  }

  function app_unserialize($string)
  {
    $error_serialized_data = base64_decode($string);
    $fixed_serialized_data = preg_replace_callback ( '!s:(\d+):“(.*?)“;!',
        function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':“' . $match[2] . '“;';
        },
    $error_serialized_data );
    return unserialize($fixed_serialized_data);
  }

  function shop_id($shop)
  {
    $ci =& get_instance();
    $shop_id = $ci->db->select('id')->where('shop',$shop)->get('shopify_stores')->row();
    return $shop_id->id;
  }

  function imageResize($image, $target) {
    $image = getimagesize($_SERVER['DOCUMENT_ROOT'].ASSETS.'images/'.$image);
    $width = $image[0];
    $height = $image[1];
    if ($width > $height) {
      $percentage = ($target / $width);
    } else {
      $percentage = ($target / $height);
    }

    $width = round($width * $percentage);
    $height = round($height * $percentage);

    return array($width,$height);
  }

  function the_thumbnail($src_image='', $height, $width)
  {
    if (null === $src_image || empty($src_image)) {
      return ASSETS.'images/no-image.png';
    }

    $source = $_SERVER['DOCUMENT_ROOT'].ASSETS.'images/'.$src_image;
    $path_parts = pathinfo($source);
    $thumb_marker = '_thumb'.$width.'X'.$height;
    $file = str_replace('.'.$path_parts['extension'], '', ASSETS.'images/'.$src_image).$thumb_marker.'.'.$path_parts['extension'];
    if (file_exists($file))
    {
      return $file;
    }else{
      return image_thumb( $src_image, $height, $width );
    }
  }

  function _site_url(){
    return sprintf(
      "%s://%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['SERVER_NAME']
    );
  }

  function image_thumb( $image, $height, $width ) {
      if (NULL === $image || empty($image)) {
        return ASSETS.'images/no-image.png';
      }

      $CI =& get_instance();
      $source = $_SERVER['DOCUMENT_ROOT'].ASSETS.'images/'.$image;

      if (!file_exists($source)) {
        return ASSETS.'images/no-image.png';
      }

      $path_parts = pathinfo($source);
      $thumb_marker = '_thumb'.$width.'X'.$height;
      $new_image = str_replace('.'.$path_parts['extension'], '', $image).$thumb_marker.'.'.$path_parts['extension'];
      $destination = $_SERVER['DOCUMENT_ROOT'].ASSETS.'images/'.$new_image;

      if (!file_exists($destination)) {
        $CI->load->library( 'image_lib' );

        $config['image_library']    = 'gd2';
        $config['source_image']     = $source;
        $config['new_image']        = $source;
        $config['maintain_ratio']   = TRUE;
        $config['create_thumb']     = TRUE;
        $config['thumb_marker']     = $thumb_marker;
        $config['height']           = $height;
        $config['width']            = $width;

        $CI->image_lib->initialize( $config );
        if ($CI->image_lib->resize())
        {
          return ASSETS.'images/'.$new_image;
        }else{
          return ASSETS.'images/'.$image;
        }
        $CI->image_lib->clear();
      }else{
        return ASSETS.'images/'.$new_image;
      }

  }


  function allowed_cors_origins($value='')
  {
    $CI =& get_instance();
     $domains = $CI->db->select('domain')->where('active',1)->get('shopify_stores')->result_array();
    $new_arr = array_map(function ($e) {
      return $e['domain'];
    }, $domains);
    return $new_arr;
  }

  function allowed_origins($value='')
  {
    $CI =& get_instance();
    $domains = $CI->db->select('domain')->where('active',1)->get('shopify_stores')->result_array();
    $new_arr = array_map(function ($e) {
      return $e['domain'];
    }, $domains);
    return $new_arr;
  }

  function is_shop_active($shop='')
  {
    $CI =& get_instance();
    $shops = $CI->db->select('active')->where(array('shop' => $shop))->get('shopify_stores');
    if ($shops->num_rows() > 0) {
      return true;
    }else{
      return false;
    }

  }

  function getYear() {
    $curr_date = date('m/d/Y h:i:s a', time());
    $curr_month = date('m');
    $curr_year = date('Y');
    $api_arr = ['-01', '-04', '-07', '-10'];
    $api_end = '';

    if($curr_month === 1) {
      $api_end = ($curr_year - 1) . $api_arr[3];
    } else if($curr_month > 1 && $curr_month <= 4) {
      $api_end = $curr_year . $api_arr[0];
    } else if($curr_month > 4 && $curr_month <= 7) {
      $api_end = $curr_year . $api_arr[1];
    } else if($curr_month > 7 && $curr_month <= 10) {
      $api_end = $curr_year . $api_arr[2];
    } else if($curr_month > 10 && $curr_month <= 12) {
      $api_end = $curr_year . $api_arr[3];
    }

    // print_r($api_end);
    return $api_end;
  }

<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
require_once APPPATH . 'libraries/simple_html_dom.php';
class Badge_Controller extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('Global_model');
        $this->load->library('form_validation');
    }
    public function loadbadges_data()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $path = $_SERVER['DOCUMENT_ROOT'].'/trustbadge/assets/upload/';
        $svg_file_new = array();
        $data = $this->Global_model->loadbadges($shop_id);
        $style = $this->db->query('select * from customize_tbl where shop_id = "'.$shop_id.'"')->result();
        $badge_type = $this->db->query('select badge_type from inserted_badges where shop_id="'.$shop_id.'" order by id asc limit 1')->result();
        foreach ($data as $value) {
          $svg_file= file_get_contents($path.$value->badge_image);
          $find_string   = '<svg';
          $position = strpos($svg_file, $find_string);
          $svg['svg'] = substr($svg_file, $position);
          $badge_name['badge_name'] = $value->badge_name;
          $svg_file_new[] = array_merge($svg,$badge_name);
        }
        $allData['data'] = $data;
        $allData['style'] = $style[0];
        $allData['badge_type'] = $badge_type[0];
        $allData['svg_file'] = $svg_file_new;
        json_send($allData);
      }
      else {

      }
    }

}

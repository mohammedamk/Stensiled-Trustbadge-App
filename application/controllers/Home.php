<?php
// defined('BASEPATH') or exit('No direct script access allowed');
// header('Access-Control-Allow-Origin: shopfyapptest4.myshopify.com');
// header('Access-Control-Allow-Methods: GET, POST');
// require_once APPPATH . 'libraries/simple_html_dom.php';
class Home extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('Global_model');
        $this->load->library('form_validation');
        // $this->load->library('session');
    }

    public function Dashboard()
    {
      if (!empty($_GET['shop'])) {
          $shop            = $_GET['shop'];
          $data['shop']    = $shop;
          $data['shop_id'] = shop_id($shop);
          $getbadges = $this->Global_model->get_badges();
          $getbadgesUsingShopId = $this->Global_model->getbadgesUsingShopId($data['shop_id']);
          $data['get_badge_type'] = $this->db->query('select badge_type from inserted_badges where shop_id="'.$data['shop_id'].'" order by id asc limit 1')->result();
          $getStyleCount = $this->db->query('select * from customize_tbl where shop_id="'.$data['shop_id'].'"')->result();
          if (count($getStyleCount) == 0) {
            $this->Global_model->updateCustomization($data['shop_id']);
          }
          $getStyle = $this->Global_model->getStyle($data['shop_id']);
          if ($this->input->post('uploadBadges')) {

              $config['upload_path'] = 'assets/images';
              $config['allowed_types'] = '*';
              $config['file_name'] = $_FILES['file']['name'];
              //Load upload library and initialize configuration
              $this->load->library('upload');
              $this->upload->initialize($config);

              if($this->upload->do_upload('file')){
                  $uploadData = $this->upload->data();
                  $badgeFile1 = $uploadData['file_name'];
                  $badgeFile=$badgeFile1;
              }else{
                $error = array('error' => $this->upload->display_errors());
                print_r($error);
                exit;
                  $badgeFile = '';
              }
              //PNG File
              $config['upload_path'] = 'assets/images';
              $config['allowed_types'] = 'png|PNG';
              $config['file_name'] = $_FILES['pngFile']['name'];
              //Load upload library and initialize configuration
              $this->load->library('upload');
              $this->upload->initialize($config);

              if($this->upload->do_upload('pngFile')){
                  $uploadPNGData = $this->upload->data();
                  $pngFile1 = $uploadPNGData['file_name'];
                  $pngFile=$pngFile1;
              }else{
                $error = array('error' => $this->upload->display_errors());
                print_r($error);
                exit;
                  $pngFile = '';
              }
               $uploadData = array(
                 'badge_image' =>$badgeFile,
                 'png_image' => $pngFile,
                 'badge_name' => $_POST['badge_name'],
                 'shop_id' => $data['shop_id']
               );
               $insertData = $this->db->insert('badges_list',$uploadData);
               if ($insertData) {
                 redirect('Home/Dashboard?shop='.$shop);
               }else {
                 echo "Something went wrong while uploading badge";
                 exit;
               }
            }
          $data['imge'] = $getbadges;
          $data['get_badges'] = $getbadgesUsingShopId;
          $data['img'] = $getStyle;
          $this->load->view('welcome', $data);
      } else {
          $this->load->view('errors/shop-errors/shop-not-found');
      }
    }
    public function insertBadges()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $checkedData = $_POST['checkedData'];
        $data = explode(",",$checkedData);
        $path = base_url('assets/images/');
        foreach ($data as $value) {
          echo "<pre>";
          $explodeData = explode('&',$value);
          $pngFileUrl = $path.$explodeData[3];
          $insertData = $this->Global_model->insertBadges($explodeData,$shop_id);
          $uploadFile = $this->base64Image($explodeData[2],$explodeData[0]);
          $uploadFile = $this->base64PNGImage($pngFileUrl,$explodeData[3]);
        }
      }
      else {
          $this->load->view('errors/shop-errors/shop-not-found');
      }
    }

    public function base64Image($checkedData,$imageName)
    {
      $upload_dir = $_SERVER['DOCUMENT_ROOT'].'/trustbadge/assets/upload/';
      $type = pathinfo($checkedData, PATHINFO_EXTENSION);
      $data = file_get_contents($checkedData);
      $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
      // echo $base64;
      $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
      $fileFile = $upload_dir.$imageName;
      $file = file_put_contents($fileFile, $data);
      return $file;
    }
    public function base64PNGImage($checkedData,$imageName)
    {
      $upload_dir = $_SERVER['DOCUMENT_ROOT'].'/trustbadge/assets/upload/';
      $type = pathinfo($checkedData, PATHINFO_EXTENSION);
      $data = file_get_contents($checkedData);
      $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
      // echo $base64;
      $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
      $fileFile = $upload_dir.$imageName;
      $file = file_put_contents($fileFile, $data);
      return $file;
    }

    public function loadbadges()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $path = $_SERVER['DOCUMENT_ROOT'].'/trustbadge/assets/upload/';
        $svg_file_new = array();
        $data = $this->Global_model->loadbadges($shop_id);
        foreach ($data as $value) {
          $svg_file= file_get_contents($path.$value->badge_image);
          $find_string   = '<svg';
          $position = strpos($svg_file, $find_string);
          $svg['svg'] = substr($svg_file, $position);
          $badge_name['badge_name'] = $value->badge_name;
          $svg_file_new[] = array_merge($svg,$badge_name);
        }
        $allData['data'] = $data;
        $allData['svg_file'] = $svg_file_new;
        json_send($allData);
      }
      else {
        $this->load->view('errors/shop-errors/shop-not-found');
      }
    }
    public function deleteBadge()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $id = $_POST['dataID'];
        $badgename = $_POST['badgename'];
        $badgenamepng = $_POST['badgenamepng'];
        $data = $this->Global_model->deleteBadge($shop_id,$id);
        echo $data;
        exit;
        if ($data) {
          $path = 'assets/upload/' . $badgename;
          $path1 = 'assets/upload/' . $badgenamepng;
          unlink($path);
          unlink($path1);
          $send_data['code']     = 200;
          $send_data['errors'][] = array(
              'message' => 'Record Deleted',
          );
        }else{
          $send_data['code']     = 201;
          $send_data['errors'][] = array(
              'message' => 'Something went wrong while deletion!',
          );
        }
        json_send($send_data);
      }
      else {
        $this->load->view('errors/shop-errors/shop-not-found');
      }
    }
    public function editBadge()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $id = $_POST['dataID'];
        $inputVlaue = $_POST['inputVlaue'];
        $data = $this->Global_model->editBadge($shop_id,$id,$inputVlaue);
        if ($data) {
          $send_data['code']     = 200;
          $send_data['errors'][] = array(
              'message' => 'Record Updated',
          );
        }else{
          $send_data['code']     = 201;
          $send_data['errors'][] = array(
              'message' => 'Something went wrong while updation!',
          );
        }
        json_send($send_data);
      }
      else {
        $this->load->view('errors/shop-errors/shop-not-found');
      }
    }

    public function test()
    {
      $this->load->view('test');
    }
    public function badgesDesign()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $elementFontSize = $_POST['elementFontSize'];
        $exploadeData = explode(',',$elementFontSize);
        if ($exploadeData[1] == "textFontSize") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set text_font="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "badgeSize") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set badge_font="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "labelFontSize") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set label_font="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "textColor") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set text_color="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "badgeColor") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set badge_color="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "labelColor") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set label_color="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "setTextValue") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set text="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "fontFamily") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set font_family="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "fontFamily1") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set text_font_family="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "text_format") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set text_align="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "bold_text") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set font_weight="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "show_hide_label") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set show_hide_label="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "elementwidth") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set max_width="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "below_text") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set below_text="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "badgeTextColor") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set fill_color="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "activate_badge") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update customize_tbl set activate_badge="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }
        if ($exploadeData[1] == "badge_type") {
          $textFontSize = $exploadeData[0];
          $this->db->query('update inserted_badges set badge_type="'.$textFontSize.'" where shop_id="'.$shop_id.'"');
        }

      }else {
        // code...
      }
    }
    public function updateOrder()
    {
      $index = $_POST['index'];
      foreach ($index as $value) {
        $id = $value['id'];
        $order_id = $value['order_id'];
        $this->db->query('update inserted_badges set order_id="'.$order_id.'" where id="'.$id.'"');
      }
    }
    public function Check()
    {
      $a = base_url('assets/upload/Paytm.svg');
      echo $a."<br>";
      $svg_file= file_get_contents($a);
      $find_string   = '<svg';
      $position = strpos($svg_file, $find_string);
      $svg_file_new = substr($svg_file, $position);
    echo "<div style='width:100px; height:100px;' >" . $svg_file_new . "</div>";
    echo "<img src='".$a."' width='100' height='100' >";
    }
    public function get_badge_type()
    {
      if (!empty($_GET['shop'])) {
        $shop            = $_GET['shop'];
        $shop_id = shop_id($shop);
        $data = $this->db->query('select badge_type from inserted_badges where shop_id="'.$shop_id.'" order by id asc limit 1')->result();
        echo json_send($data[0]->badge_type);
      }
      else {
        // code...
      }
    }
}

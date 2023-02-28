<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
class ShopifyWebhookController extends CI_Controller{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Global_model');
    $this->load->library('form_validation');
    $this->load->library('session');
  }

    public function GetShopifyCustomerdata()
    {
        http_response_code(200);
    }
    public function EraseShopifyCustomerdata()
    {
        http_response_code(200);
    }
    public function EraseShopData()
    {
        http_response_code(200);
    }


}

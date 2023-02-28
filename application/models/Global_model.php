<?php

class Global_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /*********start comman function for all*******************/
    public function check_ShopExist($shop = NULL)
    {
        $query = $this->db->query("SELECT * FROM `shopify_stores` where  shop='" . $shop . "'");
        $rows  = $query->num_rows();
        if ($rows > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function update_Shop($data, $accessToken)
    {
        if ($accessToken) {
            $sql = "update  shopify_stores set code='" . $data['code'] . "', hmac='" . $data['code'] . "', token='" . $accessToken . "' where  shop='" . $data['shop'] . "' ";
            $this->db->query($sql);
        }
    }
    public function get_shop_details($shop = NULL)
    {
        $shop_details = $this->db->select('charge_id')->where('shop', $shop)->get('shopify_stores');
        if ($shop_details->num_rows() > 0) {
            return $shop_details->row();
        } else {
            return false;
        }
    }
    public function add_newShop($data, $accessToken)
    {
        $sql = "insert into shopify_stores set code='" . $data['code'] . "', hmac='" . $data['code'] . "', domain='" . $data['shop'] . "',shop='" . $data['shop'] . "', token='" . $accessToken . "' ";
        $this->db->query($sql);
    }

    public function get_badges()
    {
      $data = $this->db->query('select * from badges_list where shop_id =0')->result();
      return $data;
    }

    public function getbadgesUsingShopId($shop_id)
    {
      $data = $this->db->query('select * from badges_list where shop_id="'.$shop_id.'"')->result();
      return $data;
    }
    public function UpdateShopDetails($where = array(), $data = array())
    {
        $this->db->where($where)->update('shopify_stores', $data);
        return $this->db->affected_rows();
    }
    public function insertBadges($explodeData,$shop_id)
    {
      $getOrder = $this->db->query("select * from inserted_badges where shop_id='".$shop_id."' ")->result();
      if (count($getOrder) == 0) {
        $order_id = 0;
      }else{
        $order_id = count($getOrder);
      }
      $sql = "insert into inserted_badges set badge_name='" . $explodeData[1] . "', badge_image='" . $explodeData[0] . "', original_image='" . $explodeData[3] . "', badge_type='svg', shop_id='" . $shop_id . "',order_id='".$order_id."' ";
      return $this->db->query($sql);
    }
    public function loadbadges($shop_id)
    {
      $data = $this->db->query('select * from inserted_badges where shop_id="'.$shop_id.'" order by order_id')->result();
      return $data;
    }
    public function deleteBadge($shop_id,$id)
    {
      $data = $this->db->query('delete from inserted_badges where shop_id="'.$shop_id.'" and id = "'.$id.'"');
      return $data;
    }
    public function editBadge($shop_id,$id,$inputVlaue)
    {
      $data = $this->db->query('update inserted_badges set badge_name="'.$inputVlaue.'" where shop_id="'.$shop_id.'" and id = "'.$id.'"');
      return $data;
    }
    public function getStyle($shop_id)
    {
      $data = $this->db->query('select * from customize_tbl where shop_id="'.$shop_id.'"')->result();
      return $data;
    }
    public function updateCustomization($shop_id)
    {
      $this->db->query('insert into customize_tbl set text_font=20,badge_font=70,label_font=14,text_color="black",badge_color="none",fill_color="black",label_color="black",text="Your text here",font_family="Arial",text_font_family="arial",text_align="center",font_weight="unset",show_hide_label= "block",max_width="0",below_text="above_text",shop_id="'.$shop_id.'",activate_badge="deactivate"');
    }
}
?>

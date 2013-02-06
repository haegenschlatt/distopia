<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cloudflare{
       
       public function Transform() {
               if(isset($_SERVER['HTTP_CF_CONNECTING_IP']))
               $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
       }
}
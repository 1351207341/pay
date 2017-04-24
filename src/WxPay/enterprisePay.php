<?php

namespace WxPay;

use WxPay\Data\WxEnterprisePay;
use WxPay\Lib\WxPayApi;


/*
 *  企业付款实现类
 *
 *  得到企业付款接口返回的结果转化后的数组
 */

class enterprisePay
{
    /*
    $config = array(
        'mch_appid'=>'',            //必传，公众账号appid
        'mchid'=>'',                //必传，商户号
        'partner_trade_no'=>'',     //必传，商户订单号
        'openid'=>'',               //必传，用户openid
        'check_name'=>'',           //必传，校验用户姓名选项（NO_CHECK,FORCE_CHECK,OPTION_CHECK）
        're_user_name'=>'',         //可选，check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
        'amount'=>'',               //必传，金额
        'desc'=>'',                 //必传，企业付款描述信息
        'spbill_create_ip'=>'',     //必传，Ip地址
        'key'=>'',                  //必传，密钥，签名用到
        'sslCert'=>'',              //必传，接口证书
        'sslKey'=>'',               //必传，接口证书
    );
    */
    public static function SetWxPayEnterprisePay($config)
    {
        $input = new WxEnterprisePay();
        if($config['key']){
            $input->key = $config['key'];
        }
        if($config['sslCert']){
            $input->sslCert = $config['sslCert'];
        }
        if($config['sslKey']){
            $input->sslKey = $config['sslKey'];
        }
        if($config['mch_appid']){
            $input->SetMchAppid($config['mch_appid']);//公众账号ID
        }
        if($config['mchid']){
            $input->SetMchid($config['mchid']);//公众账号ID
        }
        $input->SetPartner_trade_no($config['partner_trade_no']);
        $input->SetOpenid($config['openid']);
        $input->SetCheck_name($config['check_name']);
        if($config['check_name'] == 'FORCE_CHECK' || $config['check_name'] == 'OPTION_CHECK'){
            $input->SetRe_user_name($config['re_user_name']);
        }
        $input->SetAmount($config['amount']);
        $input->SetDesc($config['desc']);
        $input->SetSpbill_create_ip($config['spbill_create_ip']);
        $result = WxPayApi::enterprisePayment($input);
        return $result;
    }
}
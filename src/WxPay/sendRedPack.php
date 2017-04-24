<?php

namespace WxPay;

use WxPay\Data\WxPayRedPack;
use WxPay\Lib\WxPayApi;

/*
 *  现金红包实现类
 *
 *  得到现金接口返回的结果转化后的数组
 */

class sendRedPack
{
    public static function SetWxPaySendRedPack($config)
    {
        $input = new WxPayRedPack();
        if($config['key']){
            $input->key = $config['key'];
        }
        if($config['sslCert']){
            $input->sslCert = $config['sslCert'];
        }
        if($config['sslKey']){
            $input->sslKey = $config['sslKey'];
        }
        if($config['wxappid']){
            $input->SetWxAppid($config['wxappid']);
        }
        if($config['mch_id']){
            $input->SetMch_id($config['mch_id']);
        }
        if($config['total_amount'] > 200){
            if(isset($config['scene_id'])){
                /*
                PRODUCT_1:商品促销
                PRODUCT_2:抽奖
                PRODUCT_3:虚拟物品兑奖
                PRODUCT_4:企业内部福利
                PRODUCT_5:渠道分润
                PRODUCT_6:保险回馈
                PRODUCT_7:彩票派奖
                PRODUCT_8:税务刮奖*/
                $input->SetScene_Id($config['scene_id']);
            }else{
                $input->resultMsg['return_code'] = 'FAIL';
                $input->resultMsg['return_msg'] = '红包金额大于200时scene_id必传';
                return $inputObj->resultMsg;
            }
        }
        $input->SetMch_billno(self::GetBillno($config['mch_id']));
        $input->SetSend_name($config['send_name']);
        $input->SetRe_openid($config['re_openid']);
        $input->SetTotal_amount($config['total_amount']);
        $input->SetWishing($config['wishing']);
        $input->SetClient_ip($config['client_ip']);
        $input->SetAct_name($config['act_name']);
        $input->SetRemark($config['remark']);
        $result = WxPayApi::sendredpack($input);
        return $result;
    }

    public static function GetBillno($mchId)
    {
        $randNum = sprintf("%u", crc32(md5(uniqid().time().mt_rand(1, 999999))));
        $randNum = substr($randNum, 0, 7);
        $randNum = mt_rand(1, 9) . $randNum . mt_rand(10, 99);
        return $mchId . date('Ymd') . $randNum;
    }

}
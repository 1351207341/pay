<?php

namespace WxPay;

use WxPay\Data\WxPayResults;
use WxPay\Data\WxPayRefund;
use WxPay\Lib\WxPayApi;

class refund
{
    //退款方法
    public static function setRefundData($config){
        $input = new WxPayRefund();
        if($config['key']){
            $input->key = $config['key'];
        }
        if($config['sslCert']){
            $input->sslCert = $config['sslCert'];
        }
        if($config['sslKey']){
            $input->sslKey = $config['sslKey'];
        }
        if($config['app_id']){
            $input->SetAppid($config['app_id']);//公众账号ID
        }
        if($config['mch_id']){
            $input->SetMch_id($config['mch_id']);//商户号
            $input->SetOp_user_id($config['mch_id']);  //操作员号
        }
        if($config['transaction_id'] || $config['out_trade_no']){
            if(!empty($config['transaction_id'])){
                $input->SetTransaction_id($config['transaction_id']);
            }
            if(!empty($config['out_trade_no'])){
                $input->SetOut_trade_no($config['out_trade_no']);
            }
        }else{
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '商户订单号和微信订单号二者必选一';
            return $input->resultMsg;
        }

        if(!$config['total_fee']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少订单总金额';
            return $input->resultMsg;
        }
        $input->SetTotal_fee($config['total_fee']);

        if(!$config['refund_fee']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少退款金额';
            return $input->resultMsg;
        }
        $input->SetRefund_fee($config['refund_fee']);

        if(!$config['out_refund_no']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少商户系统内部的退款单号';
            return $input->resultMsg;
        }
        $input->SetOut_refund_no($config['out_refund_no']);

        $refund = WxPayApi::refund($input);
        if(isset($refund['result_code'])){
            if($refund['return_code'] == 'SUCCESS' && $refund['result_code'] == 'SUCCESS'){
                return $refund;
            }else{
                $input->resultMsg['return_code'] = 'FAIL';
                if(isset($refund['err_code_des'])){
                    $input->resultMsg['return_msg'] = $refund['err_code_des'].$refund['return_msg'];
                }else{
                    $input->resultMsg['return_msg'] = $refund['return_msg'];
                }
                return $input->resultMsg;
            }
        }else{
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = $refund['return_msg'];
            return $input->resultMsg;
        }
    }
}
<?php

namespace WxPay;

use WxPay\Data\WxPayJsApiPay;
use WxPay\Data\WxPayUnifiedOrder;
use WxPay\Lib\WxPayApi;
/**
 *
 * JSAPI支付实现类
 *
 * 生成jsapi支付js接口所需的参数
 *
 */
class JsApiPay
{
    /**
     *
     * JsApi下单方法
     * $config 订单信息
     * openid是微信支付jsapi支付接口必须的参数
     */
    public static function SetWxPayUnifiedOrder($config)
    {
        $input = new WxPayUnifiedOrder();
        if($config['key']){
            $input->key = $config['key'];
        }
        if($config['app_id']){
            $input->SetAppid($config['app_id']);//公众账号ID
        }
        if($config['mch_id']){
            $input->SetMch_id($config['mch_id']);//公众账号ID
        }
        if(!$config['body']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少商品描述';
            return $input->resultMsg;
        }
        $input->SetBody($config['body']);
        if(!$config['out_trade_no']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少商户订单号';
            return $input->resultMsg;
        }
        $out_trade_no = $config['out_trade_no'];
        $input->SetOut_trade_no($out_trade_no);
        if(!$config['total_fee']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少订单总金额';
            return $input->resultMsg;
        }
        $input->SetTotal_fee($config['total_fee']);
        if($config['attach']){
            $input->SetAttach($config['attach']);
        }
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        if(isset($config['goods_tag'])){
            $input->SetGoods_tag($config['goods_tag']);
        }
        if(!$config['notify_url']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少异步通知回调地址';
            return $input->resultMsg;
        }
        $input->SetNotify_url($config['notify_url']);
        $input->SetTrade_type("JSAPI");
        if(!$config['openid']){
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = '缺少JSAPI必传的openid';
            return $input->resultMsg;
        }
        $input->SetOpenid($config['openid']);
        $order = WxPayApi::unifiedOrder($input);
        if(isset($order['result_code'])){
            if($order['return_code'] == 'SUCCESS' && $order['result_code'] == 'SUCCESS'){
                $jsApiParameters = self::GetJsApiParameters($order,$input->key);
                $input->resultMsg['return_code'] = 'SUCCESS';
                $input->resultMsg['data'] = $jsApiParameters;
                return $input->resultMsg;
            }else{
                $input->resultMsg['return_code'] = 'FAIL';
                $input->resultMsg['return_msg'] = $order['err_code_des'];
                return $input->resultMsg;
            }
        }else{
            $input->resultMsg['return_code'] = 'FAIL';
            $input->resultMsg['return_msg'] = $order['return_msg'];
            return $input->resultMsg;
        }
    }


    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    private static function GetJsApiParameters($UnifiedOrderResult, $key = null)
    {
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign($key));
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }


    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    /*private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }*/

    /**
     *
     * 获取地址js参数
     *
     * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    /*public function GetEditAddressParameters()
    {
        $getData = $this->data;
        $data = array();
        $data["appid"] = WxPayConfig::APPID;
        $data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => WxPayConfig::APPID,
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }*/
}
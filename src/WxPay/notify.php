<?php

namespace WxPay;

use WxPay\Data\WxPayResults;
use WxPay\Data\WxPayNotifyReply;
use WxPay\Lib\WxPayApi;

class notify
{
    /*
	 * 验证微信服务器回调内容
	 * return  签名验证失败和支付失败返回 return_code = FAIL，成功返回微信服务器返回数据（数组形式）
	 */
    public function checkSign($xml, $key = null)
    {
        $result = WxPayResults::Init($xml,$key);
        return $result;
    }

    public function ReplyWxPay($res = false,$mse = '验证失败')
    {
        $NotifyReply = new WxPayNotifyReply();
        if($res == true) {
            $NotifyReply->SetReturn_code("SUCCESS");
            $NotifyReply->SetReturn_msg("OK");
        }else{
            $NotifyReply->SetReturn_code("FAIL");
            $NotifyReply->SetReturn_msg($mse);
        }
        WxpayApi::replyNotify($NotifyReply->ToXml());
    }
}
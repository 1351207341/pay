<?php

namespace WxPay\Data;

use WxPay\Lib\WxPayException;

/**
 *
 * 接口调用结果类
 * @author widyhu
 *
 */
class WxPayResults extends WxPayDataBase
{
    /**
     *
     * 检测签名
     */
    public function CheckSign($key = null)
    {
        //fix异常
        if(!$this->IsSignSet()){
            $this->resultMsg['return_code'] = "FAIL";
            $this->resultMsg['return_msg'] = "签名为空";
            return $this->resultMsg;
        }

        $sign = $this->MakeSign($key);
        if($this->GetSign() == $sign){
            $this->resultMsg['return_code'] = "SUCCESS";
            //$this->resultMsg['return_msg'] = "签名正确";
            return $this->resultMsg;
            //return true;
        }
        $this->resultMsg['return_code'] = "FAIL";
        $this->resultMsg['return_msg'] = "签名错误";
        return $this->resultMsg;
    }

    /**
     *
     * 使用数组初始化
     * @param array $array
     */
    public function FromArray($array)
    {
        $this->values = $array;
    }

    /**
     *
     * 使用数组初始化对象
     * @param array $array
     * @param 是否检测签名 $noCheckSign
     */
    public static function InitFromArray($array, $noCheckSign = false)
    {
        $obj = new self();
        $obj->FromArray($array);
        if($noCheckSign == false){
            $obj->CheckSign();
        }
        return $obj;
    }

    /**
     *
     * 设置参数
     * @param string $key
     * @param string $value
     */
    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }

    public static function xmlAsArr($xml)
    {
        $obj = new self();
        $obj->FromXml($xml);
        return $obj->GetValues();
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public static function Init($xml, $key = null)
    {
        $obj = new self();
        $obj->FromXml($xml);
        //fix bug 2015-06-29
        if($obj->values['return_code'] != 'SUCCESS'){
            return $obj->GetValues();
        }
        $result = $obj->CheckSign($key);
        if($result['return_code'] == 'SUCCESS'){
            return $obj->GetValues();
        }
        return $result;
    }
}
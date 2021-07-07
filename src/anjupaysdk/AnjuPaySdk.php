<?php
// +----------------------------------------------------------------------
// | Title   : 自在安居APP在线支付SDK
// +----------------------------------------------------------------------
// | Created : Henrick (me@hejinmin.cn)
// +----------------------------------------------------------------------
// | Date    : 2021/7/7 下午2:20
// +----------------------------------------------------------------------


namespace Henrick\anjupaysdk;


class AnjuPaySdk extends Core
{
    /**
     * 回调数据解密
     * @param string $body
     * @param string $sign
     * @return array
     */
    public function callBack($body, $sign){
        return $this->response($body, $sign);
    }

    /**
     * 获取订单信息
     * @param string $orderNo 商户订单号
     * @return array|mixed
     */
    public function getOrderInfo($orderNo){
        $url = "/pay/order/get-info";
        return $this->request($url, ['orderNo'=>$orderNo]);
    }

    /**
     * 生成下单信息
     * @param string $goodsName 商品名称
     * @param string $orderNo 订单号
     * @param string $amount 订单金额 单位：分
     * @param string $noticeUrl 回调通知接口
     * @param string $currency 币种：cny
     * @param string $nonceStr 请求随机值
     * @param string $noticePara 通知附加参数
     * @param string $returnUrl
     * @param string $openId H5公众号支付用户open_id
     */
    public function createOrder($goodsName, $orderNo, $amount, $noticeUrl, $nonceStr, $currency="cny",$noticePara="", $returnUrl="", $openId=""){
        $order = [
            "goods_name" => $goodsName,
            "order_no"   => $orderNo,
            "amount"     => intval($amount),
            "notice_url" => $noticeUrl,
            "notice_para" => $noticePara,
            "return_url"  => $returnUrl,
            "currency"    => $currency,
            "open_id"     => $openId,
            "nonce_str"   => $nonceStr
        ];
        return $this->request('',$order, true);
    }
}

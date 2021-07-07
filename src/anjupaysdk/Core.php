<?php
// +----------------------------------------------------------------------
// | Title   : 自在安居APP在线支付核心类
// +----------------------------------------------------------------------
// | Created : Henrick (me@hejinmin.cn)
// +----------------------------------------------------------------------
// | Date    : 2021/7/7 下午2:24
// +----------------------------------------------------------------------


namespace Henrick\anjupaysdk;


class Core
{
    private $_rsa;
    private $_mchId;
    private $_secret;
    private $_env = [
        'test'    => 'https://api-zhujianbaoan-test.inboyu.com',
        'product' => 'https://api-zhujianbaoan.inboyu.com',
    ];
    private $_domain;

    /**
     * 初始化参数
     * @param string $mchId 商户号
     * @param string $secret 商户密钥
     * @param string $priKey 商户私钥
     * @param string $pubKey 商户公钥
     * @param string $anjuPubKey 自在安居公钥
     * @param string $env SDK环境
     */
    public function __construct($mchId, $secret, $priKey, $pubKey, $anjuPubKey, $env='test') {
        $this->_mchId = $mchId;
        $this->_secret = $secret;
        $this->_domain = $this->_env[$env];
        $this->_rsa = new RSA($priKey, $pubKey);
        $this->_rsa->initOtherPubKey($anjuPubKey);
    }

    /**
     * 请求
     */
    public function request($url='', $data, $noReq=false){
        $data = array_merge([
            'mch_id'    => $this->_mchId,
            'timestamp' => time()
        ], $data);
        $body = json_encode($data, 256);
        $data = [
            'body' => $this->_rsa->publicEncrypt($body, true),
            'sign' => $this->_rsa->sign($body.$this->_secret)
        ];
        if($noReq){
            return $this->_error(0,'订单信息生成成功', $data);
        }
        $ret = $this->http($this->_domain.$url,'POST', $data);
        if(!$ret){
            return $this->_error(1,'接口请求失败');
        }
        $ret = json_decode($ret, true);
        if($ret['errcode']){
            return $this->_error($ret['errcode'],$ret['errmsg'], $ret['data']);
        }
        //验证签名
        $body = $this->_rsa->privateDecrypt($ret['data']['body']);
        if(!$body){
            return $this->_error(1,'数据解密失败');
        }
        if(!$this->_rsa->verify($body.$this->_secret, $ret['data']['sign'], true)){
            return $this->_error(1,'数据签名验证失败');
        }
        return $ret;
    }

    public function response($body, $sign){
        //验证签名
        $body = $this->_rsa->privateDecrypt($body);
        if(!$body){
            return $this->_error(1,'数据解密失败');
        }
        if(!$this->_rsa->verify($body.$this->_secret, $sign, true)){
            return $this->_error(1,'数据签名验证失败');
        }
        return $this->_error(0,'支付回调成功', $body);
    }

    private function _error($code, $msg, $data){
        return ['errcode'=>$code, 'errmsg'=>$msg, 'data'=>$data];
    }

    /**
     * CURL请求方法
     * @param $url
     * @param $method
     * @param null $postfields
     * @param array $header_array
     * @param null $userpwd
     * @return mixed
     */
    private function http ($url, $method="GET", $postfields = NULL, $header_array = array(), $userpwd = NULL)
    {
        $ci = curl_init();

        /* Curl 设置 */
        curl_setopt($ci, CURLOPT_USERAGENT, 'Mozilla/4.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        if ($userpwd) {
            curl_setopt($ci, CURLOPT_USERPWD, $userpwd);
        }

        $method = strtoupper($method);
        switch ($method) {
            case 'GET':
                if (! empty($postfields)) {
                    $url = $url . '?' . http_build_query($postfields);
                }
                break;
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (! empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
        }

        $header_array2 = array();

        foreach ($header_array as $k => $v) {
            array_push($header_array2, $k . ': ' . $v);
        }
        curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array2);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
}

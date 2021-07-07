<?php
// +----------------------------------------------------------------------
// | Title   : RSA加密解密
// +----------------------------------------------------------------------
// | Created : Henrick (me@hejinmin.cn)
// +----------------------------------------------------------------------
// | Date    : 2019-09-18 11:25
// +----------------------------------------------------------------------


namespace Henrick\anjupaysdk;

class RSA
{
    const CHAR_SET = "UTF-8";
    const BASE_64_FORMAT = "UrlSafeNoPadding";
    const RSA_ALGORITHM_KEY_TYPE = OPENSSL_KEYTYPE_RSA;
    const RSA_ALGORITHM_SIGN = OPENSSL_ALGO_SHA256;

    protected $public_key;
    protected $private_key;
    protected $key_len;
    protected $private_key_len;
    protected $other_public_key; //第三方公钥
    protected $other_pk_len; //第三方公钥长度

    public function __construct($pri_key = null, $pub_key)
    {
        $this->public_key = $this->_formatPubKey($pub_key);
        $this->private_key = $this->_formatPriKey($pri_key);
        $pub_id = openssl_get_publickey($this->public_key);
        $this->key_len = openssl_pkey_get_details($pub_id)['bits'];
    }

    public function initOtherPubKey($other_public_key){
        $this->other_public_key = $this->_formatPubKey($other_public_key);
        $pub_id = openssl_get_publickey($this->other_public_key);
        $this->other_pk_len = openssl_pkey_get_details($pub_id)['bits'];
    }

    public static function url_safe_base64_encode ($data) {
        return str_replace(array('+','/', '='),array('-','_', ''), base64_encode($data));
    }

    public static function url_safe_base64_decode ($data) {
        $base_64 = str_replace(array('-','_'),array('+','/'), $data);
        return base64_decode($base_64);
    }

    protected function _formatPubKey($pubKey) {
        $fKey = "-----BEGIN PUBLIC KEY-----\n";
        $len = strlen($pubKey);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($pubKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END PUBLIC KEY-----";
        return $fKey;
    }

    protected function _formatPriKey($pri_key) {
        $fKey = "-----BEGIN RSA PRIVATE KEY-----\n";
        $len = strlen($pri_key);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($pri_key, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END RSA PRIVATE KEY-----";
        return $fKey;
    }

    /*
     * 创建密钥对
     */
    public static function createKeys($key_size = 2048)
    {
        $config = array(
            "private_key_bits" => $key_size,
            "private_key_type" => self::RSA_ALGORITHM_KEY_TYPE,
        );
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $private_key);
        $public_key_detail = openssl_pkey_get_details($res);
        $public_key = $public_key_detail["key"];

        return [
            "public_key" => $public_key,
            "private_key" => $private_key,
        ];
    }

    /*
     * 公钥加密
     */
    public function publicEncrypt($data,  $is_other=false)
    {
        $encrypted = '';
        $part_len = $this->key_len / 8 - 11;
        if($is_other){
            $part_len = $this->other_pk_len / 8 - 11;
        }
        $parts = str_split($data, $part_len);

        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_public_encrypt($part, $encrypted_temp, $is_other ? $this->other_public_key:$this->public_key);
            $encrypted .= $encrypted_temp;
        }

        return static::url_safe_base64_encode($encrypted);
    }

    /*
     * 私钥解密
     */
    public function privateDecrypt($encrypted)
    {
        $decrypted = "";
        $part_len = $this->key_len / 8;
        $base64_decoded = static::url_safe_base64_decode($encrypted);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_private_decrypt($part, $decrypted_temp,$this->private_key);
            $decrypted .= $decrypted_temp;
        }
        return $decrypted;
    }

    /*
     * 私钥加密
     */
    public function privateEncrypt($data)
    {
        $encrypted = '';
        $part_len = $this->key_len / 8 - 11;
        $parts = str_split($data, $part_len);

        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_private_encrypt($part, $encrypted_temp, $this->private_key);
            $encrypted .= $encrypted_temp;
        }

        return static::url_safe_base64_encode($encrypted);
    }

    /*
     * 公钥解密
     */
    public function publicDecrypt($encrypted)
    {
        $decrypted = "";
        $part_len = $this->key_len / 8;
        $base64_decoded = static::url_safe_base64_decode($encrypted);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_public_decrypt($part, $decrypted_temp,$this->public_key);
            $decrypted .= $decrypted_temp;
        }
        return $decrypted;
    }

    /*
     * 数据加签
     */
    public function sign($data)
    {
        openssl_sign($data, $sign, $this->private_key, self::RSA_ALGORITHM_SIGN);

        return static::url_safe_base64_encode($sign);
    }

    /*
     * 数据签名验证
     */
    public function verify($data, $sign, $is_other=false)
    {
        $public_key = $is_other ? $this->other_public_key:$this->public_key;
        $pub_id = openssl_get_publickey($public_key);
        $res = openssl_verify($data, static::url_safe_base64_decode($sign), $pub_id, self::RSA_ALGORITHM_SIGN);

        return $res;
    }
}

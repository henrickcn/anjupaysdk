<?php
// +----------------------------------------------------------------------
// | Title   : 人才安居开发平台在线支付
// +----------------------------------------------------------------------
// | Created : Henrick (me@hejinmin.cn)
// +----------------------------------------------------------------------
// | From    : szrcaj
// +----------------------------------------------------------------------
// | Date    : 2021/7/7 下午2:15
// +----------------------------------------------------------------------
include "Loader.php";
spl_autoload_register("Loader::autoload");
//商户号
$mchId = "anju_test";
//商户密钥
$secret = "XNxuVnmODb5BWobCiQVyP5ly0897z6AcGTDuloDzxsO6CAd5g7G15KMcl5PnE1j7";
//商户私钥
$priKey = "MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDf16PpRiXboDO5/XzqCr6sw8FmUctvQLROCaVAzuNEcS9ktP4V4UmYICDhKQ8j3Qc/H1aCXTrvgc0m0moeAsS/AIWpYsRbLcSq8maYtFNLNjs0U74nTe+j+r0/naveKLqcQB5fH5TSR0vIm195ZdAE6eDwsusPex5BqaxAhXDhKGqYS6oRvn+2yjEeqmEMMzfZAzlEKjWgM0R3JfH9s2Mgj/HCcnD4Db00JT6vWR8cNzzRaLVRpj5zzfV7T+BcwLIh893LrFmEgW8MEXPryDzqTL0FErWdE0zmIZdAYZ5uciQSXpXRebjzFaP6YpN895ecoqPp5VcrATF37zPPFGIdAgMBAAECggEALC7cY8c84cUVcLjgVzIXwPJC0neCuEsFTAILZGdx5KiSukYfgSIe7LAqzUbVfja6n3MIGGNeprfwTqFp4NKbEzh/KZgdOgImt7dxGOM/LbFerk15UzjG/I8Zm9z7d7aCXyUo2Y3aSkdYZhiFF+lIzi97/wbR7xRWPI/JOFMenNKG6z81S/KKfwxjmAkUrvC9atCLLWwLdECr+DA4Z320f0E3tTxjrGhU083e+nHd4IaHARjKLdtdJ5y9fh3ROM3zH/WeEzFelHw+VawAaR6TXDpyXNasFWDBmkTowHIP1YBfUjDGQclFUKjeko3cr67aFLiP4paAUFLphVk6raOkAQKBgQD3EQCkyzYhmXISdRdjt0nZ4K80gKTwYf0VWhTlioTT2LxxuUktineYZOEMaZdb4yvnkpv+xfQLjOQQfkNk2C3P49ABl6Jetp7RRrLfSPqY75Zyf0Yn9TxBlb6KzJguGXEA4STJiJJCdJeNjKDbbjBc1+UStZSn+W1vClOzDBou4QKBgQDn76leB4Z/pYUHnmZAJDNfpCS4pzJz8N7yTW3PqV0vPeW6K0bZ0HS22u2zIxGMZcLVLHBZUsASOsjdBBuJKRUaAxKkdZf0rBuOD0H2iIrmatgy8or90qX8X0xZcnbzHHsa6LbcOWX6BJ+qi+hdEtMRiQCaUw+O64lurD8z//aGvQKBgQCDOcOmw8xvkinVhd2zd/HAyKcmcsGob9NVmPAKb0VXpAGLQceNacNV7RbfWeIrywBR0jwK2SdjTyT/YaD4Gh5TEgQ0JE74kXTPYQa2s/NucaruC42+wXGMwDVhUYPu0FKGDg9U/7X3mCe65hu2ENOdLIqeIlXf1gp69Pg53E634QKBgQCqpcfsegb4hfUB7QZ4bOorlV67SbEyYuf2SQfhgVAhgR40QsEnY1tsaln3snan4Ptf0wl6fwr4nq3JB8umuEZhVB90R10dVUAU3p7+3+mqrLQTkCa5qDIeJJPNQA3Kw4rD+rMIB2dDdAFx/uxhBoerYCzEXxaUZjJA7pS2Er701QKBgQC+jitRwApZa0jeEPU5zrsdAHoM7pOGge1XwaMhTVmJqijHtXIKL+z+6YUQ41PWL9hfAvZ9Sgy2jMn7LTDa23ho+x1egPu3/HbmWZJJ/mLkIt34BpWHwFXFoSbvp+/tChGWXqx9u5JUpZlEJFlI8WC/F7N0eoY/OGDdsmDtnTtZOQ==";
//商户公钥
$pubKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA39ej6UYl26Azuf186gq+rMPBZlHLb0C0TgmlQM7jRHEvZLT+FeFJmCAg4SkPI90HPx9Wgl0674HNJtJqHgLEvwCFqWLEWy3EqvJmmLRTSzY7NFO+J03vo/q9P52r3ii6nEAeXx+U0kdLyJtfeWXQBOng8LLrD3seQamsQIVw4ShqmEuqEb5/tsoxHqphDDM32QM5RCo1oDNEdyXx/bNjII/xwnJw+A29NCU+r1kfHDc80Wi1UaY+c831e0/gXMCyIfPdy6xZhIFvDBFz68g86ky9BRK1nRNM5iGXQGGebnIkEl6V0Xm48xWj+mKTfPeXnKKj6eVXKwExd+8zzxRiHQIDAQAB";
//安居公钥
$anjuPubKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAryi5vQJ3Sr3m5XkKerFaAQFsHHMRsKw+baNzXFv6/wxk0VbaNbZMBuX8Oh6RnO/m6Hz5ILbfSCadZ/ypMF0sW9s7J1pCSeFsAeBwnPy5L9MJ/zNAVWYF45B0JeT5MJm5jNdXcwHEbvzNHbxpQ1/1be4sZ7Glofg88KzNX02qXRCBJXQQec6QjjkLJs4hz0HythGnJXaCs26d8uVg1zms+62LFfHsCRd1QcUL5nuakWGu4MMuwoB4ff4alXfOfPRNO5h5VCEw2EqtvNoJ8NbIg+7+X36huLr1KfJf663qclXmr6wN2Tjy/rwenrikoEq6nY6nbtpEbwJ22SnVQsy4JQIDAQAB";
//接口环境 test:测试环境， product:生成环境
$apiEnv = "test";

//初始化SDK
$paySdk = new \Henrick\anjupaysdk\AnjuPaySdk($mchId, $secret, $priKey, $pubKey, $anjuPubKey, $apiEnv);
date_default_timezone_set("PRC");
//JSAPI 统一下单接口
$order = [
    "goods_name" => "测试商品",
    "order_no"   => date("YmdHis").mt_rand(100000,999999),
    "amount"     => 1,
    "notice_url" => "http://127.0.0.1/notify.php",
    "notice_para" => "测试支付",
    "return_url"  => "http://127.0.0.1",
    "currency"    => "cny",
    "open_id"     => "",
    "nonce_str"   => mt_rand(100000,999999)
];
$data = $paySdk->createOrder($order['goods_name'],$order['order_no'],
    $order['amount'],$order['notice_url'],$order['nonce_str'],
    $order['currency'], $order['notice_para'],$order['return_url'],$order['open_id']);
print_r($data);exit;

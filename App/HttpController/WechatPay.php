<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-4-8
// +----------------------------------------------------------------------
// | blog ( https://www.woann.cn )
// +----------------------------------------------------------------------
// | Author: Mr.wu <304550409@qq.com>
// +----------------------------------------------------------------------
namespace App\HttpController;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class WechatPay extends Base
{
    protected $config = [
        'miniapp_id' => '小程序appid',
        'mch_id' => '商户ID',
        'notify_url' => '回调url',
        'key' => '支付秘钥',
        'cert_client' => './apiclient_cert.pem',
        'cert_key' => './apiclient_key.pem',
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **单位：分**
            'body' => 'test body - 测试',
            'openid' => 'ozVBJ5LqeCzYS5TGkMn04ZyAZL7U',
        ];

        $pay = Pay::wechat($this->config)->miniapp($order);
        $this->success($pay);
        //结果如下
//        {
//            "code": 0,
//            "result": {
//                "appId": "wx5bc800e1e2358dcc",
//                "timeStamp": "1523177378",
//                "nonceStr": "8gvgH4znokhmFgU8",
//                "package": "prepay_id=wx081649383126961822c5d5bb0926963746",
//                "signType": "MD5",
//                "paySign": "8C022F602D986A0DB3E5101B00ECC16F"
//            }
//        }
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);
        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            Log::debug('Wechat notify', $data->all());
        } catch (Exception $e) {
//             $e->getMessage();
        }

        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }
}
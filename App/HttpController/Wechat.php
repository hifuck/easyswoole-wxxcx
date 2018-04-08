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

use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use App\Utility\Tools;
use Illuminate\Database\Capsule\Manager as DB;

class Wechat extends Base
{

    function index()
    {
        $this->response()->write('hello world');
    }

    public function actionNotFound($action):void
    {
        $this->response()->write($action.'想找到这个页面是不可能的,这辈子都不可能找到这个页面的');
    }

    /**
     * @return array
     * 小程序配置
     */
    protected function getMiniProgramConfig(){
        return [
            'app_id' => 'YOUR APPID',
            'secret' => 'YOUR SECRET',
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => Config::getInstance()->getConf('LOG_DIR').'/wechat.log',
            ],
        ];
    }

    //etToken方法
    function getToken(){
        //从小程序端接受code
        $code = $this->request()->getQueryParam('code');
        //初始化EasyWeChat，设置配置文件（也可以写在config.php中）这里为了方便就直接写在类里
        $app = Factory::miniProgram($this->getMiniProgramConfig());
        try {
            //执行外部请求，将从微信服务器获取 session_key，注意目前这个是同步操作
            $ret = $app->auth->session($code);
            var_dump($ret);
            if(!isset($ret['session_key'])){
                logger::getInstance()->log('微信session_key获取失败:('.$ret['errcode'].')'.$ret['errmsg']);
                throw new \Exception('系统繁忙，请稍后再试', 101);
            }
            //返回成功后将 session_key 回传给小程序，以便执行第二阶段。
            $this->success($ret);
        }catch (\Exception $e){
            $this->error($e->getCode(),$e->getMessage());
        }
    }


    function login(){
        $app = Factory::miniProgram($this->getMiniProgramConfig());
        //获取POST的参数
        $args = $this->request()->getParsedBody();

        try{
            //解密用户信息
            $res =  $app->encryptor->decryptData($args['session_key'], $args['iv'], $args['encryptedData']);
            if(!$res){
                $this->error(105,'获取用户信息失败，请稍后再试');
            }
            var_dump($res);
            //解密成功返回openid （这只是demo，下一节将加入加密解密用户token）
            $this->success(['session_id' => Tools::sessionEncrypt($res['openId'])]);
            //接一下解密异常的exception
        }catch(DecryptException $e){
            $this->error(102, '解密数据错误,请重新登录');
        }catch (\Exception $e){
            $this->error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * token验证
     */
    function check(){
        $header = $this->request()->getHeaders();
        if(!isset($header['authorization'])){
            $this->error(103,'access denied');
        }

        list ($bearer, $token) = explode(' ',$header['authorization'][0]);

        if(!$token){
            $this->error(104,'token error');
        }

        if(Tools::sessionCheckToken($token)){
            $this->success();
        }else{
            $this->error(106,'check token error');
        }
    }

    function test()
    {
        $this->check();
    }

}

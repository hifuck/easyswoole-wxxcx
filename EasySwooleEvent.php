<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use \EasySwoole\Core\AbstractInterface\EventInterface;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;
use \EasySwoole\Core\Swoole\Coroutine\PoolManager;
use App\Utility\MysqlPool2;

Class EasySwooleEvent implements EventInterface {

    public function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        if (version_compare(phpversion('swoole'), '2.1.0', '>=')) {
            PoolManager::getInstance()->addPool(MysqlPool2::class, 3, 10);
        }
        // TODO: Implement mainServerCreate() method.
    }

    public function onRequest(Request $request,Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public function afterAction(Request $request,Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
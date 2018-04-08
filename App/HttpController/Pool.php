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
use App\Utility\MysqlPool2;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Swoole\Coroutine\PoolManager;
class Pool extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('request over');
    }
    function test()
    {
        /*
         * Pool已经在在Event中注册了
         */
        $pool = PoolManager::getInstance()->getPool(MysqlPool2::class);
        \go(function ()use($pool){
            $db = $pool->getObj();
            if($db){
                $ret = $db->rawQuery('select sleep(1)');
                $pool->freeObj($db);
                var_dump('1 finish at '.time());
            }else{
                var_dump('db not available');
            }
        });
        \go(function ()use($pool){
            $db = $pool->getObj();
            if($db){
                $ret = $db->where('id','2')->get('ht_goods');
                $pool->freeObj($db);
                var_dump('2 finish at '.time());
            }else{
                var_dump('db not available');
            }
        });
        $this->response()->write('request over');
    }
    function test2()
    {
        //协程同步调用（优化worker 利用时间，让一个worker可以同时处理多个用户请求）
        $ret = null;
        $pool = PoolManager::getInstance()->getPool(MysqlPool2::class);
        $db = $pool->getObj();
        if($db){
            $ret = $db->getOne('user_list');
            $pool->freeObj($db);
        }else{
            var_dump('db not available');
        }
        $this->response()->write(json_encode($ret));
    }
}
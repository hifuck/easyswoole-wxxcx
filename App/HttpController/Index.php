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
class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
    function test()
    {
        $this->response()->write('666');
    }
}
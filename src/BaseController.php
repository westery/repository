<?php

namespace Westery\Repository;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Westery基础控制模块
 * Class BaseController
 * @package App\Http\Controllers\Admin
 */
class BaseController extends Controller
{

    /**
     * 抛出处理错误异常
     * @param string $message
     * @throws HttpException
     */
    public function error($message = 'error')
    {
        throw new HttpException(422,__($message));
    }

    /**
     * 逻辑成功返回
     * @param string $message
     * @param null $data
     * @param null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($message = 'success', $data = null, $meta = null)
    {
        $result = ['status_code'=>200,'status'=>200000,'message'=>__($message)];
        null === $meta || $result['meta'] = $meta;
        null === $data || $result['data'] = $data;
        return response()->json($result);
    }

    /**
     * 返回分页页面处理结果
     * @param Paginator $page
     * @param array $jsoned
     * @param array $purified
     * @return \Illuminate\Http\JsonResponse
     */
    public function responsePage($page,$jsoned=[],$purified=[])
    {
        $page = $page->toArray();
        $result = [];
        if(!empty($jsoned) && !empty($page['data'])){
            foreach ($page['data'] as $k => $item){
                foreach ($jsoned as $jsoned_item){
                    $page['data'][$k][$jsoned_item] = json_decode($item[$jsoned_item]);
                }
            }
        }
        if(!empty($purified) && !empty($page['data'])){
            foreach ($page['data'] as $k => $item){
                foreach ($purified as $purify){
                    $str = str_replace(['&nbsp;',' ',"\r\n", "\t","\r", "\n"],'',strip_tags($item[$purify]));
                    $page['data'][$k][$purify] = mb_substr($str,0,80);
                }
            }
        }

        $result['data'] = $page['data'];
        unset($page['data']);
        $meta = new \stdClass();
        $meta->pagination = $page;
        $result['meta'] = $meta;
        return response()->json($result);
    }



}

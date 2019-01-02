<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/30
 * Time: 13:37
 */

namespace PhalApi\Filter;

use PhalApi\Filter;
use PhalApi\Exception\BadRequestException;

class SimpleToken implements Filter {
    /**
     * 验证token
     */
    public function check() {
        $service = \PhalApi\DI()->request->get('s');
        $app=\PhalApi\DI()->config->get('app');
        $app = json_decode(json_encode($app),true);
        $apiTokenRules = $app['apiTokenRules'];
        if (in_array($service,$apiTokenRules)) {
            $allParams = \PhalApi\DI()->request->getAll();


            $user_id = @$allParams['uid'];
            if (empty($user_id)){
                throw new BadRequestException('缺少必要参数uid');
            }
            $token = isset($allParams['token']) ? $allParams['token'] : '';
            $service_token = \PhalApi\DI()->cache->get($user_id.'token');
            if (empty($service_token)){
                throw new BadRequestException('请重新登录',99);
            }else{
                if (strcmp($token,$service_token) !== 0){
                    \PhalApi\DI()->logger->debug('Wrong Token', array('needToken' => $service_token));
                    throw new BadRequestException('Token错误，请重新登录',99);
                }else{
                    \PhalApi\DI()->cache->set($user_id.'token',$service_token,24*60*60*7);
                }
            }
        }
        /*
        else{

            throw new BadRequestException(\PhalApi\T('缺少参数'));
        }
        */

    }

}


<?php

namespace WeChat;


use Pimple\Container;

class MaterialServiceProvider implements \Pimple\ServiceProviderInterface
{
    
    
    public function register(Container $pimple)
    {
        $temporary = function ($pimple) {
            return new \WeChat\Temporary($pimple['access_token']);
        };
    
        $pimple['material_temporary'] = $temporary;
        $pimple['material.temporary'] = $temporary;
    }
    
    
}
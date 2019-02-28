<?php

namespace qiNiu;


class Auth extends sdkAuth
{
    protected $signRequestWithData = '';
    
    
    public function setSignRequestWithData($data)
    {
        $args = [];
        self::copyPolicy($args, $data, true);
    }
    
    
    public function signRequest($urlString, $body, $contentType = null)
    {
        $url = parse_url($urlString);
        $data = '';
        if (array_key_exists('path', $url)) {
            $data = $url['path'];
        }
        if (array_key_exists('query', $url)) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";
        
        
        if ($body !== null && $contentType === 'application/x-www-form-urlencoded') {
            $data .= $body;
        }
    
        if (!empty($this->signRequestWithData)){
        
        }
        
        
        return $this->sign($data);
    }


}
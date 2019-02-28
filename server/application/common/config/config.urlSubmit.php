<?php
$homeHost = get_config_host('HOME_DOMAIN');

return [
    'baidu' => [
        'url'   => 'http://data.zz.baidu.com/urls?site=' . $homeHost . '&token=%s',
        'token' => 'JAOu5YdxAMM5aZyw',
    ],
    
    'pingBaidu' => [
        'url' => 'http://ping.baidu.com/ping/RPC2',
    ],
];


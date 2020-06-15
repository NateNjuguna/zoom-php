<?php

use Zoom\FS;

return [
    'default'   => 'storage',
    'disks'     => [
        'app'       => str_replace('config', 'app', __DIR__),
        'language'  => str_replace('config', 'lang', __DIR__),
        'root'      => str_replace(FS::OSCorrectPath('/config'), '', __DIR__),
        'storage'   => str_replace('config', 'storage', __DIR__),
        'views'     => str_replace('config', 'views', __DIR__),
    ]
];

<?php

return [
    [
        'userId' => 1,
        'networkSlug' => 'supernetwork',
        'userNetworkTokenKey' => 1234,
        'userNetworkTokenSecret' => 1234,
    ],
    [
        'userId' => 2,
        'networkSlug' => 'supernetwork',
        'userNetworkTokenKey' => 'deleteKey',
        'userNetworkTokenSecret' => 'deleteKey',
        'userNetworkTokenExpire' => '2000-10-10',
    ],
    [
        'userId' => 2,
        'networkSlug' => 'supersuper',
        'userNetworkTokenKey' => 'notdeletedkey',
        'userNetworkTokenSecret' => 'notdeletedkey',
        'userNetworkTokenExpire' => '3000-10-10',
    ],
];

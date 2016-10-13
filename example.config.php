<?php
/**
 *  這裡的設定值, 將會覆蓋 core/config/ 下相同名稱的值
 */
return [
    'app' => [
        /**
         *  不論在任何情況下, 正式的環境都必須使用 "production" 為 env 的值
         */
        'env' => 'production',
        'project' => [
            'name' => 'backup db log-tables tool',
        ],
    ],
    'db' => [
        'mysql' => [
            'host' => '',
            'user' => '',
            'pass' => '',
            'db'   => '',
        ],
    ],
    'settings' => [
        [
            'table'             => 'your_table_name',
            'date_field_name'   => 'your_create_datetime_field_name',
        ],
        [
            'table'             => 'your_table_name_2',
            'date_field_name'   => 'your_create_datetime_field_name',
        ],
    ],
];

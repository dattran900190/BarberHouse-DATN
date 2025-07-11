<?php

return [
    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE', 'your_tmn_code'),
        'hash_secret' => env('VNPAY_HASH_SECRET', 'your_hash_secret'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL'),
    ],
];
<?php

$firebaseAppKey = env('FIRE_BASE_API_KEY', 'AIzaSyDneO6KXu_qZWPasIcQS5YIbcWQDXecZqQ');

return [
    'type_number_id' => [
        '1' => 'Identity Card',
        '2' => 'Citizen ID',
        '3' => 'Driver Lisence'
    ],
    'firebase' => [
        'api_key' => $firebaseAppKey,
        'url_send_code' => env('FIRE_BASE_URL_SEND_CODE', "https://www.googleapis.com/identitytoolkit/v3/relyingparty/sendVerificationCode?key=$firebaseAppKey" ),
        'url_verify_code' => env('FIRE_BASE_URL_SEND_CODE', "https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPhoneNumber?key=$firebaseAppKey"),
        'keys' => [
            'app_key' => $firebaseAppKey,
            'app_api_key' => env('FIREBASE_API_KEY', 'AAAAzbIZHak:APA91bF1ab18rK66euY2yPCMy5nZGpN5hF7XoKljn4zpcZaF7EqQNJEs76of4vGT7LTMjLD9x7I3XWK_trHCZF_ZGMIhden7sOIiNyA0wSNuyyWtGWzJtmOwXa6fgVUM7CYwsIshXOVk')
        ],
        'time_resend_code' => 120
    ],
    'sms' => [
        'api_key' => env('SMS_API_KEY', '5ad907b6-ac98-371b-9dbc-625fc709ac01'),
        'secrect_key' => env('SMS_SECRECT_KEY', '5A1AEA214C51BF1A2FA4CE5E3E2A0B'),
        'url_otp_voice' => env('URL_OTP_VOICE', 'https://cloud.oncx.vn/oncx-campaign/campaign-runner-sessions'),
        'enable_send' => env('ENABLE_SEND_SMS', 1),
        'campaign_id' => env('CAMPAIGN_ID', '63521db368bffc65bdea100d')
    ],
    'order' => [
        '0' => 'Not Ordered',
        '1' => 'Ordered'
    ],
    'order_status' => [
        '' => 'All',
        '0' => 'Pending',
        '1' => 'Working',
        '2' => 'Done',
        '3' => 'Cancel',
    ],
    'order_status_id' => [
        '0' => 'Pending',
        '1' => 'Waiting accept for worker',
        '2' => 'Worker accepted',
        '3' => 'Worker going',
        '4' => 'Worker arrtive',
        '5' => 'Working',
        '6' => 'Done',
        '7' => 'Failed',
        '8' => 'Pause',
        '12'=> 'Cancelled'
    ],
    'order_time_status' => [
        '1' => 'Begin At',
        '2' => 'Begin End',
        '3' => 'Begin Pause',
        '4' => 'Cancel',
    ],
    'cache' => [
        'customer' => 'list_customer',
        'worker' => 'list_worker',
        'worker_pending' => 'list_worker_pending',
        'count_user' => 'list_count_user',
        'order' => 'list_order',
        'count_order' => 'list_count_order',
        'latest_order' => 'list_latest_order',
        'time' => 1200
    ],
    'user_status' => [
        '' => 'All',
        '0' => 'Inactive', //inActive
        '1' => 'Active', // run
        '2' => 'Pending', // inReview
        '3' => 'Rejected by admin' // Disabled
    ],
    'user_status_select' => [
        0 => 'Inactive',
        1 => 'Active',
        2 => 'Pending',
        3 => 'Rejected by admin'
    ],
    'user_rating' => [
        '' => 'All',
        '3' => '3 Stars',
        '4' => '4 Stars',
        '5' => '5 Stars'
    ],
    'user_gender' => [
        '' => 'All',
        'f' => 'Female',
        'm' => 'Male'
    ],
    'user_select_gender' => [
        'f' => 'Female',
        'm' => 'Male'
    ],
    'user_admin' => [
        0 => 'super_admin',
        1 => 'admin',
        2 => 'account',
        3 => 'editor',
    ],
    'menu' => [
        'fa_icons_class' => [
            'overview' => 'fas fa-tachometer-alt',   
            'users' => 'fas fa-fw fa-users',   
            'customer' => 'fas fa-user-friends',   
            'worker' => 'fas fa-people-carry',
            'account' => 'fas fa-user-cog',      
            'orders' => 'far fa-list-alt',   
            'payment' => 'fab fa-cc-visa',   
            'report' => 'fas fa-file-export',   
            'settings' => 'fas fa-cogs',   
            'cashout' => 'far fa-money-bill-alt',   
        ],
        'cache' => [
            'admin' => 'menu_left_admin',
            'account' => 'menu_left_account'
        ]
    ],
    'roles' => [
        0 => 'root',
        1 => 'admin',
        2 => 'account',
        3 => 'editor',
    ],
    'user_account_status' => [
        '' => 'All',
        '1' => 'Active',
        '0' => 'Inactive',
    ],
    'user_status_select' => [
        '1' => 'Active',
        '0' => 'Inactive',
    ],
    'vnpt' => [
        'mer_id' => env('MER_ID', 'EPAY000001'),
        'verify_account' => '9007',
	    'operation' => '9002',
	    'check_trans_status' => '9008',
	    'query_balance' => '9004',
        'partner_code' => env('PARTNER_CODE', 'PARTNERTEST02'),
        'endcode_key' => env('ENDCODE_KEY', 'rf8whwaejNhJiQG2bsFubSzccfRc/iRYyGUn6SPmT6y/L7A2XABbu9y4GvCoSTOTpvJykFi6b1G0crU8et2O0Q=='),
        'status' => [
            0 => 'Processing',
            1 => 'Done',
            2 => 'Failed',
            3 => 'Refund',
        ],
        'css_link' => env('PAYMENT_CSS_LINK', 'https://sandbox.megapay.vn/pg_was/css/payment/layer/paymentClient.css'),
        'js_link' => env('PAYMENT_JS_LINK', 'https://sandbox.megapay.vn/pg_was/js/payment/layer/paymentClient.js'),
        'domain' => env('PAYMENT_DOMAIN','https://sandbox.megapay.vn'),
        'refund_url' => env('PAYMENT_REFUND_URL', 'https://sandbox.megapay.vn/pg_was/cancel/paymentCancel.do'),
        'cancel_pw' => env('CANCEL_PW', ''),
        'pay_with_token_url' => env('PAYMENT_WITH_TOKEN_URL', 'https://sandbox.megapay.vn/pg_was/payWithTokenAPI.do'),
        'url_transfer_money' => env('TRANSFER_MONEY_URL', 'https://chihoepay.ecollect.vn:3669/Sandbox/PartnerMoneyTransfer'),

    ],
    'card_type' => [
        '001' => 'Visa',
        '002' => 'Mastercard',
        '007' => 'JCB',
    ],
    'cashout_status' => [
        0 => 'Waiting', 
        1 => 'Approved',
        2 => 'Canceled',
        3 => 'Failed',
    ],
    'iconApp' => 'assets/images/icon-app-for-worker.jpg',
    'emailRecivePayment' => 'assistmenow.info@gmail.com',
    'stripe' => [
        'key' => env('STRIPE_SECRET_KEY', 'sk_test_51NWWCAIGJ3FRhKHRHk3jFInSraduy8v2EBilgiO2XtbNgB5doRQQTjAquQSps1ugq8n5jAG0UiUrdOa6gEBcybXC00p97RV8oV'),
        'key_client' => env('STRIPE_PUBLISH_KEY', 'pk_test_51NWWCAIGJ3FRhKHRRviuoE2SteTb0Y1X86L1JsnLWpwECj9n4WUp5vnL8NJ1u03Gr9YgiyQOJDkCSBjnAKNPjnh200VupXOUtz')
    ],
    'worker_online_more_minutes' => env('WORKER_ONLINE_MORE_MINUTES', 90), // minutes
    'time_run_queue' => 90, // minutes
    'bin_code_support' => [
        970423,
        970437,
        970408,
        970407,
        970442,
        970414,
        970438,
        970422,
        970432,
        970439,
        970415,
        970431,
        970440,
        970429,
        970448,
        970425,
        970426,
        970427,
        970419,
        970418,
        970443,
        970406,
        970441,
        970424,
        970433,
        970454,
        970452,
        970430,
        970400,
        970405,
        970403,
        970412,
        970421,
        970434,
        970449,
        970457,
        970436,
        970416,
        970409,
        970446,
        970455,
        970444,
        970456,
        970462,
        970410,
        422589,
        796500,
        458761,
        801011,
        546034,
        546035,
    ],
];
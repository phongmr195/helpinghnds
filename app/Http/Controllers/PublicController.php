<?php

namespace App\Http\Controllers;

use App\Jobs\FcmJob;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use App\Jobs\NewJob;
use App\Services\Api\FcmService;
use App\Services\Api\OrderService;
use App\Models\Order;

//Stripe payment gateway
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Exception;


class PublicController extends Controller
{
    function __construct()
    {
    }

    /**
     * Dieu khoan su dung app
     * /terms-of-service
     */
    function termsOfService(Request $request)
    {
        return view('public.termsOfService', $request);
    }

    /**
     * User contact US
     */
    function contactUs(Request $request)
    {
        return view('public.contactUs', $request);
    }

    /**
     * Demo payment gateway
     */
    function stripeDemo(Request $request)
    {

        return view('payment.strip-payment', $request);
    }

    /**
     * Xu ly thanh toan demo voi stripe
     */
    public function stripeDemoPayment(Request $request)
    {
        try {
            Stripe::setApiKey('pk_live_51NWWCAIGJ3FRhKHRlLMMPVuZlGLvCj8jwjf59RuPlJha238R7DTSLpYQWkX03Ey2Ot9n8L0QmcB1prUtdwgmRYpE00SIxLzOwd');
            // $paymentIntent = PaymentIntent::create([
            //     'amount' => 1000, // Số tiền cần thanh toán (đơn vị là cent, ở đây là 10 USD)
            //     'currency' => 'usd', // Loại tiền tệ (USD)
            //     'payment_method_types' => ['card'], // Phương thức thanh toán (ở đây là thẻ tín dụng)
            // ]);

            // Lấy thông tin từ form
            $cardHolderName = $request['card_holder_name'];
            $token = $request['stripe_token'];

            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $token,
                ],
            ]);

            $paymentIntent = PaymentIntent::create([
                'amount' => 15000, // Số tiền cần thanh toán (đơn vị là cent, ở đây là 10 USD)
                'currency' => 'usd', // Loại tiền tệ (USD)
                'payment_method' => $paymentMethod->id,
                'confirmation_method' => 'manual',
                'confirm' => false,
            ]);

            // // Gửi response về trình duyệt cho phép thực hiện thanh toán
            echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
        } catch (Exception\ApiErrorException $e) {
            // Xử lý lỗi nếu có
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    function testQueue()
    {
        // $fcm->sendNotification($token, $notiData);
        // $model = new Order();
        // $order = $model::query()->with('detail')
        //     ->where('id', 3171)
        //     ->first();

        // $order->update([
        //     'order_status' => 21
        // ]);

        // $token = 'evus8eIZRaCbjLo2ub0DmM:APA91bEaf4aqsTfFqPzoCii1xEEhoeovBR9hSESfFBboIaHkcgTE5k8cJIKt1nJlaOJfyKVQaBNyyULmOmg_dRVtk7eMqclNdtPM4D4P7h_gmlXxP6lPjaJQBqT4JEWfwm9Z-49Xp8oE';
        $token = 'dg1weC-nxEL6ixPoomUJ-R:APA91bFVc1lUMy-gYdoR2IP6t42J915nFCN79KgIqvGhPn9bno8jDCMx1JqX84PjbdMhBadmR9lTIxUpvVv_BTebTYbwHFalf_TJM9IPVzpJW6GNgkY3fbI8ZuA9gnmZGBjocPZxoUvu';
        $notiData = [
            'order' => array(),
            'user' => array(),
            'worker' => array(),
            'title' => 'Assistant new work',
            'body' => '',
            'image' => 'https://helpinghnds-dev.giacongphanmem.vn/assets/images/icon-app-for-client.jpg',
            'fcm' => [
                // 'type' => 'FCM-TO-WORKER-NEW-JOB'
                // 'type' => 'FCM-ORDER-DONE'
                'type' => 'FCM-WORK-RESUMED'
            ]
        ];

        $notiData["order"] = json_encode(array(
            "id" => 202,
            "address" => "11212 Lê Văn Lương...",
            "service_name" => "sds sdsdsd sd s",
            "service_child_name" => "asasdsadasdsa",
            "note_description" => "dadadad adada da a",
        ));

        //goi ngay
        // NewJob::dispatch($token,$notiData);

        // FcmJob::dispatch($token,$notiData,'ios')->delay(now()->addMinute());
        // FcmJob::dispatch($token,$notiData,'ios');

        $fcm = new FcmService();
        $fcm->sendNotification($token, $notiData, 'ios');

        echo '333';
    }
}

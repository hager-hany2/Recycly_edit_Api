<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\TranslationGoogle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\VodafonePaymentService;
use App\Http\Requests\PaymentFormRequest;
use App\Http\Requests\callbackFormRequest;
use Illuminate\Support\Facades\Http;
class PaymentController extends Controller
{
//    public function __construct(VodafonePaymentService $paymentService)
//    {
//        $this->VodafonePaymentService = $paymentService;
//    }

//    public function processPayment(PaymentFormRequest $request)
//    {
//        $phoneNumber = $request->input('phone');
//        $amount = $request->input('Amount_paid');
//
//        // التحقق من أن المدخلات صحيحة
//        if (empty($phoneNumber) || empty($amount)) {
//            return response()->json(['status' => 'error', 'message' => 'Phone number and amount are required.'], 400);
//        }
//
////        // معالجة الدفع عبر فودافون كاش
//        $result = $this->VodafonePaymentService->processVodafonePayment($phoneNumber, $amount);
//
//        // إعادة الاستجابة بناءً على النتيجة
//        return response()->json($result);
//    }
    public function store(PaymentFormRequest $request)
    {
        //add translate in Services
        $lang = $request->header('lang', 'en');
        $translator = new TranslationGoogle($lang);
        // call in new object TranslationGoogle and add url use App\Services\TranslationGoogle; because porotect must inhert this function
//        dd($lang); //test lang
        // التحقق من البيانات

        $data=$request->validated();
//        dd($data);
        // إنشاء سجل جديد في جدول المدفوعات
        $payment = Payment::create($request->only(['user_id', 'order_id', 'Amount_paid', 'status', 'transaction_id', 'payment_method']));
//        dd($payment);
        if ($payment) {
            return response()->json([
                'message' => $translator->translate('Payment has been made successfully'),
            ], 201);
        }
        return response()->json([
            'error'=>$translator->translate('failed payment'),
        ]);
    }
    public function handleCallback(callbackFormRequest $request)
    {
        $lang = $request->header('lang', 'en');
        $translator = new TranslationGoogle($lang);
        $payment = Payment::where('transaction_id', $request->transaction_id)->first();

        if ($payment) {
            $payment->status = $request->status === 'success' ? 'completed' : 'failed';
            $payment->save();
        }
        return response()->json(['message' => $translator->translate( 'Callback received')]);
    }

}

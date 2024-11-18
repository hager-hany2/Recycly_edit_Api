<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Order;
use App\Models\Payment;
use App\Models\products;
use App\Services\TranslationGoogle;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentFormRequest;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    public function store(PaymentFormRequest $request,$id)
    {
        //add translate in Services
        $lang = $request->header('lang', 'en');
        $translator = new TranslationGoogle($lang);
        // call in new object TranslationGoogle and add url use App\Services\TranslationGoogle; because porotect must inhert this function
//        dd($lang); //test lang
        $data=$request->validated();
//        dd($data);
        DB::beginTransaction();
        $order = Order::create($request->only(['user_id', 'product_id','address','phone','status','total_price','quantity']));
//        dd($Order);
        DB::commit();
        if ($order) {
            return response()->json([
                'message' => $translator->translate('Order  successfully'),
            ], 201);
        }
        return response()->json([
        'error'=>$translator->translate('failed save Order'),
    ]);
    }
//    public function store(OrderFormRequest $request, $id)
//    {
//        // تحديد اللغة من الهيدر
//        $lang = $request->header('lang', 'en');
//        $translator = new TranslationGoogle($lang);
//
//        // التحقق من صحة البيانات المدخلة
//        $data = $request->validated();
//
//        // بدء المعاملة
//        DB::beginTransaction();
//
//        try {
//            // تحقق من وجود المستخدم
//            $user = User::findOrFail($data['user_id']);
//
//            // تحقق من وجود المنتج
//            $product = products::withTrashed()->findOrFail($data['product_id']);
//
//            // إنشاء الطلب
//            $order = Order::create([
//                'user_id' => $data['user_id'],
//                'product_id' => $data['product_id'],
//                'address' => $data['address'],
//                'phone' => $data['phone'],
//                'status' => $data['status'],
//                'total_price' => $data['total_price'],
//                'quantity' => $data['quantity'],
//            ]);
//
//            // تحديث كمية المخزون في المنتج
//            $product->quantity -= $data['quantity'];
//            $product->save();
//
//            // إتمام المعاملة
//            DB::commit();
//
//            // التحقق من نجاح عملية الحفظ
//            if ($order) {
//                return response()->json([
//                    'message' => $translator->translate('Order saved successfully'),
//                    'order' => $order
//                ], 201);
//            }
//
//        } catch (\Exception $e) {
//            // في حال حدوث خطأ، التراجع عن المعاملة
//            DB::rollBack();
//            return response()->json([
//                'message' => $translator->translate('Failed to save order'),
//                'error' => $e->getMessage()
//            ], 500);
//        }
//    }

}

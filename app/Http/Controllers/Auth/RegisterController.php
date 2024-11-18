<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Models\User;
use App\traits\upload_image;
use Illuminate\Http\Request;
use App\Models\Images;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use upload_image;
    public function Register(UserFormRequest $request)
    {
            // make request to validate data
        $lang = $request->header('lang', 'en');
        $translate = new GoogleTranslate($lang);
//        dd($lang); //test lang
        $data=$request->validated();
        //if Duplicate email
//        $existingUser = User::where('email', $request->email)->first();
//        if ($existingUser) {
//            return response()->json([
//                'error' => $translate->translate('already has been email')],402);
//        }
//        $existingUser_phone= User::where('phone', $request->phone)->first();
//        if ($existingUser_phone) {
//            return response()->json([
//                'error' => $translate->translate('already has been phone')],403);
//        }
        //تحسين الكود
        if ($existingUser = User::where('email', $request->email)->orWhere('phone', $request->phone)->first())
            return response()->json(['error' => $existingUser->email === $request->email
                ? $translate->translate('already has been email')
                : $translate->translate('already has been phone')],
                $existingUser->email === $request->email ? 401 : 403);
        //        if ($request->hasFile('image_url_profile')) {
//            $imagePath = $request->file('image_url_profile')->store('image_url_profile', 'public');
//            $user->image_url_profile = Storage::url($imagePath);
//        } else {
//            $user->image_url_profile= url('images/96fe121-5dfa-43f4-98b5-db50019738a7.jpg');
//        }
        //تحسين الكود
//        $data['image_url_profile'] = $request->hasFile('image_url_profile')
//            ? Storage::url($request->file('image_url_profile')->store('image_url_profile', 'public'))
//            : url('images/96fe121-5dfa-43f4-98b5-db50019738a7.jpg');
        $data['image_url_profile'] = $this->upload($request->file('image_url_profile'), 'user_images')
            ?? url('images/96fe121-5dfa-43f4-98b5-db50019738a7.jpg'); // في حالة عدم وجود الصورة، استخدم صورة افتراضية
        //$data['password'] =bcrypt($data['password']);
        //hashed password improve this (add function in model)because in edit profile repeat the best not repeat
        $user=User::query()->create($data);//create new user in database in PhpMyAdmin
        // إنشاء توكن جديد للمستخدم
        $token =$user->createToken('YourAppName')->plainTextToken;
//        dd($image);
        if($user){
            return response()->json([
                'message' => $translate->translate('Registration successful!'),
//            "username" => $translate->translate($data["username"]), // ترجمة اسم المستخدم
//            'email' => $data['email'],
//            'phone' => $data['phone'],
//            'password'=>$data['password'],
//            "type" => $translate->translate($data["type"]), // ترجمة النوع
//            "category_user" => $translate->translate($data["category_user"]), // ترجمة النوع
//            'price'=> $translate->translate($data["price"]),
//            'point'=> $translate->translate($data["point"]),
//            'token' => $token // إرجاع الـ Token
            ], 300);
        }else{
            return response()->json([
                'message' => $translate->translate('failed successful!'),
//            "username" => $translate->translate($data["username"]), // ترجمة اسم المستخدم
//            'email' => $data['email'],
//            'phone' => $data['phone'],
//            'password'=>$data['password'],
//            "type" => $translate->translate($data["type"]), // ترجمة النوع
//            "category_user" => $translate->translate($data["category_user"]), // ترجمة النوع
//            'price'=> $translate->translate($data["price"]),
//            'point'=> $translate->translate($data["point"]),
//            'token' => $token // إرجاع الـ Token
            ], 400);
        }

    }
}

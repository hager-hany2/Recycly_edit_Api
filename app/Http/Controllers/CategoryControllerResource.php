<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\User;
use App\traits\upload_image;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryFormRequest;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Categories;
use App\Services\TranslationGoogle;
use Storage\app\public;


class CategoryControllerResource extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use upload_image;
    public function index(Request $request)
    {
        //            //           تعيين اللغة المطلوبة والافتراضية "en" إذا لم تُحدّد
        //               //ونضع اللغة المطلوبة في header in postman
        //        $lang = $request->header('lang', 'en');
        //
        //        // تهيئة مترجم Google Translate
        //        $translator = new GoogleTranslate(); // هنا اقوم باانشاء class
        //        $translator->setTarget($lang);
        //تحسين الكود
        //add translate in Services
        $lang = $request->header('lang', 'en');
        $translator = new TranslationGoogle($lang);
        // call in new object TranslationGoogle and add url use App\Services\TranslationGoogle; because porotect must inhert this function


        // جلب جميع الفئات وترجمة الحقول المطلوبة مباشرةً
        $categories = Categories::all()->map(function ($category) use ($translator) {
            return [
                "category_name" => $translator->translate($category["category_name"]), // ترجمة اسم الفئة
                "category_description" => $translator->translate($category["category_description"]), // ترجمة وصف الفئة
                'image_url_category'=>$category['image_url_category'],//عرض الصورة

            ];
        });
        return response()->json($categories);//return json data
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryFormRequest $request)
    {
        //add translate in Services
        $lang = $request->header('lang', 'en');
        $translator = new TranslationGoogle($lang);
        // call in new object TranslationGoogle and add url use App\Services\TranslationGoogle; because porotect must inhert this function
//        dd($lang); //test lang
        $data=$request->validated();
//        dd($data);
        $existing_categories = categories::where('category_name', $request->category_name)->first();

        if ($existing_categories) {
            return response()->json([
                'error' => $translator->translate('already has been this category')],402);
        }
        $data['image_url'] = $this->upload($request->file('image_url'), 'category_images')
            ??null; // في حالة عدم وجود الصورة، استخدم صورة افتراضية
            // إنشاء الفئة وتخزينها
            $category = categories::create($request->only(['category_name', 'category_description','user_id','image_url'])); // التأكد من الحقول المطلوبة فقط

            // التحقق من نجاح عملية إنشاء الفئة
            if ($category) {
                return response()->json([
                    'message' => $translator->translate('Category saved successfully'),
//                    "category_name" => $translator->translate($category["category_name"]), // ترجمة اسم الفئة
//                    "category_description" => $translator->translate($category["category_description"]), // ترجمة وصف الفئة
                ], 201);
                }return response()->json([
                    'message'=>$translator->translate('failed save category')
            ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

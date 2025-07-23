<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Ecommerce\Entities\Sliders;
use App\Models\Product;
use App\Models\Category;
use Modules\Ecommerce\Entities\Page;
use Modules\Ecommerce\Mail\ContactUs;
use App\Models\MailSetting;
use DB;
use Mail;
use Auth;
use Cache;
use Intervention\Image\Facades\Image;
use File;

class FrontController extends Controller
{
    use \App\Traits\MailInfo;

    public function index()
    {
        $sliders = DB::table('sliders')->orderBy('order', 'asc')->get();

        $ecommerce_setting = Cache::get('ecommerce_setting');

        if(isset($ecommerce_setting->home_page)) {
            $home = $ecommerce_setting->home_page;
        }

        if(isset($home)){
            $page = DB::table('pages')->where('id',$home)->first();

            if(isset($page)){
                if($page->template == 'home'){
                    $widgets = DB::table('page_widgets')->where('page_id',$home)->orderBy('order','ASC')->get();
                }

                $recently_viewed = [];
                if(session()->has('recently_viewed')){
                    $recently_viewed = session()->get('recently_viewed');
                    // dd($recently_viewed);
                }
                try{
                $recently_viewed = array_map('intval', $recently_viewed);
        $recently_viewed = array_filter($recently_viewed, function ($value) {
    return $value !== 0; // Exclude 0 values
});
}
catch(\Exception $e){
        $recently_viewed = collect();

}

                return view('ecommerce::frontend/home', compact('sliders', 'widgets', 'recently_viewed'));
            }
        }

        return view('ecommerce::frontend/home', compact('sliders'));
    }

    public function page($slug)
    {
        $page = DB::table('pages')->where('slug', $slug)->where('status', 1)->first();

        if(isset($page)){
            if($page->template == 'faq'){
                $categories = DB::table('faq_categories')->orderBy('order','ASC')->get();
                $faqs = DB::table('faqs')->orderBy('order','ASC')->get();
                return view('ecommerce::frontend.faq', compact('page','faqs','categories'));
            }

            if($page->template == 'contact'){
                return view('ecommerce::frontend.contact', compact('page'));
            }
        }

        return view('ecommerce::frontend.page-show', compact('page'));
    }

    public function blog()
    {
        $blogs = DB::table('blogs')->get();

        return view('ecommerce::frontend.blog', compact('blogs'));
    }

    public function blogPost($slug)
    {
        $post = DB::table('blogs')->where('slug', $slug)->first();

        return view('ecommerce::frontend.blog-details', compact('post'));
    }

    public function trackOrder($order_id='',$email='')
    {
        if(($order_id != '') && ($email != '')){
            $customer = DB::table('customers')->where('email',$email)->first();
            $sale = DB::table('sales')->where('customer_id',$customer->id)->where('reference_no',$order_id)->first();
            $delivery = DB::table('deliveries')->where('sale_id',$sale->id)->first();
            $product_sales = DB::table('product_sales')
                             ->join('products','product_sales.product_id','=','products.id')
                             ->select('products.name','products.image','products.is_variant','products.variant_option','product_sales.*')
                             ->where('sale_id',$sale->id)
                             ->get();
            if(!isset($delivery)){
                $delivery = 0;
            }
            return view('ecommerce::frontend.track-order', compact('sale','customer','delivery','product_sales'));
        } else {
            return view('ecommerce::frontend.track-order');
        }
    }

    public function search($product)
    {
        $search = $product;
        $data = DB::table('products')->select('id', 'image', 'name', 'slug','in_stock','qty','price','promotion_price','promotion','last_date','is_variant')
            ->where('is_active', 1)
            ->where('is_online', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tags', 'LIKE', '%' . $search . '%');
            })
            ->get();

        return response()->json($data);
    }

    public function searchProduct(Request $request)
    {
        $search = htmlspecialchars($request->input('search'));
        $products = DB::table('products')->where('is_active', 1)->where('is_online', 1)
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('tags', 'LIKE', '%' . $search . '%');
                    })
                    ->get();

        return view('ecommerce::frontend/products-search', compact('products', 'search'));
    }

    public function productDetails($product_name, $product_id)
    {
        // dd('aasda', $product_id);
        // $product_id = [$product_id];
        try{
             if (is_array($product_id)) {
                    $product_id = array_map('intval', $product_id);
                    $product_id = array_filter($product_id, function ($value) {
                return $value !== 0; // Exclude 0 values

            });
            }

        }
        catch(\Exception $e){
            $product_id = collect();
        }

        $recently_viewed = session()->has('recently_viewed') ? session()->get('recently_viewed') : [];
        try{
                $recently_viewed = array_map('intval', $recently_viewed);
                        $recently_viewed = array_filter($recently_viewed, function ($value) {
                    return $value !== 0; // Exclude 0 values
                });
                }
                catch(\Exception $e){
                        $recently_viewed = collect();

                }
        // dd($recently_viewed,$product_id);
        if(!array_key_exists($product_id, $recently_viewed)) {
            array_push($recently_viewed,$product_id);
            session(['recently_viewed' => $recently_viewed]);
    	}

        $product = DB::table('products')
                   ->where('id', $product_id)
                   ->where('is_active', 1)
                   ->where('is_online', 1)
                   ->first();


        if($product->variant_option) {
            $product->variant_option = json_decode($product->variant_option);
            $product->variant_value = json_decode($product->variant_value);
        }

        $brand = DB::table('brands')->where('id',$product->brand_id)->first();

        $categories = Cache::get('category_list');
        $category = $categories->where('id',$product->category_id)->first();

        try {
                $product_arr = explode(',',$product->related_products);
                // dd($product->related_products);
                if($product->related_products!=null)
                {

                $related_products = DB::table('products')->whereIn('id',$product_arr)->get();
                }
                else
                {
                $related_products = collect();
                }

        } catch (\Exception $e) {
            $related_products = collect();
        }

        // dd($related_products);
        $recently_viewed = [];
        if(session()->has('recently_viewed')){
            $recently_viewed = session()->get('recently_viewed');
        }

        // if($product->is_variant == 1){
            $variant = DB::table('product_variants')
                       ->join('variants','product_variants.variant_id','=','variants.id')
                       ->select('name','qty')
                       ->where('product_id',$product->id)
                       ->get();
            //return $variant;
            // dd($variant);

            // return view('ecommerce::frontend/product-details', compact('product','brand','category','related_products','recently_viewed','variant'));
        // }

        $ratings = DB::table('ratings')
                ->where('product_id', $product_id)
                ->leftJoin('customers', 'ratings.customer_id', '=', 'customers.id') // Join the customers table
                ->select('ratings.*', 'customers.name as customer_name')
                ->get();


        return view('ecommerce::frontend/product-details', compact('product','brand','category','related_products','recently_viewed','variant','ratings'));

    }

    public function productModal($product_id)
    {
        $product = DB::table('products')
                   ->where('id', $product_id)
                   ->where('is_active', 1)
                   ->where('is_online', 1)
                   ->first();

        if($product->variant_option) {
            $product->variant_option = json_decode($product->variant_option);
            $product->variant_value = json_decode($product->variant_value);
        }

        return $product;
    }

    public function allProducts()
    {
        $data = Product::select('id', 'image', 'name', 'price', 'promotion_price')->where('is_active', 1)->where('is_online', 1)->take(10)->get();

        return response()->json($data);
    }

//     public function category($category, Request $request)
//     {
// // dd('adsad');
//         $queryParams = $request->query();
//         $category = DB::table('categories')
//                         ->select('id','name','slug','image','page_title','short_description','content')
//                         ->where('slug', $category)
//                         ->where('is_active', 1)->first();

//         $sub_categories = DB::table('categories')
//                             ->select('id','name','slug','page_title','short_description')
//                             ->where('parent_id', $category->id)
//                             ->where('is_active', 1)->get();


//         if (count($sub_categories) > 0) {
//             $sub_cats = [];
//             foreach($sub_categories as $cat){
//                 array_push($sub_cats, $cat->id);
//             }

//             $products = DB::table('products')
//                             ->where('is_active', 1)
//                             // ->where('is_online', 1)
//                             //->where('is_variant', 1)
//                             ->where(function($query) use ($category,$sub_cats){
//                                 $query->where('category_id', $category->id);
//                                 $query->orWhereIn('category_id',$sub_cats);
//                             });

//             // Loop through each query parameter to build flexible conditions
//             foreach ($queryParams as $key => $value) {
//                 $valuesArray = explode(',', $value); // Split the parameter values into an array

//                 $products->where(function ($query) use ($key, $valuesArray) {
//                     $query->whereRaw("variant_option LIKE ?", ["%\"$key\"%"]);

//                     // Apply OR conditions for each individual value
//                     $query->where(function ($subQuery) use ($valuesArray) {
//                         foreach ($valuesArray as $val) {
//                             $subQuery->orWhereRaw("variant_value LIKE ?", ["%$val%"]);
//                         }
//                     });
//                 });
//             }

//             // Execute the query and paginate results
//             $products = $products->paginate(100);

//             if ($request->ajax()) {
//                 $view = view('ecommerce::frontend/products-load-more', compact('products'))->render();
//                 return response()->json(['html' => $view]);
//             }

//             $variants = DB::table('products')
//                             ->where('is_active', 1)
//                             ->where('is_online', 1)
//                             ->where('is_variant', 1)
//                             ->where(function ($query) use ($category, $sub_cats) {
//                                 $query->where('category_id', $category->id)
//                                     ->orWhereIn('category_id', $sub_cats);
//                             })
//                             ->get(['variant_option', 'variant_value']);

//             return view('ecommerce::frontend/products', compact('products', 'category','variants'));

//         } else {
//             $products = DB::table('products')
//                             ->where('category_id', $category->id)
//                             ->where('is_active', 1)
//                             // ->where('is_online', 1)
//                             // ->where('is_variant', 1)
//                             ;

//             foreach ($queryParams as $key => $value) {
//                 $valuesArray = explode(',', $value); // Split the parameter values into an array

//                 $products->where(function ($query) use ($key, $valuesArray) {
//                     $query->whereRaw("variant_option LIKE ?", ["%\"$key\"%"]);

//                     // Apply OR conditions for each individual value
//                     $query->where(function ($subQuery) use ($valuesArray) {
//                         foreach ($valuesArray as $val) {
//                             $subQuery->orWhereRaw("variant_value LIKE ?", ["%$val%"]);
//                         }
//                     });
//                 });
//             }

//             // Execute the query and paginate results
//             $products = $products->paginate(100);

//             if ($request->ajax()) {
//                 $view = view('ecommerce::frontend/products-load-more', compact('products'))->render();
//                 return response()->json(['html' => $view]);
//             }

//             $variants = DB::table('products')
//                         ->where('category_id', $category->id)
//                         ->where('is_active', 1)
//                         ->where('is_online', 1)
//                         ->where('is_variant', 1)
//                         ->get(['variant_option', 'variant_value']);

//             return view('ecommerce::frontend/products', compact('products', 'category','variants'));
//         }
//     }

    public function category($category, Request $request)
        {
            $queryParams = $request->query();
            // dd($queryParams);
            $category = DB::table('categories')
                            ->select('id','name','slug','image','page_title','short_description','content')
                            ->where('slug', $category)
                            ->where('is_active', 1)
                            ->first();

            $sub_categories = DB::table('categories')
                                ->select('id','name','slug','page_title','short_description')
                                ->where('parent_id', $category->id)
                                ->where('is_active', 1)
                                ->get();

            $brands = Brand::where('is_active', 1)->get(); // Fetch all active brands

            if (count($sub_categories) > 0) {
                $sub_cats = [];
                foreach($sub_categories as $cat){
                    array_push($sub_cats, $cat->id);
                }

                $products = DB::table('products')
                                ->where('is_active', 1)
                                ->where(function($query) use ($category, $sub_cats) {
                                    $query->where('category_id', $category->id);
                                    $query->orWhereIn('category_id', $sub_cats);
                                });

                // Apply brand filter if a brand query parameter is present
                if ($request->has('brand')) {
                    $brandIds = explode(',', $request->query('brand')); // Get the brand IDs from query parameter
                    $products->whereIn('brand_id', $brandIds); // Filter products by brand(s)
                }

                // dd($products->get());

                // Loop through each query parameter to build flexible conditions for variants
                foreach ($queryParams as $key => $value) {
                    if ($key != 'brand') { // Skip brand filtering since we already handled it
                        $valuesArray = explode(',', $value); // Split the parameter values into an array

                        $products->where(function ($query) use ($key, $valuesArray) {
                            $query->whereRaw("variant_option LIKE ?", ["%\"$key\"%"]);

                            // Apply OR conditions for each individual value
                            $query->where(function ($subQuery) use ($valuesArray) {
                                foreach ($valuesArray as $val) {
                                    $subQuery->orWhereRaw("variant_value LIKE ?", ["%$val%"]);
                                }
                            });
                        });
                    }
                }

                // Execute the query and paginate results
                $products = $products->paginate(100);

                if ($request->ajax()) {
                    $view = view('ecommerce::frontend/products-load-more', compact('products'))->render();
                    return response()->json(['html' => $view]);
                }

                $variants = DB::table('products')
                                ->where('is_active', 1)
                                ->where('is_variant', 1)
                                ->get(['variant_option', 'variant_value']);

                return view('ecommerce::frontend/products', compact('products', 'category', 'variants', 'brands'));
            } else {
                $products = DB::table('products')
                                ->where('category_id', $category->id)
                                ->where('is_active', 1);

                // Apply brand filter if a brand query parameter is present
                if ($request->has('brand')) {
                    $brandIds = explode(',', $request->query('brand')); // Get the brand IDs from query parameter
                    $products->whereIn('brand_id', $brandIds); // Filter products by brand(s)
                }

                // Loop through each query parameter to build flexible conditions for variants
                foreach ($queryParams as $key => $value) {
                    if ($key != 'brand') { // Skip brand filtering since we already handled it
                        $valuesArray = explode(',', $value); // Split the parameter values into an array

                        $products->where(function ($query) use ($key, $valuesArray) {
                            $query->whereRaw("variant_option LIKE ?", ["%\"$key\"%"]);

                            // Apply OR conditions for each individual value
                            $query->where(function ($subQuery) use ($valuesArray) {
                                foreach ($valuesArray as $val) {
                                    $subQuery->orWhereRaw("variant_value LIKE ?", ["%$val%"]);
                                }
                            });
                        });
                    }
                }

                // Execute the query and paginate results
                $products = $products->paginate(100);

                if ($request->ajax()) {
                    $view = view('ecommerce::frontend/products-load-more', compact('products'))->render();
                    return response()->json(['html' => $view]);
                }

                $variants = DB::table('products')
                            ->where('category_id', $category->id)
                            ->where('is_active', 1)
                            ->where('is_variant', 1)
                            ->get(['variant_option', 'variant_value']);

                return view('ecommerce::frontend/products', compact('products', 'category', 'variants', 'brands'));
            }
        }

    public function shop()
    {
        $categories = cache('category_list')->where('parent_id', Null);

        return view('ecommerce::frontend.shop', compact('categories'));
    }

    public function collections()
    {
        $collections = DB::table('collections')->where('status', 1)->get();

        $product_ids = $collections // Replace with your actual table name
                        ->pluck('products') // Get the 'products' column values as a collection
                        ->flatMap(function ($item) {
                            return explode(',', $item); // Split each row's products by comma
                        })
                        ->unique() // Remove duplicate entries
                        ->values() // Reset keys
                        ->toArray();
        $products = DB::table('products')
                    ->select('id', 'image', 'name', 'slug','in_stock','qty','price','promotion_price','promotion','last_date','is_variant')
                    ->whereIn('id',$product_ids)
                    ->where('is_active', 1)
                    ->where('is_online', 1)
                    ->get();

        return view('ecommerce::frontend.collections', compact('collections','products'));
    }

    public function collectionProducts($collection, Request $request)
    {
        $collections = DB::table('collections')->where('status', 1)->get();

        $collection = $collections->where('slug', $collection)->where('status', 1)->first();

        $product_arr = explode(',',$collection->products);

        $products = DB::table('products')
                    ->select('id', 'image', 'name', 'slug','in_stock','qty','price','promotion_price','promotion','last_date','is_variant')
                    ->whereIn('id', $product_arr)
                    ->where('is_active', 1)
                    ->where('is_online', 1)
                    ->get();

        return view('ecommerce::frontend.collection-products', compact('products', 'collection', 'collections'));
    }

    public function brandProducts($brand, Request $request)
    {
        $brand = DB::table('brands')->where('slug', $brand)->where('is_active', 1)->first();

        $products = DB::table('products')->where('brand_id', $brand->id)
                    ->where('is_active', 1)
                    ->where('is_online', 1)
                    ->paginate(5);

        if ($request->ajax()) {
            $view = view('ecommerce::frontend/brand-products-load-more', compact('products'))->render();
            return response()->json(['html' => $view]);
        }

        return view('ecommerce::frontend/brand-products', compact('products', 'brand'));
    }

    public function contactMail(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
        ];

        $email_to = Cache::get('ecommerce_setting')->contact_form_email;

        $mail_setting = MailSetting::latest()->first();
        $this->setMailInfo($mail_setting);
        Mail::to($email_to)->send(new ContactUs($data));

        return response()->json('success');
    }

    public function newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => 'required|unique:newsletter,email',
        ]);

        if($validator->fails()) {
            $messages = $validator->messages();
            return $validator->errors();
        } else {

            $data = $request->except('_token');
            DB::table('newsletter')->insert($data);

            return response()->json('success');
        }
    }

    public function sessionRenew(Request $request)
    {
        return response()->json('success');
    }

}

<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\PaymentWithCreditCard;
use App\Models\PaymentWithGiftCard;
use App\Models\PaymentWithPaypal;
use App\Models\Product_Sale;
use Auth;
use Session;
use DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        return view('ecommerce::frontend.dashboard', compact('customer'));
    }

	public function orders()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if($customer){
            $sales = Sale::select('id','reference_no','grand_total','shipping_cost','coupon_discount','sale_status','created_at')->where('customer_id',$customer->id)->orderBy('created_at','DESC')->get();

            return view('ecommerce::frontend.orders', compact('customer', 'sales'));
        }

        return view('ecommerce::frontend.orders');
    }


// madfhjhfasjdkfjaskdf dfhaskdjfh adfjhasjkfh asdfjdhasfjhasfds sefsedf dfgdg


public function getOrderProducts(Request $request)
{
    $customer = Customer::where('user_id', Auth::id())->first();

    if (!$customer) {
        return response()->json(['message' => 'Customer not found'], 404);
    }

    $products = Product_Sale::select(
            'product_sales.product_id',
            'products.name',
            'product_sales.qty',
            'product_sales.net_unit_price',
            'ratings.value as rating' // Fetch rating directly
        )
        ->join('products', 'products.id', '=', 'product_sales.product_id')
        ->leftJoin('ratings', function ($join) use ($customer, $request) {
            $join->on('ratings.product_id', '=', 'product_sales.product_id')
                 ->where('ratings.customer_id', '=', $customer->id)
                 ->where('ratings.sale_id', '=', $request->order_id);
        })
        ->where('product_sales.Sale_id', $request->order_id) 
        ->get();

    return response()->json(['products' => $products]);
}














    public function rating()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if($customer){
            $sales = Sale::select('id','reference_no','grand_total','shipping_cost','coupon_discount','sale_status','created_at')->where('customer_id',$customer->id)->orderBy('created_at','DESC')->get();

            return view('ecommerce::frontend.rating', compact('customer', 'sales'));
        }

        return view('ecommerce::frontend.rating');
    }
    public function submitRating(Request $request)
{
    // Validate request data
    $request->validate([
        'order_id' => 'required|integer',
        'ratings'  => 'required|array',
    ]);

    // Get the customer
    $customer = Customer::where('user_id', Auth::id())->first();

    if (!$customer) {
        return response()->json(['message' => 'Customer not found'], 404);
    }

    // Fetch products from the given order
    $products = Product_Sale::where('Sale_id', $request->order_id)->pluck('product_id')->toArray();

    if (empty($products)) {
        return response()->json(['message' => 'No products found in order'], 404);
    }

    // Process ratings for each product
    foreach ($request->ratings as $productId => $ratingValue) {
        // Check if the product exists in the order
        if (!in_array($productId, $products)) {
            continue;
        }

        // Check if the rating already exists
        $rating = Rating::where('product_id', $productId)
                        ->where('customer_id', $customer->id)
                        ->where('sale_id', $request->order_id)
                        ->first();

        if ($rating) {
            // Update existing rating
            $rating->update(['value' => $ratingValue]);
        } else {
            // Create a new rating entry
            Rating::create([
                'product_id'   => $productId,
                'customer_id'  => $customer->id,
                'sale_id'      => $request->order_id,
                'value'        => $ratingValue,
            ]);
        }
    }

    return response()->json(['message' => 'Ratings submitted successfully']);
}




    public function orderDetails($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $sale = Sale::select('id','reference_no','grand_total','shipping_cost','coupon_discount','sale_status','created_at')->where('id',$id)->where('customer_id',$customer->id)->first();
        $products = Product_Sale::select('products.name', 'product_sales.qty', 'product_sales.net_unit_price')->join('products','products.id','=','product_sales.product_id')->where('Sale_id', $id)->get();
        return view('ecommerce::frontend.order-details', compact('products', 'sale'));
    }

    public function orderCancel($id)
    {
        $sale = Sale::find($id);
        $sale->sale_status = 3;
        $sale->save();

        return redirect()->back()->with('message', 'You have canceled your order.');
    }

    public function wishlist()
    {
        $customer = Customer::select('id','wishlist','user_id')->where('user_id', Auth::id())->first();
        if(isset($customer->wishlist)){
            $products = DB::table('products')->whereIn('id',explode(',', $customer->wishlist))->get();
            return view('ecommerce::frontend.wishlist', compact('products'));
        }
        return view('ecommerce::frontend.wishlist');
    }

    public function addToWishlist($product_id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if(isset($customer->wishlist)){
            if(in_array($product_id,explode(',',$customer->wishlist))){
                return response()->json('fail');
            }
            $customer->wishlist = $customer->wishlist.$product_id.',';
        } else {
            $customer->wishlist = $product_id.',';
        }
        $customer->save();

        return response()->json('success');
    }

    public function deleteFromWishlist($product_id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $customer->wishlist = str_replace($product_id.',', '', $customer->wishlist);
        $customer->save();

        return response()->json('success');
    }

    public function address()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $addresses = DB::table('customer_addresses')->where('customer_id',$customer->id)->get();
        return view('ecommerce::frontend.addresses', compact('customer','addresses'));
    }


    public function addressCreate(Request $request)
    {
        $data = [
            'name'              => trim(htmlspecialchars($request->input('name'))),
            'phone'             => trim(htmlspecialchars($request->input('phone'))),
            'address'           => trim(htmlspecialchars($request->input('address'))),
            'state'             => trim(htmlspecialchars($request->input('state'))),
            'city'              => trim(htmlspecialchars($request->input('city'))),
            'country'           => trim(htmlspecialchars($request->input('country'))),
            'zip'               => trim(htmlspecialchars($request->input('zip'))),
            'customer_id'       => $request->input('customer_id'),
        ];

        DB::table('customer_addresses')->insert($data);

        Session::flash('message', 'Address inserted');
        Session::flash('type', 'success');

        return redirect()->back();

    }

    public function addressDefault($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $addresses = DB::table('customer_addresses')->where('customer_id',$customer->id)->update(['default' => 0]);
        DB::table('customer_addresses')->where('id',$id)->update(['default' => 1]);

        $default = DB::table('customer_addresses')->where('id',$id)->first();

        $customer->address = $default->address;
        $customer->city = $default->city;
        $customer->state = $default->state;
        $customer->country = $default->country;
        $customer->postal_code = $default->zip;
        $customer->save();

        return redirect()->back();
    }

    public function addressEdit($id)
    {
        $address = DB::table('customer_addresses')->where('id',$id)->first();
        return $address;
    }

    public function addressUpdate(Request $request)
    {
        $data = $request->except('_token');
        DB::table('customer_addresses')->where('id',$request->id)->update($data);
        $default = DB::table('customer_addresses')->where('id',$request->id)->where('default',1)->first();
        if(isset($default)){
            $customer = Customer::where('user_id', Auth::id())->first();
            $customer->address = $default->address;
            $customer->city = $default->city;
            $customer->state = $default->state;
            $customer->country = $default->country;
            $customer->postal_code = $default->zip;
            $customer->save();
        }

        Session::flash('message', 'Address updated');
        Session::flash('type', 'success');
        return redirect()->back();
    }

    public function addressDelete($id)
    {
        DB::table('customer_addresses')->where('id',$id)->delete();

        Session::flash('message', 'Address deleted');
        Session::flash('type', 'success');
        return redirect()->back();
    }

    public function accountDetails()
    {
        $customer = Customer::select('id')->where('user_id', Auth::id())->first();
        return view('ecommerce::frontend.account-details', compact('customer'));
    }

    public function updateAccountDetails(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required'
        ]);

        $id = auth()->user()->id;

        $user = User::find($id);
        $user->name = trim(htmlspecialchars($request->input('name')));
        $user->email = trim(htmlspecialchars($request->input('email')));
        $user->phone = trim(htmlspecialchars($request->input('phone')));
        $user->save();

        $customer = Customer::where('user_id',$id)->first();
        $customer->name = $user->name;
        $customer->email = $user->email;
        $customer->phone_number = $user->phone;
        $customer->save();

        Session::flash('message', 'Details updated');
        Session::flash('type', 'success');

        return redirect()->back();
    }
}

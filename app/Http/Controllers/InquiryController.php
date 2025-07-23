<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Terms;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Biller;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Tax;
use App\Models\Quotation;
use App\Models\Inquiry;
use App\Models\Delivery;
use App\Models\PosSetting;
use App\Models\ProductQuotation;
use App\Models\Product_Warehouse;
use App\Models\ProductVariant;
use App\Models\ProductBatch;
use App\Models\Variant;
use DB;
use Illuminate\Support\Facades\Session;
use NumberToWords\NumberToWords;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\QuotationDetails;
use Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\MailSetting;
use App\Traits\MailInfo;
use App\Traits\StaffAccess;
use App\Traits\TenantInfo;
use Illuminate\Support\Carbon;


class InquiryController extends Controller
{
    use TenantInfo, MailInfo, StaffAccess;

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Inquiry::query();

            if ($request->name) {
                $data->where('name', 'LIKE', '%' . $request->name . '%');
            }

            if ($request->phone) {
                $data->where('phone', 'LIKE', '%' . $request->phone . '%');
            }

            if ($request->company_name) {
                $data->where('company_name', 'LIKE', '%' . $request->company_name . '%');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('inquiries.show', $row->id) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.inquiry.index');
    }
public function getData(Request $request)
{

    $data = Inquiry::query();

    return DataTables::of($data)
        ->filter(function ($query) use ($request) {
            if ($search = $request->get('search')['value']) {
                $query->where('company_name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('contact_number', 'like', "%{$search}%");
            }
        })
        ->addColumn('action', function ($row) {
            return '<a href="' . route('inquiries.show', $row->id) . '" class="btn btn-sm btn-info">View</a>';
        })
        ->make(true);
}


    public function inquiryData(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $query = Inquiry::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
    }

    $totalFiltered = $query->count();
    $inquiries = $query->offset($start)->limit($length)->get();

    $data = [];

    foreach ($inquiries as $index => $inquiry) {
        $data[] = [
            'key' => $start + $index + 1,
            'date' => Carbon::parse($inquiry->created_at)->format('Y-m-d'),
            'company_name' => $inquiry->company->name ?? "Null",
            'contact_person' => $inquiry->contact_person??'Null',
            'contact_number' => $inquiry->contact_number??'Null',
            'email' => $inquiry->email,
            'head_office' => $inquiry->head_office,
            'factory' => $inquiry->factory,
            'requirement' => $inquiry->product->name??'null',
            'options' => '
                <a href="' . route('inquiry.create_quotation', $inquiry->id) . '" class="btn btn-sm btn-success" target="_blank" style="margin-bottom: 4px;">Create Quotation</a>
                <a href="' . route('inquiry.print', $inquiry->id) . '" class="btn btn-sm btn-success printBtn" target="_blank">Print</a>
                <button class="btn btn-sm btn-danger delete-inquiry" data-id="' . $inquiry->id . '" style="margin-top: -3px;">Delete</button>
            '
        ];
    }

    return response()->json([
        'draw' => intval($draw),
        'recordsTotal' => Inquiry::count(),
        'recordsFiltered' => $totalFiltered,
        'data' => $data,
    ]);
}
    public function create()
    {
        $products = Product::where('is_active', 1)->get();
        $customers = Customer::all();

        // $areas = $customers->pluck('address')->unique()->filter()->values();
        $areas = $customers->pluck('address')->unique()->filter()->values();
        $groupNamesByArea = [];

        foreach ($areas as $area) {
            $groupNamesByArea[$area] = Customer::where('address', $area)
                                               ->pluck('city')
                                               ->unique()
                                               ->values();
    }
// dd($areas);
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo(permission: 'quotes-add')){
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_supplier_list = Supplier::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $companies = Company::where('is_active', true)->get();


            return view('backend.inquiry.create', compact('customers', 'areas', 'groupNamesByArea', 'products','lims_biller_list', 'lims_warehouse_list', 'lims_customer_list', 'lims_supplier_list', 'lims_tax_list','companies'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    public function getGroupNames(Request $request)
{
    // dd('aaa', $request->all());
    $groupNames = Customer::where('address', $request->area)
                    ->pluck('city')
                    ->unique()
                    ->values();
                    // dd('bbb',$groupNames);

    return response()->json($groupNames);
}

public function getCompanyNames(Request $request)
{
    $companyNames = Customer::where('address', $request->area)
                    ->where('city', $request->group_name)
                    ->pluck('company_name')
                    ->unique()
                    ->values();

    return response()->json($companyNames);
}

    public function getContactPerson(Request $request)
    {
        $contactPersons = Customer::where('company_name', $request->company_name)
            ->select('id', 'name', 'phone_number','postal_code')
            ->get()
            ->unique('id') // Optional, if duplicates exist
            ->values();

        return response()->json($contactPersons);
    }

    public function store(Request $request){

        // dd($request->all());

        $data = $request->validate([
            'date' => 'nullable|date',
            'company_name' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'customer_id' => 'nullable',
            'contact_number' => 'nullable|string',
            'email' => 'nullable|email',
            'head_office' => 'nullable|string',
            'factory' => 'nullable|string',
            'requirement' => 'nullable|array',
            'reffer' => 'nullable|string',
            'remark' => 'nullable|string',
            'department_id' =>'nullable'
        ]);

        $data['user_id'] = Auth::id();
        $data['requirement'] = implode(', ', $request->requirement);
        $data['warehouse_id'] = 1;
        Inquiry::create($data);
        return redirect()->route('inquiries.index')->with('success','Inquiry created successfully');
    }

    public function sendMail(Request $request)
    {
        $data = $request->all();
        $lims_quotation_data = Quotation::find($data['quotation_id']);
        $lims_product_quotation_data = ProductQuotation::where('quotation_id', $data['quotation_id'])->get();
        $lims_customer_data = Customer::find($lims_quotation_data->customer_id);
        $mail_setting = MailSetting::latest()->first();

        if(!$mail_setting) {
            $message = 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
        }else if(!$lims_customer_data->email) {
            $message = 'Customer doesnt have email!';
        }
        else if($lims_customer_data->email && $mail_setting) {
            //collecting male data
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_quotation_data->reference_no;
            $mail_data['total_qty'] = $lims_quotation_data->total_qty;
            $mail_data['total_price'] = $lims_quotation_data->total_price;
            $mail_data['order_tax'] = $lims_quotation_data->order_tax;
            $mail_data['order_tax_rate'] = $lims_quotation_data->order_tax_rate;
            $mail_data['order_discount'] = $lims_quotation_data->order_discount;
            $mail_data['shipping_cost'] = $lims_quotation_data->shipping_cost;
            $mail_data['grand_total'] = $lims_quotation_data->grand_total;

            foreach ($lims_product_quotation_data as $key => $product_quotation_data) {
                $lims_product_data = Product::find($product_quotation_data->product_id);
                if($product_quotation_data->variant_id) {
                    $variant_data = Variant::find($product_quotation_data->variant_id);
                    $mail_data['products'][$key] = $lims_product_data->name . ' [' . $variant_data->name . ']';
                }
                else
                    $mail_data['products'][$key] = $lims_product_data->name;
                if($product_quotation_data->sale_unit_id){
                    $lims_unit_data = Unit::find($product_quotation_data->sale_unit_id);
                    $mail_data['unit'][$key] = $lims_unit_data->unit_code;
                }
                else
                    $mail_data['unit'][$key] = '';

                $mail_data['qty'][$key] = $product_quotation_data->qty;
                $mail_data['total'][$key] = $product_quotation_data->total;
            }
            $this->setMailInfo($mail_setting);
            try{
                Mail::to($mail_data['email'])->send(new QuotationDetails($mail_data));
                $message = 'Mail sent successfully';
            }
            catch(\Exception $e){
                $message = 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }

        return redirect()->back()->with('message', $message);
    }


    public function sendWhatsApp(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        $lims_sale_data = Quotation::find($data['sale_id']);

        if (!$lims_sale_data) {
            return response()->json(['success' => false, 'message' => 'Quotation not found!']);
        }

        $lims_customer_data = Customer::find($lims_sale_data->customer_id);

        if (!$lims_customer_data || !$lims_customer_data->phone_number) {
            return response()->json(['success' => false, 'message' => 'Customer does not have a phone number!']);
        }


        // Get status text
        $message = "🛒 Quation Details\n";
        $message .= "📧 Email: " . $lims_customer_data->email . "\n";
        $message .= "📌 Reference No: " . $lims_sale_data->reference_no . "\n";
        $message .= "📊 Total Qty: " . $lims_sale_data->total_qty . "\n";
        $message .= "💰 Total Price: " . number_format($lims_sale_data->total_price, 2) . "\n";
        $message .= "🛃 Order Tax: " . $lims_sale_data->order_tax . "\n";
        $message .= "🎁 Order Discount: " . $lims_sale_data->order_discount . "\n";
        $message .= "🚚 Shipping Cost: " . $lims_sale_data->shipping_cost . "\n";
        $message .= "💵 Grand Total: " . number_format($lims_sale_data->grand_total, 2) . "\n";
        $message .= "🙏 Please let us know as soon as possible!";

        $phone = preg_replace('/\D/', '', $lims_customer_data->phone_number);
        if (strpos($phone, '880') !== 0) {
            $phone = '880' . ltrim($phone, '0');
        }

        return response()->json([
            'success' => true,
            'phone' => $phone,
            'message' => $message
        ]);
    }

    public function getCustomerGroup($id)
    {
         $lims_customer_data = Customer::find($id);
         $lims_customer_group_data = CustomerGroup::find($lims_customer_data->customer_group_id);
         return $lims_customer_group_data->percentage;
    }

    public function getProduct($id)
    {
        $product_code = [];
        $product_name = [];
        $product_qty = [];
        $product_price = [];
        $product_data = [];
        $batch_no = [];
        $product_batch_id = [];
        $expired_date = [];
        $is_embeded = [];
        $imei_number = [];

        //retrieve data of product without variant
        $lims_product_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])
        ->whereNull('product_warehouse.variant_id')
        ->whereNull('product_warehouse.product_batch_id')
        ->select('product_warehouse.*')
        ->get();

        foreach ($lims_product_warehouse_data as $product_warehouse)
        {
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $lims_product_data = Product::find($product_warehouse->product_id);
            $product_code[] =  $lims_product_data->code;
            $product_name[] = $lims_product_data->name;
            $product_type[] = $lims_product_data->type;
            $product_id[] = $lims_product_data->id;
            $product_list[] = null;
            $qty_list[] = null;
            $batch_no[] = null;
            $product_batch_id[] = null;
            $expired_date[] = null;
            if($product_warehouse->is_embeded)
                $is_embeded[] = $product_warehouse->is_embeded;
            else
                $is_embeded[] = 0;
            $imei_number[] = null;
        }

        $lims_product_with_imei_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['products.is_imei', true],
            ['product_warehouse.warehouse_id', $id],
            ['product_warehouse.qty', '>', 0]
        ])
        ->whereNull('product_warehouse.variant_id')
        ->whereNotNull('product_warehouse.imei_number')
        ->select('product_warehouse.*', 'products.is_embeded')
        ->groupBy('product_warehouse.product_id')
        ->get();

        config()->set('database.connections.mysql.strict', false);
        \DB::reconnect(); //important as the existing connection if any would be in strict mode

        $lims_product_with_batch_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])
        ->whereNull('product_warehouse.variant_id')
        ->whereNotNull('product_warehouse.product_batch_id')
        ->select('product_warehouse.*')
        ->groupBy('product_warehouse.product_id')
        ->get();

        //now changing back the strict ON
        config()->set('database.connections.mysql.strict', true);
        \DB::reconnect();

        foreach ($lims_product_with_batch_warehouse_data as $product_warehouse)
        {
            $product_qty[] = $product_warehouse->qty;
            $product_price[] = $product_warehouse->price;
            $lims_product_data = Product::find($product_warehouse->product_id);
            $product_code[] =  $lims_product_data->code;
            $product_name[] = $lims_product_data->name;
            $product_type[] = $lims_product_data->type;
            $product_id[] = $lims_product_data->id;
            $product_list[] = null;
            $qty_list[] = null;
            $product_batch_data = ProductBatch::select('id', 'batch_no')->find($product_warehouse->product_batch_id);
            $batch_no[] = $product_batch_data->batch_no;
            $product_batch_id[] = $product_batch_data->id;
            $expired_date[] = null;
            if($product_warehouse->is_embeded)
                $is_embeded[] = $product_warehouse->is_embeded;
            else
                $is_embeded[] = 0;
            $imei_number[] = null;
        }

          //product with imei
          foreach ($lims_product_with_imei_warehouse_data as $product_warehouse)
          {
              $imei_numbers = explode(",", $product_warehouse->imei_number);
              foreach ($imei_numbers as $key => $number) {
                  $product_qty[] = $product_warehouse->qty;
                  $product_price[] = $product_warehouse->price;
                  $lims_product_data = Product::find($product_warehouse->product_id);
                  $product_code[] =  $lims_product_data->code;
                  $product_name[] = htmlspecialchars($lims_product_data->name);
                  $product_type[] = $lims_product_data->type;
                  $product_id[] = $lims_product_data->id;
                  $product_list[] = $lims_product_data->product_list;
                  $qty_list[] = $lims_product_data->qty_list;
                  $batch_no[] = null;
                  $product_batch_id[] = null;
                  $expired_date[] = null;
                  $is_embeded[] = 0;
                  $imei_number[] = $number;
              }
          }

        //retrieve data of product with variant
        $lims_product_warehouse_data = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->where([
            ['products.is_active', true],
            ['product_warehouse.warehouse_id', $id],
        ])->whereNotNull('product_warehouse.variant_id')->select('product_warehouse.*')->get();
        foreach ($lims_product_warehouse_data as $product_warehouse)
        {
            $lims_product_data = Product::find($product_warehouse->product_id);
            $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_warehouse->product_id, $product_warehouse->variant_id)->first();
            if($lims_product_variant_data) {
                $product_qty[] = $product_warehouse->qty;
                $product_code[] =  $lims_product_variant_data->item_code;
                $product_name[] = $lims_product_data->name;
                $product_type[] = $lims_product_data->type;
                $product_id[] = $lims_product_data->id;
                $product_list[] = null;
                $qty_list[] = null;
                $batch_no[] = null;
                $product_batch_id[] = null;
            }
            $expired_date[] = null;
            if($product_warehouse->is_embeded)
                $is_embeded[] = $product_warehouse->is_embeded;
            else
                $is_embeded[] = 0;
            $imei_number[] = null;
        }
        //retrieve product data of digital and combo
        $lims_product_data = Product::whereNotIn('type', ['standard'])->where('is_active', true)->get();
        foreach ($lims_product_data as $product)
        {
            $product_qty[] = $product->qty;
            $product_code[] =  $product->code;
            $product_name[] = $product->name;
            $product_type[] = $product->type;
            $product_id[] = $product->id;
            $product_list[] = $product->product_list;
            $lims_product_data = $product->id;
            $qty_list[] = $product->qty_list;
            $expired_date[] = null;
            $is_embeded[] = 0;
            $imei_number[] = null;
        }
        $product_data = [$product_code, $product_name, $product_qty, $product_type, $product_id, $product_list, $qty_list, $product_price, $batch_no, $product_batch_id, $expired_date, $is_embeded, $imei_number];
        return $product_data;
    }

    public function limsProductSearch(Request $request)
    {
        // dd($request->all());
        $todayDate = date('Y-m-d');
        $product_data = explode("|", $request['data']);
        // $product_code = explode("(", $request['data']);
        $product_info = explode("?", $request['data']);
        $customer_id = $product_info[1];
        // if(strpos($request['data'], '|')) {
        //     $product_info = explode("|", $request['data']);
        //     $embeded_code = $product_code[0];
        //     $product_code[0] = substr($embeded_code, 0, 7);
        //     $qty = substr($embeded_code, 7, 5) / 1000;
        // }
        // else {
        //     $product_code[0] = rtrim($product_code[0], " ");
        //     $qty = $product_info[2];
        // }
        if($product_data[3][0]) {
            $product_info = explode("|", $request['data']);
            $embeded_code = $product_data[0];
            $product_data[0] = substr($embeded_code, 0, 7);
            $qty = substr($embeded_code, 7, 5) / 1000;
        }
        else {
            $qty = $product_info[2];
        }
        $product_variant_id = null;
        $all_discount = DB::table('discount_plan_customers')
                        ->join('discount_plans', 'discount_plans.id', '=', 'discount_plan_customers.discount_plan_id')
                        ->join('discount_plan_discounts', 'discount_plans.id', '=', 'discount_plan_discounts.discount_plan_id')
                        ->join('discounts', 'discounts.id', '=', 'discount_plan_discounts.discount_id')
                        ->where([
                            ['discount_plans.is_active', true],
                            ['discounts.is_active', true],
                            ['discount_plan_customers.customer_id', $customer_id]
                        ])
                        ->select('discounts.*')
                        ->get();
        // return $product_data[0];
        $lims_product_data = Product::where([
            ['code', $product_data[0]],
            ['is_active', true]
        ])->first();

        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.id as product_variant_id', 'product_variants.item_code', 'product_variants.additional_price')
                ->where([
                    ['product_variants.item_code', $product_data[0]],
                    ['products.is_active', true]
                ])->first();
// dd($lims_product_data);
            // return $lims_product_data;
            $product_variant_id = $lims_product_data->product_variant_id;
        }

        $product[] = $lims_product_data->name;
        if($lims_product_data->is_variant){
            $product[] = $lims_product_data->item_code;
            // $product[] = $lims_product_data->code;
            $lims_product_data->price += $lims_product_data->additional_price;
        }
        else
            $product[] = $lims_product_data->code;


            // dd($product);
        $no_discount = 1;
        foreach ($all_discount as $key => $discount) {
            $product_list = explode(",", $discount->product_list);
            $days = explode(",", $discount->days);

            if( ( $discount->applicable_for == 'All' || in_array($lims_product_data->id, $product_list) ) && ( $todayDate >= $discount->valid_from && $todayDate <= $discount->valid_till && in_array(date('D'), $days) && $qty >= $discount->minimum_qty && $qty <= $discount->maximum_qty ) ) {
                if($discount->type == 'flat') {
                    $product[] = $lims_product_data->price - $discount->value;
                }
                elseif($discount->type == 'percentage') {
                    $product[] = $lims_product_data->price - ($lims_product_data->price * ($discount->value/100));
                }
                $no_discount = 0;
                break;
            }
            else {
                continue;
            }
        }

        if($lims_product_data->promotion && $todayDate <= $lims_product_data->last_date && $no_discount) {
            $product[] = $lims_product_data->promotion_price;
        }
        elseif($no_discount)
            $product[] = $lims_product_data->price;

        if($lims_product_data->tax_id) {
            $lims_tax_data = Tax::find($lims_product_data->tax_id);
            $product[] = $lims_tax_data->rate;
            $product[] = $lims_tax_data->name;
        }
        else{
            $product[] = 0;
            $product[] = 'No Tax';
        }
        $product[] = $lims_product_data->tax_method;
        if($lims_product_data->type == 'standard'){
            $units = Unit::where("base_unit", $lims_product_data->unit_id)
                    ->orWhere('id', $lims_product_data->unit_id)
                    ->get();
            $unit_name = array();
            $unit_operator = array();
            $unit_operation_value = array();
            foreach ($units as $unit) {
                if($lims_product_data->sale_unit_id == $unit->id) {
                    array_unshift($unit_name, $unit->unit_name);
                    array_unshift($unit_operator, $unit->operator);
                    array_unshift($unit_operation_value, $unit->operation_value);
                }
                else {
                    $unit_name[]  = $unit->unit_name;
                    $unit_operator[] = $unit->operator;
                    $unit_operation_value[] = $unit->operation_value;
                }
            }
            $product[] = implode(",",$unit_name) . ',';
            $product[] = implode(",",$unit_operator) . ',';
            $product[] = implode(",",$unit_operation_value) . ',';
        }
        else{
            $product[] = 'n/a'. ',';
            $product[] = 'n/a'. ',';
            $product[] = 'n/a'. ',';
        }
        $product[] = $lims_product_data->id;
        $product[] = $product_variant_id;
        $product[] = $lims_product_data->promotion;
        $product[] = $lims_product_data->is_batch;
        $product[] = $lims_product_data->is_imei;
        $product[] = $lims_product_data->is_variant;
        $product[] = $qty;
        $product[] = $lims_product_data->wholesale_price;
        $product[] = $lims_product_data->cost;
        $product[] = $product_data[2];

        return $product;
    }

    public function productInquiryData($id)
    {
        $lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
        foreach ($lims_product_quotation_data as $key => $product_quotation_data) {
            $product = Product::find($product_quotation_data->product_id);
            if($product_quotation_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('item_code')->FindExactProduct($product_quotation_data->product_id, $product_quotation_data->variant_id)->first();
                $product->code = $lims_product_variant_data->item_code;
            }
            if($product_quotation_data->sale_unit_id){
                $unit_data = Unit::find($product_quotation_data->sale_unit_id);
                $unit = $unit_data->unit_code;
            }
            else
                $unit = '';

            $product_quotation[0][$key] = $product->name . ' [' . $product->code . ']';
            $product_quotation[1][$key] = $product_quotation_data->qty;
            $product_quotation[2][$key] = $unit;
            $product_quotation[3][$key] = $product_quotation_data->tax;
            $product_quotation[4][$key] = $product_quotation_data->tax_rate;
            $product_quotation[5][$key] = $product_quotation_data->discount;
            $product_quotation[6][$key] = $product_quotation_data->total;
            if($product_quotation_data->product_batch_id) {
                $product_batch_data = ProductBatch::select('batch_no')->find($product_quotation_data->product_batch_id);
                $product_quotation[7][$key] = $product_batch_data->batch_no;
            }
            else
                $product_quotation[7][$key] = 'N/A';
        }
        return $product_quotation;
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('quotes-edit')){
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_supplier_list = Supplier::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();
            $lims_quotation_data = Quotation::find($id);
            $lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
            return view('backend.quotation.edit',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list', 'lims_quotation_data','lims_product_quotation_data', 'lims_supplier_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('document');
        //return dd($data);
        $document = $request->document;
        $lims_quotation_data = Quotation::find($id);

        if($document) {
            $v = Validator::make(
                [
                    'extension' => strtolower($request->document->getClientOriginalExtension()),
                ],
                [
                    'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
                ]
            );
            if ($v->fails())
                return redirect()->back()->withErrors($v->errors());

            $this->fileDelete(public_path('documents/quotation/'), $lims_quotation_data->document);

            $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
            $documentName = date("Ymdhis");
            if(!config('database.connections.saleprosaas_landlord')) {
                $documentName = $documentName . '.' . $ext;
                $document->move(public_path('documents/quotation'), $documentName);
            }
            else {
                $documentName = $this->getTenantId() . '_' . $documentName . '.' . $ext;
                $document->move(public_path('documents/quotation'), $documentName);
            }
            $data['document'] = $documentName;
        }
        $lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
        //update quotation table
        $lims_quotation_data->update($data);
        if($lims_quotation_data->quotation_status == 2){
            //collecting mail data
            $lims_customer_data = Customer::find($data['customer_id']);
            $mail_data['email'] = $lims_customer_data->email;
            $mail_data['reference_no'] = $lims_quotation_data->reference_no;
            $mail_data['total_qty'] = $data['total_qty'];
            $mail_data['total_price'] = $data['total_price'];
            $mail_data['order_tax'] = $data['order_tax'];
            $mail_data['order_tax_rate'] = $data['order_tax_rate'];
            $mail_data['order_discount'] = $data['order_discount'];
            $mail_data['shipping_cost'] = $data['shipping_cost'];
            $mail_data['grand_total'] = $data['grand_total'];
        }
        $product_id = $data['product_id'];
        $product_batch_id = $data['product_batch_id'];
        $product_variant_id = $data['product_variant_id'];
        $qty = $data['qty'];
        $sale_unit = $data['sale_unit'];
        $net_unit_price = $data['net_unit_price'];
        $discount = $data['discount'];
        $tax_rate = $data['tax_rate'];
        $tax = $data['tax'];
        $total = $data['subtotal'];

        foreach ($lims_product_quotation_data as $key => $product_quotation_data) {
            $old_product_id[] = $product_quotation_data->product_id;
            $lims_product_data = Product::select('id')->find($product_quotation_data->product_id);
            if($product_quotation_data->variant_id) {
                $lims_product_variant_data = ProductVariant::select('id')->FindExactProduct($product_quotation_data->product_id, $product_quotation_data->variant_id)->first();
                $old_product_variant_id[] = $lims_product_variant_data->id;
                if(!in_array($lims_product_variant_data->id, $product_variant_id))
                    $product_quotation_data->delete();
            }
            else {
                $old_product_variant_id[] = null;
                if(!in_array($product_quotation_data->product_id, $product_id))
                    $product_quotation_data->delete();
            }
        }

        foreach ($product_id as $i => $pro_id) {
            if($sale_unit[$i] != 'n/a'){
                $lims_sale_unit_data = Unit::where('unit_name', $sale_unit[$i])->first();
                $sale_unit_id = $lims_sale_unit_data->id;
            }
            else
                $sale_unit_id = 0;
            $lims_product_data = Product::select('id', 'name', 'is_variant')->find($pro_id);
            if($sale_unit_id)
                $mail_data['unit'][$i] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$i] = '';
            $input['quotation_id'] = $id;
            $input['product_id'] = $pro_id;
            $input['product_batch_id'] = $product_batch_id[$i];
            $input['qty'] = $mail_data['qty'][$i] = $qty[$i];
            $input['sale_unit_id'] = $sale_unit_id;
            $input['net_unit_price'] = $net_unit_price[$i];
            $input['discount'] = $discount[$i];
            $input['tax_rate'] = $tax_rate[$i];
            $input['tax'] = $tax[$i];
            $input['total'] = $mail_data['total'][$i] = $total[$i];
            $flag = 1;
            if($lims_product_data->is_variant) {
                $lims_product_variant_data = ProductVariant::select('variant_id')->where('id', $product_variant_id[$i])->first();
                $input['variant_id'] = $lims_product_variant_data->variant_id;
                if(in_array($product_variant_id[$i], $old_product_variant_id)) {
                    ProductQuotation::where([
                        ['product_id', $pro_id],
                        ['variant_id', $input['variant_id']],
                        ['quotation_id', $id]
                    ])->update($input);
                }
                else {
                    ProductQuotation::create($input);
                }
                $variant_data = Variant::find($input['variant_id']);
                $mail_data['products'][$i] = $lims_product_data->name . ' [' . $variant_data->name . ']';
            }
            else {
                $input['variant_id'] = null;
                if(in_array($pro_id, $old_product_id)) {
                    ProductQuotation::where([
                        ['product_id', $pro_id],
                        ['quotation_id', $id]
                    ])->update($input);
                }
                else {
                    ProductQuotation::create($input);
                }
                $mail_data['products'][$i] = $lims_product_data->name;
            }
        }

        $message = 'Quotation updated successfully';
        $mail_setting = MailSetting::latest()->first();
        if($lims_quotation_data->quotation_status == 2 && $mail_data['email'] && $mail_setting) {
            $this->setMailInfo($mail_setting);
            try{
                Mail::to($mail_data['email'])->send(new QuotationDetails($mail_data));
            }
            catch(\Exception $e){
                $message = 'Quotation updated successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect('quotations')->with('message', $message);
    }


    public function createQuotation($id)
    {



        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_biller_list = Biller::where('is_active', true)->get();

        $lims_supplier_list = Supplier::where('is_active', true)->get();
        $lims_tax_list = Tax::where('is_active', true)->get();
        $lims_quotation_data = Inquiry::find($id);

        $lims_customer_list = Customer::where([['is_active', true], ['company_name', $lims_quotation_data->company_name]])->get();

        $requirementString = $lims_quotation_data->requirement;
        preg_match_all('/\((.*?)\)/', $requirementString, $matches);
        $productCodes = $matches[1]; // This gives you: ['87351162', '69674153', '02202177']
        $productIds = array_filter(explode(',', $lims_quotation_data->requirement));

// Fetch products by ID
$products = Product::whereIn('id', $productIds)->get();
        $terms = Terms::all();
        $lims_pos_setting_data = PosSetting::latest()->first();

        $company = Company::where('id', $lims_quotation_data->company_name)->first();

        return view('backend.inquiry.create_quotation',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_supplier_list', 'lims_tax_list', 'lims_quotation_data','products', 'lims_pos_setting_data','terms','company'));
    }

    public function createPurchase($id)
    {
        $lims_supplier_list = Supplier::where('is_active', true)->get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_tax_list = Tax::where('is_active', true)->get();
        $lims_quotation_data = Quotation::find($id);
        $lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
        $lims_product_list_without_variant = $this->productWithoutVariant();
        $lims_product_list_with_variant = $this->productWithVariant();

        return view('backend.quotation.create_purchase',compact('lims_product_list_without_variant', 'lims_product_list_with_variant', 'lims_supplier_list', 'lims_warehouse_list', 'lims_tax_list', 'lims_quotation_data','lims_product_quotation_data'));
    }

    public function productWithoutVariant()
    {
        return Product::ActiveStandard()->select('id', 'name', 'code')
                ->whereNull('is_variant')->get();
    }

    public function productWithVariant()
    {
        return Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->ActiveStandard()
                ->whereNotNull('is_variant')
                ->select('products.id', 'products.name', 'product_variants.item_code')
                ->orderBy('position')->get();
    }

    public function deleteBySelection(Request $request)
    {
        $quotation_id = $request['quotationIdArray'];
        foreach ($quotation_id as $id) {
            $lims_quotation_data = Quotation::find($id);
            $lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
            foreach ($lims_product_quotation_data as $product_quotation_data) {
                $product_quotation_data->delete();
            }
            $lims_quotation_data->delete();
            $this->fileDelete(public_path('documents/quotation/'), $lims_quotation_data->document);
        }
        return 'Quotation deleted successfully!';
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::find($id);

    if (!$inquiry) {
        return response()->json([
            'message' => 'Inquiry not found.'
        ], 404);
    }

    $inquiry->delete();

    return response()->json([
        'message' => 'Inquiry deleted successfully.'
    ]);
    }
    public function deleteInquiry($id)
{
    $inquiry = Inquiry::find($id);

    if (!$inquiry) {
        return response()->json([
            'message' => 'Inquiry not found.'
        ], 404);
    }

    $inquiry->delete();

    return response()->json([
        'message' => 'Inquiry deleted successfully.'
    ]);
}

    public function print($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        // Convert requirement string to array of product IDs
        $productIds = explode(',', $inquiry->requirement);
        // Fetch related product info from products table
        $products = Product::whereIn('id', $productIds)->get();


        return view('backend.inquiry.print', compact('inquiry', 'products'));
    }



}

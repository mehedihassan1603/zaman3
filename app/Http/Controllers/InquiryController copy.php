<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
{
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
    dd('asd');
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

    // public function inquiryData(Request $request)
    // {
    //     dd('adsad', $request->all());
    //     $columns = array(
    //         1 => 'created_at',
    //     );

    //     $warehouse_id = $request->input('warehouse_id');
    //     if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
    //         $totalData = Inquiry::where('user_id', Auth::id())
    //                     ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                     ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                     ->count();
    //     //check staff access
    //     elseif(Auth::user()->role_id > 2 && config('staff_access') == 'warehouse')
    //         $totalData = Inquiry::where('warehouse_id', Auth::user()->warehouse_id)
    //         ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //         ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //         ->count();
    //     elseif($warehouse_id != 0)
    //         $totalData = Inquiry::where('warehouse_id', $warehouse_id)
    //                     ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                     ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                     ->count();
    //     elseif($warehouse_id != 0)
    //         $totalData = Inquiry::where('warehouse_id', $warehouse_id)
    //                     ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                     ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                     ->count();
    //     else
    //         $totalData = Inquiry::whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                     ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                     ->count();

    //     $totalFiltered = $totalData;

    //     if($request->input('length') != -1)
    //         $limit = $request->input('length');
    //     else
    //         $limit = $totalData;
    //     $start = $request->input('start');
    //     $order = $columns[$request->input('order.0.column')];
    //     $dir = $request->input('order.0.dir');
    //     if(empty($request->input('search.value'))) {
    //         if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
    //             $quotations = Quotation::with('biller', 'customer', 'supplier', 'user')->offset($start)
    //                         ->where('user_id', Auth::id())
    //                         ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                         ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                         ->limit($limit)
    //                         ->orderBy($order, $dir)
    //                         ->get();
    //         elseif(Auth::user()->role_id > 2 && config('staff_access') == 'warehouse')
    //             $quotations = Quotation::with('biller', 'customer', 'supplier', 'user')->offset($start)
    //                         ->where('warehouse_id', Auth::user()->warehouse_id)
    //                         ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                         ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                         ->limit($limit)
    //                         ->orderBy($order, $dir)
    //                         ->get();
    //         elseif($warehouse_id != 0)
    //             $quotations = Quotation::with('biller', 'customer', 'supplier', 'user')->offset($start)
    //                         ->where('warehouse_id', $warehouse_id)
    //                         ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                         ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                         ->limit($limit)
    //                         ->orderBy($order, $dir)
    //                         ->get();
    //         else
    //             $quotations = Quotation::with('biller', 'customer', 'supplier', 'user')->offset($start)
    //                         ->whereDate('created_at', '>=' ,$request->input('starting_date'))
    //                         ->whereDate('created_at', '<=' ,$request->input('ending_date'))
    //                         ->limit($limit)
    //                         ->orderBy($order, $dir)
    //                         ->get();
    //     }
    //     else
    //     {
    //         $search = $request->input('search.value');
    //         if(Auth::user()->role_id > 2 && config('staff_access') == 'own') {
    //             $quotations =  Quotation::select('quotations.*')
    //                         ->with('biller', 'customer', 'supplier', 'user')
    //                         ->join('billers', 'quotations.biller_id', '=', 'billers.id')
    //                         ->join('customers', 'quotations.customer_id', '=', 'customers.id')
    //                         ->leftJoin('suppliers', 'quotations.supplier_id', '=', 'suppliers.id')
    //                         ->whereDate('quotations.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
    //                         ->where('quotations.user_id', Auth::id())
    //                         ->orwhere([
    //                             ['quotations.reference_no', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->orwhere([
    //                             ['billers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->orwhere([
    //                             ['customers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->orwhere([
    //                             ['suppliers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->offset($start)
    //                         ->limit($limit)
    //                         ->orderBy($order,$dir)->get();

    //             $totalFiltered = Quotation::join('billers', 'quotations.biller_id', '=', 'billers.id')
    //                         ->join('customers', 'quotations.customer_id', '=', 'customers.id')
    //                         ->leftJoin('suppliers', 'quotations.supplier_id', '=', 'suppliers.id')
    //                         ->whereDate('quotations.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
    //                         ->where('quotations.user_id', Auth::id())
    //                         ->orwhere([
    //                             ['quotations.reference_no', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->orwhere([
    //                             ['billers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->orwhere([
    //                             ['customers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->orwhere([
    //                             ['suppliers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.user_id', Auth::id()]
    //                         ])
    //                         ->count();
    //         }
    //         elseif(Auth::user()->role_id > 2 && config('staff_access') == 'warehouse') {
    //             $quotations =  Quotation::select('quotations.*')
    //                         ->with('biller', 'customer', 'supplier', 'user')
    //                         ->join('billers', 'quotations.biller_id', '=', 'billers.id')
    //                         ->join('customers', 'quotations.customer_id', '=', 'customers.id')
    //                         ->leftJoin('suppliers', 'quotations.supplier_id', '=', 'suppliers.id')
    //                         ->whereDate('quotations.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
    //                         ->where('quotations.user_id', Auth::id())
    //                         ->orwhere([
    //                             ['quotations.reference_no', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->orwhere([
    //                             ['billers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->orwhere([
    //                             ['customers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->orwhere([
    //                             ['suppliers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->offset($start)
    //                         ->limit($limit)
    //                         ->orderBy($order,$dir)->get();

    //             $totalFiltered = Quotation::join('billers', 'quotations.biller_id', '=', 'billers.id')
    //                         ->join('customers', 'quotations.customer_id', '=', 'customers.id')
    //                         ->leftJoin('suppliers', 'quotations.supplier_id', '=', 'suppliers.id')
    //                         ->whereDate('quotations.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
    //                         ->where('quotations.user_id', Auth::id())
    //                         ->orwhere([
    //                             ['quotations.reference_no', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->orwhere([
    //                             ['billers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->orwhere([
    //                             ['customers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->orwhere([
    //                             ['suppliers.name', 'LIKE', "%{$search}%"],
    //                             ['quotations.warehouse_id', Auth::user()->warehouse_id]
    //                         ])
    //                         ->count();
    //         }
    //         else {
    //             $quotations =  Quotation::select('quotations.*')
    //                         ->with('biller', 'customer', 'supplier', 'user')
    //                         ->join('billers', 'quotations.biller_id', '=', 'billers.id')
    //                         ->join('customers', 'quotations.customer_id', '=', 'customers.id')
    //                         ->leftJoin('suppliers', 'quotations.supplier_id', '=', 'suppliers.id')
    //                         ->whereDate('quotations.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
    //                         ->orwhere('quotations.reference_no', 'LIKE', "%{$search}%")
    //                         ->orwhere('billers.name', 'LIKE', "%{$search}%")
    //                         ->orwhere('customers.name', 'LIKE', "%{$search}%")
    //                         ->orwhere('suppliers.name', 'LIKE', "%{$search}%")
    //                         ->offset($start)
    //                         ->limit($limit)
    //                         ->orderBy($order,$dir)
    //                         ->get();

    //             $totalFiltered = Quotation::join('billers', 'quotations.biller_id', '=', 'billers.id')
    //                         ->join('customers', 'quotations.customer_id', '=', 'customers.id')
    //                         ->leftJoin('suppliers', 'quotations.supplier_id', '=', 'suppliers.id')
    //                         ->whereDate('quotations.created_at', '=' , date('Y-m-d', strtotime(str_replace('/', '-', $search))))
    //                         ->orwhere('quotations.reference_no', 'LIKE', "%{$search}%")
    //                         ->orwhere('billers.name', 'LIKE', "%{$search}%")
    //                         ->orwhere('customers.name', 'LIKE', "%{$search}%")
    //                         ->orwhere('suppliers.name', 'LIKE', "%{$search}%")
    //                         ->count();
    //         }
    //     }
    //     $data = array();
    //     if(!empty($quotations))
    //     {
    //         foreach ($quotations as $key => $quotation)
    //         {
    //             $nestedData['id'] = $quotation->id;
    //             $nestedData['key'] = $key;
    //             $nestedData['date'] = date(config('date_format'), strtotime($quotation->created_at->toDateString()));
    //             $nestedData['reference_no'] = $quotation->reference_no;
    //             $nestedData['warehouse'] = $quotation->warehouse->name;
    //             $nestedData['biller'] = $quotation->biller->name;
    //             $nestedData['customer'] = $quotation->customer->name;

    //             if($quotation->supplier_id) {
    //                 $supplier = $quotation->supplier;
    //                 $nestedData['supplier'] = $supplier->name;
    //             }
    //             else {
    //                 $nestedData['supplier'] = 'N/A';
    //             }

    //             if($quotation->quotation_status == 1) {
    //                 $nestedData['status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
    //                 $status = trans('file.Pending');
    //             }
    //             else{
    //                 $nestedData['status'] = '<div class="badge badge-success">'.trans('file.Sent').'</div>';
    //                 $status = trans('file.Sent');
    //             }

    //             $nestedData['grand_total'] = number_format($quotation->grand_total, config('decimal'));
    //             $nestedData['options'] = '<div class="btn-group">
    //                         <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
    //                           <span class="caret"></span>
    //                           <span class="sr-only">Toggle Dropdown</span>
    //                         </button>
    //                         <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
    //                             <li>
    //                                 <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
    //                             </li>';
    //             if(in_array("quotes-edit", $request['all_permission']))
    //                 $nestedData['options'] .= '<li>
    //                     <a href="'.route('quotations.edit', $quotation->id).'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
    //                     </li>';
    //             $nestedData['options'] .= '<li>
    //                     <a href="'.route('quotation.create_sale', $quotation->id).'" class="btn btn-link"><i class="fa fa-shopping-cart"></i> '.trans('file.Create Sale').'</a>
    //                     </li>';
    //             $nestedData['options'] .= '<li>
    //                     <a href="'.route('quotation.create_purchase', $quotation->id).'" class="btn btn-link"><i class="fa fa-shopping-basket"></i> '.trans('file.Create Purchase').'</a>
    //                     </li>';
    //             if(in_array("quotes-delete", $request['all_permission']))
    //                 $nestedData['options'] .= \Form::open(["route" => ["quotations.destroy", $quotation->id], "method" => "DELETE"] ).'
    //                         <li>
    //                           <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button>
    //                         </li>'.\Form::close().'
    //                     </ul>
    //                 </div>';

    //             // data for quotation details by one click

    //             $nestedData['quotation'] = array( '[ "'.date(config('date_format'), strtotime($quotation->created_at->toDateString())).'"', ' "'.$quotation->reference_no.'"', ' "'.$status.'"',  ' "'.$quotation->biller->name.'"', ' "'.$quotation->biller->company_name.'"', ' "'.$quotation->biller->email.'"', ' "'.$quotation->biller->phone_number.'"', ' "'.$quotation->biller->address.'"', ' "'.$quotation->biller->city.'"', ' "'.$quotation->customer->name.'"', ' "'.$quotation->customer->phone_number.'"', ' "'.$quotation->customer->address.'"', ' "'.$quotation->customer->city.'"', ' "'.$quotation->id.'"', ' "'.$quotation->total_tax.'"', ' "'.$quotation->total_discount.'"', ' "'.$quotation->total_price.'"', ' "'.$quotation->order_tax.'"', ' "'.$quotation->order_tax_rate.'"', ' "'.$quotation->order_discount.'"', ' "'.$quotation->shipping_cost.'"', ' "'.$quotation->grand_total.'"', ' "'.preg_replace('/\s+/S', " ", $quotation->note).'"', ' "'.$quotation->user->name.'"', ' "'.$quotation->user->email.'"', ' "'.$quotation->document.'"]'
    //             );
    //             $data[] = $nestedData;
    //         }
    //     }
    //     $json_data = array(
    //         "draw"            => intval($request->input('draw')),
    //         "recordsTotal"    => intval($totalData),
    //         "recordsFiltered" => intval($totalFiltered),
    //         "data"            => $data
    //     );

    //     echo json_encode($json_data);
    // }

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
            'company_name' => $inquiry->company_name,
            'contact_person' => $inquiry->contact_person,
            'contact_number' => $inquiry->contact_number,
            'email' => $inquiry->email,
            'requirement' => $inquiry->requirement,
            'options' => '
                <a href="' . route('inquiry.print', $inquiry->id) . '" class="btn btn-sm btn-success" target="_blank">Print</a>
    <button class="btn btn-sm btn-danger delete-inquiry" data-id="' . $inquiry->id . '">Delete</button>
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
        
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('quotes-add')){
            $lims_biller_list = Biller::where('is_active', true)->get();
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_customer_list = Customer::where('is_active', true)->get();
            $lims_supplier_list = Supplier::where('is_active', true)->get();
            $lims_tax_list = Tax::where('is_active', true)->get();

            return view('backend.inquiry.create', compact('lims_biller_list', 'lims_warehouse_list', 'lims_customer_list', 'lims_supplier_list', 'lims_tax_list'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }


    // public function store(Request $request)
    // {
    //     $data = $request->except('document');
    //     //return dd($data);
    //     $data['user_id'] = Auth::id();
    //     $document = $request->document;
    //     if($document){
    //         $v = Validator::make(
    //             [
    //                 'extension' => strtolower($request->document->getClientOriginalExtension()),
    //             ],
    //             [
    //                 'extension' => 'in:jpg,jpeg,png,gif,pdf,csv,docx,xlsx,txt',
    //             ]
    //         );
    //         if ($v->fails())
    //             return redirect()->back()->withErrors($v->errors());
    //         $ext = pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION);
    //         $documentName = date("Ymdhis");
    //         if(!config('database.connections.saleprosaas_landlord')) {
    //             $documentName = $documentName . '.' . $ext;
    //             $document->move(public_path('documents/quotation'), $documentName);
    //         }
    //         else {
    //             $documentName = $this->getTenantId() . '_' . $documentName . '.' . $ext;
    //             $document->move(public_path('documents/quotation'), $documentName);
    //         }
    //         $data['document'] = $documentName;
    //     }
    //     $data['reference_no'] = 'qr-' . date("Ymd") . '-'. date("his");
    //     $lims_quotation_data = Quotation::create($data);
    //     if($lims_quotation_data->quotation_status == 2){
    //         //collecting mail data
    //         $lims_customer_data = Customer::find($data['customer_id']);
    //         $mail_data['email'] = $lims_customer_data->email;
    //         $mail_data['reference_no'] = $lims_quotation_data->reference_no;
    //         $mail_data['total_qty'] = $lims_quotation_data->total_qty;
    //         $mail_data['total_price'] = $lims_quotation_data->total_price;
    //         $mail_data['order_tax'] = $lims_quotation_data->order_tax;
    //         $mail_data['order_tax_rate'] = $lims_quotation_data->order_tax_rate;
    //         $mail_data['order_discount'] = $lims_quotation_data->order_discount;
    //         $mail_data['shipping_cost'] = $lims_quotation_data->shipping_cost;
    //         $mail_data['grand_total'] = $lims_quotation_data->grand_total;
    //     }
    //     $product_id = $data['product_id'];
    //     $product_batch_id = $data['product_batch_id'];
    //     $product_code = $data['product_code'];
    //     $qty = $data['qty'];
    //     $sale_unit = $data['sale_unit'];
    //     $net_unit_price = $data['net_unit_price'];
    //     $discount = $data['discount'];
    //     $tax_rate = $data['tax_rate'];
    //     $tax = $data['tax'];
    //     $total = $data['subtotal'];
    //     $product_quotation = [];

    //     foreach ($product_id as $i => $id) {
    //         if($sale_unit[$i] != 'n/a'){
    //             $lims_sale_unit_data = Unit::where('unit_name', $sale_unit[$i])->first();
    //             $sale_unit_id = $lims_sale_unit_data->id;
    //         }
    //         else
    //             $sale_unit_id = 0;
    //         if($sale_unit_id)
    //             $mail_data['unit'][$i] = $lims_sale_unit_data->unit_code;
    //         else
    //             $mail_data['unit'][$i] = '';
    //         $lims_product_data = Product::find($id);
    //         if($lims_product_data->is_variant) {
    //             $lims_product_variant_data = ProductVariant::select('variant_id')->FindExactProductWithCode($id, $product_code[$i])->first();
    //             $product_quotation['variant_id'] = $lims_product_variant_data->variant_id;
    //         }
    //         else
    //             $product_quotation['variant_id'] = null;
    //         if($product_quotation['variant_id']){
    //             $variant_data = Variant::find($product_quotation['variant_id']);
    //             $mail_data['products'][$i] = $lims_product_data->name . ' [' . $variant_data->name .']';
    //         }
    //         else
    //             $mail_data['products'][$i] = $lims_product_data->name;
    //         $product_quotation['quotation_id'] = $lims_quotation_data->id ;
    //         $product_quotation['product_id'] = $id;
    //         $product_quotation['product_batch_id'] = $product_batch_id[$i];
    //         $product_quotation['qty'] = $mail_data['qty'][$i] = $qty[$i];
    //         $product_quotation['sale_unit_id'] = $sale_unit_id;
    //         $product_quotation['net_unit_price'] = $net_unit_price[$i];
    //         $product_quotation['discount'] = $discount[$i];
    //         $product_quotation['tax_rate'] = $tax_rate[$i];
    //         $product_quotation['tax'] = $tax[$i];
    //         $product_quotation['total'] = $mail_data['total'][$i] = $total[$i];
    //         ProductQuotation::create($product_quotation);
    //     }
    //     $message = 'Quotation created successfully';
    //     $mail_setting = MailSetting::latest()->first();
    //     if($lims_quotation_data->quotation_status == 2 && $mail_data['email'] && $mail_setting) {
    //         $this->setMailInfo($mail_setting);
    //         try{
    //             Mail::to($mail_data['email'])->send(new QuotationDetails($mail_data));
    //         }
    //         catch(\Exception $e){
    //             $message = 'Quotation created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
    //         }
    //     }
    //     return redirect('quotations')->with('message', $message);
    // }


    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'company_name' => 'required|string',
            'contact_person' => 'required|string',
            'designation' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'email' => 'nullable|email',
            'head_office' => 'nullable|string',
            'factory' => 'nullable|string',
            'requirement' => 'nullable|string',
            'reffer' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['warehouse_id'] = 1;

        Inquiry::create($data);

        return redirect()->back()->with('success', 'Inquiry submitted successfully.');
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

    public function createSale($id)
    {
        $lims_customer_list = Customer::where('is_active', true)->get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_biller_list = Biller::where('is_active', true)->get();
        $lims_tax_list = Tax::where('is_active', true)->get();
        $lims_quotation_data = Quotation::find($id);
        $lims_product_quotation_data = ProductQuotation::where('quotation_id', $id)->get();
        $lims_pos_setting_data = PosSetting::latest()->first();
        return view('backend.quotation.create_sale',compact('lims_customer_list', 'lims_warehouse_list', 'lims_biller_list', 'lims_tax_list', 'lims_quotation_data','lims_product_quotation_data', 'lims_pos_setting_data'));
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
    return view('backend.inquiry.print', compact('inquiry'));
}


}

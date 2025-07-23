<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\Product_Warehouse;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SalePrice;
use DB;
use App\Models\StockCount;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StockCountController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if( $role->hasPermissionTo('stock_count') ) {
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $general_setting = DB::table('general_settings')->latest()->first();
            if(Auth::user()->role_id > 2 && $general_setting->staff_access == 'own')
                $lims_stock_count_all = StockCount::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_stock_count_all = StockCount::orderBy('id', 'desc')->get();

            return view('backend.stock_count.index', compact('lims_warehouse_list', 'lims_brand_list', 'lims_category_list', 'lims_stock_count_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }
    public function sale_index()
    {
        $general = GeneralSetting::where('id', 1)->first();

        $role = Role::find(Auth::user()->role_id);
        if( $role->hasPermissionTo('stock_count') ) {
            // $get_all_products = Product::get();
            $get_all_products = Product::select(
                'product_warehouse.id',
                'product_warehouse.product_id',
                'products.name as product_name',
                'product_warehouse.product_batch_id',
                'product_batches.batch_no',
                'product_warehouse.variant_id',
                'product_warehouse.imei_number',
                'product_warehouse.warehouse_id',
                'warehouses.name as warehouse_name',
                'product_warehouse.qty',
                'categories.name',
                'brands.title',
                // 'product_warehouse.price',
                DB::raw('COALESCE(if(product_warehouse.price>0,product_warehouse.price,products.price),0) as price'),
                DB::raw('COALESCE(IF(product_purchases.net_unit_cost > 0, product_purchases.net_unit_cost, products.cost), 0) as cost'),
                DB::raw('COALESCE(((purchases.shipping_cost / purchases.total_qty) * product_purchases.net_unit_cost) / (purchases.total_cost / purchases.total_qty), 0) as t_shipping'),
                DB::raw('COALESCE(((purchases.order_discount / purchases.total_qty) * product_purchases.net_unit_cost) / (purchases.total_cost / purchases.total_qty), 0) as t_disc'),
                DB::raw('(COALESCE(IF(product_purchases.net_unit_cost > 0, product_purchases.net_unit_cost, products.cost), 0) +
                          COALESCE(((purchases.shipping_cost / purchases.total_qty) * product_purchases.net_unit_cost) / (purchases.total_cost / purchases.total_qty), 0) -
                          COALESCE(((purchases.order_discount / purchases.total_qty) * product_purchases.net_unit_cost) / (purchases.total_cost / purchases.total_qty), 0)) as actual_cost'),
                DB::raw('10 as profit_margin'),
                DB::raw('((COALESCE(IF(product_purchases.net_unit_cost > 0, product_purchases.net_unit_cost, products.cost), 0) +
                           COALESCE(((purchases.shipping_cost / purchases.total_qty) * product_purchases.net_unit_cost) / (purchases.total_cost / purchases.total_qty), 0) -
                           COALESCE(((purchases.order_discount / purchases.total_qty) * product_purchases.net_unit_cost) / (purchases.total_cost / purchases.total_qty), 0)) * 1.10) as Sale_price')
            )
            ->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
            ->leftJoin('product_batches', 'product_batches.id', '=', 'product_warehouse.product_batch_id') // Join batch table
            ->leftJoin('warehouses', 'warehouses.id', '=', 'product_warehouse.warehouse_id') // Join warehouse table
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('product_purchases', function ($join) {
                $join->on('product_purchases.product_id', '=', 'product_warehouse.product_id')
                     ->on('product_purchases.product_batch_id', '=', 'product_warehouse.product_batch_id')
                     ->whereRaw('COALESCE(product_purchases.variant_id, 0) = COALESCE(product_warehouse.variant_id, 0)');
            })
            ->leftJoin('purchases', function ($join) {
                $join->on('purchases.warehouse_id', '=', 'product_warehouse.warehouse_id')
                     ->on('purchases.id', '=', 'product_purchases.purchase_id');
            })
            ->groupBy(
                'product_warehouse.id',
                'product_warehouse.product_id',
                'products.name',
                'product_warehouse.product_batch_id',
                'product_batches.batch_no',
                'product_warehouse.variant_id',
                'product_warehouse.imei_number',
                'product_warehouse.warehouse_id',
                'warehouses.name',
                'product_warehouse.qty',
                'product_warehouse.price',
                'products.price',
                'product_purchases.net_unit_cost',
                'products.cost',
                'purchases.shipping_cost',
                'purchases.order_discount',
                'purchases.total_qty',
                'purchases.total_cost'
            )
            ->get();
            // dd($get_all_products);
            $lims_warehouse_list = Warehouse::where('is_active', true)->get();
            $lims_brand_list = Brand::where('is_active', true)->get();
            $lims_category_list = Category::where('is_active', true)->get();
            $general_setting = DB::table('general_settings')->latest()->first();
            if(Auth::user()->role_id > 2 && $general_setting->staff_access == 'own')
                $lims_stock_count_all = SalePrice::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_stock_count_all = SalePrice::orderBy('id', 'desc')->get();

            return view('backend.adjustment.salePrice', compact('lims_warehouse_list', 'general', 'get_all_products', 'lims_brand_list', 'lims_category_list', 'lims_stock_count_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }




    public function updateProductWarehouse(Request $request)
{
    // dd($request->all());
    // Get the products data from the request
    $items = $request->input('products'); // Get the 'products' field from the request
// dd($items);
    // Check if products exist and are an array
    if (!is_array($items)) {
        return response()->json(['error' => 'Invalid data format. Expected an array of products.'], 400);
    }

    foreach ($items as $item) {
        // Ensure $item is an array (you might want to validate its structure further)
        if (is_array($item)) {
            Product_Warehouse::updateOrCreate(
                [
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $item['warehouse_id'],
                    'product_batch_id' => $item['product_batch_id'],
                ],
                [
                    'qty' => $item['qty'],
                    'price' => $item['sale_price']
                ]
            );
        }
    }

    return response()->json(['message' => 'Product Warehouse updated successfully!']);
}








    public function sale_store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        if( isset($data['brand_id']) && isset($data['category_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.category_id', $data['category_id'] )->whereIn('products.brand_id', $data['brand_id'] )->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.price', 'products.cost', 'product_warehouse.product_batch_id', 'product_warehouse.qty')->get();

            $data['category_id'] = implode(",", $data['category_id']);
            $data['brand_id'] = implode(",", $data['brand_id']);
        }
        elseif( isset($data['category_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.category_id', $data['category_id'])->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.product_batch_id', 'product_warehouse.price', 'products.cost', 'product_warehouse.qty')->get();

            $data['category_id'] = implode(",", $data['category_id']);
        }
        elseif( isset($data['brand_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.brand_id', $data['brand_id'])->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.product_batch_id', 'product_warehouse.price', 'products.cost', 'product_warehouse.qty')->get();

            $data['brand_id'] = implode(",", $data['brand_id']);
        }
        else{
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.qty')->get();
        }
        if( count($lims_product_list) ){
            $csvData=array('Product Name, Product Batch, Price, Cost, Profit Margin');
            foreach ($lims_product_list as $product) {
                $csvData[]=$product->name.','.$product->product_batch_id.','.$product->price.','.$product->cost.','.'';
            }
            //return $csvData;
            $filename= date('Ymd').'-'.date('his'). ".csv";
            $file_path= public_path().'/sale_price/'.$filename;
            $file = fopen($file_path, "w+");
            foreach ($csvData as $cellData){
              fputcsv($file, explode(',', $cellData));
            }
            fclose($file);

            $data['user_id'] = Auth::id();
            $data['reference_no'] = 'scr-' . date("Ymd") . '-'. date("his");
            $data['initial_file'] = $filename;
            $data['is_adjusted'] = false;
            SalePrice::create($data);
            return redirect()->back()->with('message', 'Manage Sale created successfully! Please download the initial file to complete it.');
        }
        else
            return redirect()->back()->with('not_permitted', 'No product found!');
    }
    public function store(Request $request)
    {
        $data = $request->all();
        if( isset($data['brand_id']) && isset($data['category_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.category_id', $data['category_id'] )->whereIn('products.brand_id', $data['brand_id'] )->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.qty')->get();

            $data['category_id'] = implode(",", $data['category_id']);
            $data['brand_id'] = implode(",", $data['brand_id']);
        }
        elseif( isset($data['category_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.category_id', $data['category_id'])->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.qty')->get();

            $data['category_id'] = implode(",", $data['category_id']);
        }
        elseif( isset($data['brand_id']) ){
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->whereIn('products.brand_id', $data['brand_id'])->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.qty')->get();

            $data['brand_id'] = implode(",", $data['brand_id']);
        }
        else{
            $lims_product_list = DB::table('products')->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')->where([ ['products.is_active', true], ['product_warehouse.warehouse_id', $data['warehouse_id']] ])->select('products.name', 'products.code', 'product_warehouse.imei_number', 'product_warehouse.qty')->get();
        }
        if( count($lims_product_list) ){
            $csvData=array('Product Name, Product Code, IMEI or Serial Numbers, Expected, Counted');
            foreach ($lims_product_list as $product) {
                $csvData[]=$product->name.','.$product->code.','.str_replace(",","/",$product->imei_number).','.$product->qty.','.'';
            }
            //return $csvData;
            $filename= date('Ymd').'-'.date('his'). ".csv";
            $file_path= public_path().'/stock_count/'.$filename;
            $file = fopen($file_path, "w+");
            foreach ($csvData as $cellData){
              fputcsv($file, explode(',', $cellData));
            }
            fclose($file);

            $data['user_id'] = Auth::id();
            $data['reference_no'] = 'scr-' . date("Ymd") . '-'. date("his");
            $data['initial_file'] = $filename;
            $data['is_adjusted'] = false;
            StockCount::create($data);
            return redirect()->back()->with('message', 'Stock Count created successfully! Please download the initial file to complete it.');
        }
        else
            return redirect()->back()->with('not_permitted', 'No product found!');
    }

    public function finalize(Request $request)
    {
        $ext = pathinfo($request->final_file->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');

        $data = $request->all();
        $document = $request->final_file;
        $documentName = date('Ymd').'-'.date('his'). ".csv";
        $document->move(public_path('stock_count/'), $documentName);
        $data['final_file'] = $documentName;
        $lims_stock_count_data = StockCount::find($data['stock_count_id']);
        $lims_stock_count_data->update($data);
        return redirect()->back()->with('message', 'Stock Count finalized successfully!');
    }
    public function sale_finalize(Request $request)
    {
        // dd($request->all());
        $ext = pathinfo($request->final_file->getClientOriginalName(), PATHINFO_EXTENSION);
        //checking if this is a CSV file
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');

        $data = $request->all();
        // dd($data);
        $document = $request->final_file;
        $documentName = date('Ymd').'-'.date('his'). ".csv";
        $document->move(public_path('sale_price/'), $documentName);
        $data['final_file'] = $documentName;
        $lims_stock_count_data = SalePrice::find($data['stock_count_id']);
        $lims_stock_count_data->update($data);
        return redirect()->back()->with('message', 'Manage Sale finalized successfully!');
    }

    public function stockDif($id)
    {
        // dd($id);
        $lims_stock_count_data = StockCount::find($id);
        $file_handle = fopen('stock_count/'.$lims_stock_count_data->final_file, 'r');
        $i = 0;
        $temp_dif = -1000000;
        $data = [];
        $product = [];
        while( !feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if( $current_line && $i > 0 && ($current_line[3] != $current_line[4]) ){
                $product_data = Product::where('code', $current_line[1])->first();
                if(!$product_data) {
                    $product_data = Product::where('code', 'LIKE', "%{$current_line[1]}%")->first();
                }
                if($product_data) {
                    $product[] = $current_line[0].' ['.$product_data->code.']';
                    $expected[] = $current_line[3];
                    if($current_line[4]){
                        $difference[] = $temp_dif = $current_line[4] - $current_line[3];
                        $counted[] = $current_line[4];
                    }
                    else{
                        $difference[] = $temp_dif = $current_line[3] * (-1);
                        $counted[] = 0;
                    }
                    $cost[] = $product_data->cost * $temp_dif;
                }
            }
            $i++;
        }
        if($temp_dif == -1000000){
            $lims_stock_count_data->is_adjusted = true;
            $lims_stock_count_data->save();
        }
        if( count($product) ) {
            $data[] = $product;
            $data[] = $expected;
            $data[] = $counted;
            $data[] = $difference;
            $data[] = $cost;
            $data[] = $lims_stock_count_data->is_adjusted;
        }
        return $data;
    }

    public function qtyAdjustment($id)
    {
        // dd('a', $id);
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $lims_stock_count_data = StockCount::find($id);
        $warehouse_id = $lims_stock_count_data->warehouse_id;
        $file_handle = fopen('stock_count/'.$lims_stock_count_data->final_file, 'r');
        $i = 0;
        $product_id = [];
        while( !feof($file_handle) ) {
            $current_line = fgetcsv($file_handle);
            if( $current_line && $i > 0 && ($current_line[3] != $current_line[4]) ){
                $product_data = Product::where('code', $current_line[1])->first();
                $product_id[] = $product_data->id;
                $names[] = $current_line[0];
                $code[] = $current_line[1];

                if($current_line[4])
                    $temp_qty = $current_line[4] - $current_line[3];
                else
                    $temp_qty = $current_line[3] * (-1);

                if($temp_qty < 0){
                    $qty[] = $temp_qty * (-1);
                    $action[] = '-';
                }
                else{
                    $qty[] = $temp_qty;
                    $action[] = '+';
                }
            }
            $i++;
        }
        return view('backend.stock_count.qty_adjustment', compact('lims_warehouse_list', 'warehouse_id', 'id', 'product_id', 'names', 'code', 'qty', 'action'));
    }
    public function destroy($id)
    {
        //
    }
}

@extends('backend.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif


<section>
    <div class="filter-section">
        <div style="display: flex; justify-content: center; gap: 20px;;">
            <div>
                <label for="warehouse-filter">Warehouse:</label>
                <select id="warehouse-filter" class="form-control">
                    <option value="">All</option>
                    @foreach($lims_warehouse_list as $warehouse)
                        <option value="{{$warehouse->name}}">{{$warehouse->name}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="category-filter">Category:</label>
                <select id="category-filter" class="form-control">
                    <option value="">All</option>
                    @foreach($lims_category_list as $category)
                    <option value="{{$category->name}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="brand-filter">Brand:</label>
                <select id="brand-filter" class="form-control">
                    <option value="">All</option>
                    @foreach($lims_brand_list as $brand)
                    <option value="{{$brand->title}}">{{$brand->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-top: 10px; text-align: center;">
            <button id="filter-button" class="btn btn-primary">Filter</button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="products-table" class="table stock-count-list">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Product Name')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.Category')}}</th>
                    <th>{{trans('file.Brand')}}</th>
                    <th>{{trans('file.Quantity')}}</th>
                    <th>{{trans('file.Price')}}</th>
                    <th>{{trans('file.Total Shipping/Discount')}}</th>
                    <th style="display: none;">{{trans('file.Total Discount')}}</th>
                    <th>{{trans('file.Actual Cost')}}</th>
                    <th>{{trans('file.Profit Type')}}</th>
                    <th>{{trans('file.Profit Margin')}}</th>
                    <th>{{trans('file.Sale Price')}}</th>
                    <th style="display: none;">{{trans('file.aaa')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($get_all_products as $key => $get_P)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{ $get_P->product_name}} <br>Batch No-{{ $get_P->batch_no }} </td>
                        <td>{{ $get_P->warehouse_name  }}</td>
                        <td>{{ $get_P->name }}</td>
                        <td>{{ $get_P->title }}</td>
                        <td>{{ $get_P->qty }}</td>
                        <td>{{ $get_P->price }}</td>
                        <td>{{ $get_P->t_shipping }}/ {{ $get_P->t_disc }}</td>
                        <td style="display: none;">{{ $get_P->t_disc }}</td>
                        <td>{{ $get_P->actual_cost }}</td>
                        <td>
                            <select class="profit-type form-control" data-key="{{$key}}">
                                <option value="discount">Percentage</option>
                                <option value="flat">Flat</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="profit-margin form-control" data-key="{{$key}}" value="10">
                        </td>
                        <td>
                            <input type="text" class="sale-price form-control" data-key="{{$key}}" value="{{ $get_P->Sale_price }}">
                        </td>
                        <td style="display: none;">
                            <input type="hidden" class="product-id" value="{{ $get_P->product_id }}">
                            <input type="hidden" class="product-batch-id" value="{{ $get_P->product_batch_id }}">
                            <input type="hidden" class="warehouse-id" value="{{ $get_P->warehouse_id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="tfoot active">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
        <button id="submit-updates">Submit</button>
    </div>
</section>



<section>
    <div class="container-fluid">
        <button class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> {{trans('Manage Sale Price')}} </button>
    </div>
    <div class="table-responsive">
        <table id="stock-count-table" class="table stock-count-list">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.category')}}</th>
                    <th>{{trans('file.Brand')}}</th>
                    <th>{{trans('file.Type')}}</th>
                    <th class="not-exported">{{trans('file.Initial File')}}</th>
                    <th class="not-exported">{{trans('file.Final File')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_stock_count_all as $key => $stock_count)
                <?php
                    $warehouse = DB::table('warehouses')->find($stock_count->warehouse_id);
                    $category_name = [];
                    $brand_name = [];
                    $initial_file = 'sale_price/' . $stock_count->initial_file;
                    $final_file = 'sale_price/' . $stock_count->final_file;
                ?>
                <tr>
                    <td>{{$key}}</td>
                    <td>{{ date($general_setting->date_format, strtotime($stock_count->created_at->toDateString())) . ' '. $stock_count->created_at->toTimeString() }}</td>
                    <td>{{ $stock_count->reference_no }}</td>
                    <td>{{ $warehouse->name }}</td>
                    <td>
                        @if($stock_count->category_id)
                            @foreach(explode(",",$stock_count->category_id) as $cat_key=>$category_id)
                            @php
                                $category = \DB::table('categories')->find($category_id);
                                $category_name[] = $category->name;
                            @endphp
                                @if($cat_key)
                                    {{', ' . $category->name}}
                                @else
                                    {{$category->name}}
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if($stock_count->brand_id)
                            @foreach(explode(",",$stock_count->brand_id) as $brand_key=>$brand_id)
                            @php
                                $brand = \DB::table('brands')->find($brand_id);
                                $brand_name[] = $brand->title;
                            @endphp
                                @if($brand_key)
                                    {{', '.$brand->title}}
                                @else
                                    {{$brand->title}}
                                @endif
                            @endforeach
                        @endif
                    </td>
                    @if($stock_count->type == 'full')
                        @php $type = trans('file.Full') @endphp
                        <td><div class="badge badge-primary">{{trans('file.Full')}}</div></td>
                    @else
                        @php $type = trans('file.Partial') @endphp
                        <td><div class="badge badge-info">{{trans('file.Partial')}}</div></td>
                    @endif

                    <td class="text-center">
                        {{-- <a download href="{{$stock_count->initial_file}}" title="{{trans('file.Download')}}"><i class="dripicons-copy"></i></a> --}}
                        <a download href="{{ url('sale_price/' . $stock_count->initial_file) }}" title="{{ trans('file.Download') }}">
                            <i class="dripicons-copy"></i>
                        </a>

                    </td>
                    <td class="text-center">
                        @if($stock_count->final_file)
                        {{-- <a download href="{{$stock_count->final_file}}" title="{{trans('file.Download')}}"><i class="dripicons-copy"></i></a> --}}
                        <a download href="{{ url('sale_price/' . $stock_count->final_file) }}" title="{{ trans('file.Download') }}">
                            <i class="dripicons-copy"></i>
                        </a>

                        @endif
                    </td>
                    <td>
                        @if($stock_count->final_file)
                            <div style="cursor: pointer;" class="badge badge-success final-report" data-stock_count='["{{date($general_setting->date_format, strtotime($stock_count->created_at->toDateString()))}}", "{{$stock_count->reference_no}}", "{{$warehouse->name}}", "{{$type}}", "{{implode(", ", $category_name)}}", "{{implode(", ", $brand_name)}}", "{{$initial_file}}", "{{$final_file}}", "{{$stock_count->id}}"]'>{{trans('file.Final Report')}}
                            </div>
                        @else
                            <div style="cursor: pointer;" class="badge badge-primary finalize" data-id="{{$stock_count->id}}">{{trans('file.Finalize')}}
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="tfoot active">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'stock-count.sale_store', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Manage Sale Price')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Warehouse')}} *</label>
                    <select required name="warehouse_id" id="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                        @foreach($lims_warehouse_list as $warehouse)
                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Type')}} *</label>
                    <select class="form-control" name="type">
                        <option value="full">{{trans('file.Full')}}</option>
                        <option value="partial">{{trans('file.Partial')}}</option>
                    </select>
                </div>
                <div class="col-md-6 form-group" id="category">
                    <label>{{trans('file.category')}}</label>
                    <select name="category_id[]" id="category_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Category..." multiple>
                        @foreach($lims_category_list as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group" id="brand">
                    <label>{{trans('file.Brand')}}</label>
                    <select name="brand_id[]" id="brand_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Brand..." multiple>
                        @foreach($lims_brand_list as $brand)
                        <option value="{{$brand->id}}">{{$brand->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
              <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

<div id="finalizeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
        {{ Form::open(['route' => 'stock-count.sale_finalize', 'method' => 'POST', 'files' => true] ) }}
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title"> {{trans('file.Finalize Manage Sale')}}</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
      </div>
        <div class="modal-body">
            <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.<strong>{{trans('file.You just need to update the Counted column in the initial file')}}</strong> </small></p>
            <div class="form-group">
                <label>{{trans('file.Upload File')}} *</label>
                <input required type="file" name="final_file" class="form-control" />
            </div>
            <input type="hidden" name="stock_count_id">
            <div class="form-group">
                <label>{{trans('file.Note')}}</label>
                <textarea rows="3" name="note" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
              </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<div id="stock-count-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="container mt-3 pb-3">
                <div class="row border-bottom pb-2">
                    <div class="col-md-3">
                        <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                    </div>
                    <div class="col-md-6">
                        <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="col-md-12 text-center">
                        <i style="font-size: 15px;">{{trans('file.Stock Count')}}</i>
                    </div>
                </div>
                <br>
                <div id="stock-count-content">
                </div>
                <br>
                <table class="table table-bordered stockdif-list">
                    <thead>
                        <th>#</th>
                        <th>{{trans('file.product')}}</th>
                        <th>{{trans('file.Expected')}}</th>
                        <th>{{trans('file.Counted')}}</th>
                        <th>{{trans('file.Difference')}}</th>
                        <th>{{trans('file.Cost')}}</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div id="stock-count-footer"></div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css">
<script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
<script type="text/javascript">

    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #stock-count-menu").addClass("active");

    $("#category, #brand").hide();

    $('select[name=type]').on('change', function(){
        if($(this).val() == 'partial')
            $("#category, #brand").show(500);
        else
            $("#category, #brand").hide(500);
    });

    $(document).on('click', '.finalize', function(){
        $('input[name="stock_count_id"]').val($(this).data('id'));
        $('#finalizeModal').modal('show');
    });

    $(document).on('click', '.final-report', function(){
        var stock_count = $(this).data('stock_count');
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+stock_count[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+stock_count[1]+'<br><strong>{{trans("file.Warehouse")}}: </strong>'+stock_count[2]+'<br><strong>{{trans("file.Type")}}: </strong>'+stock_count[3];
        if(stock_count[4])
            htmltext += '<br><strong>{{trans("file.category")}}: </strong>'+stock_count[4];
        if(stock_count[5])
            htmltext += '<br><strong>{{trans("file.Brand")}}: </strong>'+stock_count[5];
        htmltext += '<br><span class="d-print-none mt-1"><strong>{{trans("file.Files")}}: </strong>&nbsp;&nbsp;<a href="'+stock_count[6]+'" class="btn btn-sm btn-primary"><i class="dripicons-download"></i> {{trans("file.Initial File")}}</a>&nbsp;&nbsp;<a href="'+stock_count[7]+'" class="btn btn-sm btn-info"><i class="dripicons-download"></i> {{trans("file.Final File")}}</a></span>';
        $.get('stock-count/stockdif/' + stock_count[8], function(data){
            $(".stockdif-list tbody").remove();
            var name_code = data[0];
            var expected = data[1];
            var counted = data[2];
            var dif = data[3];
            var cost = data[4];
            var newBody = $("<tbody>");
            if(name_code){
                $('.stockdif-list').removeClass('d-none')
                $.each(name_code, function(index){
                    var newRow = $("<tr>");
                    var cols = '';
                    cols += '<td><strong>' + (index+1) + '</strong></td>';
                    cols += '<td>' + name_code[index] + '</td>';
                    cols += '<td>' + parseFloat(expected[index]).toFixed({{$general_setting->decimal}}) + '</td>';
                    cols += '<td>' + parseFloat(counted[index]).toFixed({{$general_setting->decimal}}) + '</td>';
                    cols += '<td>' + parseFloat(dif[index]).toFixed({{$general_setting->decimal}}) + '</td>';
                    cols += '<td>' + parseFloat(cost[index]).toFixed({{$general_setting->decimal}}) + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                });

                if( !parseInt(data[5]) ) {
                    htmlFooter = '<a class="btn btn-primary d-print-none" href="stock-count/'+stock_count[8]+'/qty_adjustment"><i class="dripicons-plus"></i> {{trans("file.Add Adjustment")}}</a>';
                    $('#stock-count-footer').html(htmlFooter);
                }
            }
            else{
                $('.stockdif-list').addClass('d-none');
                $('#stock-count-footer').html('');
            }

            /*var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.Order Discount")}}:</strong></td>';
            cols += '<td>' + sale[19] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);


            newRow.append(cols);
            newBody.append(newRow);*/

            $("table.stockdif-list").append(newBody);
        });

        $('#stock-count-content').html(htmltext);
        $('#stock-count-details').modal('show');
    });

    $(document).on("click", "#print-btn", function(){
          var divToPrint=document.getElementById('stock-count-details');
          var newWin=window.open('','Print-Window');
          newWin.document.open();
          newWin.document.write('<link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
          newWin.document.close();
          setTimeout(function(){newWin.close();},10);
    });

    $(document).ready(function () {
        // When profit-type or profit-margin is changed or typed
        $(".profit-type, .profit-margin").on("change keyup", function () {
            var key = $(this).data("key");
            console.log('key', key);
            console.log('actualCost Raw:', $("tr").eq(key + 1).find("td:eq(9)").html());

            var actualCost = parseFloat($("tr").eq(key + 1).find("td:eq(9)").text());
            console.log('actualCost', actualCost);
            var profitType = $(".profit-type[data-key='" + key + "']").val();
            console.log('profitType', profitType);
            var profitMargin = parseFloat($(".profit-margin[data-key='" + key + "']").val()) || 0;
            console.log('profitMargin', profitMargin);

            var salePrice = 0;

            if (profitType === "discount") {
                salePrice = actualCost * (1 + (profitMargin / 100));
                console.log('salePrice', salePrice);
            } else if (profitType === "flat") {
                salePrice = actualCost + profitMargin;
            }

            $(".sale-price[data-key='" + key + "']").val(salePrice.toFixed(2)); // Update Sale Price
        });

        // When sale price is typed (reverse calculation)
        $(".sale-price").on("keyup", function () {
            var key = $(this).data("key");
            console.log('key', key);
            console.log('actualCost Raw:', $("tr").eq(key + 1).find("td:eq(9)").html());

            var actualCost = parseFloat($("tr").eq(key + 1).find("td:eq(9)").text());
            console.log('actualCost', actualCost);
            var profitType = $(".profit-type[data-key='" + key + "']").val();
            console.log('profitType', profitType);
            var salePrice = parseFloat($(this).val()) || 0;
            console.log('salePrice', salePrice);

            var profitMargin = 0;

            if (profitType === "discount") {
                profitMargin = ((salePrice / actualCost) - 1) * 100;
                console.log('profitMargin (percentage)', profitMargin);
            } else if (profitType === "flat") {
                profitMargin = salePrice - actualCost;
                console.log('profitMargin (flat)', profitMargin);
            }

            $(".profit-margin[data-key='" + key + "']").val(profitMargin.toFixed(2)); // Update Profit Margin
        });
    });



    $(document).ready(function() {
        var table = $('#products-table').DataTable({
            "order": [],
            'language': {
                'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
                "info": '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                "search": '{{trans("file.Search")}}',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0, 7, 8, 9]
                },
                {
                    'render': function(data, type, row, meta) {
                        if (type === 'display') {
                            data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                        }
                        return data;
                    },
                    'checkboxes': {
                        'selectRow': true,
                        'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                    },
                    'targets': [0]
                }
            ],
            'select': { style: 'multi', selector: 'td:first-child input.dt-checkboxes' },
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"row"lfB>rtip',
            buttons: [
                    {
                        extend: 'pdf',
                        text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                        exportOptions: {
                            columns: ':visible:Not(.not-exported)',
                            rows: ':visible',
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i title="export to excel" class="dripicons-document-new"></i>',
                        exportOptions: {
                            columns: ':visible:Not(.not-exported)',
                            rows: ':visible',
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                        exportOptions: {
                            columns: ':visible:Not(.not-exported)',
                            rows: ':visible',
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i title="print" class="fa fa-print"></i>',
                        exportOptions: {
                            columns: ':visible:Not(.not-exported)',
                            rows: ':visible',
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i title="column visibility" class="fa fa-eye"></i>',
                        columns: ':gt(0)'
                    },
                ],
        });

        // $('#products-table').on('change keyup', '.sale-price',  function() {

        //     var row = $(this).closest('tr');
        //     console.log('awe',row);
        //     var checkbox = row.find('.dt-checkboxes input');
        //     checkbox.prop('checked', true);
        // });


        $('#products-table').on('change keyup', '.sale-price, .profit-margin', function() {
    var row = $(this).closest('tr');
    var rowIndex = table.row(row).index();

    if (rowIndex !== undefined) {
        table.row(rowIndex).select();
        table.cell(rowIndex, 0).checkboxes.select(); // Select the checkbox
    }




});


        $('#products-table').on('draw.dt', function() {
    $('.dt-checkboxes input:checked').each(function() {
        $(this).prop('checked', true);
    });
});




        // $('#products-table').on('change keyup', '.profit-margin', function() {

        //     var row = $(this).closest('tr');
        //     console.log('awe',row);
        //     var checkbox = row.find('.dt-checkboxes input');
        //     console.log(checkbox);
        //     checkbox.prop('checked', true);
        // });

        // Filter button click event
        $('#filter-button').on('click', function() {
            var warehouse = $('#warehouse-filter').val();
            var category = $('#category-filter').val();
            var brand = $('#brand-filter').val();
            table.columns(2).search(warehouse).draw();
            table.columns(3).search(category).draw();
            table.columns(4).search(brand).draw();
        });

        // Submit only selected rows when clicking the submit button
        $('#submit-updates').on('click', function() {
            console.log('Submitting selected rows...');

            let productData = [];

            $('#products-table tbody tr').each(function() {
                var checkbox = $(this).find('.dt-checkboxes input');
                if (checkbox.is(':checked')) {
                    let row = $(this);
                    let product_id = row.find('.product-id').val();
                    let warehouse_id = row.find('.warehouse-id').val();
                    let product_batch_id = row.find('.product-batch-id').val();
                    let qty = row.find('td:eq(4)').text().trim(); // Quantity column
                    let sale_price = row.find('.sale-price').val().trim();

                    // Ensure values are not undefined before pushing
                    if (product_id && warehouse_id && product_batch_id) {
                        productData.push({
                            product_id: product_id,
                            warehouse_id: warehouse_id,
                            product_batch_id: product_batch_id,
                            qty: qty,
                            sale_price: sale_price
                        });
                    }
                }
            });

            console.log('submitData', productData);

            // Send data to the backend
            $.ajax({
                url: "{{ route('stock-count.updateProductWarehouse') }}", // Adjust route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}", // CSRF Token
                    products: productData
                },
                success: function(response) {
                    console.log('response', response);
                    if (response.message) {
                        alert("Stock updated successfully!");
                    } else {
                        alert("Error updating stock.");
                    }
                },
                error: function(xhr, status, error) {
                    alert("An error occurred while updating stock.");
                }
            });
        });
    });


    $('#stock-count-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 7, 8, 9]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                },
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                },
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                },
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                },
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );


    // $(document).ready(function () {
    //     $('#submit-updates').on('click', function () {
    //         console.log('alaoajdsd');
    //         let productData = [];

    //         $('#products-table tbody tr').each(function () {
    //             let row = $(this);
    //             let product_id = row.find('.product-id').val();
    //             let warehouse_id = row.find('.warehouse-id').val();
    //             let product_batch_id = row.find('.product-batch-id').val();
    //             let qty = row.find('td:eq(4)').text().trim(); // Quantity column
    //             let sale_price = row.find('.sale-price').val().trim();

    //             // Ensure values are not undefined before pushing
    //             if (product_id && warehouse_id && product_batch_id) {
    //                 productData.push({
    //                     product_id: product_id,
    //                     warehouse_id: warehouse_id,
    //                     product_batch_id: product_batch_id,
    //                     qty: qty,
    //                     sale_price: sale_price
    //                 });
    //             }
    //         });
    //         console.log('submitData', productData);

    //         // Send data to the backend
    //         $.ajax({
    //             url: "{{ route('stock-count.updateProductWarehouse') }}", // Adjust route
    //             type: "POST",
    //             data: {
    //                 _token: "{{ csrf_token() }}", // CSRF Token
    //                 products: productData
    //             },
    //             success: function (response) {
    //                 console.log('response',response);
    //                 if (response.message) {
    //                     alert("Stock updated successfully!");
    //                 } else {
    //                     alert("Error updating stock.");
    //                 }
    //             },
    //             error: function (xhr, status, error) {
    //                 // console.error(xhr.responseText);
    //                 alert("An error occurred while updating stock.");
    //             }
    //         });
    //     });
    // });




</script>
@endpush

@extends('backend.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<style>
@media print {
    .page-break {
        page-break-before: always;
    }
}
</style>


<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Quotation List')}}</h3>
            </div>
            {!! Form::open(['route' => 'quotations.index', 'method' => 'get']) !!}
            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Your Date')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$starting_date}} To {{$ending_date}}" required />
                                <input type="hidden" name="starting_date" value="{{$starting_date}}" />
                                <input type="hidden" name="ending_date" value="{{$ending_date}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Warehouse')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($lims_warehouse_list as $warehouse)
                                    @if($warehouse->id == $warehouse_id)
                                        <option selected value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @else
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" id="filter-btn" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @if(in_array("quotes-add", $all_permission))
            <a href="{{route('quotations.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Quotation')}}</a>&nbsp;
        @endif
    </div>
    <div class="table-responsive">
        <table id="quotation-table" class="table quotation-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('Company Name')}}</th>
                    <th>{{trans('Head Office Address')}}</th>
                    <th>{{trans('Factory Office Address')}}</th>
                    <th>{{trans('Contact Person')}}</th>
                    <th>{{trans('Phone')}}</th>
                    <th>{{trans('Email')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>

            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
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

<div id="quotation-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                <div class="col-md-6 d-print-none">
                    <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                    {{ Form::open(['route' => 'quotation.sendmail', 'method' => 'post', 'class' => 'sendmail-form'] ) }}
                        <input type="hidden" name="quotation_id">
                        <button class="btn btn-default btn-sm d-print-none"><i class="dripicons-mail"></i> {{trans('file.Email')}}</button>
                    {{ Form::close() }}

                    <button type="button" class="btn btn-success btn-sm d-print-none" id="whatsapp-btn">
                        <i class="dripicons-message"></i> Send via WhatsApp
                    </button>
                </div>

                <div class="col-md-6 d-print-none">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="col-md-12 text-center">
                    <img src="{{url('logo', $general_setting->site_logo)}}" height="70" width="120">
                </div>
                <div class="col-md-12 text-center">
                    <i style="font-size: 15px;">{{trans('file.Quotation Details')}}</i>
                </div>
            </div>
        </div>
            <div id="quotation-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-quotation-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>{{trans('file.Batch No')}}</th>
                    <th>Qty</th>
                    <th>{{trans('file.Unit Price')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Discount')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="quotation-footer" class="modal-body"></div>
      </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script type="text/javascript">

    $("ul#quotation").siblings('a').attr('aria-expanded','true');
    $("ul#quotation").addClass("show");
    $("ul#quotation #quotation-list-menu").addClass("active");

    $(".daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period){
        var starting_date = startDate.format('YYYY-MM-DD');
        var ending_date = endDate.format('YYYY-MM-DD');
        var title = starting_date + ' To ' + ending_date;
        $(this).val(title);
        $('input[name="starting_date"]').val(starting_date);
        $('input[name="ending_date"]').val(ending_date);
      }
    });

    var all_permission = <?php echo json_encode($all_permission) ?>;
    var quotation_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    $(document).on("click", "tr.quotation-link td:not(:first-child, :last-child)", function() {
        var quotation = $(this).parent().data('quotation');
        quotationDetails(quotation);
    });

    $(document).on("click", ".view", function() {
        var quotation = $(this).parent().parent().parent().parent().parent().data('quotation');
        quotationDetails(quotation);
    });

    // $("#print-btn").on("click", function(){
    //     var divContents = document.getElementById("quotation-details").innerHTML;
    //     var a = window.open('');
    //     a.document.write('<html>');
    //     a.document.write('<body><style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
    //     a.document.write(divContents);
    //     a.document.write('</body></html>');
    //     a.document.close();
    //     setTimeout(function(){a.close();},10);
    //     a.print();
    // });
    $("#print-btn").on("click", function(){
    var divContents = document.getElementById("quotation-details").innerHTML;
    var attachment = $('.product-quotation-list').data('attachment'); // get from a data attribute
    console.log('attachment',attachment);


    var a = window.open('');
    a.document.write('<html>');
    a.document.write('<body><style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');

    a.document.write(divContents);

    // if (attachment) {
    //     a.document.write('<hr><h4>Attachment:</h4><iframe src="' + attachment + '" width="100%" height="600px" style="border:none;"></iframe>');
    // }
//





    a.document.write('</body></html>');
    a.document.close();

    setTimeout(function(){
        a.focus();
        a.print();
    }, 1000);
});


    $(document).on("click", "#whatsapp-btn", function () {
        let saleId = $(this).closest('div').find('input[name="quotation_id"]').val(); // Fetch the correct sale ID
    console.log('saleId:', saleId);

        if (!saleId) {
            alert("Sale ID is missing!");
            return;
        }

        fetch("{{ route('quotation.sendwhatsapp') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ sale_id: saleId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log(data);
                let whatsappUrl = `https://api.whatsapp.com/send?phone=${data.phone}&text=${encodeURIComponent(data.message)}`;

                window.open(whatsappUrl, '_blank');
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    var starting_date = $("input[name=starting_date]").val();
    var ending_date = $("input[name=ending_date]").val();
    var warehouse_id = $("#warehouse_id").val();
    $('#quotation-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"quotations/quotation-data",
            data:{
                all_permission: all_permission,
                starting_date: starting_date,
                ending_date: ending_date,
                warehouse_id: warehouse_id
            },
            dataType: "json",
            type:"post",
            /*success:function(data){
                console.log(data);
            }*/
        },
        "createdRow": function( row, data, dataIndex ) {
            console.log('dataaaaaa',data);
            $(row).addClass('quotation-link');
            $(row).attr('data-quotation', data['quotation']);
        },
        "columns": [
            {"data": "key"},
            {"data": "date"},
            {"data": "reference_no"},
            {"data": "company_name"},
            {"data": "head"},
            {"data": "factory"},
            {"data": "customer"},
            {"data": "phone"},
            {"data": "email"},
            {"data": "grand_total"},
            {"data": "options"},
        ],
        'language': {
            /*'searchPlaceholder': "{{trans('file.Type date or quotation reference...')}}",*/
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['1', 'desc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 3, 4, 7, 8,9]
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
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        quotation_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var quotation = $(this).closest('tr').data('quotation');
                                quotation_id[i-1] = quotation[13];
                            }
                        });
                        if(quotation_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'quotations/deletebyselection',
                                data:{
                                    quotationIdArray: quotation_id
                                },
                                success:function(data) {
                                    alert(data);
                                    //dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!quotation_id.length)
                            alert('Nothing is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }

    if(all_permission.indexOf("quotes-delete") == -1)
        $('.buttons-delete').addClass('d-none');

    function quotationDetails(quotation){
        console.log('quotation',quotation);
        $('input[name="quotation_id"]').val(quotation[13]);
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+quotation[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+quotation[1];
        if(quotation[25])
            // htmltext += '<strong>{{trans("file.Attach Document")}}: </strong><a href="documents/quotation/'+quotation[25]+'">Download</a><br>';
        // htmltext += '<br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>'+quotation[3]+'<br>'+quotation[4]+'<br>'+quotation[5]+'<br>'+quotation[6]+'<br>'+quotation[7]+'<br>'+quotation[8]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+quotation[9]+'<br>'+quotation[10]+'<br>'+quotation[11]+'<br>'+quotation[12]+'</div></div></div>';
        htmltext += `
<table style="width: 100%;border: 0px solid black!important; margin-top: 0px !important; margin-bottom: 0px !important;">
    <tr>
        <td style="border: 0px solid black!important; padding: 0px !important;">
            <div style="display: flex; justify-content: space-between;">
                <div>
                     <strong>{{ trans("From") }}:</strong> <br>
                     ${quotation[3]}<br>
   ${quotation[4]}<br>
   ${quotation[7]}<br>
                  ${quotation[8]}
                </div>
                <div >
                    <strong>{{ trans("To") }}:</strong> <br>
                      ${quotation[25]}<br>
                     ${quotation[27]}<br>
                    ${quotation[28]}
                </div>
            </div>
        </td>
    </tr>
</table>
`;
        $.get('quotations/product_quotation/' + quotation[13], function(data){
            $(".product-quotation-list tbody").remove();
            var name_code = data[0];
            var qty = data[1];
            var unit_code = data[2];
            var tax = data[3];
            var tax_rate = data[4];
            var discount = data[5];
            var subtotal = data[6];
            var batch_no = data[7];
       var attachment = (Array.isArray(data[8]) ? data[8][0] : data[8]) || '';
attachment = String(attachment).trim();

console.log('Processed attachment:', attachment);
        $('.product-quotation-list').attr('data-attachment', attachment || '');
            var newBody = $("<tbody>");
            $.each(name_code, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + name_code[index] + '</td>';
                cols += '<td>' + batch_no[index] + '</td>';
                cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';
                cols += '<td>' + parseFloat(subtotal[index] / qty[index]).toFixed({{$general_setting->decimal}}) + '</td>';
                cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';
                cols += '<td>' + discount[index] + '</td>';
                cols += '<td>' + subtotal[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=5><strong>{{trans("file.Total")}}:</strong></td>';
            cols += '<td>' + quotation[14] + '</td>';
            cols += '<td>' + quotation[15] + '</td>';
            cols += '<td>' + quotation[16] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            // var newRow = $("<tr>");
            // cols = '';
            // cols += '<td colspan=7><strong>{{trans("Tax")}}:</strong></td>';
            // cols += '<td>' + quotation[17] + '(' + quotation[18] + '%)' + '</td>';
            // newRow.append(cols);
            // newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=7><strong>{{trans(" Discount")}}:</strong></td>';
            cols += '<td>' + quotation[19] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=7><strong>{{trans("Transportation Cost")}}:</strong></td>';
            cols += '<td>' + quotation[20] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=7><strong>{{trans("file.grand total")}}:</strong></td>';
            cols += '<td>' + quotation[21] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            $("table.product-quotation-list").append(newBody);
             // ✅ Render PDF as image if attachment exists
             console.log('ATTACHMENT VALUE:', attachment);
console.log('TYPE:', typeof attachment);



            if (attachment && typeof attachment === 'string' && attachment.endsWith('.pdf')) {


    const url = window.location.origin + '/zaman/uploads/attachments/' + attachment;
console.log('dfsvdv', url);
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const loadingTask = pdfjsLib.getDocument(url);
    loadingTask.promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
            const scale = 1.5;
            const viewport = page.getViewport({ scale: scale });

            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext).promise.then(function () {
                const imageData = canvas.toDataURL("image/png");

                // Add a page break before the image
                const imgWrapper = document.createElement('div');
                imgWrapper.className = "page-break";
                imgWrapper.innerHTML = '<img src="' + imageData + '" style="width:100%;max-width:1000px;" />';

                document.getElementById('quotation-footer').appendChild(imgWrapper);
            });
        });
    }, function (reason) {
        console.error("PDF load error: ", reason);
    });
}

        });
       var note = quotation[22];
var terms = quotation[29]; // comma-separated string
var termsArray = terms.split(','); // convert to array

// Build the HTML for terms
var termsHtml = '<ul>';
termsArray.forEach(function(term) {
    termsHtml += '<li>' + term.trim() + '</li>';
});
termsHtml += '</ul>';

// Final HTML footer
var htmlfooter = '<p><strong>{{trans("file.Note")}}:</strong> ' + note + '</p><br>';
htmlfooter += '<p><strong>Terms & Conditions:</strong></p>' + termsHtml;

// $('.product-quotation-list').attr('data-attachment', attachment);

        $('#quotation-content').html(htmltext);
        $('#quotation-footer').html(htmlfooter);
        $('#quotation-details').modal('show');


    }
</script>
@endpush

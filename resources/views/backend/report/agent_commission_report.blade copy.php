@extends('backend.layout.main')

@section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    {{ session()->get('not_permitted') }}
  </div>
@endif

<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Agent Commission Report')}}</h3>
            </div>

            {!! Form::open(['route' => 'report.agentCommissionReport', 'method' => 'get']) !!}
            <div class="row mb-3 product-report-filter">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Your Date')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$start_date}} To {{$end_date}}" required />
                                <input type="hidden" name="start_date" value="{{$start_date}}" />
                                <input type="hidden" name="end_date" value="{{$end_date}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Warehouse')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <select name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins">
                                <option value="0">{{trans('file.All Warehouse')}}</option>
                                @foreach($lims_warehouse_list as $warehouse)
                                    <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="table-responsive">
        <table id="agent-commission-report-table" class="table table-hover" style="width: 100%">
            <thead>
                <tr>
                    <th>{{trans('file.Reference No')}}</th>
                    <th>{{trans('file.Warehouse Name')}}</th>
                    <th>{{trans('file.Agent Name')}}</th>
                    <th>{{trans('file.Grand Total')}}</th>
                    <th>{{trans('file.Comission Value')}}</th>
                    <th>{{trans('file.Date')}}</th>
                </tr>
            </thead>

            <tfoot class="tfoot active">
                <th></th>
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var warehouse_id = <?php echo json_encode($warehouse_id)?>;
    $('.product-report-filter select[name="warehouse_id"]').val(warehouse_id);
    $('.selectpicker').selectpicker('refresh');

    $(".daterangepicker-field").daterangepicker({
        callback: function(startDate, endDate, period){
            var start_date = startDate.format('YYYY-MM-DD');
            var end_date = endDate.format('YYYY-MM-DD');
            var title = start_date + ' To ' + end_date;
            $(this).val(title);
            $(".product-report-filter input[name=start_date]").val(start_date);
            $(".product-report-filter input[name=end_date]").val(end_date);
        }
    });

    var start_date = $(".product-report-filter input[name=start_date]").val();
    var end_date = $(".product-report-filter input[name=end_date]").val();
    var warehouse_id = $(".product-report-filter select[name=warehouse_id]").val();
    $('#agent-commission-report-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "agent_commission_report_data",
        type: "POST",
        data: {
            start_date: start_date,
            end_date: end_date,
            warehouse_id: warehouse_id
        }
    },
    columns: [
        { data: "reference_no" },
        { data: "warehouse_name" },
        { data: "agent_name" },
        { data: "grand_total" },
        { data: "commission_value" },
        { data: "created_at" }
    ],
    'language': {
        'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
        "info": '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
        "search": '{{trans("file.Search")}}',
        'paginate': {
            'previous': '<i class="dripicons-chevron-left"></i>',
            'next': '<i class="dripicons-chevron-right"></i>'
        }
    },
    order: [['1', 'desc']],
    'columnDefs': [
        {
            "orderable": false,
            'targets': [0, 3]
        }
    ],
    'lengthMenu': [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
    dom: '<"row"lfB>rtip',
    buttons: [
        {
            extend: 'pdf',
            text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
            exportOptions: {
                columns: ':visible:Not(.not-exported)',
                rows: ':visible'
            }
        }
    ]
});

</script>
@endpush

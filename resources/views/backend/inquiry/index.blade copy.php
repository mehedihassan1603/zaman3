@extends('backend.layout.main')
@section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    {!! session()->get('message') !!}
  </div>
@endif

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    {{ session()->get('not_permitted') }}
  </div>
@endif

<section>
  <div class="container-fluid">
    <div class="card">
      <div class="card-header mt-2">
        <h3 class="text-center">{{ trans('file.Inquiry List') }}</h3>
      </div>
    </div>

    <div class="table-responsive">
      <table id="inquiries-table" class="table inquiries-list" style="width: 100%">
        <thead>
          <tr>
            <th class="not-exported"></th>
            <th>{{ trans('file.Date') }}</th>
            <th>Company Name</th>
            <th>Contact Person</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Requirement</th>
            <th class="not-exported">{{ trans('file.action') }}</th>
          </tr>
        </thead>
        <tfoot class="tfoot active">
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
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

  function confirmDelete() {
    return confirm("Are you sure want to delete?");
  }

  var user_verified = 1; // Set to 0 if demo mode
  var quotation_id = [];

  $('#inquiries-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ url('inquiries/inquiry-data') }}",
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    },
    columns: [
        { data: 'key' },
        { data: 'date' },
        { data: 'company_name' },
        { data: 'contact_person' },
        { data: 'contact_number' },
        { data: 'email' },
        { data: 'requirement' },
        { data: 'options' }
    ],
    language: {
      lengthMenu: '_MENU_ {{trans("file.records per page")}}',
      info: '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
      search: '{{trans("file.Search")}}',
      paginate: {
        previous: '<i class="dripicons-chevron-left"></i>',
        next: '<i class="dripicons-chevron-right"></i>'
      }
    },
    order: [[1, 'desc']],
    columnDefs: [
      {
        orderable: false,
        targets: [0, 3, 4, 7]
      },
      {
        render: function(data, type, row, meta) {
          if (type === 'display') {
            data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
          }
          return data;
        },
        checkboxes: {
          selectRow: true,
          selectAllRender: '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
        },
        targets: [0]
      }
    ],
    select: { style: 'multi', selector: 'td:first-child' },
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom: '<"row"lfB>rtip',
    buttons: [
      {
        extend: 'pdf',
        text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
        exportOptions: {
          columns: ':visible:not(.not-exported)',
          rows: ':visible'
        },
        footer: true
      },
      {
        extend: 'excel',
        text: '<i title="export to excel" class="dripicons-document-new"></i>',
        exportOptions: {
          columns: ':visible:not(.not-exported)',
          rows: ':visible'
        },
        footer: true
      },
      {
        extend: 'print',
        text: '<i title="print" class="fa fa-print"></i>',
        exportOptions: {
          columns: ':visible:not(.not-exported)',
          rows: ':visible'
        },
        footer: true
      },
      {
        text: '<i title="delete" class="dripicons-cross"></i>',
        className: 'buttons-delete',
        action: function(e, dt, node, config) {
          if (user_verified == '1') {
            quotation_id = [];
            $(':checkbox:checked').each(function(i) {
              if (i) {
                var quotation = $(this).closest('tr').data('quotation');
                quotation_id.push(quotation); // Assuming it's just the ID
              }
            });
            if (quotation_id.length && confirm("Are you sure want to delete?")) {
              $.ajax({
                type: 'POST',
                url: "{{ url('quotations/deletebyselection') }}",
                data: {
                  quotationIdArray: quotation_id,
                  _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                  alert(data);
                  dt.rows({ page: 'current', selected: true }).remove().draw(false);
                }
              });
            } else if (!quotation_id.length) {
              alert('Nothing is selected!');
            }
          } else {
            alert('This feature is disabled in demo mode!');
          }
        }
      },
      {
        extend: 'colvis',
        text: '<i title="column visibility" class="fa fa-eye"></i>',
        columns: ':gt(0)'
      }
    ]
  });
</script>
<script>
    const deleteInquiryUrl = "{{ route('inquiry.delete', ':id') }}";

    $(document).on('click', '.delete-inquiry', function() {
        if (!confirm('Are you sure you want to delete this inquiry?')) return;

        let inquiryId = $(this).data('id');
        let url = deleteInquiryUrl.replace(':id', inquiryId);

        $.ajax({
            url: url, // ✅ Now using the correct named route
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#inquiries-table').DataTable().ajax.reload();
                alert(response.message || 'Inquiry deleted successfully.');
            },
            error: function(xhr) {
                alert('Failed to delete. Please try again.');
            }
        });
    });
</script>


@endpush

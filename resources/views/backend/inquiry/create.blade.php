<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inquiry Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .form-label {
            font-weight: 500;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
        }
        a.btn-link {
            color: white;
            text-decoration: none;
        }
    </style>

</head>
<body>
<section class="py-5 bg-light">
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Add New Inquiry</h4>
            <a href="{{route('inquiries.index')}}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('inquiries.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
            @csrf
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                    <select name="company_name" id="company_name" data-url="{{ url('get-company-address') }}" class="form-control" required>

                        <option value="" selected disabled>Select Company Name</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('Head Office Address')}}</label>
                        <input type="text" name="head_office" id="head_office_address" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('Factory Office Address')}}</label>
                        <input type="text" name="factory" id="factory_office_address" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                    <select name="contact_person" id="contact_person" data-url="{{ url('get-customer-details') }}" class="form-control" required>
                        <option value="" selected disabled>Select Contact Person</option>
                    </select>

                    <input type="hidden" name="customer_id" id="customer_id">

                    <input type="hidden" name="contact_person" id="contact_person_name">
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('Phone Number')}}</label>
                        <input type="text" name="contact_number" id="mobile" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('Department')}}</label>
                        <input type="text" name="department_id" id="department" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('Email')}}</label>
                        <input type="text" name="email" id="email" class="form-control">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Requirement (Select Products)</label>
                    <select name="requirement[]" class="form-control select2" multiple required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} ({{ $product->code }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">You can select multiple products</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Referred By</label>
                    <input type="text" name="reffer" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Remark</label>
                    <input type="text" name="remark" class="form-control">
                </div>

                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-primary px-5">Submit Inquiry</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- jQuery, Bootstrap JS, Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    $(document).ready(function () {

        // company select korle office address auto asar code
        $('#company_name').on('change', function () {
            var companyId = $(this).val();
            var baseUrl = $(this).data('url');

            if (companyId) {
                $.ajax({
                    url: baseUrl + '/' + companyId, // e.g., /get-company-address/5
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#head_office_address').val(data.head_office_address);
                        $('#factory_office_address').val(data.factory_office_address);
                    },
                    error: function () {
                        alert('Something went wrong. Please try again.');
                    }
                });
            }
        });


        // contact select korle email mobile asar code
        $('#contact_person').on('change', function () {
            var customerId = $(this).val();
            var baseUrl = $(this).data('url'); // base: /get-customer-details

            if (customerId) {
                $.ajax({
                    url: baseUrl + '/' + customerId, // full: /get-customer-details/5
                    type: 'GET',
                    success: function (data) {
                        console.log('CD data', data);
                        $('#mobile').val(data.phone);
                        $('#department').val(data.department);
                        $('#email').val(data.email);
                         $('#customer_id').val(customerId);   
                        $('#contact_person_name').val(data.name);
                    },
                    error: function () {
                        alert('Something went wrong while fetching data!');
                    }
                });
            }
        });

        // select2 add code
        $('.select2').select2({
            placeholder: "Select Products",
            width: '100%'
        });

        //company select korle contact person asar code
        $('#company_name').on('change', function () {
            var company = $(this).val();
            if (company) {
                $.ajax({
                    url: '{{ route("getContactPerson") }}',
                    type: 'GET',
                    data: { company_name: company },
                    success: function (data) {
                        let $personSelect = $('#contact_person');
                        $personSelect.empty().append('<option value="">Select Contact Person</option>');
                        $.each(data, function (index, person) {
                            let displayText = `${person.name} ( ${person.postal_code} )`;
                            $personSelect.append(`<option value="${person.id}" data-name="${person.name}">${displayText}</option>`);
                        });
                    }
                });
            } else {
                $('#contact_person').empty().append('<option value="">Select Contact Person</option>');
            }
        });

        $('#contact_person').on('change', function () {
            var name = $(this).find(':selected').data('name') || '';
            $('#contact_person_name').val(name);
        });
    });
</script>

</body>
</html>

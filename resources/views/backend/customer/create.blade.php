@extends('backend.layout.main') @section('content')
    @if(session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>{{trans('Add Contact Person')}}</h4>
                        </div>
                        <div class="card-body">
{{--                            <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>--}}
                            {!! Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-4 mt-4 d-none">
                                    <div class="form-group">
                                        <input type="checkbox" name="both" value="1" />&nbsp;
                                        <label>{{trans('file.Both Customer and Supplier')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-4 d-none">
                                    <div class="form-group">
                                        <label>{{trans('file.Customer Group')}}</label>
                                        <select required class="form-control selectpicker" id="customer-group-id" name="customer_group_id">
                                            @foreach($lims_customer_group_all as $customer_group)
                                                <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Company Name')}} <span class="asterisk">*</span></label>
                                        <select class="form-control form-select" id="company_id" name="company_name" required>
                                            <option selected disabled value="">Select Company</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('company_name'))
                                            <span>
                                       <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Contact Person')}} *</strong> </label>
                                        <input type="text" id="name" name="customer_name" required class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Department')}} <span class="asterisk">*</span></label>
                                        <select class="form-control form-select" id="department_id" name="department_id" required>
                                            <option selected disabled value="">Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('department_name'))
                                           <span>
                                                <strong>{{ $errors->first('department_name') }}</strong>
                                           </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Designation')}}</label>
                                        <input type="text" name="postal_code" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Contact Number-1')}} *</label>
                                        <input type="text" name="phone_number" required class="form-control">
                                        @if($errors->has('phone_number'))
                                            <span>
                                       <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Contact Number-2')}}</label>
                                        <input type="text" name="tax_no" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Email')}} <span class="asterisk">*</span></label>
                                        <input type="email" name="email" placeholder="example@example.com" class="form-control">
                                        @if($errors->has('email'))
                                            <span>
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Head Office Address')}}</label>
                                        <input type="text" name="head_office_address" id="head_office_address" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('Factory Office Address')}}</label>
                                        <input type="text" name="factory_office_address" id="factory_office_address" class="form-control">
                                    </div>
                                </div>


                                @foreach($custom_fields as $field)
                                    @if(!$field->is_admin || \Auth::user()->role_id == 1)
                                        <div class="{{'col-md-'.$field->grid_value}}">
                                            <div class="form-group">
                                                <label>{{$field->name}}</label>
                                                @if($field->type == 'text')
                                                    <input type="text" name="{{str_replace(' ', '_', strtolower($field->name))}}" value="{{$field->default_value}}" class="form-control" @if($field->is_required){{'required'}}@endif>
                                                @elseif($field->type == 'number')
                                                    <input type="number" name="{{str_replace(' ', '_', strtolower($field->name))}}" value="{{$field->default_value}}" class="form-control" @if($field->is_required){{'required'}}@endif>
                                                @elseif($field->type == 'textarea')
                                                    <textarea rows="5" name="{{str_replace(' ', '_', strtolower($field->name))}}" value="{{$field->default_value}}" class="form-control" @if($field->is_required){{'required'}}@endif></textarea>
                                                @elseif($field->type == 'checkbox')
                                                    <br>
                                                        <?php $option_values = explode(",", $field->option_value); ?>
                                                    @foreach($option_values as $value)
                                                        <label>
                                                            <input type="checkbox" name="{{str_replace(' ', '_', strtolower($field->name))}}[]" value="{{$value}}" @if($value == $field->default_value){{'checked'}}@endif @if($field->is_required){{'required'}}@endif> {{$value}}
                                                        </label>
                                                        &nbsp;
                                                    @endforeach
                                                @elseif($field->type == 'radio_button')
                                                    <br>
                                                        <?php $option_values = explode(",", $field->option_value); ?>
                                                    @foreach($option_values as $value)
                                                        <label class="radio-inline">
                                                            <input type="radio" name="{{str_replace(' ', '_', strtolower($field->name))}}" value="{{$value}}" @if($value == $field->default_value){{'checked'}}@endif @if($field->is_required){{'required'}}@endif> {{$value}}
                                                        </label>
                                                        &nbsp;
                                                    @endforeach
                                                @elseif($field->type == 'select')
                                                        <?php $option_values = explode(",", $field->option_value); ?>
                                                    <select class="form-control" name="{{str_replace(' ', '_', strtolower($field->name))}}" @if($field->is_required){{'required'}}@endif>
                                                        @foreach($option_values as $value)
                                                            <option value="{{$value}}" @if($value == $field->default_value){{'selected'}}@endif>{{$value}}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->type == 'multi_select')
                                                        <?php $option_values = explode(",", $field->option_value); ?>
                                                    <select class="form-control" name="{{str_replace(' ', '_', strtolower($field->name))}}[]" @if($field->is_required){{'required'}}@endif multiple>
                                                        @foreach($option_values as $value)
                                                            <option value="{{$value}}" @if($value == $field->default_value){{'selected'}}@endif>{{$value}}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->type == 'date_picker')
                                                    <input type="text" name="{{str_replace(' ', '_', strtolower($field->name))}}" value="{{$field->default_value}}" class="form-control date" @if($field->is_required){{'required'}}@endif>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="col-md-4 mt-4 d-none">
                                    <div class="form-group">
                                        <input type="checkbox" name="user" value="1" />&nbsp;
                                        <label>{{trans('file.Add User')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-4 user-input">
                                    <div class="form-group">
                                        <label>{{trans('file.UserName')}} *</label>
                                        <input type="text" name="name" class="form-control">
                                        @if($errors->has('name'))
                                            <span>
                                       <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 user-input">
                                    <div class="form-group">
                                        <label>{{trans('file.Password')}} *</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="pos" value="0">
                                <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            $('#company_id').on('change', function () {
                var companyId = $(this).val();

                if (companyId) {
                    $.ajax({
                        url: '/get-company-address/' + companyId,
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
        });
    </script>




    <script type="text/javascript">
        $("ul#people").siblings('a').attr('aria-expanded','true');
        $("ul#people").addClass("show");
        $("ul#people #customer-create-menu").addClass("active");

        $('.asterisk').hide();
        $(".user-input").hide();

        $('input[name="both"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('.asterisk').show();
                $('input[name="company_name"]').prop('required',true);
                $('input[name="email"]').prop('required',true);
                $('input[name="address"]').prop('required',true);
                $('input[name="city"]').prop('required',true);
            }
            else{
                $('.asterisk').hide();
                $('input[name="company_name"]').prop('required',false);
                $('input[name="email"]').prop('required',false);
                $('input[name="address"]').prop('required',false);
                $('input[name="city"]').prop('required',false);
            }
        });

        $('input[name="user"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('.user-input').show(300);
                $('input[name="name"]').prop('required',true);
                $('input[name="password"]').prop('required',true);
            }
            else{
                $('.user-input').hide(300);
                $('input[name="name"]').prop('required',false);
                $('input[name="password"]').prop('required',false);
            }
        });

    </script>
@endpush

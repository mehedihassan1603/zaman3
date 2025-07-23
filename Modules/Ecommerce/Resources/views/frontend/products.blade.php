@extends('ecommerce::frontend.layout.main')

@section('title') @if($category->page_title) {{$category->page_title}} @else {{ $ecommerce_setting->site_title ?? '' }} @endif @endsection

@section('description') @if($category->short_description) {{$category->short_description}} @else  @endif @endsection

@push('css')
<style>
.form-check-label.selected{background: #ddd;opacity:0.5;}
</style>
@endpush
@section('content')
	<!--Breadcrumb Area start-->
    {{-- <div class="breadcrumb-section" style="background: url('https://img.freepik.com/free-vector/stylish-glowing-digital-red-lines-banner_1017-23964.jpg') no-repeat center center/cover; height: 350px;"> --}}
        {{-- <div class="breadcrumb-section" style="background: url('{{ $category->image ? url('images/category/' . $category->image) : asset('images/category/default.jfif') }}') no-repeat center center/cover; height: 350px;">
            <div class="container">
                <div class="row">
                    <div class="col text-center text-white py-5">
                        <h1 class="page-title text-white">{{ $category->name }}</h1>
                        <ul>
                            <li><a href="{{ url('/') }}" class="text-white">Home</a></li>
                            <li class="active">{{ $category->name }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> --}}
        <h1 class="page-title text-white text-center text-black">{{ $category->name }}</h1>

    <!--Breadcrumb Area ends-->
    {{-- <div class="container">
        <div class="row">
            <div class="col text-center text-black py-5">
                <h1 class="page-title">{{ $category->name }}</h1>
                <ul class="breadcrumb">
                    <li><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    <li class="active">{{ $category->name }}</li>
                </ul>
            </div>
        </div>
    </div> --}}

    <!--Shop cart starts-->
    <section class="shop-cart-section">
        <div class="container-fluid">
            <div class="row">
                @if(count($variants) > 0)
                    <div class="col-md-3">


                        @if(count($brands) > 0) <!-- Display brand filter section -->

                            <div class="brand-section mb-3">
                                <h5>Brand</h5>
                                <div class="brand-options">
                                    @foreach($brands as $brand)
                                        <button type="button"
                                            data-b-name="{{ $brand->title }}"
                                            data-b-id="{{ $brand->id }}"
                                            class="brand-btn">
                                            {{ $brand->title }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif



                        @php
                            $uniqueVariants = [];

                            foreach ($variants as $variant) {
                                $options = json_decode($variant->variant_option, true);
                                $values = json_decode($variant->variant_value, true);

                                if (is_array($options) && is_array($values)) {
                                    foreach ($options as $index => $option) {
                                        if (isset($values[$index])) {
                                            if (!isset($uniqueVariants[$option])) {
                                                $uniqueVariants[$option] = [];
                                            }
                                            $uniqueVariants[$option] = array_unique(array_merge($uniqueVariants[$option], explode(',', $values[$index])));
                                        }
                                    }
                                }
                            }
                        @endphp

                        @foreach ($uniqueVariants as $variantType => $options)
                            <div class="variant-section mb-3">
                                <h5>{{ $variantType }}</h5>
                                <div class="variant-options">
                                    @foreach ($options as $option)
                                        <button type="button"
                                            data-v-option="{{ $variantType }}"
                                            data-v-value="{{ $option }}"
                                            class="variant-btn">
                                            {{ $option }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                @endif


            </div>

                <style>
                    .variant-section h5 {
                        font-size: 1.2rem;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }

                    .variant-options {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 10px;
                    }

                    .variant-btn {
                        background: #f8f9fa;
                        border: 2px solid #ddd;
                        padding: 8px 15px;
                        border-radius: 5px;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        min-width: 80px;
                        text-align: center;
                    }

                    .variant-btn:hover {
                        background: #007bff;
                        color: #fff;
                        border-color: #007bff;
                    }

                    .variant-btn.selected {
                        background: #28a745;
                        color: #fff;
                        border-color: #28a745;
                    }
                    .brand-btn {
                        background: #f8f9fa;
                        border: 2px solid #ddd;
                        padding: 8px 15px;
                        border-radius: 5px;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        min-width: 80px;
                        text-align: center;
                    }

                    .brand-btn:hover {
                        background: #007bff;
                        color: #fff;
                        border-color: #007bff;
                    }

                    .brand-btn.selected {
                        background: #28a745;
                        color: #fff;
                        border-color: #28a745;
                    }
                </style>

                <div class="@if(count($variants) > 0 || count($brands) > 0) col-md-9 @else col-12 @endif">
                    <div class="product-grid">
                        @foreach($products as $product)
                            @include('ecommerce::frontend.includes.product-template')
                        @endforeach
                        @if(count($products) == 0)
                            <h3 class="text-center mt-5 mb-5 d-block w-100">Sorry, no products found</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            {!! nl2br(e($category->content)) !!}
        </div>
    </section>

    <!--Shop cart ends-->
@endsection

@section('script')
	<script type="text/javascript">
		"use strict";

		$(document).on('click', '.add-to-cart', function(e){
			e.preventDefault();
            var id = $(this).data('id');
            var parent = '#add_to_cart_'+id;

			var qty = $(parent+" input[name=qty]").val();

			var route = "{{ route('addToCart') }}";

			$.ajax({
		        url: route,
		        type:"POST",
		        data:{
					product_id: id,
					qty: qty,
		        },
		        success:function(response){
			        console.log(response);
		            if(response) {
		            	$('.alert').addClass('alert-custom show');
			            $('.alert-custom .message').html(response.success);
			            $('.cart__menu .cart_qty').html(response.total_qty);
			            $('.cart__menu .total').html('{{$currency->symbol ?? $currency->code}}'+response.subTotal.toFixed(2));

                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
		            }
		        },
		    });
		})

        // Load more
        var page_num = 1;
        var total_page = <?php echo json_encode($products->total()) ?>;
        $(window).scroll( function() {
            if( ( $(window).scrollTop() + $(window).height() > ( $(document).height() * (2/3) ) ) && (total_page>=page_num) ) {
                loadMoreData(++page_num);
            }

        });

        function loadMoreData(page_num) {
            $.ajax({
                url: '?page=' + page_num,
                type: "get",
            }).done(function(data) {
                $(".product-grid").append(data.html);
                $('.product-img').each(function(){
                    var img = $(this).data('src');
                    $(this).attr('src', img);
                })
            }).fail(function(jqXHR, ajaxOptions, thrownError)
            {
                 console.log('server not responding...');
            });
        }

        $(document).on('click', '.variant-btn', function () {
            var option = $(this).data('v-option');
            var value = $(this).data('v-value').toString();

            var currentUrl = new URL(window.location.href);
            var existingValues = currentUrl.searchParams.get(option);
            var valuesArray = existingValues ? existingValues.split(',').map(v => v.trim()) : [];

            if (valuesArray.includes(value)) {
                valuesArray = valuesArray.filter(v => v !== value);
                $(this).removeClass('selected'); // Remove selected state
            } else {
                valuesArray.push(value);
                $(this).addClass('selected'); // Add selected state
            }

            if (valuesArray.length > 0) {
                currentUrl.searchParams.set(option, valuesArray.join(','));
            } else {
                currentUrl.searchParams.delete(option);
            }

            window.location.href = decodeURIComponent(currentUrl.toString());
        });

        $(document).on('click', '.brand-btn', function () {
            var brandName = $(this).data('b-id').toString();
            // var brandId = $(this).data('b-id').toString();

            var currentUrl = new URL(window.location.href);
            var existingBrands = currentUrl.searchParams.get('brand');
            var brandsArray = existingBrands ? existingBrands.split(',').map(b => b.trim()) : [];

            if (brandsArray.includes(brandName)) {
                brandsArray = brandsArray.filter(b => b !== brandName);
                $(this).removeClass('selected'); // Remove selected state
            } else {
                brandsArray.push(brandName);
                $(this).addClass('selected'); // Add selected state
            }

            if (brandsArray.length > 0) {
                console.log('abc',brandsArray);
                currentUrl.searchParams.set('brand', brandsArray.join(','));
            } else {
                currentUrl.searchParams.delete('brand');
            }

            window.location.href = decodeURIComponent(currentUrl.toString());
        });

        $(document).ready(function () {
            var currentUrl = new URL(window.location.href);

            // Mark selected variants
            $('.variant-btn').each(function () {
                var option = $(this).data('v-option');
                var value = $(this).data('v-value').toString();

                var existingValues = currentUrl.searchParams.get(option);

                if (existingValues) {
                    var valuesArray = existingValues.split(',').map(v => v.trim());
                    if (valuesArray.includes(value)) {
                        $(this).addClass('selected');
                    }
                }
            });

            // Mark selected brands
            $('.brand-btn').each(function () {
                var brandName = $(this).data('b-id').toString();
                console.log('www',brandName);

                var existingBrands = currentUrl.searchParams.get('brand');
                console.log('ppp',existingBrands);

                if (existingBrands) {
                    var brandsArray = existingBrands.split(',').map(b => b.trim());
                    if (brandsArray.includes(brandName)) {
                        $(this).addClass('selected');
                    }
                }
            });
        });




	</script>
@endsection

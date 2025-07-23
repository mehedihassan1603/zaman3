@extends('ecommerce::frontend.layout.main')

@if ($product->image !== null)
    @php
        $images = explode(',', $product->image);
        $product->image = $images[0];
    @endphp
@endif

@section('title')
    {{ $product->meta_title ?? $product->name }}
@endsection

@section('description')
    {{ $product->meta_description ?? $product->name }}
@endsection

@section('image')
    {{ url('images/product/large') }}/{{ $product->image }}
@endsection

@section('brand')
    {{ $brand->title ?? '' }}
@endsection

@section('stock')
    @if ($product->qty > 0)
        {{ 'in stock' }}
    @else
        {{ 'out of stock' }}
    @endif
@endsection

@section('price')
    @if (!empty($product->promotion_price))
        {{ $product->promotion_price }}@else{{ $product->price }}
    @endif
@endsection

@section('id')
    {{ $product->id }}
@endsection

@section('category_id')
    {{ $product->category_id }}
@endsection


@push('css')
    <style>
        li {
            font-size: 14px
        }

        .slick-list,
        .slick-slider,
        .slick-track {
            position: relative;
            display: block
        }

        .slick-loading .slick-slide,
        .slick-loading .slick-track {
            visibility: hidden
        }

        .slick-slider {
            box-sizing: border-box;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -khtml-user-select: none;
            -ms-touch-action: pan-y;
            touch-action: pan-y;
            -webkit-tap-highlight-color: transparent
        }

        .slick-list {
            overflow: hidden;
            margin: 0;
            padding: 0
        }

        .slick-list:focus {
            outline: 0
        }

        .slick-list.dragging {
            cursor: pointer;
            cursor: hand
        }

        .slick-slider .slick-list,
        .slick-slider .slick-track {
            -webkit-transform: translate3d(0, 0, 0);
            -moz-transform: translate3d(0, 0, 0);
            -ms-transform: translate3d(0, 0, 0);
            -o-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0)
        }

        .slick-track:after,
        .slick-track:before {
            display: table;
            content: ''
        }

        .slick-track:after {
            clear: both
        }

        .slick-slide {
            display: none;
            float: left;
            height: 100%;
            min-height: 1px
        }

        [dir=rtl] .slick-slide {
            float: right
        }

        .slick-initialized .slick-slide,
        .slick-slide img {
            display: block
        }

        .slick-arrow.slick-hidden,
        .slick-slide.slick-loading img {
            display: none
        }

        .slick-slide.dragging img {
            pointer-events: none
        }

        .slick-vertical .slick-slide {
            display: block;
            height: auto;
            border: 1px solid transparent
        }

        .slider-for__item img {
            width: 100%
        }

        .slider-for {
            overflow: hidden;
            width: 100%
        }

        .slider-nav {
            margin-top: 15px;
            width: 100%
        }

        .slick-track {
            top: 0;
            left: 0;
            display: flex;
            justify-content: center
        }

        .slider-nav__item {
            width: 100px !important
        }

        .slider-nav__item.slick-slide {
            border: 1px solid #f5f6f7;
            border-radius: 5px;
            margin: 3px;
            padding: 10px
        }

        .slider-nav__item.slick-slide.slick-current {
            border: 1px solid #333
        }

        .slider-nav__item.slick-slide.slick-active img {
            opacity: .3;
            cursor: pointer
        }

        .slider-nav__item.slick-slide.slick-current.slick-active img {
            opacity: 1;
            cursor: pointer
        }

        .product-details-section .slider-nav__item.slick-slide {
            border: 1px solid transparent;
            padding: 5px
        }

        .variant_val {
            border: 1px solid #ddd;
            cursor: pointer;
            padding: 5px;
            min-width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .variant_val.selected {
            border: 2px solid var(--theme-color);
            padding: 5px
        }

        .slick-dots,
        .slick-next,
        .slick-prev {
            display: none !important
        }

        label {
            color: #111;
            font-size: 14px;
            padding-top: 5px;
        }

        .custom-modal {
            display: none;
            /* Hide modal initially */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Background overlay */
        }

        .custom-modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        #reviews-section {
    padding: 40px 0;
}

.review-item {
    margin-bottom: 20px;
}

.reviews-slider {
    display: flex;
    overflow-x: scroll;
    gap: 20px;
    padding-bottom: 20px;
}

.review-card {
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    width: 300px;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.review-card:hover {
    transform: scale(1.05);
}

.review-card-header {
    display: flex;
    padding: 15px;
    border-bottom: 1px solid #ddd;
}

.customer-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
}

.customer-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.customer-name {
    font-size: 16px;
    margin-bottom: 5px;
}

.customer-rating {
    font-size: 18px;
}

.star {
    color: #ccc; /* Default color for empty stars */
    transition: color 0.3s;
}

.star.filled {
    color: #f39c12; /* Gold color for filled stars */
}

.review-card-body {
    padding: 15px;
    font-size: 14px;
    color: #555;
}

.slider-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: #f39d12b4;
    border: none;
    color: #fff;
    font-size: 24px;
    padding: 10px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
}

.left-arrow {
    left: 20px;
}

.right-arrow {
    right: 20px;
}

.reviews-slider::-webkit-scrollbar {
    display: none;
}

    </style>
@endpush

@section('content')
    <!--Product details section starts-->
    <section class="product-details-section">
        {{-- {{dd($brand)}} --}}

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 mt-5">
                    @if ($product->promotion == 1 && ($product->last_date > date('Y-m-d') || !isset($product->last_date)))
                        <div class="product-promo-text style1 bg-danger">
                            <span>-{{ round((($product->price - $product->promotion_price) / $product->price) * 100) }}%</span>
                        </div>
                    @endif
                    @if (isset($images))
                        <div class="slider-wrapper">
                            <div class="slider-for">
                                @foreach ($images as $image)
                                    @if (file_exists(url('images/product/xlarge')))
                                        <div class="slider-for__item ex1"
                                            data-src="{{ url('images/product/xlarge') }}/{{ $image }}">
                                            <img src="{{ url('images/product/xlarge') }}/{{ $image }}"
                                                alt="{{ $product->name }}" />
                                        </div>
                                    @else
                                        <div class="slider-for__item ex1"
                                            data-src="{{ url('images/product/large') }}/{{ $image }}">
                                            <img src="{{ url('images/product/large') }}/{{ $image }}"
                                                alt="{{ $product->name }}" />
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="slider-nav">
                                @foreach ($images as $image)
                                    <div class="slider-nav__item">
                                        <img src="{{ url('images/product/large') }}/{{ $image }}"
                                            alt="{{ $product->name }}" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <img src="https://placehold.co/550x550" alt="{{ $product->name }}" />
                    @endif
                </div>
                <div class="col-md-4 offset-md-1 mt-5">


                    <h1 class="item-name">{!! $product->name !!}</h1>
                    <h4>Category: <a class="theme-color"
                            href="{{ url('/shop') }}/{{ $category->slug }}">{{ $category->name }}</a></h4>
                    <h4>Brand: {{ $brand ? $brand->title : 'Undefined' }}</h4>

                    <!-- View User Reviews Button -->
                    <a href="#reviews-section" class="btn btn-primary mt-2 mb-2">View User Reviews</a>

                    <div class="item-price mb-3">
                        @if ($product->promotion == 1 && ($product->last_date > date('Y-m-d') || !isset($product->last_date)))
                            <span
                                class="price">{{ $currency->symbol ?? $currency->code }}{{ $product->promotion_price }}</span>
                            <span class="old-price">{{ $currency->symbol ?? $currency->code }}{{ $product->price }}</span>
                        @else
                            <span class="price">{{ $currency->symbol ?? $currency->code }}{{ $product->price }}</span>
                        @endif
                    </div>
                    @if (isset($product->short_description) && strlen($product->short_description) > 0)
                        <div class="mt-5 mb-5">
                            <div class="item-short-description">
                                {!! $product->short_description !!}
                            </div>
                        </div>
                    @endif
                    <div class="item-options mb-5">
                        {{-- {{dd($product)}} --}}
                        @if ($product->variant_option)
                        <div class="row" id="variant-input-section">
                            @php
                                    $count_var_val = 0;
                                    @endphp
                                @foreach ($product->variant_option as $key => $variant_option)
                                @php
                                        $count_var_val += count(explode(',', $product->variant_value[$key]));
                                        @endphp
                                    <div class="col-md-2 form-group mt-2">
                                        <label>{{ $product->variant_option[$key] }}</label>
                                    </div>
                                    <div class="col-md-10 form-group mt-2">
                                        @php
                                            $val_list = explode(',', $product->variant_value[$key]);
                                            @endphp
                                        <ul class="d-flex">
                                            @foreach ($val_list as $val)
                                            <li class="ml-3 variant_val">{{ $val }}a</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                {{-- {{dd($product)}} --}}

                        @if ($product->in_stock == 1)
                            <form method="post" id="add_to_cart_{{ $product->id }}" class="mt-3 mb-3 d-flex">
                                @csrf
                                <div class="input-qty">
                                    <button type="button" class="quantity-left-minus">
                                        <i class="material-symbols-outlined">remove</i>
                                    </button>
                                    <input type="number" name="qty" class="input-number" value="1" min="1"
                                        max="100">
                                    <button type="button" class="quantity-right-plus">
                                        <i class="material-symbols-outlined">add</i>
                                    </button>
                                </div>
                                <button data-id="{{ $product->id }}"
                                    class="button @if ($ecommerce_setting->theme == 'fashion') style2 lg @else style1 @endif add-to-cart"
                                    @if ($product->is_variant == 1) disabled="true" @endif>
                                    <span class="material-symbols-outlined mr-2">shopping_bag</span>
                                    {{ trans('file.Add to cart') }}
                                </button>
                            </form>
                        @else
                            @if ($product->qty > 0)
                                <form method="post" id="add_to_cart_{{ $product->id }}" class="mb-3 d-flex">
                                    @csrf
                                    <div class="input-qty">
                                        <button type="button" class="quantity-left-minus">
                                            <i class="material-symbols-outlined">remove</i>
                                        </button>
                                        <input type="number" name="qty" class="input-number" value="1"
                                            min="1" max="{{ $product->qty }}">
                                        <button type="button" class="quantity-right-plus">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </div>
                                    <button data-id="{{ $product->id }}"
                                        class="button @if ($ecommerce_setting->theme == 'fashion') style2 lg @else style1 @endif add-to-cart"
                                        @if ($product->is_variant == 1) disabled="true" @endif>
                                        <span class="material-symbols-outlined mr-2">shopping_bag</span>
                                        {{ trans('file.Add to cart') }}
                                    </button>
                                </form>
                            @else
                                <span>{{ trans('file.Out of stock') }}</span>
                            @endif
                        @endif
                    </div>
                    <hr>
                    <div class="mt-5">
                        <span class="d-inline-block">SKU </span>
                        <ul class="footer-social d-inline">
                            <li>:</li>
                            <li> {{ $product->code }}</li>
                        </ul> <br>
                    </div>
                    @if (isset($product->tags))
                        <div class="mt-2">
                            <span class="d-inline-block mt-3">{{ trans('file.Tags') }} </span>
                            <ul class="footer-social d-inline">
                                <li>:</li>
                                <li> {{ $product->tags }}</li>
                            </ul>
                        </div>
                    @endif
                    <div class="item-share mt-3">
                        <span>{{ trans('file.Share') }}</span>
                        <ul class="footer-social d-inline pl-3 pr-3">
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url('/product') }}/{{ $product->slug }}/{{ $product->id }}"
                                    target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="10"
                                        viewBox="0 0 320 512">
                                        <path
                                            d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" />
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/intent/tweet?text={{ $product->meta_title ?? $product->name }}&url={{ url('/product') }}/{{ $product->slug }}/{{ $product->id }}&via="
                                    target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16"
                                        viewBox="0 0 512 512">
                                        <path
                                            d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="http://pinterest.com/pin/create/button/?url={{ url('/product') }}/{{ $product->slug }}/{{ $product->id }}&media={{ url('images/product/large') }}/{{ $product->image }}&description={{ $product->meta_title ?? $product->name }}"
                                    target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">
                                        <path
                                            d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0" />
                                    </svg>
                                </a>
                            </li>
                            <!-- WhatsApp Share Button -->
                            <li>
                                <a href="https://wa.me/?text={{ urlencode($product->meta_title ?? $product->name) }}%20{{ urlencode(url('/product/' . $product->slug . '/' . $product->id)) }}"
                                    target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M20.52 3.48A11.79 11.79 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.09.54 4.13 1.55 5.94L0 24l6.3-1.6a11.9 11.9 0 0 0 5.7 1.46c6.63 0 12-5.37 12-12 0-3.18-1.25-6.2-3.48-8.52zM12 22a9.88 9.88 0 0 1-5.17-1.43l-.37-.23-3.74.95.94-3.64-.24-.38A9.96 9.96 0 1 1 12 22zm5.49-7.4c-.3-.15-1.78-.88-2.05-.97-.27-.1-.46-.15-.65.15-.2.3-.75.97-.92 1.18s-.34.22-.64.08a8.05 8.05 0 0 1-2.35-1.45 8.5 8.5 0 0 1-1.57-1.94c-.16-.3-.02-.46.12-.61l.44-.55c.15-.15.2-.27.3-.46.1-.18.05-.35-.02-.5-.07-.15-.65-1.57-.89-2.15-.24-.57-.48-.5-.66-.5h-.56c-.2 0-.5.07-.75.35-.25.3-.99.97-.99 2.37 0 1.4 1.02 2.77 1.16 2.97.14.2 2 3.04 4.85 4.27 2.85 1.24 2.85.82 3.36.77.51-.05 1.78-.73 2.03-1.43.25-.72.25-1.33.18-1.47-.07-.14-.25-.2-.55-.35z" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-3 mt-3">
                    <div class="p-3">
                        <h5 class="fw-bold mb-3" style="font-size: 20px;">
                            <i class="fas fa-shipping-fast me-2"></i> Delivery Options
                        </h5>
                        <hr>
                        <ul class="list-unstyled mb-3">
                            <li>
                                <strong style="font-size: 16px;">
                                    <i class="fas fa-map-marker-alt me-2"></i> Available Delivery Area:
                                </strong>
                                <span style="font-size: 14px;">All over Bangladesh</span>

                            </li>
                            <li>
                                <strong style="font-size: 16px;">
                                    <i class="fas fa-map-marker me-2"></i> Selected Area:
                                </strong>
                                <span id="selectedArea" style="font-size: 14px;">Dhaka, Dhaka North, Tejgao</span>
                            </li>
                            <button class="btn btn-primary btn-sm mt-3" id="openModal" style="font-size: 14px;">
                                <i class="fas fa-edit me-1"></i> Change
                            </button>
                        </ul>

                        <!-- Modal -->
                        <div id="customModal" class="custom-modal">
                            <div class="custom-modal-content">
                                <span class="close-btn" id="closeModal">&times;</span>
                                <h5><i class="fas fa-map-marker-alt me-2"></i> Select Delivery Area</h5>

                                <label for="district"><i class="fas fa-city me-2"></i> District</label>
                                <select id="district" class="form-control mb-3" disabled>
                                    <option value="">Select District</option>
                                </select>

                                <label for="upazilla"><i class="fas fa-map-marked-alt me-2"></i> Upazilla</label>
                                <select id="upazilla" class="form-control mb-3" disabled>
                                    <option value="">Select Upazilla</option>
                                </select>

                                <button class="btn btn-success mt-3" id="doneButton">
                                    <i class="fas fa-check-circle me-1"></i> Done
                                </button>
                            </div>
                        </div>


                        <ul class="list-unstyled mb-3">
                            <li>
                                <strong style="font-size: 16px;">
                                    <i class="fas fa-clock me-2"></i> Delivery Info:
                                </strong>
                            </li>
                            <li style="font-size: 14px;">Delivery Time: 1-7 working days</li>
                            <li id="shipping-charge" style="font-size: 14px;">Shipping Charge: Tk 100</li>
                        </ul>


                        <ul class="list-unstyled">
                            <li>
                                <strong style="font-size: 16px;">
                                    <i class="fas fa-credit-card me-2"></i> Payment Option:
                                </strong>
                            </li>
                            <li style="font-size: 14px;">Cash on Delivery Available</li>
                            <img src="https://ecdn.dhakatribune.net/contents/cache/images/640x359x1/uploads/dten/2021/02/untitled-1614532861513.jpg"
                                alt="">
                        </ul>
                    </div>
                </div>
                @if (isset($product->product_details) && strlen($product->product_details) > 0)
                    <div class="col-12 mt-5 mb-5">
                        <h2>{{ trans('file.Description') }}</h2>
                        <div class="item-description">
                            {!! $product->product_details !!}
                        </div>
                    </div>
                @endif

                <!-- Reviews Section -->
                <!-- Reviews Section -->
            <div id="reviews-section" class="col-12 mt-2">
                <h2>User Reviews</h2>

                @if(isset($ratings) && $ratings->isNotEmpty())

                    <div class="reviews-slider">
                        @foreach($ratings as $rating)
                            <div class="review-item">
                                <div class="review-card">
                                    <div class="review-card-header">
                                        <img src="https://www.cgg.gov.in/wp-content/uploads/2017/10/dummy-profile-pic-male1.jpg" alt="Customer Image" class="customer-img">
                                        <div class="customer-info">
                                            <p class="customer-name"><strong>{{ $rating->customer_name }}</strong></p>
                                            <div class="customer-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="star {{ $i <= $rating->value ? 'filled' : '' }}">★</span>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($ratings->count() > 5)
                        <button class="slider-arrow left-arrow">&#8592;</button>
                        <button class="slider-arrow right-arrow">&#8594;</button>
                    @endif
                @else
                    <p>No reviews yet. Be the first to review this product!</p>


                @endif

            </div>




            </div>
        </div>
    </section>
    {{-- {{dd('ttt',$product)}} --}}
    <!--Product details section ends-->
    @php
    $take_product_id = $product->id;
    $cat_id = $product->category_id;

    $related_p = \DB::table('products')
    ->where('category_id', $cat_id)
    ->where('id', '!=', $take_product_id)
    ->latest()
    // ->take(5)
    ->get();

// dd('aaa', $related_p);


    @endphp

{{-- {{dd('ttt',$product)}} --}}
    @if ($related_p && count($related_p) > 0)
        <section class="container">
            <div class="container-fluid">
                <div class="section-title mb-3">
                    <div class="d-flex align-items-center">
                        <h3>{{ trans('file.You may also like') }}</h3>
                    </div>
                    @if (count($related_p) > 5 && $ecommerce_setting->theme != 'fashion')
                        <div class="product-navigation">
                            <div class="product-button-next v1"><span
                                    class="material-symbols-outlined">chevron_right</span></div>
                            <div class="product-button-prev v1"><span
                                    class="material-symbols-outlined">chevron_left</span></div>
                        </div>
                    @endif
                </div>
                <div class="product-slider-wrapper swiper-container" data-loop="" data-autoplay=""
                    data-autoplay-speed="">
                    <div class="swiper-wrapper">
                        @foreach ($related_p as $pro)
                            <div class="swiper-slide">
                                @include('ecommerce::frontend.includes.related-product-template')
                            </div>
                        @endforeach
                    </div>
                    @if (count($related_p) > 5 && $ecommerce_setting->theme == 'fashion')
                        <div class="product-navigation">
                            <div class="product-button-next v1"><span
                                    class="material-symbols-outlined">chevron_right</span></div>
                            <div class="product-button-prev v1"><span
                                    class="material-symbols-outlined">chevron_left</span></div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

{{-- {{dd('rrr',$product)}} --}}
    @if (count($recently_viewed) > 0)
        @include('ecommerce::frontend.includes.recently-viewed-products')
    @endif



@endsection
{{-- {{dd('rrr',$product)}} --}}

@section('script')
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-zoom/1.6.1/jquery.zoom.min.js"></script>

    @if (!config('database.connections.saleprosaas_landlord'))
        <script>
            {!! file_get_contents(Module::find('Ecommerce')->getPath() . '/assets/js/swiper.min.js') !!}
        </script>
    @else
        <script>
            {!! file_get_contents(Module::find('Ecommerce')->getPath() . '/assets/js/swiper.min.js') !!}
        </script>
    @endif

    <script type="text/javascript">
        "use strict";

        // SLICK
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.slider-nav'
        });
        $('.slider-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: false,
            focusOnSelect: true
        });

        // ZOOM
        $('.ex1').zoom();

        // STYLE GRAB
        $('.ex2').zoom({
            on: 'grab'
        });

        // STYLE CLICK
        $('.ex3').zoom({
            on: 'click'
        });

        // STYLE TOGGLE
        $('.ex4').zoom({
            on: 'toggle'
        });


        @if ($product->is_variant == 1)
            $(document).on('click', '.variant_val', function() {
                console.log('aaa');

                $(this).parent().children('.variant_val').removeClass('selected');
                $(this).addClass('selected');

                // Check if all variant options are selected
                if ($('.variant_val.selected').length == {{ count($product->variant_option) }}) {
                    var combination = '';

                    // Build the combination string
                    $('.variant_val.selected').each(function() {
                        if (combination.length) {
                            combination = combination + '/' + $(this).html();
                        } else {
                            combination = $(this).html();
                        }
                    });

                    // Parse the variants JSON
                    var variants = JSON.parse('{!! json_encode($variant) !!}');

                    // Find the matching combination
                    var match = variants.find(variant => variant.name.toUpperCase() === combination.toUpperCase());
                    // Get quantity or set to 0 if not found
                    var qty = match ? match.qty : 0;
                    @if ($product->in_stock != 1)
                        // Update the max attribute of the input field
                        $('.input-number').attr('max', qty);
                        if (qty > 0) {
                            $('.add-to-cart').attr('disabled', false);
                            $('.alert').removeClass('show');
                        } else {
                            $('.add-to-cart').attr('disabled', true);

                            $('.alert').addClass('alert-custom show');
                            $('.alert-custom .message').html('{{ trans('file.Out of stock') }}');
                        }
                    @else
                        $('.add-to-cart').attr('disabled', false);
                    @endif

                } else {
                    // Disable Add-to-Cart button if not all options are selected
                    $('.add-to-cart').attr('disabled', true);
                }
            });
        @endif


        $(document).on('click', '.add-to-cart', function(e) {
            console.log('aaabbb');
            e.preventDefault();
            var id = $(this).data('id');
            var parent = '#add_to_cart_' + id;
            console.log('parent',parent);

            var qty = $(parent + " input[name=qty]").val();
            console.log('qty',qty);
            @if ($product->is_variant == 1)
                var variant = [];
                $('.variant_val.selected').each(function() {
                    variant.push($(this).html());
                })
            @endif
            console.log('variant',variant);

            var route = "{{ route('addToCart') }}";

            $.ajax({
                url: route,
                type: "POST",
                data: {
                    @if ($product->is_variant == 1)

                        product_id: id + ',' + variant,
                        qty: qty,
                        variant: variant,
                    @else

                        product_id: id,
                        qty: qty,
                        variant: 0
                    @endif
                },
                success: function(response) {
                    console.log(response);
                    if (response) {
                        $('.alert').addClass('alert-custom show');
                        $('.alert-custom .message').html(response.success);
                        $('.cart__menu .cart_qty').html(response.total_qty);
                        $('.cart__menu .total').html('{{ $currency->symbol ?? $currency->code }}' +
                            response.subTotal);

                        @if ($product->is_variant == 1)
                            $('.add-to-cart').attr('disabled', true);
                            $('.variant_val').removeClass('selected');
                        @endif

                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
                    }
                },
            });
        })
        $(document).ready(function() {
            const openModalBtn = document.getElementById('openModal');
            const closeModalBtn = document.getElementById('closeModal');
            const modal = document.getElementById('customModal');
            const districtSelect = document.getElementById('district');
            const upazillaSelect = document.getElementById('upazilla');
            const selectedAreaElement = document.getElementById('selectedArea');
            const doneButton = document.getElementById('doneButton');
            const division_id = 18;

            // Ensure elements exist before adding event listeners
            if (openModalBtn && closeModalBtn && modal) {
                // Open Modal
                openModalBtn.addEventListener('click', function() {
                    modal.style.display = 'block';
                });

                // Close Modal
                closeModalBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });

                // Close modal when clicking outside of it
                window.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }

            // Load Districts based on a predefined division ID
            if (districtSelect) {
                let url = `{{ route('delivery.districts', ['division_id' => '__DIVISION_ID__']) }}`.replace(
                    '__DIVISION_ID__', division_id);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        districtSelect.innerHTML = '<option value="">Select District</option>';
                        data.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.id;
                            option.textContent = district.name;
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                    })
                    .catch(error => console.error("Error loading districts:", error));
            }

            if (districtSelect && upazillaSelect) {
                districtSelect.addEventListener('change', function() {
                    upazillaSelect.innerHTML = '<option value="">Select Upazilla</option>';
                    upazillaSelect.disabled = true;

                    // Update Shipping Charge
                    let shippingChargeElement = document.getElementById('shipping-charge');
                    if (this.options[this.selectedIndex].text === "Dhaka") {
                        shippingChargeElement.textContent = "Shipping Charge: Tk 60";
                    } else if (this.value) {
                        shippingChargeElement.textContent = "Shipping Charge: Tk 120";
                    } else {
                        shippingChargeElement.textContent = "Shipping Charge: Tk 100"; // Default
                    }

                    if (this.value) {
                        let url2 =
                            `{{ route('delivery.upazillas', ['district_id' => '__DISTRICT_ID__']) }}`
                            .replace('__DISTRICT_ID__', this.value);
                        fetch(url2)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(upazilla => {
                                    const option = document.createElement('option');
                                    option.value = upazilla.id;
                                    option.textContent = upazilla.name;
                                    upazillaSelect.appendChild(option);
                                });
                                upazillaSelect.disabled = false;
                            })
                            .catch(error => console.error("Error loading upazillas:", error));
                    }
                });
            }


            // Handle "Done" button click
            if (doneButton) {
                doneButton.addEventListener('click', function() {
                    const selectedDistrict = districtSelect.options[districtSelect.selectedIndex].text;
                    const selectedUpazilla = upazillaSelect.options[upazillaSelect.selectedIndex].text;

                    if (districtSelect.value && upazillaSelect.value) {
                        selectedAreaElement.textContent = `${selectedDistrict}, ${selectedUpazilla}`;
                        modal.style.display = 'none'; // Close modal after selection
                    } else {
                        alert("Please select both District and Upazilla before proceeding.");
                    }
                });
            }
        });



        //product carousel
        if (('.product-slider-wrapper').length > 0) {
            $('.product-slider-wrapper').each(function() {
                var swiper = new Swiper('.product-slider-wrapper', {
                    @if ($ecommerce_setting->theme == 'fashion')
                        slidesPerView: 4,
                    @else
                        slidesPerView: 5,
                    @endif
                    spaceBetween: 0,
                    lazy: true,
                    //centeredSlides: true,
                    loop: $(this).data('loop'),
                    navigation: {
                        nextEl: '.product-button-next',
                        prevEl: '.product-button-prev',
                    },
                    autoplay: {
                        delay: 4000,
                    },
                    // Responsive breakpoints
                    breakpoints: {
                        // when window width is <= 675
                        675: {
                            slidesPerView: 2,
                            spaceBetween: 30
                        },

                        // when window width is <= 991
                        991: {
                            slidesPerView: 4,
                            spaceBetween: 30
                        },
                        // when window width is <= 1024px
                        1024: {
                            slidesPerView: 6,
                            spaceBetween: 15
                        }
                    }
                });
            })
        }


        $(document).ready(function() {
    const slider = $('.reviews-slider');
    const leftArrow = $('.left-arrow');
    const rightArrow = $('.right-arrow');

    // Hide arrows initially if there are less than 6 reviews
    if ($('.review-item').length <= 5) {
        leftArrow.hide();
        rightArrow.hide();
    }

    // Left arrow click to scroll left
    leftArrow.on('click', function() {
        slider.animate({
            scrollLeft: slider.scrollLeft() - 320
        }, 300);
    });

    // Right arrow click to scroll right
    rightArrow.on('click', function() {
        slider.animate({
            scrollLeft: slider.scrollLeft() + 320
        }, 300);
    });
});
</script>
@endsection



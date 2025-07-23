@extends('ecommerce::frontend.layout.main')

@section('title')
    {{ $ecommerce_setting->site_title ?? '' }}
@endsection

@section('description')
    {{ '' }}
@endsection
<style>
 .single-carousel-item img {
    width: 100%;   /* Make sure the image takes full width */
    height: 100%;  /* Ensures it fills the parent */
    object-fit: cover; /* Ensures it covers the entire space without stretching */
}


</style>
@section('content')
    @if (isset($sliders))









    <!--Home Banner starts -->
<section class="banner-area v3 pt-0">
    @if(isset($ecommerce_setting->theme) && $ecommerce_setting->theme != 'fashion')
    <div class="">
    @endif

        <div class="single-banner-item">
            <div class="row">
                @if(isset($ecommerce_setting->theme) && $ecommerce_setting->theme == 'default')
                <div class="col-md-9 offset-md-3">
                @else
                <div class="col-md-12">
                @endif
                    <div id="hero-slider" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner" style="height: 500px;">
                            @foreach($sliders as $key=>$slider)
                            <a class="carousel-item @if($key == 0) active @endif" href="{{$slider->link}}">
                                <div class="single-carousel-item">
                                    <img data-src-m="@if(!empty($slider->image3)){{ url('frontend/images/slider/mobile/') }}/{{$slider->image3}}@endif" src="{{ url('frontend/images/slider/desktop/') }}/{{$slider->image1}}" alt="" />
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @if(count($sliders) > 1)
                        <button class="carousel-control-prev" type="button" data-target="#hero-slider" data-slide="prev">
                            <span aria-hidden="true"><i class="material-symbols-outlined">chevron_left</i></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-target="#hero-slider" data-slide="next">
                            <span aria-hidden="true"><i class="material-symbols-outlined">chevron_right</i></span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @if(isset($ecommerce_setting->theme) && $ecommerce_setting->theme != 'fashion')
    </div>
    @endif
</section>
<!--Home Banner Area ends-->






















        {{-- <!--Home Banner starts -->
        <section class="banner-area v3 pt-0" style="z-index: -1;">
            @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme != 'fashion')
                <div class="">
            @endif

            <div class="single-banner-item">
                <div class="row">
                    <div class="col-md-12">
                        <div id="hero-slider" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner" style="height: 500px;">
                                @foreach ($sliders as $key => $slider)
                                    <a class="carousel-item @if ($key == 0) active @endif"
                                        href="{{ $slider->link }}">
                                        <div class="single-carousel-item">
                                            <img data-src-m="@if (!empty($slider->image3)) {{ url('frontend/images/slider/mobile/') }}/{{ $slider->image3 }} @endif"
                                                src="{{ url('frontend/images/slider/desktop/') }}/{{ $slider->image1 }}"
                                                alt="" />
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            @if (count($sliders) > 1)
                                <button class="carousel-control-prev" type="button" data-target="#hero-slider"
                                    data-slide="prev">
                                    <span aria-hidden="true"><i class="material-symbols-outlined">chevron_left</i></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-target="#hero-slider"
                                    data-slide="next">
                                    <span aria-hidden="true"><i class="material-symbols-outlined">chevron_right</i></span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Product Section -->
                    {{-- <div class="col-md-3" style="z-index: 10 !important;">
                        <h5 class="mb-3 text-center"
                            style="background-color: rgb(196, 1, 1); font-size: 36px; color: white;">Latest Products</h5>
                        <!-- Title added -->
                        <div class="product-list d-flex flex-wrap justify-content-between"
                            style="max-width: 500px; overflow-y: auto; padding-right: 5px;">
                            @php
                                $products = App\Models\Product::latest()->take(4)->get();
                            @endphp

                            @foreach ($products as $product)
                                <div class="card position-relative text-white mb-3 product-card"
                                    style="width: 48%; height: 200px; background: url('{{ url('images/product/') }}/{{ $product->image }}') no-repeat center center / cover; border-radius: 10px; overflow: hidden; transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;">

                                    <!-- Overlay for readability -->
                                    <div class="card-img-overlay d-flex flex-column justify-content-center align-items-center text-center"
                                        style="background: rgba(0, 0, 0, 0.092); border-radius: 10px;">
                                        <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                        <p class="card-text">${{ $product->price }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>





                </div>
            </div>

            @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme != 'fashion')
                </div>
            @endif
        </section>
        <!--Home Banner Area ends--> --}}
    @endif
    <div style="background-color: rgb(227, 227, 227);">
        <div class="container py-5">
            <div class="row row-cols-1 row-cols-md-4 g-4 text-center">

                <!-- Terms & Conditions -->
                <div class="col">
                    <div class="p-4 bg-light rounded-3 shadow-sm">
                        <img src="https://cdn-icons-png.flaticon.com/512/9374/9374384.png" alt="Terms" class="mb-2"
                            width="50">
                        <p class="fw-bold">Terms & Conditions</p>
                    </div>
                </div>

                <!-- Return Policy -->
                <div class="col">
                    <div class="p-4 bg-light rounded-3 shadow-sm">
                        <img src="https://cdn-icons-png.flaticon.com/128/2714/2714821.png" alt="Return" class="mb-2"
                            width="50">
                        <p class="fw-bold">Return Policy</p>
                    </div>
                </div>

                <!-- Support Policy -->
                <div class="col">
                    <div class="p-4 bg-light rounded-3 shadow-sm">
                        <img src="https://cdn-icons-png.flaticon.com/128/1828/1828926.png" alt="Support" class="mb-2"
                            width="50">
                        <p class="fw-bold">Support Policy</p>
                    </div>
                </div>

                <!-- Privacy Policy -->
                <div class="col">
                    <div class="p-4 bg-light rounded-3 shadow-sm">
                        <img src="https://cdn-icons-png.flaticon.com/128/7021/7021085.png" alt="Privacy" class="mb-2"
                            width="50">
                        <p class="fw-bold">Privacy Policy</p>
                    </div>
                </div>

            </div>
        </div>
    </div>



    @if (isset($widgets))
        @foreach ($widgets as $widget)
            @if ($widget->name == 'category-slider-widget')
                @include('ecommerce::frontend.includes.category-slider-widget')
            @endif

            @if ($widget->name == 'brand-slider-widget')
                @include('ecommerce::frontend.includes.brand-slider-widget')
            @endif

            @if ($widget->name == 'product-category-widget')
                @include('ecommerce::frontend.includes.product-category-widget')
            @endif

            @if ($widget->name == 'product-collection-widget')
                @include('ecommerce::frontend.includes.product-collection-widget')
            @endif

            @if ($widget->name == 'text-widget')
                @include('ecommerce::frontend.includes.text-widget')
            @endif

            @if ($widget->name == 'three-c-banner-widget')
                @include('ecommerce::frontend.includes.three-c-banner-widget')
            @endif

            @if ($widget->name == 'two-c-banner-widget')
                @include('ecommerce::frontend.includes.two-c-banner-widget')
            @endif

            @if ($widget->name == 'one-c-banner-widget')
                @include('ecommerce::frontend.includes.one-c-banner-widget')
            @endif

            @if ($widget->name == 'tab-product-category-widget')
                @include('ecommerce::frontend.includes.tab-product-category-widget')
            @endif

            @if ($widget->name == 'tab-product-collection-widget')
                @include('ecommerce::frontend.includes.tab-product-collection-widget')
            @endif

            @if ($widget->name == 'image-slider-widget')
                @include('ecommerce::frontend.includes.image-slider-widget')
            @endif
        @endforeach
    @endif

    @if (isset($recently_viewed) && count($recently_viewed) > 0)
        {{-- @include('ecommerce::frontend.includes.recently-viewed-products') --}}
    @endif

    <!-- Google Map Section -->
    <div class="map-container" style="width: 100%; height: 400px; margin-top: 20px;">
        <iframe width="100%" height="400" frameborder="0" style="border:0" allowfullscreen loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d444.06454691090914!2d90.41817555670679!3d23.73359966080735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b85c0d39f115%3A0x9876c18ffd3434c3!2sGreat%20Tech%20GOSPELL!5e1!3m2!1sen!2sbd!4v1738755928808!5m2!1sen!2sbd">
        </iframe>

    </div>
@endsection

@section('script')
    <script>
        {!! file_get_contents(Module::find('Ecommerce')->getPath() . '/assets/js/swiper.min.js') !!}
    </script>
    <script type="text/javascript">
        "use strict";

        //category carousel
        if (('.category-slider-wrapper').length > 0) {
            var swiper = new Swiper('.category-slider-wrapper', {
                @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme == 'fashion')
                    slidesPerView: 3,
                    centeredSlides: true,
                @else
                    slidesPerView: 6,
                @endif
                spaceBetween: 30,
                lazy: true,
                loop: true,
                navigation: {
                    nextEl: '.category-button-next',
                    prevEl: '.category-button-prev',
                },
                autoplay: {
                    delay: 4000,
                },
                // Responsive breakpoints
                breakpoints: {
                    // when window width is <= 675
                    @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme == 'fashion')
                        675: {
                            slidesPerView: 1,
                        },
                    @else
                        675: {
                            slidesPerView: 2,
                            spaceBetween: 30
                        },
                    @endif

                    // when window width is <= 991
                    991: {
                        slidesPerView: 4,
                        spaceBetween: 30
                    },
                    // when window width is <= 1024px
                    1024: {
                        @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme == 'fashion')
                            slidesPerView: 4,
                        @else
                            slidesPerView: 6,
                        @endif
                        spaceBetween: 15
                    }
                }
            });
        }

        $(document).ready(function() {
            $('.category-img').each(function() {
                var img = $(this).data('src');
                $(this).attr('src', img);
            })

            $('.banner-img').each(function() {
                var img = $(this).data('src');
                $(this).attr('src', img);
            })
        })

        //product carousel
        if (('.product-slider-wrapper').length > 0) {
            var swiper = new Swiper('.product-slider-wrapper', {
                @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme == 'fashion')
                    slidesPerView: 4,
                @else
                    slidesPerView: 5,
                @endif
                spaceBetween: 0,
                lazy: true,
                observer: true,
                observeParents: true,
                loop: false,
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
                    @if (isset($ecommerce_setting->theme) && $ecommerce_setting->theme == 'fashion')
                        675: {
                            slidesPerView: 1,
                        },
                    @else
                        675: {
                            slidesPerView: 2,
                            spaceBetween: 30
                        },
                    @endif

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
        }

        $(document).on('click', '.add-to-cart', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var parent = '#add_to_cart_' + id;

            var qty = $(parent + " input[name=qty]").val();

            var route = "{{ route('addToCart') }}";

            var btn = $(this);

            var btn_text = $(this).html();

            $(this).html(
                '<span class="spinner-border spinner-border-sm" role="status"><span class="sr-only">...</span></span>'
            );

            $.ajax({
                url: route,
                type: "POST",
                data: {
                    product_id: id,
                    qty: qty,
                },
                success: function(response) {
                    if (response) {
                        $('.alert').addClass('alert-custom show');
                        $('.alert-custom .message').html(response.success);
                        $('.cart__menu .cart_qty').html(response.total_qty);
                        $('.cart__menu .total').html('{{ $currency->symbol ?? $currency->code }}' +
                            response.subTotal.toFixed(2));
                        $(btn).html(btn_text);
                        setTimeout(function() {
                            $('.alert').removeClass('show');
                        }, 4000);
                    }
                },
            });
        })
    </script>
@endsection

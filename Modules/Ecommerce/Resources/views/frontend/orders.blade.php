@extends('ecommerce::frontend.layout.main')

@section('title') {{ $ecommerce_setting->site_title ?? '' }} @endsection

@section('description') @endsection

@section('content')
<!--Breadcrumb Area start-->
<div class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="page-title">{{trans('file.My Orders')}}</h1>
                <ul>
                    <li><a href="{{url('customer/profile')}}">{{trans('file.dashboard')}}</a></li>
                    <li class="active">{{trans('file.My Orders')}}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--Breadcrumb Area ends-->

<!--My account Dashboard starts-->
<section class="my-account-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="user-sidebar-menu mb-5">
                    @include('ecommerce::frontend.customer-menu')
                </div>
            </div>
            <div class="col-md-9 tabs style1">
                <div class="row">
                    <div class="col-md-12">
                        @if(!empty($sales))
                            @foreach($sales as $sale)
                            <div class="card mb-5">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Order ID- {{ $sale->reference_no }}</strong>
                                            <span class="d-block">{{ date('d-m-Y', strtotime($sale->created_at)) }}</span>
                                        </div>
                                        <div>
                                            @if($sale->sale_status == 1)
                                            <span class="badge badge-success">Complete</span>
                                            @elseif($sale->sale_status == 2)
                                            <span class="badge badge-danger">Pending</span>
                                            @elseif($sale->sale_status == 3)
                                            <span class="badge badge-warning">Canceled</span>
                                            @else
                                            <span class="badge badge-primary">On The Way</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="mt-3 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="price">{{$currency->symbol ?? $currency->code}}{{ ($sale->grand_total - $sale->coupon_discount) }}</h4>
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-success" href="{{url('customer/order-details')}}/{{$sale->id}}">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                            &nbsp;&nbsp;
                                            @if($sale->sale_status == 2)
                                                <a class="btn btn-sm btn-danger" href="{{url('customer/order-cancel')}}/{{$sale->id}}">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </a>
                                            @endif
                                            @if($sale->sale_status == 1)
                                                <button class="btn btn-sm btn-warning add-rating-btn" data-order-id="{{ $sale->id }}">
                                                    <span class="material-symbols-outlined">star</span> Add Rating
                                                </button>
                                            @endif


                                            {{-- @if($sale->sale_status == 1)
    @php
        // Fetch products associated with the sale
        $products = \App\Models\Product_Sale::select('product_sales.product_id', 'products.name', 'product_sales.qty', 'product_sales.net_unit_price')
            ->join('products', 'products.id', '=', 'product_sales.product_id')
            ->where('Sale_id', $sale->id)
            ->get();

            // dd($products[0]->product_id);

        // Check if any product has already been rated by the logged-in customer
        $alreadyRated = $products->contains(function ($product) {
            // dd('aaa',auth()->id());
            $get = \App\Models\Rating::where('product_id', $product->product_id)
                ->where('customer_id', 9)
                ->exists();
                // dd($get);
        });
    @endphp

    @if($alreadyRated)
        <button class="btn btn-sm btn-secondary">
            <span class="material-symbols-outlined">star</span> Rated Product
        </button>
    @else
        <button class="btn btn-sm btn-warning add-rating-btn" data-order-id="{{ $sale->id }}">
            <span class="material-symbols-outlined">star</span> Add Rating
        </button>
    @endif
@endif --}}




                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="card mb-5">
                            <div class="card-body">
                                <h3>{{trans('file.You have not ordered anything yet!')}}</h3>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!--My account Dashboard ends-->

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Rate Your Products</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ratingForm">
                    @csrf
                    <input type="hidden" id="order_id" name="order_id">

                    <div id="product-rating-list">
                        <!-- Products will be loaded here -->
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Ratings</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
$(document).ready(function() {
    $('.add-rating-btn').on('click', function() {
        let orderId = $(this).data('order-id');
        $('#order_id').val(orderId);

        $.ajax({
            url: '{{ route("getOrderProducts") }}',
            type: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                let productList = '';
                console.log(response.products);
                response.products.forEach(function(product) {
                    let ratingValue = product.rating; // Fetch stored rating
                    console.log('ratingvalue', ratingValue);
                    productList += `
                        <div class="product-rating-item">
                            <strong>${product.name}</strong>
                            <div class="rating-stars" data-product-id="${product.product_id}">
                                ${generateStars(product.product_id, ratingValue)}
                            </div>
                            <input type="hidden" name="ratings[${product.product_id}]" id="rating-${product.product_id}" value="${ratingValue}">
                        </div>
                        <hr>
                    `;
                });

                $('#product-rating-list').html(productList);
                $('#ratingModal').modal('show');
            }
        });
    });

    function generateStars(productId, rating) {
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<span class="star ${i <= rating ? 'filled' : ''}" data-value="${i}" data-product-id="${productId}">★</span>`;
        }
        return starsHtml;
    }

    // Click event to update rating
    $(document).on('click', '.star', function() {
        let productId = $(this).data('product-id');
        let value = $(this).data('value');

        // Update the hidden input value
        $(`#rating-${productId}`).val(value);

        // Update UI: Highlight the selected stars
        $(`.rating-stars[data-product-id="${productId}"] .star`).each(function() {
            let starValue = $(this).data('value');
            $(this).toggleClass('filled', starValue <= value);
        });
    });

    $('#ratingForm').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: '{{ route("submitRating") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#ratingModal').modal('hide');
                alert(response.message);
                location.reload();
            },
            error: function(response) {
                alert('Something went wrong. Please try again.');
            }
        });
    });
});



</script>

<style>
.star {
    font-size: 24px;
    color: #ccc; /* Default star color (gray) */
    cursor: pointer;
    transition: color 0.3s ease-in-out;
}

.star.filled {
    color: #f39c12; /* Gold color for selected stars */
}


</style>
@endsection

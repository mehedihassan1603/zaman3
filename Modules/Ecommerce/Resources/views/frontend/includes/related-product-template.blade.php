<div class="single-product-wrapper">
    <div class="single-product-item">
        @if(($pro->promotion == 1) && (($pro->last_date > date('Y-m-d')) || !isset($pro->last_date)))
        <div class="product-promo-text style1 bg-danger">
            <span>-{{ round(($pro->price - $pro->promotion_price) / $pro->price * 100) }}%</span>
        </div>
        @endif
        <a href="{{url('product')}}/{{$pro->slug}}/{{$pro->id}}"  class="view-details">
            @if($pro->image!==null)
            @php
                $images = explode(',', $pro->image);
                $pro->image = $images[0];
            @endphp
            <img loading="lazy" class="product-img" data-src="{{ url('images/product/large/') }}/{{ $pro->image }}" alt="{{ $pro->name }}">
            @else
            <img loading="lazy" src="https://dummyimage.com/300x300/e5e8ec/e5e8ec&text={{ $pro->name }}" alt="{{ $pro->name }}">
            @endif
        </a>
        <div class="product-overlay">
            @if(in_array($pro->id,explode(',',$wishlist)))
            <a><span style="color: var(--theme-color);" class="material-symbols-outlined">favorite</span></a>
            @else
            <a data-id="{{$pro->id}}" class="add-to-wishlist"><span class="material-symbols-outlined">favorite</span></a>
            @endif
            <a class="quick-view" data-id="{{$pro->id}}" data-toggle="modal" data-target="#detailsModal" title="quick view"><span class="material-symbols-outlined">zoom_in</span></a>
        </div>
    </div>

    <div class="product-details">
        <a class="product-name" href="{{url('product')}}/{{$pro->slug}}/{{$pro->id}}">
            {!! ucwords($pro->name) !!}
            @if(isset($pro->unit))
            <span class="product-quantity">({{ $pro->unit->unit_name }})</span>
            @endif
        </a>
        <div class="product-price">
            @if(($pro->promotion == 1) && (($pro->last_date > date('Y-m-d')) || !isset($pro->last_date)))
            <span class="price">{{$currency->symbol ?? $currency->code}}{{ $pro->promotion_price }}</span>
            <span class="old-price">{{$currency->symbol ?? $currency->code}}{{ $pro->price }}</span>
            @else
            <span class="price">{{$currency->symbol ?? $currency->code}}{{ $pro->price }}</span>
            @endif
        </div>
        @if($ecommerce_setting->theme != 'fashion')
            @if($pro->in_stock == 1)
                @if(is_null($pro->is_variant))
                <form class="d-flex justify-content-between" method="post" id="add_to_cart_{{ $pro->id }}">
                    @csrf
                    <div class="d-flex align-items-center">
                        <div class="input-qty">
                            <button type="button" class="quantity-left-minus">
                                <i class="material-symbols-outlined">remove</i>
                            </button>
                            <input type="number" name="qty" class="input-number" value="1" min="1" max="{{ $pro->qty }}">
                            <button type="button" class="quantity-right-plus">
                                <i class="material-symbols-outlined">add</i>
                            </button>
                        </div>
                    </div>
                    <button data-id="{{ $pro->id }}" type="submit" class="button style1 add-to-cart"><span class="material-symbols-outlined">shopping_bag</span></button>
                </form>
                @else
                <div class="text-center">
                    <a href="{{url('/')}}/product/{{$pro->slug}}/{{$pro->id}}" class="button style1">{{trans('file.Add to cart')}}</a>
                </div>
                @endif
            @else
                @if($pro->qty > 0)
                    @if(is_null($pro->is_variant))
                    <form class="d-flex justify-content-between" method="post" id="add_to_cart_{{ $pro->id }}">
                        @csrf
                        <div class="d-flex align-items-center">
                            <div class="input-qty">
                                <button type="button" class="quantity-left-minus">
                                    <i class="material-symbols-outlined">remove</i>
                                </button>
                                <input type="number" name="qty" class="input-number" value="1" min="1" max="{{ $pro->qty }}">
                                <button type="button" class="quantity-right-plus">
                                    <i class="material-symbols-outlined">add</i>
                                </button>
                            </div>
                        </div>
                        <button data-id="{{ $pro->id }}" type="submit" class="button style1 add-to-cart"><span class="material-symbols-outlined">shopping_bag</span></button>
                    </form>
                    @else
                    <div class="text-center">
                        <a href="{{url('/')}}/product/{{$pro->slug}}/{{$pro->id}}" class="button style1">{{trans('file.Add to cart')}}</a>
                    </div>
                    @endif
                @else
                <span>{{trans('file.Out of stock')}}</span>
                @endif
            @endif
        @endif
    </div>
</div>


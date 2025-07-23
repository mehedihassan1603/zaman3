<header>
    <div id="header-middle" class="header-middle" style="background-color: #6453F7">
        <div class="">
            <div class="d-flex justify-content-between align-baseline">
                <div class="category__menu show-on-mobile"><i class="material-symbols-outlined">menu</i></div>
                <div class="logo" >
                    <a href="{{ url('/') }}">
                        @if (!config('database.connections.saleprosaas_landlord'))
                            @if (isset($ecommerce_setting->logo))
                                <img src="{{ url('frontend/images/') }}/{{ $ecommerce_setting->logo }}"

                                    alt="{{ $ecommerce_setting->site_title ?? '' }}">
                            @else
                                <img src="{{ asset('logo') }}/{{ $general_setting->site_logo }}"
                                    alt="{{ $ecommerce_setting->site_title ?? '' }}">
                            @endif
                        @else
                            @if (isset($ecommerce_setting->logo))
                                <img src="{{ asset('../../frontend/images/') }}/{{ $ecommerce_setting->logo }}"
                                    alt="{{ $ecommerce_setting->site_title ?? '' }}">
                            @else
                                <img src="{{ asset('../../logo') }}/{{ $general_setting->site_logo }}"
                                    alt="{{ $ecommerce_setting->site_title ?? '' }}">
                            @endif
                        @endif
                    </a>
                </div>
                <form action="{{ route('products.search') }}" method="post" class="header-search"
                    style="max-width:100%">
                    @csrf
                    <div class="header-search-container">
                        <input id="search" type="text" placeholder="Search products..." name="search">
                        <div class="search_result"></div>
                    </div>
                    <button class="btn btn-search" type="submit" style="margin-top:-2px; background-color: rgb(0, 0, 0);"><span class="d-flex"><i
                                class="material-symbols-outlined">search</i></span></button>
                </form>
                <ul class="offset-menu-wrapper">
                    <!-- <li class="language"><a  class="active" href="">En</a> / <a href="">Bn</a></li> -->
                    @guest
                        <li>
                            <a style="color: white;" href="{{ url('customer/login') }}">Login</a>
                        </li>
                    @endguest
                    @if (auth()->user() && auth()->user()->role_id == 5)
                        <li class="user-menu">
                            <i class="material-symbols-outlined">person_add</i>
                            <ul class="user-dropdown-menu">
                                <li><a style="color: white;" href="{{ url('customer/account-details') }}">My Account</a></li>
                                <li><a style="color: white;" href="{{ url('customer/orders') }}">Order History</a></li>
                                <li><a style="color: white;" href="{{ url('customer/address') }}">Addresses</a></li>
                                <li><a style="color: white;" href="{{ url('customer/logout') }}"> {{ trans('file.logout') }}</a></li>
                            </ul>
                        </li>
                    @endif
                    <li>
                        <a href="{{ url('track-order') }}" title="{{ trans('file.Track Order') }}"><i
                            style="color: white;" class="material-symbols-outlined">pin_drop</i></a>
                    </li>
                    <li class="wishlist__menu">
                        <a href="{{ url('customer/wishlist') }}" title="{{ trans('file.Wishlist') }}"><i
                                class="material-symbols-outlined"
                                style="color: white;"
                                title="{{ trans('file.My Wishlist') }}">favorite</i></a>
                        <span class="badge badge-light cart_qty">
                            {{ $wishlist_count }}
                        </span>
                    </li>
                    @php

                        $total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
                        $subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

                        if ($total_qty == 0) {
                            $subTotal = 0;
                        }

                    @endphp
                    <li class="cart__menu">
                        <i style="color: white;" class="material-symbols-outlined" title="{{ trans('file.Cart') }}">shopping_bag</i>
                        <span class="badge badge-light cart_qty">{{ $total_qty ?? 0 }}</span>
                        <span class="total">{{ $currency->symbol }}{{ $subTotal ?? 0.0 }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    {{-- Header bottom start  --}}
    <div class="header-bottom">
            <div class="">
                <div class="row">
                    <div class="col-md-3 d-none d-lg-flex d-xl-flex">
                        <div class="category-list">
                            <ul>
                                <li class="has-dropdown">
                                    <a class="category-button" href="#">
                                        <i class="material-symbols-outlined">menu</i> Categories
                                    </a>
                                    <ul class="dropdown sidebar {{ (request()->is('/')) ? 'show' : '' }}">
                                        @php
                                            function renderCategories($categories_list, $parent_id) {
                                                $subCategories = $categories_list->where('parent_id', $parent_id)->where('is_active', 1);
                                                if ($subCategories->count() > 0) {
                                                    echo '<ul class="dropdown">';
                                                    foreach ($subCategories as $subCategory) {
                                                        echo '<li class="category-item has-dropdown">';
                                                        echo '<a href="' . url('shop/' . $subCategory->slug) . '">';
                                                        if (isset($subCategory->icon)) {
                                                            echo '<img src="' . url('images/category/icons/' . $subCategory->icon) . '" alt="' . $subCategory->name . '">';
                                                        }
                                                        echo '<span>' . $subCategory->name . '</span>';
                                                        echo '</a>';

                                                        // Recursively render child categories
                                                        renderCategories($categories_list, $subCategory->id);

                                                        echo '</li>';
                                                    }
                                                    echo '</ul>';
                                                }
                                            }
                                        @endphp

                                        @foreach($all_categories as $category)
                                            @if(in_array($category->id, $parents))
                                                <li class="category-item has-dropdown">
                                                    <a href="{{ url('shop') }}/{{ $category->slug }}">
                                                        @if(isset($category->icon))
                                                            <img src="{{ url('images/category/icons/') }}/{{ $category->icon }}" alt="{{ $category->name }}">
                                                        @endif
                                                        <span class="category-name">{{ $category->name }}</span>
                                                    </a>

                                                    @php
                                                        renderCategories($categories_list, $category->id);
                                                    @endphp
                                                </li>
                                            @endif
                                        @endforeach

                                        <li>
                                            <a class="button style3 text-center" href="{{ url('shop') }}/">All Categories</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="main-header-inner">
                            <div id="main-menu" class="main-menu">
                                <nav id="mobile-nav" class="d-flex justify-content-between">
                                    <ul>
                                        @if (!empty($topNavItems))
                                            @foreach ($topNavItems as $nav)
                                                @if (!empty($nav->children[0]))
                                                <li class="user-menu">
                                                    <a href="#">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif <i class="caret"></i>
                                                        <ul class="user-dropdown-menu">
                                                            @foreach ($nav->children[0] as $childNav)
                                                            @if ($childNav->type == 'custom')
                                                            <li><a href="{{$childNav->slug}}" target="_blank">@if ($childNav->name == null) {{$childNav->title}} @else {{$childNav->name}} @endif</a></li>
                                                            @elseif($childNav->type == 'category')
                                                            <li><a href="{{url('shop')}}/{{$childNav->slug}}">@if ($childNav->name == null) {{$childNav->title}} @else {{$childNav->name}} @endif</a></li>
                                                            @else
                                                            <li><a href="{{url('')}}/{{$childNav->slug}}">@if ($childNav->name == null) {{$childNav->title}} @else {{$childNav->name}} @endif</a></li>
                                                            @endif
                                                            @endforeach
                                                        </ul>
                                                    </a>
                                                </li>
                                                @else
                                                    @if ($nav->type == 'custom')
                                                    <li><a href="{{$nav->slug}}" target="_blank">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'category')
                                                    <li><a href="{{url('shop')}}/{{$nav->slug}}">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'page' && ($nav->slug == 'home'))
                                                    <li><a href="{{url('/')}}">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'collection')
                                                    <li><a href="{{url('products')}}/{{$nav->slug}}">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @elseif($nav->type == 'brand')
                                                    <li><a href="{{url('brand')}}/{{$nav->slug}}">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @else
                                                    <li><a href="{{url('')}}/{{$nav->slug}}">@if ($nav->name == null) {{$nav->title}} @else {{$nav->name}} @endif</a></li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                    @if (isset($social_links))
                                    <ul class="social-links">
                                        @foreach ($social_links as $link)
                                        <li><a href="{{$link->link}}">{!!$link->icon!!}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    {{-- <div class="category-list">
        <ul class="category-row">
            @foreach ($all_categories as $category)
                @if (in_array($category->id, $parents))
                    <li class="category-item">
                        <a href="{{ url('shop') }}/{{ $category->slug }}">
                            @if (isset($category->icon))
                                <img src="{{ url('images/category/icons/') }}/{{ $category->icon }}"
                                    alt="{{ $category->name }}">
                            @endif
                        </a>
                        <span class="category-name">{{ $category->name }}</span> <!-- Placed outside the <a> tag -->
                    </li>
                @endif
            @endforeach
        </ul>
    </div> --}}
    {{-- @if(request()->is('/'))
    <div class="category-list">
        <ul class="category-row">
            @foreach($all_categories as $category)
                @if(in_array($category->id, $parents))
                    @php
                        $childCategories = $categories_list->where('parent_id', $category->id)->where('is_active', 1);
                    @endphp
                    <li class="category-item has-dropdown">
                        <a href="{{ url('shop') }}/{{ $category->slug }}">
                            @if(isset($category->icon))
                                <img src="{{ url('images/category/icons/') }}/{{ $category->icon }}" alt="{{ $category->name }}">
                            @endif
                        </a>
                        <span class="category-name">{{ $category->name }}</span>

                        @if($childCategories->count() > 0)
                            <ul class="dropdown">
                                @foreach ($childCategories as $child)
                                    <li>
                                        <a href="{{ url('shop') }}/{{ $child->slug }}">
                                            @if (isset($child->icon))
                                                <img src="{{ url('images/category/icons/') }}/{{ $child->icon }}" alt="{{ $child->name }}">
                                            @endif
                                            <span>{{ $child->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif --}}




    {{-- Header bottom end  --}}
</header>

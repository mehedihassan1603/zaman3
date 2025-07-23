       {{-- $products = DB::table('products')->where('is_active', true)->where('is_online', true)->where('category_id',$widget->product_category_id)->offset(0)->limit($widget->product_category_limit)->get();--}}

       {{-- $products = DB::table('products')->where('is_active', true)->where('is_online', true)->get(); --}}

    @php

        // $products = DB::table('products')->where('is_active', true)->where('category_id',$widget->product_category_id)->offset(0)->limit($widget->product_category_limit)->get();

       // Ensure $widget is defined before using it
    if (!isset($widget) || empty($widget->product_category_id)) {
        return []; // Return an empty array if widget category ID is missing
    }

    // Get category based on widget's category ID
    $category = DB::table('categories')
        ->select('id', 'name', 'slug', 'page_title', 'short_description')
        ->where('id', $widget->product_category_id) // Use widget's category ID
        ->where('is_active', 1)
        ->first();

    // Ensure category exists
    if ($category) {
        // Get subcategories based on the selected widget category
        $sub_categories = DB::table('categories')
            ->select('id', 'name', 'slug', 'page_title', 'short_description')
            ->where('parent_id', $category->id)
            ->where('is_active', 1)
            ->get();

        // Extract subcategory IDs
        $sub_cats = $sub_categories->pluck('id')->toArray();

        // Define product limit with a fallback value
        $product_limit = $widget->product_category_limit ?? 10; // Default limit to 10 if not set

        // Get products based on widget category ID and its subcategories
        $products = DB::table('products')
            ->where('is_active', 1)
            ->where(function ($query) use ($category, $sub_cats) {
                $query->where('category_id', $category->id);
                if (!empty($sub_cats)) {
                    $query->orWhereIn('category_id', $sub_cats);
                }
            })
            ->limit($product_limit)
            ->get();
    } else {
        $products = collect(); // Return an empty collection if category not found
    }



    @endphp

    <section class="product-tab-section">
        <div class="container-fluid">
            <div class="section-title mb-3">
                <div class="d-flex align-items-center">
                    <h3>{{$widget->product_category_title}}</h3>
                </div>
                @if($widget->product_category_type == 'slider')
                <div class="product-navigation">
                    <div class="product-button-next v1"><span class="material-symbols-outlined">chevron_right</span></div>
                    <div class="product-button-prev v1"><span class="material-symbols-outlined">chevron_left</span></div>
                </div>
                @endif
            </div>

            @if($widget->product_category_type == 'slider')
            <div class="product-slider-wrapper swiper-container" data-loop="{{$widget->category_slider_loop}}" data-autoplay="{{$widget->category_slider_autoplay}}">
                <div class="swiper-wrapper">

                    @forelse ($products as $product)
                    <div class="swiper-slide">
                    @include('ecommerce::frontend.includes.product-template')
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
            @else
            <div class="product-grid">
                @foreach($products as $product)
                @include('ecommerce::frontend.includes.product-template')
                @endforeach
            </div>
            @endif
        </div>
    </section>

@php($overallRating = App\Services\ProductManager::get_overall_rating($product->reviews))

<div class="product-card card {{$product['current_stock']==0?'stock-card':''}}"
     style="margin-bottom: 40px;display: flex; align-items: center; justify-content: center;">
    @if($product['current_stock']<=0)
        <label style="left: 29%!important; top: 29%!important;"
               class="badge badge-danger stock-out">{{translate('stock_out')}}</label>
    @endif

    <div class="card-header inline_product clickable" style="cursor: pointer;max-height: 250px;min-height: 250px">
        @if($product->discount > 0)
            <div class="d-flex" style="right: 0;top:0;position: absolute">
                    <span class="for-discoutn-value pr-1 pl-1">
                    @if ($product->discount_type == 'percent')
                            {{round($product->discount,2)}}%
                        @elseif($product->discount_type =='flat')
                            {{\App\Services\AdditionalServices::currency_converter($product->discount)}}
                        @endif
                        {{translate('off')}}
                    </span>
            </div>
        @else
            <div class="d-flex justify-content-end for-dicount-div-null">
                <span class="for-discoutn-value-null"></span>
            </div>
        @endif
        <div class="d-flex d-block center-div element-center" style="cursor: pointer">
            <a href="{{"https://shojonsl.com/product/".$product->slug}}">
                <img src="{{App\Services\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
                     onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                     style="width: 100%;max-height: 215px!important;">
            </a>
        </div>
    </div>

    <div class="card-body inline_product text-center p-1 clickable"
         style="cursor: pointer; max-height:5rem;">
        {{--        <div class="rating-show">--}}
        {{--            <span class="d-inline-block font-size-sm text-body">--}}
        {{--                @for($inc=0;$inc<5;$inc++)--}}
        {{--                    @if($inc<$overallRating[0])--}}
        {{--                        <i class="sr-star czi-star-filled active"></i>--}}
        {{--                    @else--}}
        {{--                        <i class="sr-star czi-star"></i>--}}
        {{--                    @endif--}}
        {{--                @endfor--}}
        {{--                <label class="badge-style">( {{$product->reviews_count}} )</label>--}}
        {{--            </span>--}}
        {{--        </div>--}}
        <div style="position: relative;" class="product-title1">
            <a href="{{"https://shojonsl.com/product/".$product->slug}}">
                {{ Str::limit($product['name'], 25) }}
            </a>
        </div>
        <div class="justify-content-between text-center">
            <div class="product-price text-center">
                @if($product->discount > 0)
                    <strike style="font-size: 12px!important;color: grey!important;">
                        {{\App\Services\AdditionalServices::currency_converter($product->unit_price)}}
                    </strike><br>
                @endif
                <span class="text-accent">
                    {{\App\Services\AdditionalServices::currency_converter(
                        $product->unit_price-(\App\Services\AdditionalServices::get_product_discount($product,$product->unit_price))
                    )}}
                </span>
            </div>
        </div>
    </div>
</div>

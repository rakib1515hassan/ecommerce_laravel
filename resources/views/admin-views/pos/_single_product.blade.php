<style>

</style>

<div class="product-card card" onclick="quickView('{{$product->id}}')" style="cursor: pointer;">
    <div class="card-header inline_product clickable p-0" style="height:134px;width:100%;overflow:hidden;">
        <div class="d-flex align-items-center justify-content-center d-block">
            <img src="{{asset('storage/product')}}/{{$product['image']}}"
                 onerror="this.src='{{asset('assets/back-end/img/160x160/img2.jpg')}}'"
                 style="width: 100%; border-radius: 5%;">
        </div>
    </div>

    <div class="card-body inline_product text-center p-1 clickable"
         style="height:3.5rem; max-height: 3.5rem">
        <div style="position: relative;" class="product-title1 text-dark font-weight-bold">
            {{ Str::limit($product['name'], 13) }}
        </div>
        <div class="justify-content-between text-center">
            <div class="product-price text-center">
                {{ \App\Services\AdditionalServices::currency_converter($product['price']- \App\Services\AdditionalServices::get_product_discount($product, $product['price']))  }}
                @if($product->discount > 0)
                    <strike style="font-size: 12px!important;color: grey!important;">
                        {{ \App\Services\AdditionalServices::currency_converter($product['price']) }}
                    </strike><br>
                @endif
            </div>
        </div>
    </div>
</div>

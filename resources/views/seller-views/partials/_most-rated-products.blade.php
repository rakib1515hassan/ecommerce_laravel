<!-- Header -->
<div class="card-header">
    <h5 class="card-header-title">
        <i class="tio-star"></i> {{translate('most_rated_products')}}
    </h5>
    <i class="tio-crown-outlined" style="font-size: 45px"></i>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tbody>
                @foreach($most_rated_products as $key=>$item)
                    @php($product=\App\Models\Product::find($item['product_id']))
                    @if(isset($product))
                        <tr onclick="location.href='{{route('seller.product.view',[$item['product_id']])}}'"
                            style="cursor: pointer">
                            <td scope="row">
                                <img height="35" style="border-radius: 5px"
                                     src="{{asset('storage/product')}}/{{$product['thumbnail']}}"
                                     onerror="this.src='{{asset('assets/back-end/img/160x160/img2.jpg')}}'"
                                     alt="{{$product->name}} image">
                                <span class="ml-2">
                                    {{isset($product)?substr($product->name,0,30) . (strlen($product->name)>20?'...':''):'not exists'}}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 18px">
                                    {{round($item['ratings_average'],2)}}
                                    <i style="color: gold" class="tio-star"></i>
                                </span>
                            </td>
                            <td>
                              <span style="font-size: 18px">
                                {{$item['total']}} <i class="tio-users-switch"></i>
                              </span>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- End Body -->

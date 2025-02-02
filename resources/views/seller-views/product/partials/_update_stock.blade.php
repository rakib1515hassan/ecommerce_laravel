<div class="card-header">
    <h4>{{translate('Product price & stock')}}</h4>
    <input name="product_id" value="{{$product['id']}}" style="display: none">
</div>
<div class="card-body">
    <div class="form-group">
        <div class="row">
            <div class="col-12 pt-4 sku_combination" id="sku_combination">
                @include('seller-views.product.partials._edit_sku_combinations',['combinations'=>json_decode($product['variation'],true)])
            </div>
            <div class="col-md-6" id="quantity">
                <label
                        class="control-label">{{translate('total')}} {{translate('Quantity')}}</label>
                <input type="number" min="0" value={{ $product->current_stock }} step="1"
                       placeholder="{{translate('Quantity') }}"
                       name="current_stock" class="form-control" required>
            </div>
        </div>
    </div>
    <br>
</div>

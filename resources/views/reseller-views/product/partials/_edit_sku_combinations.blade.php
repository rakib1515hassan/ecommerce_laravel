@if (count($combinations) > 0)
    <table class="table table-bordered">
        <thead>
            <tr class="btn-secondary">
                <td class="text-center">
                    <label for="" class="control-label">{{ translate('Variant') }}</label>
                </td>
                <td class="text-center">
                    <label for="" class="control-label">{{ translate('Variant Price') }}</label>
                </td>
                <td class="text-center">
                    <label for="" class="control-label">{{ translate('SKU') }}</label>
                </td>
                <td class="text-center">
                    <label for="" class="control-label">{{ translate('Quantity') }}</label>
                </td>
            </tr>
        </thead>
        <tbody>
@endif
@foreach ($combinations as $key => $combination)
    <tr>
        <td>
            <label for="" class="control-label">{{ $combination['type'] }}</label>
            <input value="{{ $combination['type'] }}" name="type[]" style="display: none">
        </td>
        <td>
            <input type="number" name="price_{{ $combination['type'] }}"
                value="{{ \App\Services\Converter::default($combination['price']) }}" min="0" step="0.01"
                class="form-control" required>
        </td>
        <td>
            <label for="" class="control-label">{{ $combination['sku'] }}</label>
            <input value="{{ $combination['sku'] }}" name="type[]" style="display: none">

            {{-- <input type="text" name="sku_{{ $combination['type'] }}" value="{{ $combination['sku'] }}"
                           class="form-control" required> --}}
        </td>
        <td>
            <label for="" class="control-label">{{ $combination['qty'] }}</label>
            <input value="{{ $combination['qty'] }}" name="type[]" style="display: none">

            {{-- <input type="number" onkeyup="update_qty()" name="qty_{{ $combination['type'] }}"
                           value="{{ $combination['qty'] }}" min="1" max="100000" step="1"
                           class="form-control" style="display: none"
                           required> --}}
        </td>
    </tr>
@endforeach
</tbody>
</table>

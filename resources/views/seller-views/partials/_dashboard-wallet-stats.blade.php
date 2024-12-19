<style>
    @media (min-width: 767px) {
        .row.row-10 .col-md-2 {
            flex: 0 0 20%;
            max-width: 20%;
        }
    }

</style>

<div class="row row-10">
    <div class="col-md-2 mt-1">
        <div class="card card-body card-hover-shadow h-100 text-white text-center" style="background-color: #22577A;">
            <h1 class="p-2 text-white">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['commission_given']))}}</h1>
            <div class="text-uppercase">{{translate('commission_given')}}</div>
        </div>
    </div>

    <div class="col-md-2  mt-1">
        <div class="card card-body card-hover-shadow h-100 text-white text-center" style="background-color: #595260;">
            <h1 class="p-2 text-white">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['pending_withdraw']))}}</h1>
            <div class="text-uppercase">{{translate('pending_withdraw')}}</div>
        </div>
    </div>

    <div class="col-md-2 mt-1">
        <div class="card card-body card-hover-shadow h-100 text-white text-center" style="background-color: #a66f2e;">
            <h1 class="p-2 text-white">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['delivery_charge_earned']))}}</h1>
            <div class="text-uppercase">{{translate('delivery_charge_earned')}}</div>
        </div>
    </div>

    <div class="col-md-2 mt-1">
        <div class="card card-body card-hover-shadow h-100 text-white text-center" style="background-color: #6E85B2;">
            <h1 class="p-2 text-white">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['collected_cash']))}}</h1>
            <div class="text-uppercase">{{translate('collected_cash')}}</div>
        </div>
    </div>

    <div class="col-md-2 mt-1">
        <div class="card card-body card-hover-shadow h-100 text-white text-center" style="background-color: #6D9886;">
            <h1 class="p-2 text-white">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['total_tax_collected']))}}</h1>
            <div class="text-uppercase">{{translate('total_collected_tax')}}</div>
        </div>
    </div>
</div>

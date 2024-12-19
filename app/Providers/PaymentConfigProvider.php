<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PaymentConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $mode = env('APP_MODE');

        try {
            $data = BusinessSetting::where(['type' => 'ssl_commerz_payment'])->first();
            $ssl = json_decode($data['value'], true);
            if ($ssl) {
                if ($mode == 'live') {
                    $url = "https://securepay.sslcommerz.com";
                    $host = false;
                } else {
                    $url = "https://sandbox.sslcommerz.com";
                    $host = true;
                }
                $config = array(
                    'projectPath' => env('PROJECT_PATH'),
                    'apiDomain' => env("API_DOMAIN_URL", $url),
                    'apiCredentials' => [
                        'store_id' => $ssl['store_id'],
                        'store_password' => $ssl['store_password'],
                    ],
                    'apiUrl' => [
                        'make_payment' => "/gwprocess/v4/api.php",
                        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
                        'order_validate' => "/validator/api/validationserverAPI.php",
                        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
                        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
                    ],
                    'connect_from_localhost' => env("IS_LOCALHOST", $host), // For Sandbox, use "true", For Live, use "false"
                    'success_url' => '/success',
                    'failed_url' => '/fail',
                    'cancel_url' => '/cancel',
                    'ipn_url' => '/ipn',
                );
                Config::set('sslcommerz', $config);
            }
        } catch (\Exception $ex) {

        }
    }
}

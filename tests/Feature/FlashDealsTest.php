<?php

namespace Tests\Feature;

use App\Models\FlashDeal;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FlashDealsTest extends TestCase
{
    public $token;
    public function setup(): void
    {
        parent::setup();
        $response = $this->postJson('api/v2/seller/auth/login', [
            'email' => 'shuvo.punam@gmail.com',
            'password' => 'password'
        ]);
        $this->token = $response['token'];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_add_product_to_flash_deal_from_seller_app()
    {
        $flashDeal = FlashDeal::where('deal_type', 'flash_deal')->first();
        $response = $this->postJson("api/v2/seller/flash-deals/{$flashDeal->id}/add-product", [
            'product_id' => 18,
            'discount'   => 30,
            'discount_type' => 'flat'
        ], [
            'Authorization' => "Bearer " . $this->token
        ]);

        $response->assertStatus(200)
            ->assertJson(function ($data) {
                $data['success'] = true;
            });
    }
}

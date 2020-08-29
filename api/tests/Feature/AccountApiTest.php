<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests if a token is given while logging in.
     *
     * @return void
     */
    public function test_it_gives_token_on_login()
    {
        $account1 = factory(Account::class)->create(['balance' => 10000]);
        factory(Currency::class)->create();
        $token = $account1->token;
        $this->assertEquals($token,'');
        $response = $this->call('GET', "api/accounts/$account1->id");
        $response->assertStatus(200);
        $response = $response->json();
        $this->assertArrayHasKey('token',$response[0]);
        $this->assertNotEquals($response[0]['token'],'');
    }

    /**
     * Tests if logging out clears token.
     *
     * @return void
     */
    public function test_it_clears_token_on_logout()
    {
        $account1 = factory(Account::class)->create(['balance' => 10000]);
        $token = $account1->token;
        $this->assertEquals($token,'');
        
        $response = $this->call('GET', "api/accounts/$account1->id");
        $account1 = $account1->fresh();
        $token = $account1->token;
        $this->assertNotEquals($token,'');

        $response = $this->call('POST', "api/accounts/$account1->id/logout");

        $account1 = $account1->fresh();
        $token = $account1->token;
        $this->assertEquals($token,'');

    }
}

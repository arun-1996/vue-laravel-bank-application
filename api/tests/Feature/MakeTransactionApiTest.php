<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Generator as Faker;

class MakeTransactionApiTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Tests if a valid transaction is successfull. 
     *
     * @return void
     */
    public function test_it_makes_transaction_if_token_and_balance_is_valid()
    {
        factory(Currency::class)->create();
        $account1 = factory(Account::class)->create(['balance' => 10000]);
        $account2 = factory(Account::class)->create(['balance' => 10000]);
        
        $account1Id = $account1->id;
        $response = $this->call('GET', "api/accounts/$account1Id");
        $response = $response->json();
        $token = $response[0]['token'];
        $amount = rand(1,100);
        $payment = [
            'from' => $account1->id,
            'to' => $account2->id,
            'amount' => $amount,
            'token' => $token
        ];
        $payment = factory(Transaction::class)->make($payment)->toArray();
        $response = $this->call('POST', 'api/accounts/'.$account1Id.'/transactions',$payment);
        $response = $response->json();
        $this->assertEquals($response["message"],'Transaction successfull.');
    }

    /**
     * Tests whether if a transaction fails when the token is incorrect.
     *
     * @return void
     */
    public function test_it_cancels_transaction_if_token_is_incorrect()
    {
        factory(Currency::class)->create();
        $account1 = factory(Account::class)->create(['balance' => 10000]);
        $account2 = factory(Account::class)->create(['balance' => 10000]);
        
        $account1Id = $account1->id;
        $response = $this->call('GET', "api/accounts/$account1Id");
        $response = $response->json();
        $token = $response[0]['token'];
        $amount = rand(1,100);
        $payment = [
            'from' => $account1->id,
            'to' => $account2->id,
            'amount' => $amount,
            'token' => $token . 'incorrect' , 
        ];
        $payment = factory(Transaction::class)->make($payment)->toArray();
        $response = $this->call('POST', 'api/accounts/'.$account1Id.'/transactions',$payment);
        $response = $response->json();
        $this->assertEquals($response["message"],'Transaction failed!');
    }

    /**
     * Tests whether if a transaction fails when there is not enough account balance.
     *
     * @return void
     */
    public function test_it_cancels_transaction_if_balance_is_insufficient()
    {
        factory(Currency::class)->create();
        $account1 = factory(Account::class)->create(['balance' => 10000]);
        $account2 = factory(Account::class)->create(['balance' => 10000]);
        
        $account1Id = $account1->id;
        $response = $this->call('GET', "api/accounts/$account1Id");
        $response = $response->json();
        $token = $response[0]['token'];
        $amount = 10001;
        $payment = [
            'from' => $account1->id,
            'to' => $account2->id,
            'amount' => $amount,
            'token' => $token . 'incorrect' , 
        ];
        $payment = factory(Transaction::class)->make($payment)->toArray();
        $response = $this->call('POST', 'api/accounts/'.$account1Id.'/transactions',$payment);
        $response = $response->json();
        $this->assertEquals($response["message"],'Transaction failed!');
    }

    /**
     * Tests whether if a transaction fails if there is no token provided.
     *
     * @return void
     */
    public function test_it_cancels_transaction_if_token_is_missing()
    {
        factory(Currency::class)->create();
        $account1 = factory(Account::class)->create(['balance' => 10000]);
        $account2 = factory(Account::class)->create(['balance' => 10000]);
        
        $account1Id = $account1->id;
        $response = $this->call('GET', "api/accounts/$account1Id");
        $response = $response->json();
        $amount = 10001;
        $payment = [
            'from' => $account1->id,
            'to' => $account2->id,
            'amount' => $amount,
        ];
        $payment = factory(Transaction::class)->make($payment)->toArray();
        $response = $this->call('POST', 'api/accounts/'.$account1Id.'/transactions',$payment);
        $response = $response->json();
        $this->assertEquals($response["message"],'Transaction failed!');
    }

}

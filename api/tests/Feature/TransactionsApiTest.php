<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Tests if all transactions of a user is listed.
     *
     * @return void
     */
    public function test_it_lists_transactions()
    {
        $transaction = [];
        for($i = 0; $i < 5 ; $i++){
            $from = rand(1,2);
            $transactionData = [
                'from' => $from,
                'to' => $from == 1 ? 2 : 1,
            ];
            
            $transaction[] = factory(Transaction::class)->create($transactionData);
        }

        $response = $this->call('GET', 'api/accounts/1/transactions');
        $response = $response->json();
        $this->assertCount(5,$response);
    }
}

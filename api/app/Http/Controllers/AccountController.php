<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\AccountToken;
use App\Models\Transaction;

class AccountController extends Controller
{
    /**
     * Get account info and token for making transactions while logging in.
     *
     * @param  int  $id
     * @return array
     */
    public function account($id)
    {
        $account = Account::with('accountToken')->find($id);
        $token = Str::random(32);
        
        if($account->accountToken == null)
        {
            $accountToken =  new AccountToken([
                'account_id' => $account->id,
                'token' => $token,
            ]);
            $account->accountToken()->save($accountToken);
        }
        else
        {
            $account->accountToken->update(['token' => $token]);
        }

        $account->load('accountToken');
        $account->token = $token;

        return [$account];
    }

    /**
     * Get transactions for user.
     *
     * @param  int  $id
     * @return array
     */
    public function transactions($id)
    {
        $account = Account::with('currencyInfo')->find($id);
        $hiddenAttributes = ['created_at','fromAccount','toAccount'];
        $transactions = Transaction::where('transactions.from',$id)
            ->orWhere('transactions.to',$id)
            ->get();
        $transactions = $transactions->makeHidden($hiddenAttributes);

        if($account->currency != 'usd'){
            foreach($transactions as $key => $transaction){
                $currencyInfo  = $account->currencyInfo;
                $amount = $transaction->amount * $currencyInfo->value_in_usd;
                $transactions[$key]->amount = round($amount,2,PHP_ROUND_HALF_DOWN);
            }
        }
        return $transactions;
    }

    /**
     * Makes a transactions if transaction is valid else return failed status.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return array $response
     */
    public function makeTransaction(Request $request, $id)
    {
        $to = $request->input('to');
        $amount = $request->input('amount');
        $details = $request->input('details');
        $token = $request->input('token');
        $account = Account::with('accountToken')->find($id);
        $response = [];
        $response['message'] = 'Transaction failed!';
        $response['status'] = false;
        $hasBalance = $account->balance >= $amount;
        if( self::tokenValid($token,$account) && $hasBalance)
        {
            $account->update(['balance' => \DB::raw('balance-' . $amount)]);

            $account = Account::whereRaw("id=$to")
                ->update(['balance' => \DB::raw('balance+' . $amount)]);

            \DB::table('transactions')->insert(
                [
                    'from' => $id,
                    'to' => $to,
                    'amount' => $amount,
                    'details' => $details
                ]
            );
            $response['message'] = 'Transaction successfull.';
            $response['status'] = true;
        }

        return $response;
    }

    /**
     * Validates the token while making a transaction
     *
     * @param  int  $token
     * @param  App\Models\Account  $account
     * @return bool
     */
    function tokenValid($token, $account){
        $accountToken = $account->accountToken;
        if($accountToken != null)
        {
            $tokenInDb = $accountToken->token;
            return $tokenInDb == $token;
        }

        return false;
    }

    /**
     * Clears the token when the user logs out.
     *
     * @param  int  $id
     * @return array $response
     */
    public function logout($id){
        $response = [];
        $response['status'] = false;
        $account = Account::with('accountToken')->find($id);
        if($account != null ){
            $response['status'] = true;
            $accountToken = $account->accountToken;
            if($accountToken != null && $accountToken->token != ''){
                $accountToken->update(['token' => '']);
            }
        }
        return $response;
    }

    /**
     * List Currencies
     *
     * @return array $currencies
     */
    public function currencies()
    {
        $currencies = DB::table('currencies')
            ->get();

        return $currencies;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = ['created_at'];
    protected $dates = ['created_at'];
    protected $appends = ['from_account_holder','to_account_holder','transaction_on'];

    /**
     * Account details of transaction from account.
     */
    public function fromAccount()
    {
        return $this->hasOne(Account::class,'id','from');
    }

    /**
     * Account details of transaction to account.
     */
    public function toAccount()
    {
        return $this->hasOne(Account::class,'id','to');
    }

    public function getFromAccountHolderAttribute()
    {
        return $this->fromAccount->name;
    }

    public function getToAccountHolderAttribute()
    {
        return $this->toAccount->name;
    }

    public function getTransactionOnAttribute()
    {
        return $this->created_at->format('M d yy');
    }

}

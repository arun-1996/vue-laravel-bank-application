<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;
    protected $dates = ['created_at'];
    protected $appends = ['from_account_holder','to_account_holder','transaction_on'];
    protected $fillable = ['from','to','amount','details'];

    /**
     * Overriding boot for created_at.
     */
    public static function boot()
    {

        parent::boot();
        static::creating(function ($model) {

            $model->created_at = $model->freshTimestamp();

        });

    }

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

    /**
     * From account holder name.
     */
    public function getFromAccountHolderAttribute()
    {
        return $this->fromAccount->name;
    }

    /**
     * To account holder name.
     */
    public function getToAccountHolderAttribute()
    {
        return $this->toAccount->name;
    }

    /**
     * Transaction date.
     */
    public function getTransactionOnAttribute()
    {
        return $this->created_at->format('M d yy');
    }

}

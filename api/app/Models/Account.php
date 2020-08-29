<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['balance'];
    public $timestamps = false;
    public $appends = ['currency'];

    /**
     * Tokens to validate transaction requests.
     */
    public function accountToken()
    {
        return $this->hasOne(AccountToken::class,'account_id','id');
    }

    /**
     * Currency infromation of account.
     */
    public function currencyInfo()
    {
        return $this->hasOne(Currency::class,'id','currency_id');
    }

    /**
     * Returns token if present.
     */
    public function getTokenAttribute()
    {
        if( $this->accountToken != null )
        {
            return $this->accountToken->token;
        }

        return '';
    }

    /**
     * Returns Currency of account.
     */
    public function getCurrencyAttribute()
    {
        return strtolower($this->currencyInfo->currency_short);
    }
}

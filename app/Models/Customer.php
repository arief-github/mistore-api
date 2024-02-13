<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Authenticable implements JWTSubject
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */

    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'remember_token'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * getCreatedAtAttribute
     *
     * @param mixed $date
     * @return void
     *
     */

     public function getCreatedAtAttribute($date) {
        $value = Carbon::parse($date);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
     }

     /**
      * get the identifier that will be stored in the subject claim of JWT
      */

     public function getJWTIdentifier()
     {
         // TODO: Implement getJWTIdentifier() method.
         return $this->getKey();
     }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT
     * @return array
     */
     public function getJWTCustomClaims()
     {
         return [];
     }
}

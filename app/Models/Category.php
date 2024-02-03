<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image'];

      /**
     * Relationship Category to Products is One to Many (Inverse)
     * 
     * it means 1 category can have many products
     * 
     */

    public function products()
    {
        return $this->hasMany(Product::class);
    } 

    /**
     * getImageAttribute
     * 
     * @param mixed $image
     * @return void
     *
     */

     public function getImageAttribute($image)
     {
        return asset('storage/categories'. $image);
     }
}

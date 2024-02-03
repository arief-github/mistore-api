<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

     protected $fillable = ['image', 'title', 'slug', 'category_id', 'user_id', 'description', 'weight', 'price', 'stock', 'discount'];

    // Relation One to Many Inverse

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cart()
    {
        return $this->belongsTo(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * getImageAttribute
     *
     * @param mixed $image
     * @return void
     */

    public function getImageAttribute($image)
    {
        return asset('storage/products/'. $image);
    }

    /**
     * getReviewsAvgRatingAttribute
     *
     * @param mixed $value
     * @return void
     *
     */

    public function getReviewsAvgRatingAttribute($value)
    {
        return $value ? substr($value, 0,3) : 0;
    }
}

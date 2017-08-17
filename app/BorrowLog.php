<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BorrowLog extends Model
{
    protected $fillable = ['book_id','user_id','is_returned'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    protected $casts = [
        'is_returned' => 'boolean', // true or false 
    ];

    public function scopeReturned($query)
    {
        return $query->where('is_returned',1);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('is_returned',0);
    }
}

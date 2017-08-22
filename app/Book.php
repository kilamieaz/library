<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Book extends Model
{
    protected $fillable = ['title','author_id','amount'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function borrowLogs()
    {
        return $this->hasMany(BorrowLog::class);
    }

    public function getStockAttribute()
    {
            $borrowed = $this->borrowLogs()->borrowed()->count();
        $stock = $this->amount - $borrowed;
        return $stock;
    }

    public static function boot()
    {
        parent::boot();
        // supaya jika update jumlah buku tidak kurang dari buku yang di pinjam
        self::updating(function($book)
        {
            if ($book->amount < $book->borrowed) {
                session::flash("flash_notification", [
                    "level"     => "danger",
                    "message"   => "Jumlah buku $book->title harus >= " . $book->borrowed
                ]);
                return false;
            }
        });
        // membatasi penghapusan ketika buku masih di pinjam
        self::deleting(function($book)
        {
            if ($book->borrowLogs()->count() > 0){
                session::flash("flash_notification", [
                    "level"     => "danger",
                    "message"   => "Buku $book->title sedang di pinjam"
                ]);
                return false;
            }
        });
    }

    public function getBorrowedAttribute()
    {
        return $this->borrowLogs()->borrowed()->count();
    }

    

}

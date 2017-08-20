<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class member extends Model
{
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function($member) {
            $member = User::get();
            if ($member->books->count() > 0){
                //menyiapkan pesan error
                $html  = 'Member tidak bisa di hapus karena masih memiliki buku : ';
                $html .= '<ul>';
                foreach ($member->books as $book) {
                    $html .= "<li>$book->title</li>";
                }
                $html .= '</ul>' ;

                Session::flash("flash_notification", [
                    "level"     => "danger",
                    "message"   => $html
                ]);

                // membatalkan proses penghapusan
                return false;
            }
        });
    }
}

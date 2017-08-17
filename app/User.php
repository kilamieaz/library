<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;

use App\Book;
use App\BorrowLog;
use App\Exceptions\BookException;
// buat kirim email
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function borrowLogs() //semua data peminjaman
    {
        return $this->hasMany(BorrowLog::class);
    }

    public function Borrow(Book $book)
    {
        // cek apakah masih ada stok buku
        if ($book->stock < 1 ){
            throw new BookException("Buku $book->title sedang tidak tersedia.");
        }
        // cek apakah buku ini sedang dipinjam oleh user
        if($this->borrowlogs()->where('book_id', $book->id)->where('is_returned', 0)->count() > 0)
        {
            throw new BookException("Buku $book->title Sedang Anda Pinjam.");   //$e
        }
        
        $borrowLog = BorrowLog::create(['user_id' => $this->id, 'book_id' => $book->id]);
        return $borrowLog;
    }

    // casting
    protected $casts = [
        'is_verified' => 'boolean',
    ];

    //jika dia sudah verifikasi dan ada token sebelumnya
    public function generateVerificationToken()
    {
        $token = $this->verification_token;
        if (!$token) {
            $token = str_random(40);
            $this->verification_token = $token;
            $this->save();
        }
        return $token;
    }

    // buat kirim verification ke user
    public function sendVerification()
    {
        $token = $this->generateVerificationToken();
        $user = $this;
        Mail::send('auth.emails.verification', compact('user','token'), function ($m) use ($user){
            $m->to($user->email, $user->name)->subject('verifikasi Akun Larapus');
        });
    }

    public function verify()
    {
        $this->is_verified = 1;
        $this->verification_token = null;
        $this->save();
    }
}

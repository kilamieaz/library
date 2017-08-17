<?php

use Illuminate\Database\Seeder;
use App\Book;
use App\Author;
use App\BorrowLog;
use App\User;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample Penulis
        $author1 = Author::create(['name'=>'Anugrah Roby Syahputra']);
        $author2 = Author::create(['name'=>'Mohammad Fauzil Adhim']);
        $author3 = Author::create(['name'=>'Anis Matta']);
        $author4 = Author::create(['name'=>'Cahyadi Takariawan']);
        $author5 = Author::create(['name'=>'Asma Nadia']);
        $author6 = Author::create(['name'=>'Indra Noveldy']);
        $author7 = Author::create(['name'=>'Abu Umar Basyir']);
        $author8 = Author::create(['name'=>'Ahmad Rifai Rifan']);
        $author9 = Author::create(['name'=>'Ahmad Arif Lubis']);
        $author10 = Author::create(['name'=>'Ikhsanun Kamil']);
        $author11 = Author::create(['name'=>'Salim A. Fillah']);

        // Sample Buku
        $book1 = Book::create(['title'=>'Married Because Of Allah',
        'amount'=>3, 'author_id'=>$author1->id]);
        $book2 = Book::create(['title'=>'Kupinang Engkau Dengan Hamdalah',
        'amount'=>2, 'author_id'=>$author2->id]);
        $book3 = Book::create(['title'=>'Sebelum Mengambil Keputusan Besar Itu',
        'amount'=>4, 'author_id'=>$author3->id]);
        $book4 = Book::create(['title'=>'Di Jalan Dakwah Kugapai Sakinah',
        'amount'=>3, 'author_id'=>$author4->id]);
        $book5 = Book::create(['title'=>'Sakinah Bersamamu',
        'amount'=>3, 'author_id'=>$author5->id]);
        $book6 = Book::create(['title'=>'Menikah Untuk Bahagia',
        'amount'=>3, 'author_id'=>$author6->id]);
        $book7 = Book::create(['title'=>'Sutra Ungu',
        'amount'=>3, 'author_id'=>$author7->id]);
        $book8 = Book::create(['title'=>'jadikan Aku Halal bagimu',
        'amount'=>3, 'author_id'=>$author8->id]);
        $book9 = Book::create(['title'=>'Halaqah Cinta',
        'amount'=>3, 'author_id'=>$author9->id]);
        $book10 = Book::create(['title'=>'Jodohku, inilah Proposalku',
        'amount'=>3, 'author_id'=>$author10->id]);
        $book11 = Book::create(['title'=>'Bahagianya Merayakan Cinta',
        'amount'=>0, 'author_id'=>$author11->id]);
        

        // Sample peminjaman buku
        $member = User::where('email', 'member@gmail.com')->first();
        #member = User::find('id',2)  (juga bisa)
        BorrowLog::create(['user_id' => $member->id ,'book_id' => $book1->id, 'is_returned' => 0]);
        BorrowLog::create(['user_id' => $member->id ,'book_id' => $book2->id, 'is_returned' => 0]);
        BorrowLog::create(['user_id' => $member->id ,'book_id' => $book3->id, 'is_returned' => 1]);
    }
}

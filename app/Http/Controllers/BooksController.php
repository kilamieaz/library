<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use App\Book;
use Session;                                     //digunakan untuk mengirim data flash
use Illuminate\Support\Facades\File;             //digunakan di update
use App\Http\Requests\StoreBookRequest;          //digunakan di function Store 
use App\Http\Requests\UpdateBookRequest;         //digunakan di function Update

// untuk method borrow
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\BorrowLog;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\BookException;   //memanggil exception
use Excel;

class BooksController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            $books = Book::with('author');      //penggunaan method with() akan meload relasi dari book
            return Datatables::of($books)
                ->addColumn('action', function($book){
                    return view('datatable._action', [
                        'model'         => $book,
                        'destroy_url'   => route('books.destroy', $book->id),
                        'edit_url'      => route('books.edit', $book->id),
                        'confirm_message' => 'Yakin mau menghapus ' . $book->title . ' ?'
                    ]);
                })->make(true);
        }
        //DATA DARI $BOOKS
        $html = $htmlBuilder
            ->addColumn(['data' => 'title'          ,'name' => 'title'                  ,'title' => 'Judul'])
            ->addColumn(['data' => 'amount'         ,'name' => 'amount'                 ,'title' => 'Jumlah'])
            ->addColumn(['data' => 'author.name'    ,'name' => 'author.name'            ,'title' => 'Penulis'])
            ->addColumn(['data' => 'action'         ,'name' => 'action'                 ,'title' => '', 'orderable' => false,'searchable' => false]);
            return view('books.index', compact('html'));

    }

    public function create()
    {
        return view('books.create');
    }

    public function store(StoreBookRequest $request)
    {
         $book = Book::create($request->except('cover'));

         if ($request->hasFile('cover')) {
             $uploaded_cover = $request->file('cover'); // DATA
             $extension = $uploaded_cover->getClientOriginalExtension();
             $filename = md5(rand(1111, 9999)) . '.' . $extension; // NAMA
             //destionation = tujuan 
             $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';  //TUJUAN
             //(DATA) dipindahkan ke (TUJUAN) dan di beri (NAMA)
             $uploaded_cover->move($destinationPath, $filename);  
             $book->cover = $filename;
             $book->save();
         }
             Session::flash("flash_notification", [
                "level"   => "success",
                "message" => "Berhasil menyimpan $book->title"
             ]);
            
        return redirect()->route('books.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $book = Book::find($id);
        return view('books.edit', compact('book'));
    }

    public function update(UpdateBookRequest $request, $id)
    {
         $book = Book::find($id);
        //  dd($book);
         if (!$book->update($request->all())) return redirect()->back();

         if ($request->hasFile('cover')) {
             $filename = null;
             $uploaded_cover = $request->file('cover');                             //DATA
             $extension = $uploaded_cover->getClientOriginalExtension();            //EXTENSION = JPG

             $filename = md5(time()). '.' . $extension;                             //NAMA
             $destionationPath = public_path() . DIRECTORY_SEPARATOR . 'IMG';       //TUJUAN

             $uploaded_cover->move($destionationPath, $filename);                   //DATA->TUJUAN,NAMA
            
            // hapus cover lama, jika ada 
             if ($book->cover)
                $old_cover = $book->cover;
                $filepath = public_path() . DIRECTORY_SEPARATOR . 'IMG'
                . DIRECTORY_SEPARATOR . $book->cover;
                
                try {
                    file::delete($filepath);
                } catch (FileNotFoundException $e){

                }
                //buat cover baru
                $book->cover = $filename;
                $book->save();
         }
         //feedback
         Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "berhasil menyimpan $book->title"
         ]);

         return redirect('/admin/books');
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        $cover = $book->cover;
        if(!$book->delete()) return redirect()->back();
        // hapus cover lama, jika ada
        if ($cover) {
            $old_cover = $book->cover;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $book->cover;
            try {
                File::delete($filepath);
                } catch (FileNotFoundException $e) {
                // File sudah dihapus/tidak ada
                }
        }
        Session::flash("flash_notification", [
            "level"=>"success",
            "message"=>"Buku berhasil dihapus"
            ]);
        return redirect('/admin/books');
    }

    public function borrow($id)   //(semacam store)
    {
        try {
            $book = Book::findOrFail($id);      //memastikan id buku yang digunakan valid.
            
            Auth::user()->borrow($book);        //App\User()->borrow($book)
            
            Session::flash('flash_notification', [
                "level"     => "success",
                "message"   => "Berhasil meminjam $book->title"
            ]);
        } catch (BookException $e) {                     
            Session::flash('flash_notification', [
                "level"     => "danger",
                "message"   => $e->getMessage()
            ]);
        } catch (ModleNotFoundException $e) {
            Session::flash('flash_notification', [
                "level"     => "danger",
                "message"   => "Buku tidak ditemukan."
            ]);
        }

        return redirect('/');
    }

    public function returnBack($book_id)
    {
        $borrowLog = BorrowLog::where('user_id',Auth::user()->id)   //ambil data berdsarkan
            ->where('book_id', $book_id)                            // book id
            ->where('is_returned', 0)                               // returned = 0
            ->first();                                              //ambil data pertama
        if ($borrowLog) {
            $borrowLog->is_returned = true;                         //ubah data nya menjadi 1 = true
            $borrowLog->save();

            Session::flash('flash_notification', [
                "level"     => "success",
                "message"   => "Berhasil mengembalikan ". $borrowLog->book->title
            ]);
        }
        return redirect('/home');
    }

    public function export()
    {
        return view('books.export');
    }

    public function exportPost(Request $request)
    {
        // validasi
        $this->validate($request, [
            'author_id' => 'required',
        ],[
            'author_id.required' => 'Anda belum memilih penulis. Pilih minimal 1 penulis.'
        ]);
        $books = Book::whereIn('id', $request->get('author_id'))->get();  //cari buku berdasarkan author yg di dapatkan
        Excel::create('Data Buku Larapus', function($excel) use ($books) {
            //Set property
            $excel->setTitle('Data Buku Larapus')
            ->setCreator(Auth::user()->name);   //berdasarkan yg sedang login
            $excel->sheet('Data Buku', function($sheet) use ($books){
                $row = 1 ;             //no baris
                $sheet->row($row, [    //title
                    'judul',
                    'jumlah',
                    'stok',
                    'penulis'
                ]);
                foreach ($books as $book) {
                    $sheet->row(++$row, [
                        $book->title,
                        $book->amount,
                        $book->stock,
                        $book->author->name,
                    ]);
                }
            });
        })->export('xls');
        // return view('books.export');
    }   
}
  
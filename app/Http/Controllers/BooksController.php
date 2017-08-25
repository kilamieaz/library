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
use PDF;
use Validator;
use App\Author;

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

    public function destroy(Request $request, $id)
    {
        $book = Book::find($id);
        $cover = $book->cover;
        if(!$book->delete()) return redirect()->back();
        // handle hapus buku via Ajax
        if ($request->ajax()) return response()->json(['id' => $id]);
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
            'type'      => 'required|in:pdf,xls'
        ],[
            'author_id.required' => 'Anda belum memilih penulis. Pilih minimal 1 penulis.'
        ]);
        $books = Book::whereIn('id', $request->get('author_id'))->get();  //cari buku berdasarkan author yg di dapatkan
        $handler = 'export' .  ucFirst($request->get('type'));  //request nya bisa random
        return $this->$handler($books);
        // return view('books.export');
    }   

    private function exportXls($books)
    {
        Excel::create('Data Buku Larapus', function($excel) use ($books) { //nama folder
            // Set the properties
            $excel->setTitle('Data Buku Larapus')   //judul
                  ->setCreator('Auth::user()->name');        //pembuat
            $excel->sheet('Data Buku', function($sheet) use ($books) {
                $row = 1;
                $sheet->row($row, [
                    'Judul',
                    'Jumlah',
                    'Stok',
                    'Penulis'
                ]);
                foreach ($books as $book) {
                    $sheet->row(++$row, [
                        $book->title,
                        $book->amount,
                        $book->stock,
                        $book->author->name
                    ]);
                }
            });
        })->export('xls');
    }
    
    private function exportPdf($books)
    {
        $pdf = PDF::loadview('pdf.books', compact('books')); //membuat file pdf pada view yg di set
        return $pdf->download('books.pdf');
    }

    public function generateExcelTemplate()
    {
        Excel::create('Template Import Buku', function($excel) {
            // Set the properties
            $excel->setTitle('Template Import Buku')
                ->setCreator('Larapus')
                ->setCompany('Larapus')
                ->setDescription('Template import buku untuk Larapus');
                
            $excel->sheet('Data Buku', function($sheet) {
                $row = 1;
                $sheet->row($row, [
                    'judul',
                    'penulis',
                    'jumlah'
                ]);
            }); 
        })->download();
    }

    public function importExcel(Request $request)
    {
        // validasi untuk memastikan file yang diupload adalah excel
        $this->validate($request, [ 'excel' => 'required']);
        // ambil file yang baru diupload
        $excel = $request->file('excel');
        // baca sheet pertama
        $excels = Excel::selectSheetsByIndex(0)->load($excel, function($reader) {
            // options, jika ada
        })->get();
        // rule untuk validasi setiap row pada file excel
        $rowRules = [
            'judul'     => 'required',
            'penulis'   => 'required',
            'jumlah'    => 'required'
        ];
        // Catat semua id buku baru
        // ID ini kita butuhkan untuk menghitung total buku yang berhasil diimport
        $books_id = [];
        // looping setiap baris, mulai dari baris ke 2 (karena baris ke 1 adalah nama kolom)
        foreach ($excels as $row) {
            // Membuat validasi untuk row di excel
            // Disini kita ubah baris yang sedang di proses menjadi array
            $validator = Validator::make($row->toArray(), $rowRules);
            // Skip baris ini jika tidak valid, langsung ke baris selanjutnya
            if ($validator->fails()) continue;
            // Syntax dibawah dieksekusi jika baris excel ini valid
            // Cek apakah Penulis sudah terdaftar di database
            $author = Author::where('name', $row['penulis'])->first();
            // buat penulis jika belum ada
        if (!$author) {
            $author = Author::create(['name'=>$row['penulis']]);
        }
        // buat buku baru
        $book = Book::create([
            'title'      => $row['judul'],
            'author_id'  => $author->id,
            'amount'     => $row['jumlah']
        ]);
        // catat id dari buku yang baru dibuat
        array_push($books_id, $book->id);
        }
        // Ambil semua buku yang baru dibuat
        $books = Book::whereIn('id', $books_id)->get();
        // redirect ke form jika tidak ada buku yang berhasil diimport
        if ($books->count() == 0) {
            Session::flash("flash_notification", [
                "level"     => "danger",
                "message"   => "Tidak ada buku yang berhasil diimport."
            ]);
            return redirect()->back();
        }
        // set feedback
        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Berhasil mengimport" . $books->count() . " buku."
        ]);
        // Tampilkan index buku
        return redirect()->route('books.index');

        // Tampilkan halaman review buku
        // return view('books.import-review')->with(compact('books'));
        }

}
  
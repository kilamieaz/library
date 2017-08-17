<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Author;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use Session;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;

class AuthorsController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            $authors = Author::select(['id', 'name']); 
                return Datatables::of($authors)
                
                ->addColumn('action', function($author){
                    return view('datatable._action', [
                        'model'         => $author,
                        'destroy_url'   => route('authors.destroy', $author->id),
                        'edit_url'      => route('authors.edit', $author->id),
                        'confirm_message' => 'Yakin mau menghapus ' . $author->name . ' ?' 
                        // 'edit_url' => '/admin/authors/$author->id/edit', 
                    ]);
                })->make(true);
        }

        $html = $htmlBuilder
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Nama'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false,'searchable' => false]);
        return view('authors.index' , compact('html'));                         
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(StoreAuthorRequest $request)
    {
        $author = Author::create($request->only('name'));
        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil menyimpan $author->name"
        ]);
        return redirect('/admin/authors');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $author = Author::find($id);
        return view('authors.edit' ,compact('author'));
    }

    public function update(Request $request, $id)
    {
        $author = Author::find($id);
        $author->update($request->only('name'));    //update hanya nama berdasarkan id
                      //$request->all()
        Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil menyimpan $author->name"
        ]);
        return redirect()->route('authors.index');
    }

    public function destroy($id)
    {
        // Author::destroy($id);
        if(!Author::destroy($id)) return redirect()->back();
        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Penulis berhasil dihapus"
        ]);
        return redirect('/admin/authors');
    }
}

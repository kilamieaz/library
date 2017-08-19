<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Role;
use App\User;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Facades\Datatables;

class MembersController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()){
            $members = Role::where('name','member')->first()->users;
            return Datatables::of($members)
            ->addColumn('action', function($member){
                return view('datatable._action', [
                   'model'           => $member,
                   'destroy_url'     => route('members.destroy', $member->id),
                   'edit_url'        => route('members.edit', $member->id),
                   'confirm_message' => 'Yakin mau menghapus' . $member->name . '?'
                ]);
            })->make(true);
        }

        $html = $htmlBuilder
        ->addColumn(['data'=>'name','name'=>'name','title'=>'Nama'])
        ->addColumn(['data'=>'email','name'=>'email','title'=>'Email'])
        ->addColumn(['data'=>'action','name'=>'action','title'=>'','orderable'=>false,'searchable'=>false]);

        return view('members.index',compact('html')); 
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Role;
use App\User;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Facades\Datatables;

use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\UpdateMemberRequest;

class MembersController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()){
            $members = Role::where('name','member')->first()->users;
            return Datatables::of($members)
            ->addColumn('name', function($member){
                return '<a href="'.route('members.show',$member->id).'">'.$member->name.'</a>';
            })
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
        return view('members.create');  
    }

    public function store(StoreMemberRequest $request)
    {
        $password = str_random(6);
        $data = $request->all();
        $data['password'] = bcrypt($password);
        // bypass verifikasi
        $data['is_verified'] = 1;
        $member = User::create($data);
        //set role
        $memberRole = Role::where('name','member')->first();
        $member->attachRole($memberRole);

        //kirim email
        Mail::send('auth.emails.invite',compact('member', 'password'), function ($m) use ($member) {
            $m->to($member->email, $member->name)->subject('Anda telah di daftarkan di Larapus!');
        });

        Session::flash("flash_notification",[
            "level"     => "success",
            "message"   => "Berhasil menyimpan member dengan email ".
                           "<strong>". $data['email'] ."</strong>".
                           " dan password <strong>".$password."</strong>"
        ]);

        return redirect()->route('members.index');

    }

    public function show($id)
    {
        $member = User::find($id);
        return view('members.show', compact('member'));
    }

    public function edit($id)
    {
        $member = User::find($id);
        return view('members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        $member = User::find($id);
        $member->update($request->only('name','email'));
        Session::flash("flash_notification", [
            "level"     =>  "success",
            "message"   =>  "Berhasil menyimpan $member->name"
        ]);
        return redirect()->route('members.index');
    }

    public function destroy($id)
    {
        //
    }
}

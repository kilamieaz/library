@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
               <ul class="breadcrumb">
                    <li><a href="/home">Dashboard</a></li>
                    <li><a href="/admin/books">Buku</a></li>
                    <li class="active">Tambah buku</li>
               </ul> 
               <div class="panel panel-default">
                    <div class="panel-heading">
                       <h2 class="panel-title">Tambah Buku</h2> 
                    </div>

                    <div class="panel-body">
                        {!! Form::open(['url'    => '/admin/books',
                                        'method' => 'POST',
                                        'files'  => 'true',
                                        'class'  => 'form-horizontal']) 
                        !!}
                        @include('books._form')
                        {!! Form::close() !!}
                    </div>
               </div>
            </div>
        </div>
    </div>
@endsection
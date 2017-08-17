@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
               <ul class="breadcrumb">
                    <li><a href="/home">Dasboard</a></li>
                    <li><a href="/admin/authors">Penulis</a></li>
                    <li class="active">Tambah Penulis</li>
               </ul> 
               <div class="panel panel-default">
                    <div class="panel-heading">
                       <h2 class="panel-title">Tambah Penulis</h2> 
                    </div>

                    <div class="panel-body">
                        {!! Form::open(['url'    => '/admin/authors',
                                        'method' => 'POST', 
                                        'class'  => 'form-horizontal']) 
                        !!}
                        @include('authors._form')
                        {!! Form::close() !!}
                    </div>
               </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app-web-layout')

@section('title', 'Import Parents Data') 

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mt-20px">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>Import parents data</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('parents/imports') }}" method="POST" enctype="multipart/form-data">
                            @csrf 
                            <div class="input-group">
                                <input type="file" name="import_file" class="form-control" />
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>

                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Adm No</th>
                                    <th>Parent Name</th>
                                    <th>Telephone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parents as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->adm_no }}</td>
                                        <td>{{ $item->parent_name }}</td>
                                        <td>{{ $item->telephone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
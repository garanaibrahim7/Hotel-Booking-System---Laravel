@extends('layouts.adminlte')

@section('title', 'UI Elements')

@section('content')


    @if (!empty($users))

        <div class="card my-4">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3 class="card-title">Users Table</h3>
                    <a class="btn btn-dark" href="{{ route('create-user') }}">Add User</a>
                </div>
                <div class="card-body">
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width: 40px">Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $row)
                            <tr class="align-middle">
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->email }}</td>
                                <td><span class="badge text-bg-success">User</span></td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $users->links() }}
            </div>
        </div>
    @endif

    @if (session('message'))
        <div id="flashAlert" class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show">

            {{ session('message') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>

        </div>
    @endif
@endsection

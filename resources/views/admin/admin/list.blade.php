@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $header_title }} (Total: {{ $getRecord->total() }})</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ url('admin/admin/add') }}" class="btn btn-primary">Add New Admin</a>
                    <a href="{{ route('admin.showDeletedAdmins') }}" class="btn btn-secondary">View Deleted Admins</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- General form elements -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search Admin</h3>
                </div>
                <form method="get" action="">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label>
                                <input type="text" id="name" class="form-control" value="{{ Request::get('name') }}" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">Email</label>
                                <input type="text" id="email" class="form-control" value="{{ Request::get('email') }}" name="email" placeholder="Email">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date">Date</label>
                                <input type="date" id="date" class="form-control" value="{{ Request::get('date') }}" name="date" placeholder="Date">
                            </div>
                            <div class="form-group col-md-3 align-self-end">
                                <button class="btn btn-primary" type="submit">Search</button>
                                <a href="{{ url('admin/admin/list') }}" class="btn btn-success">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @include('_message')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin List</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created Date</th>
                                <th>Edited Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($getRecord as $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->email }}</td>
                                    <td>{{ date('d-m-Y H:i A', strtotime($value->created_at)) }}</td>
                                    <td>{{ date('d-m-Y H:i A', strtotime($value->updated_at)) }}</td>
                                    <td>
                                        <a href="{{ url('admin/admin/edit/' . $value->id) }}" class="btn btn-primary">Edit</a>
                                        <!-- Button to Open the Modal -->
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{$value->id}}">
                                            Delete
                                        </button>
                                        <!-- The Modal -->
                                        <div class="modal fade" id="deleteModal{{$value->id}}">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Delete User</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('admin.deleteUser', $value->id) }}" method="POST">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="reason">Reason for Deletion:</label>
                                                                <input type="text" class="form-control" id="reason" name="reason" required>
                                                            </div>
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="padding: 10px; float:right;">
                        {!! $getRecord->appends(Request::except('page'))->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const dateInput = document.getElementById('date');

        function toggleFields() {
            const nameHasValue = nameInput.value.trim() !== '';
            const emailHasValue = emailInput.value.trim() !== '';
            const dateHasValue = dateInput.value.trim() !== '';

            nameInput.disabled = emailHasValue || dateHasValue;
            emailInput.disabled = nameHasValue || dateHasValue;
            dateInput.disabled = nameHasValue || emailHasValue;
        }

        nameInput.addEventListener('input', toggleFields);
        emailInput.addEventListener('input', toggleFields);
        dateInput.addEventListener('input', toggleFields);

        // Set max date for date input
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);

        // Initial check to disable fields based on existing values
        toggleFields();
    });
</script>
@endsection

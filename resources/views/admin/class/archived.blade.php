@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $header_title }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ url('admin/class/list') }}" class="btn btn-secondary">View Active Classes</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- General form elements -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search Class</h3>
                </div>
                <form method="get" action="">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label>
                                <input type="text" id="name" class="form-control" value="{{ Request::get('name') }}" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date">Date</label>
                                <input type="date" id="date" class="form-control" value="{{ Request::get('date') }}" name="date" placeholder="Date">
                            </div>
                            <div class="form-group col-md-3 align-self-end">
                                <button class="btn btn-primary" type="submit">Search</button>
                                <a href="{{ url('admin/class/archived') }}" class="btn btn-success">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @include('_message')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Class List</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Deleted By</th>
                                <th>Reason to archived</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getRecord as $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value->name }}</td>
                                <td>
                                    @if($value->status == 0)
                                    <span class="text-success">Active</span>
                                    @else
                                    <span class="text-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ optional($value->deletedBy)->name }}</td>
                                <td><a href="#" class="reason" data-reason="{{ $value->reason }}">View</a></td>
                                <td>{{ date('d-m-Y H:i A', strtotime($value->created_at)) }}</td>
                                <td>
                                    <form action="{{ url('admin/class/restore/' . $value->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Restore</button>
                                    </form>
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
        const dateInput = document.getElementById('date');

        function toggleFields() {
            const nameHasValue = nameInput.value.trim() !== '';
            const dateHasValue = dateInput.value.trim() !== '';

            nameInput.disabled = dateHasValue;
            dateInput.disabled = nameHasValue;
        }

        nameInput.addEventListener('input', toggleFields);
        dateInput.addEventListener('input', toggleFields);

        // Initial check to disable fields based on existing values
        toggleFields();

        // Display reason when clicked
        const reasons = document.querySelectorAll('.reason');

        reasons.forEach(reason => {
            reason.addEventListener('click', function () {
                const reasonText = this.getAttribute('data-reason');
                alert(reasonText); // You can replace this with your modal or tooltip logic
            });
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('date');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);
    });
</script>
@endsection

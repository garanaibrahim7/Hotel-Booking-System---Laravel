{{-- @extends('layouts.adminlte')

@section('title', 'Dashboard')

@section('content')

    <input type="file" class="filepond" name="images[]" multiple>

@endsection

@push('styles')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            FilePond.create(
                document.querySelector('.filepond')
            );

        });
    </script>
@endpush --}}



@extends('layouts.adminlte')

@section('title', 'Dashboard')

@section('content')

    <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ $room->room_number }}">
        <input type="hidden" name="id" value="{{ $room->id }}">
        <button type="submit">Submit</button>
    </form>

    <input id="input-id" type="file" multiple>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-fileinput@5.5.0/css/fileinput.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            $("#input-id").fileinput();

        });
    </script>
@endpush

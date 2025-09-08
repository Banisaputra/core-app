{{-- resources/views/sql/index.blade.php --}}
@extends('layouts.sqlmaintenance')


@section('content')
<div class="container">
    <h3>SQL Query Runner</h3>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('sql.run') }}">
        @csrf
        <input type="hidden" name="key" value="{{ config('app.sql_runner_key') }}">
        <div class="form-group mb-2">
            <textarea name="query" class="form-control" rows="5">{{ old('query', session('query')) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Run Query</button>
    </form>

    @if(!empty(session('results')) && count(session('results')) > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    @foreach(array_keys((array) session('results')[0]) as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(session('results') as $row)
                    <tr>
                        @foreach((array) $row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning mt-3">
            Tidak ada data ditemukan.
        </div>
    @endif
</div>
@endsection

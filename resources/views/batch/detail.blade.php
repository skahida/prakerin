@extends('layouts._app')

@section('content')
    <div class="container">
        <h3>Daftar Detail Batch</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Mentor</th>
                    <th>Tempat Magang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>{{ $detail->batch->batch_name }}</td>
                        <td>{{ $detail->mentor->name }}</td>
                        <td>{{ $detail->internshipPlace->name }}</td>
                        <td>
                            <form action="{{ route('internship_batch_details.destroy', $detail->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

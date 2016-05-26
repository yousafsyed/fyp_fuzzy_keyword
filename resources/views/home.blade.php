@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      @include('sidebar')
          <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                <h3>List Of Files</h3>
                    {!! $files->links() !!}
                    <table class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr><th>Title</th><th>File Name</th><th>Uploaded at</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{$file->title}}</td>
                                    <td>{{$file->filename}}</td>
                                    <td>{{$file->created_at}}</td>
                                    <td><a href="#">Download</a> <a href="{{ url('/dashboard/deletefile?file_id='.$file->id) }}">Delete</a></td>
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

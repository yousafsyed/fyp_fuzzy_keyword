@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      @include('sidebar')
          <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">

                    <div class="right">
                    <form action="{{ url('/home') }}" method="get" >
                        <div class="input-group">
                          <input type="text" class="form-control" name="q" placeholder="Fuzzy keyword Search here...">
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Go!</button>
                            <a class="btn btn-default" href="{{ url('/home') }}">Clear</a>
                          </span>
                        </div>
                    </form>
                    </div>
                </div>

                <div class="panel-body">
                    @if(Session::has('message'))
                        @if(stristr(Session::get('message'),'Error:') !== false)
                        <div class="alert alert-danger text-center"  role="alert">
                            <p>
                                {{Session::get('message')}}
                            </p>
                        </div>
                        @endif
                        @if(stristr(Session::get('message'),'Success:') !== false)
                        <div class="alert alert-success text-center"  role="alert">
                            <p>
                               {{Session::get('message')}}
                            </p>
                        </div>
                        @endif
                    @endif
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
                                    <td><a href="{{ url('/download?file_id='.$file->id) }}">Download</a> <a href="{{ url('/dashboard/deletefile?file_id='.$file->id) }}">Delete</a></td>
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

@extends('/layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @include('/sidebar')
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                <h3>Add New File</h3>
                <p>Upload you file. We can handle any file format up to 2GB.</p>
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
                    <form action="{{ url('/dashboard/savefile') }}" method="post" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                                <label for="file" > Upload File </label>
                                <input type="file" name="file" id="file" multiple>
                                @if ($errors->has('file'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('file') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title"> File Title </label>
                                <input class="form-control" type="text" id="title" name="title" value ="{{old('title')}}" placeholder="File Title">
                                 @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('tags') ? ' has-error' : '' }}">
                                <label for="tags" >Add Tags/Keywords</label>
                                <input class="form-control" type="text" id ="tags" name="tags" placeholder="tags must be seperated and with space eg.(your file name)" value ="{{old('tags')}}">
                                 @if ($errors->has('tags'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tags') }}</strong>
                                    </span>
                                @endif
                            </div>
                             
                            <button type="submit" class="btn btn-primary pull-right" id="">Upload File</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

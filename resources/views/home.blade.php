@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      @include('sidebar')
          <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

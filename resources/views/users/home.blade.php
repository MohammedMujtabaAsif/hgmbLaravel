@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Home</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p> Hello {{Auth::user()->prefName}}, please use the mobile application to access the service. <br/>
                    Click the link below to take you to our App Store page</p>
                    <a href="#" > Android App </a>
                    <br/>
                    <a href="#" > iOS App </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin-app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    @if (session('status.message'))
                        <div class="alert alert-{{session('status.level')}}" role="alert">
                            {{ session('status.message') }}
                        </div>
                    @endif

                    <div id="users-list">
                        <h3 class="tableTitle">{{$title}}</h3>
                        @if ( count($users) == 0 )

                            <p> No {{ Request::segment(2) }} users </p>

                        @else
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="20%"scope="col">First Name</th>
                                        <th width="20%" scope="col">Surname</th>
                                        <th width="30%" scope="col">Email Address</th>
                                        <th width="15%" scope="col">Actions</th>
                                    </tr>
                                </thread>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr> 
                                            <td>{{$user->firstNames}}</td>
                                            <td>{{$user->surname}}</td>
                                            <td>{{$user->email}}</td>
                                            <td><a type="button" class="btn btn-link" href="{{route('admin.user', ['id' => $user->id])}}" role="button">View Profile</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$users->links()}}
                            @endif

                    </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/switchVisibility.js') }}"></script>
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@stop
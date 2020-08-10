@extends('layouts.head')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{$userProf->prefName}}'s Profile</div>

                <div class="card-body">
                    @if (session('status.message'))
                        <div class="alert alert-{{session('status.level')}}" role="alert">
                            {{ session('status.message') }}
                        </div>
                    @endif

                        
                        @if(is_null($userProf))        

                            <div class="alert alert-danger">
                                <strong>User Not Found</strong>
                            </div>

                            <div>
                                <button class="btn btn-primary" onClick="goBack()">Go Back</button>
                            </div>

                            <script>
                                function goBack() {
                                    window.history.back();
                                }
                            </script>

                        @else

                            <img class="centerImage" src="{{asset('images/no-profile-image.png')}}" alt="User {{$userProf->id}} has no image">


                            @if($userProf->adminBanned == '1')
                                <span class="badge badge-danger centerIcon">Banned</span>
                            @elseif($userProf->adminApproved == '1')
                                <span class="badge badge-success centerIcon">Approved</span>
                            @elseif($userProf->deactivated == '1')
                                <span class="badge badge-secondary centerIcon">Deactivated</span>
                            @else
                                <span class="badge badge-warning centerIcon">Unapproved</span>
                            @endif


                            <div class="list-group">                            
                                @if(!empty($userProf->adminBannedMessage))
                                    <a class="list-group-item list-group-item-danger flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><strong>Ban Reason</strong></h5>
                                        </div>
                                        <p class="mb-1">{{$userProf->adminBannedMessage}}</p>
                                    </a>
                                @endif
                            </div>

                            <div class="list-group">
                                @if(!empty($userProf->adminUnapprovedMessage))
                                    <a class="list-group-item list-group-item-warning flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><strong>Unapproval Reason</strong></h5>
                                        </div>
                                        <p class="mb-1">{{$userProf->adminUnapprovedMessage}}</p>
                                    </a>
                                @endif
                            </div>

                            <div class = "list-group">
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <h4 class="mb-1"><strong>User Details</strong></h4>
                                </a>
                            
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Full Name</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->firstNames}} {{$userProf->surname}}</p>
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Preferred Name</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->prefName}}</p>
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Email Address</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->email}}</p>
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Phone Number</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->phoneNumber}}</p>
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Gender</strong></h5>
                                    </div>
                                    @foreach($userProf->gender as $gender)
                                    <p class="mb-1">{{$gender['name']}}</p>
                                    @endforeach
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>City</strong></h5>
                                    </div>
                                    @foreach($userProf->city as $city)
                                    <p class="mb-1">{{$city['name']}}</p>
                                    @endforeach
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Marital Status</strong></h5>
                                    </div>
                                    @foreach($userProf->maritalStatus as $maritalStatus)
                                    <p class="mb-1">{{$maritalStatus['name']}}</p>
                                    @endforeach
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Age</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->age}}</p>
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Number of Children</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->numOfChildren}}</p>
                                </a>
                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Biography</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->bio}}</p>
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong></strong></h5>
                                    </div>
                                    <p class="mb-1"></p>
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <h4 class="mb-1"><strong>Preferred Partner Details</strong></h4>
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Cities</strong></h5>
                                    </div>
                                    @foreach($userProf->prefCities as $city)
                                    <p class="mb-1">{{$city->name}}</p>

                                    @endforeach
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Gender</strong></h5>
                                    </div>
                                    @foreach($userProf->prefGenders as $gender)
                                    <p class="mb-1">{{$gender->name}}</p>

                                    @endforeach
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Marital Statuses</strong></h5>
                                    </div>
                                    @foreach($userProf->prefMaritalStatuses as $ms)
                                    <p class="mb-1">{{$ms->name}}</p>

                                    @endforeach
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Number of Children</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->prefMaxNumOfChildren}}</p>
                                </a>

                                <a class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><strong>Age Range</strong></h5>
                                    </div>
                                    <p class="mb-1">{{$userProf->prefMinAge}}   -  {{$userProf->prefMaxAge}}</p>
                                </a>

                            </div>

                        @endif

                    <div class="container">
                        @yield('buttons')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/toggleVisibility.js') }}"></script>
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
@stop
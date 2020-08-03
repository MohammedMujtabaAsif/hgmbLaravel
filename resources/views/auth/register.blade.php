@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Form secition for user's details -->
                        <div class="heading">
                            <center><h5>Your Details:</h5></center>
                        </div>


                        <div class="form-group row">
                            <label for="firstNames" class="col-md-4 col-form-label text-md-right">{{ __('First Name(s):') }}</label>

                            <div class="col-md-6">
                                <input id="firstNames" type="text" class="form-control @error('firstNames') is-invalid @enderror" name="firstNames" value="{{ old('firstNames') }}" required autocomplete="given-name">

                                @error('firstNames')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="surname" class="col-md-4 col-form-label text-md-right">{{ __('Surname:') }}</label>

                            <div class="col-md-6">
                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="family-name">

                                @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="prefName" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Name:') }}</label>

                            <div class="col-md-6">
                                <input id="prefName" type="text" class="form-control @error('prefName') is-invalid @enderror" name="prefName" value="{{ old('prefName') }}" required autocomplete="nickname">

                                @error('prefName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address:') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password:') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password:') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phoneNumber" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Phone Number:') }}</label>

                            <div class="col-md-6">
                                <input id="phoneNumber" type="tel" maxlength=11 class="form-control @error('phoneNumber') is-invalid @enderror" name="phoneNumber" value="{{ old('phoneNumber') }}" required autocomplete="tel">

                                @error('phoneNumber')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>   

                        <div class="form-group row" style="display:flex; flex-direction: row; align-items: center">
                            <label for="city_id" class="col-md-4 col-form-label text-md-right">{{ __('City:') }}</label>

                            <div class="col-md-6">

                                <select name="city_id" id="city_id" value="{{ old('city_id') }}" required autocomplete="city">
                                    <option value="">Select your City</option>
                                    <option value=1>Birmingham</option>
                                    <option value=2>London</option>
                                    <option value=3>Manchester</option>
                                </select> 

                            </div>
                        </div>              
                        
                        <div class="form-group row" style="display:flex; flex-direction: row; align-items: center">
                            <label for="gender_id" class="col-md-4 col-form-label text-md-right">{{ __('Gender:') }}</label>

                            <div class="col-md-6">

                                <select name="gender_id" id="gender_id" value="{{ old('gender_id') }}" required autocomplete="gender">
                                    <option value="">Select your Gender</option>
                                    <option value=1>Female</option>
                                    <option value=2>Male</option>
                                </select> 

                            </div>
                        </div>

                        <div class="form-group row" style="display:flex; flex-direction: row; align-items: center">
                            <label for="marital_status_id" class="col-md-4 col-form-label text-md-right">{{ __('Marital Status:') }}</label>

                            <div class="col-md-6">

                                <select name="marital_status_id" id="marital_status_id" value="{{ old('marital_status_id') }}" required>
                                    <option value="">Select your Marital Status</option>
                                    <option value=1>Single</option>
                                    <option value=2>Divorced</option>
                                    <option value=3>Widowed</option>
                                </select> 

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="dob" class="col-md-4 col-form-label text-md-right">{{ __("Date of Birth:") }}</label>

                            <div class="col-md-6">

                                <!-- Automatically calculate the date values for the DOB selector -->
                                <?php                            
                                function getNewDate($time){
                                    echo date('Y-m-d', strtotime("-$time"));
                                }
                                ?>

                                <input type="date" id="dob" name="dob" value="<?php getNewDate('18 years 1 day')?>" min="<?php getNewDate('68 years')?>" max="<?php getNewDate('18 years')?>" required autocomplete="bday">

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="numOfChildren" class="col-md-4 col-form-label text-md-right">{{ __('Number of Children:') }}</label>

                            <div class="col-md-3">
                                <input id="numOfChildren" type="number" min="0" max="20" class="form-control @error('numOfChildren') is-invalid @enderror" name="numOfChildren" value="{{ old('numOfChildren') }}" required>

                                @error('numOfChildren')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="bio" class="col-md-4 col-form-label text-md-right">{{ __('Biography (1000 chars max):') }}</label>

                            <div class="col-md-6">
                                <textarea rows="4" cols="50" maxlength=500 placeholder="Briefly Describe Yourself" id="bio" class="form-control @error('bio') is-invalid @enderror" name="bio" value="{{ old('bio') }}" required>
                                </textarea>

                                @error('bio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Page Splitter -->
                        <hr style="height:2px;border-width:0;color:gray;background-color:gray"></hr>
                        

                        <!-- Form secition for user's partner preferences -->
                        <div class="heading">
                            <center><h5>Preferred Partner's Details:</h5></center>
                        </div>

                        <div class="form-group row">
                            <label for="prefCities" class="col-md-4 col-form-label text-md-right">{{ __("Preferred Cities:") }}</label>

                            <div class="col-md-6">

                                <input type="checkbox" name="prefCities[]" id="birmingham" value=1 /> Birmingham
                                <br>
                                <input type="checkbox" name="prefCities[]" id="london" value=2 /> London
                                <br>
                                <input type="checkbox" name="prefCities[]" id="manchester" value=3 /> Manchester

                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="prefMaritalStatuses" class="col-md-4 col-form-label text-md-right">{{ __("Preferred Marital Statuses:") }}</label>

                            <div class="col-md-6">

                                <input type="checkbox" name="prefMaritalStatuses[]" id="single" value=1 /> Single
                                <br>
                                <input type="checkbox" name="prefMaritalStatuses[]" id="divorced" value=2 /> Divorced
                                <br>
                                <input type="checkbox" name="prefMaritalStatuses[]" id="widowed" value=3 /> Widowed

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="prefMinAge" class="col-md-4 col-form-label text-md-right">{{ __('Minimum Age:') }}</label>

                            <div class="col-md-3">
                                <input id="prefMinAge" type="number" min="18" max="40" step="1" class="form-control @error('PrefMinAge') is-invalid @enderror" name="prefMinAge" value="{{ old('prefMinAge') }}" required>

                                @error('prefMinAge')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="prefMaxAge" class="col-md-4 col-form-label text-md-right">{{ __('Maximum Age:') }}</label>

                            <div class="col-md-3">
                                <input id="prefMaxAge" type="number" min="20" max="50" class="form-control @error('prefMaxAge') is-invalid @enderror" name="prefMaxAge" value="{{ old('prefMaxAge') }}" required>

                                @error('prefMaxAge')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        
                        <div class="form-group row">
                            <label for="prefMaxNumOfChildren" class="col-md-4 col-form-label text-md-right">{{ __('Preferred Max Number of Children:') }}</label>

                            <div class="col-md-3">
                                <input id="prefMaxNumOfChildren" type="number" min="0" max="20" class="form-control @error('prefMaxNumOfChildren') is-invalid @enderror" name="prefMaxNumOfChildren" value="{{ old('prefMaxNumOfChildren') }}" required>

                                @error('prefMaxNumOfChildren')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

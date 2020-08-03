@extends('layouts.app')
@extends('layouts.profile')

@section('buttons')

    <!-- <div style="display:inline-block">
        <form method="POST" action="{{ route('user.deactivateAccount', ['id' => $userProf->id]) }}">
            @csrf
            <div style="display:inline-block">
                <button class="btn btn-warning" onClick="return confirm('Confirm Deactivation');" type="submit">Deactivate</button>
            </div>
        </form>
    </div> -->
        
    <div style="display:inline-block">
        <form method="POST" action="{{ route('user.deleteAccount', ['id' => $userProf->id]) }}">
            @csrf
            <div style="display:inline-block">
                <button class="btn btn-danger" onClick="return confirm('Confirm Deletion');" type="submit">Delete</button>
            </div>
        </form>
    </div>

@endsection
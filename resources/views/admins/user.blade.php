@extends('layouts.admin-app')
@extends('layouts.profile')

@section('buttons')
    @if($userProf->adminBanned == '1')

        <form method="POST" action="{{ route('admin.unbanUser', ['id' => $userProf->id]) }}">
            @csrf
            <div style="display:inline-block">
                <button class="btn btn-success"  onClick="return confirm('Confirm Unban');" type="submit">Unban</button>
            </div>
        </form>
        
        <div style="display:inline-block">
            <form method="POST" action="{{ route('admin.deleteUser', ['id' => $userProf->id]) }}">
                @csrf
                <div style="display:inline-block">
                    <button class="btn btn-danger" onClick="return confirm('Confirm Account Deletion');" type="submit">Delete</button>
                </div>
            </form>
        </div>


    @elseif($userProf->adminApproved == '1')

        <div style="display:inline-block">
            <button class="btn btn-warning" onClick="toggleVisibility(['unapproval-message-box'], ['ban-message-box'])" type="submit">Unapprove</button>
        </div>


        <div style="display:inline-block">
            <button class="btn btn-danger" onClick="toggleVisibility(['ban-message-box'], ['unapproval-message-box'])" type="submit">Ban</button>
        </div>

        <div style="display:inline-block">
            <form method="POST" action="{{ route('admin.deleteUser', ['id' => $userProf->id]) }}">
                @csrf
                <div style="display:inline-block">
                    <button class="btn btn-danger" onClick="return confirm('Confirm Deletion');" type="submit">Delete</button>
                </div>
            </form>
        </div>

        <div style = "display:block">
            <div style="display:inline-block">
                <form input="textarea" method="POST" action="{{ route('admin.unapproveUser', ['id' => $userProf->id]) }}">
                    @csrf

                    <div class="form-group" id="unapproval-message-box" style = "display:none">
                        <label for="bio" class=" col-form-label text-md-right">{{ __('Unapproval Explanation (255 chars max):') }}</label>

                        <div>
                            <textarea rows="4" cols="50" maxlength=500 placeholder="Explain Why User is being Unapproved" id="adminUnapprovedMessage" class="form-control" name="adminUnapprovedMessage" required>
                            </textarea>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" onClick="return confirm('Confirm Unapproval Message');" class="btn btn-primary">
                                    {{ __('Unapprove User') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div style="display:inline-block">
                <form input="textarea" method="POST" action="{{ route('admin.banUser', ['id' => $userProf->id]) }}">
                    @csrf

                    <div class="form-group" id="ban-message-box" style = "display:none">
                        <label for="bio" class=" col-form-label text-md-right">{{ __('Ban Explanation (255 chars max):') }}</label>

                        <div>
                            <textarea rows="4" cols="50" maxlength=500 placeholder="Explain Why User Has Been Banned" id="adminBannedMessage" class="form-control" name="adminBannedMessage" required>
                            </textarea>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" onClick="return confirm('Confirm Ban');" class="btn btn-primary">
                                    {{ __('Ban User') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @elseif($userProf->deactivated == '1')
        <div style="display:inline-block">
            <form method="POST" action="{{ route('admin.deleteUser', ['id' => $userProf->id]) }}">
                @csrf
                <div style="display:inline-block">
                    <button class="btn btn-danger" onClick="return confirm('Confirm Deletion');" type="submit">Delete</button>
                </div>
            </form>
        </div>
        
    @else

        <form method="POST" action="{{ route('admin.approveUser', ['id' => $userProf->id]) }}">
            @csrf
            <div style="display:inline-block">
                <button class="btn btn-success"  onClick="return confirm('Confirm Approval');" type="submit">Approve</button>
            </div>                           
        </form>

        <div style="display:inline-block">
            <button class="btn btn-warning" onClick="toggleVisibility(['unapproval-message-box'], ['ban-message-box'])" type="submit">Message</button>
        </div>


        <div style="display:inline-block">
            <button class="btn btn-danger" onClick="toggleVisibility(['ban-message-box'], ['unapproval-message-box'])" type="submit">Ban</button>
        </div>


        <div style="display:inline-block">
            <form method="POST" action="{{ route('admin.deleteUser', ['id' => $userProf->id]) }}">
                @csrf
                <div style="display:inline-block">
                    <button class="btn btn-danger" onClick="return confirm('Confirm Deletion');" type="submit">Delete</button>
                </div>
            </form>
        </div>

        <div style="display:block">

            <div style="display:inline-block">
                <form input="textarea" method="POST" action="{{ route('admin.unapproveUser', ['id' => $userProf->id]) }}">
                    @csrf

                    <div class="form-group" id="unapproval-message-box" style = "display:none">
                        <label for="bio" class=" col-form-label text-md-right">{{ __('Unapproval Explanation (255 chars max):') }}</label>

                        <div>
                            <textarea rows="4" cols="50" maxlength=500 placeholder="Explain Why User Cannot be Approved" id="adminUnapprovedMessage" class="form-control" name="adminUnapprovedMessage" required>
                            </textarea>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" onClick="return confirm('Confirm Unapproval Message');" class="btn btn-primary">
                                    {{ __('Message User') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div style="display:inline-block">
                <form input="textarea" method="POST" action="{{ route('admin.banUser', ['id' => $userProf->id]) }}">
                    @csrf

                    <div class="form-group" id="ban-message-box" style = "display:none">
                        <label for="bio" class=" col-form-label text-md-right">{{ __('Ban Explanation (255 chars max):') }}</label>

                        <div>
                            <textarea rows="4" cols="50" maxlength=500 placeholder="Explain Why User Has Been Banned" id="adminBannedMessage" class="form-control" name="adminBannedMessage" required>
                            </textarea>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" onClick="return confirm('Confirm Ban');" class="btn btn-primary">
                                    {{ __('Ban User') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection


@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/toggleVisibility.js') }}"></script>
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
@stop
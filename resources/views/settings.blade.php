@extends('layouts.admin')

@section('content')
<div class="container image-manager">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>Settings</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
		        	<form method="post" action="{{ route('admin.settings.update') }}" id="settings_form" class="" enctype="multipart/form-data" autocomplete="off">
                        <input autocomplete="false" name="hidden" type="text" style="display:none;">
                        @csrf
                        
                        <div class="form-group row">
                            <label for="exampleFormControl1" class="col-md-4 col-form-label text-md-right">Email 1</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="cc_email1" id="exampleFormControl1" value="{{ $user->cc_email1 }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="exampleFormControl2" class="col-md-4 col-form-label text-md-right">Email 2</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="cc_email2" id="exampleFormControl2" value="{{ $user->cc_email2 }}">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('admin.home') }}"><button type="button" class="btn btn-dark">Back</button></a>
                                <button type="submit" class="btn btn-dark">Save</button>
                            </div>
                        </div>
                    </form>

				</div>
			</div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection
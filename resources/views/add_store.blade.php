@extends('layouts.admin')

@section('content')
<div class="container image-manager">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>Add new store & user</strong></div>
                <div class="card-body">
		        	<form method="post" action="{{ route('admin.shop.add.post') }}" id="store_create_form" class="" enctype="multipart/form-data" autocomplete="off">
					  	<input autocomplete="false" name="hidden" type="text" style="display:none;">
					  	@csrf
					  	<div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

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
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

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
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

						<div class="form-group row">
							<label for="exampleFormControl1" class="col-md-4 col-form-label text-md-right">Shopify domain</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="shopify_domain" id="exampleFormControl1" placeholder="spar.myshopify.com" required="required">
							</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControl2" class="col-md-4 col-form-label text-md-right">Shopify API KEY</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="shopify_api_key" id="exampleFormControl2" required="required">
							</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControl3" class="col-md-4 col-form-label text-md-right">Shopify API Password</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="shopify_api_password" id="exampleFormControl3" required="required">
							</div>
						</div>
                        <div class="form-group row">
                            <label for="exampleFormControl4" class="col-md-4 col-form-label text-md-right">Shopify Shared Secret</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="shopify_shared_secret" id="exampleFormControl4" required="required">
                            </div>
                        </div>
						<div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('admin.home') }}"><button type="button" class="btn btn-dark">Back</button></a>
                                <button type="submit" class="btn btn-dark">Add</button>
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
@extends('layouts.admin')

@section('content')
<div class="container image-manager">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>Edit store</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

		        	<form method="post" action="{{ route('admin.shop.update',$shop->id) }}" id="store_update_form_{{$shop->id}}" class="" enctype="multipart/form-data" autocomplete="off">
					  	<input autocomplete="false" name="hidden" type="text" style="display:none;">
					  	@csrf
                        
					  	<div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $shop->name }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

						<div class="form-group row">
							<label for="exampleFormControl1" class="col-md-4 col-form-label text-md-right">Shopify domain</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="shopify_domain" id="exampleFormControl1" value="{{ $shop->shopify_domain }}" required="required">
							</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControl2" class="col-md-4 col-form-label text-md-right">Shopify API KEY</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="shopify_api_key" id="exampleFormControl2" value="{{ $shop->api_key }}" required="required">
							</div>
						</div>
						<div class="form-group row">
							<label for="exampleFormControl3" class="col-md-4 col-form-label text-md-right">Shopify API Password</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="shopify_api_password" id="exampleFormControl3" value="{{ $shop->api_password }}" required="required">
							</div>
						</div>
                        <div class="form-group row">
                            <label for="exampleFormControl4" class="col-md-4 col-form-label text-md-right">Shopify Shared Secret</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="shopify_shared_secret" id="exampleFormControl4" value="{{ $shop->shared_secret }}" required="required">
                            </div>
                        </div>
						<div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('admin.home') }}"><button type="button" class="btn btn-dark">Back</button></a>
                                <button type="submit" class="btn btn-dark">Update</button>
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
@extends('layouts.admin')

@section('content')
<div class="container image-manager">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4><strong>Welcome to admin dashboard</strong></h4>
            <div class="card">
                <div class="card-header">
                    <strong>Stores</strong>
                    <a href="{{ route('admin.shop.add') }}" class="notify-me"><button class="btn btn-dark" type="button">Add New Store</button></a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Store domain</th>
                              <th scope="col">Name</th>
                              <th scope="col">Email</th>
                              <th scope="col">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($shops as $key => $shop)
                            <tr>
                                <th scope="row">{{ ++$key }}</th>
                                <td>{{ $shop->shopify_domain }}</td>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->email }}</td>
                                <td>
                                    <ul class="actions">
                                        <li><a href="{{ route('admin.shop.view',$shop->id)}}"><button class="btn btn-dark" type="submit">View</button></a></li> 
                                        <li><a href="{{ route('admin.shop.edit',$shop->id)}}"><button class="btn btn-dark" type="submit">Edit</button></a></li> 
                                        <li><form action="{{ route('admin.shop.delete', $shop->id)}}" method="post">  
                                          @csrf  
                                          @method('DELETE')  
                                          <button class="btn btn-dark" type="submit">Delete</button>  
                                        </form></li>  
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent


@endsection
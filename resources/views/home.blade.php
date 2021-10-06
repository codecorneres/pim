@extends('layouts.app')

@section('content')
<div class="container image-manager">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Products</strong>
                        </div>
                        @if(count($products) > 0)
                        <div class="col-md-3">
                            <div class="form-group">
                              <select class="form-control" data-csrf="{{ csrf_token() }}" id="filter">
                                <option value="">-Select Vendor Type-</option>
                                <?php foreach ($filterVendors as $vendor) { ?>
                                    <option value="{{ $vendor->vendor_type }}" {{ $vendorType == $vendor->vendor_type ? 'selected' : '' }}>{{ $vendor->vendor_type }}</option>
                                <?php }?>
                              </select>
                            </div>
                        </div>
						
						<div class="col-md-3">
							<a href="javascript:void(0)"><button class="btn btn-dark bclick" type="button">Bulk Import</button></a>
							<a href="{{ url('/notify') }}" class="notify-me"><button class="btn btn-dark" type="button">Notify me</button></a>
						</div>
						@endif
                	</div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <form style="display: none;" class="row mb-3 impBulk" method="POST" action="{{ route('bulk_import') }}" enctype="multipart/form-data">
                    @csrf
                        <div class="col-md-8">
                            <div class="form-group">
                              <label><strong>Upload CSV file</strong></label>
                              <input required type="file" class="form-control" name="file" value="" accept=".csv">
                              <input type="hidden" class="form-control" value="{{ $shop->id }}" name="shop_id">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn bbtn btn-primary">Import</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Product ID</th>
                            <th scope="col">Admin URL</th>
                            <th scope="col">Frontend URL</th>
                            <th scope="col" class="check-image"><input type="checkbox" id="has_product_img" name="has_product_img" value="yes" /> <label for="has_product_img">Has Image ?</label></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($products as $key => $product)
                          <tr>
                            <th scope="row">{{ ++$key }}</th>
                            <td style="width: 50%;">{{ $product->title }}</td>
                            <td>{{ $product->product_id }}</td>
                            <td><a href="javascript:void(0)" onclick="window.open('{{ 'https://'.$shop->shopify_domain.'/admin/products/'.$product->product_id }}');" >Update</a></td>
                            <td><a href="javascript:void(0)" onclick="window.open('{{ 'https://'.$shop->shopify_domain.'/products/'.$product->handle }}');" >View</a></td>
                            <td>{{ $product->image ? 'yes' : 'no' }}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
                @if(count($products) > 0)
                <div class="card-footer">
                    <div class="pagination"> 
                      {{ $products->links() }}
                    </div>
                </div>
                @endif  
            </div>
        </div>
    </div>
    <div class="loading" style="display: none;">Loading&#8230;</div>
</div>

@endsection

@section('scripts')
@parent
<script type="text/javascript">
$(document).ready(function() {
    if(window.location.href.indexOf("with-image") > -1) {
        $( "#has_product_img").prop('checked', true);
    }else if(window.location.href.indexOf("without-image") > -1) {
        $( "#has_product_img").prop('checked', false);
        $( "#has_product_img").next('label').text('No image');
    }
    $('#has_product_img').change(function() {
        if(this.checked) {
            var src = "{{ route('products_withImage') }}";
        }else{
            var src = "{{ route('products_withoutImage') }}";
        }
        window.location.href = src;
            // $.ajax({
            //     url: src,
            //     dataType: "json",
            //     success: function(result) {
            //         console.log(result);
            //         if(result.status == 'success'){
            //             if(result.redirect == 'reload'){
            //                 window.location.href = src;
            //             }
            //         }
            //     }
            // });
        // }      
    });

    $('#filter').on('change',function(e){
        e.preventDefault();

        jQuery.ajax({
            url: "/product-img-manager/public/filter",
            method: 'post',
            data: {
                '_token' : $(this).data('csrf') ,
                'vendor_type' : $(this).val(),
            },
            success: function(res){
                window.location.reload();
            }});
    });

    $('.bclick').click(function(){
        $('.impBulk').fadeToggle();
    });
});
/*
window.addEventListener('load', (event) => {
  $('.loading').css('display', 'block');
});*/
</script>

@endsection

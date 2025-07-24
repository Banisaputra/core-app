@extends('layouts.main')

@section('page_css')
    <style>
    .product-card {
      cursor: pointer;
    }
    .cart-table td, .cart-table th {
      vertical-align: middle;
    }
    /* Scrollable product list */
    
    #productList {
      max-height: 80vh;
      overflow-y: auto;
    }
    /* Custom scrollbar styling */
    #productList::-webkit-scrollbar {
    width: 8px; /* Width of the scrollbar */
    }

    #productList::-webkit-scrollbar-track {
    background: #5c5c5ce1; /* Color of the track */
    border-radius: 10px;
    }

    #productList::-webkit-scrollbar-thumb {
    background: #979797; /* Color of the scroll thumb */
    border-radius: 10px;
    }

    #productList::-webkit-scrollbar-thumb:hover {
    background: #d4d4d4; /* Color on hover */
    }
       
    .cart-table {
        display: block;
    }

    .cart-table thead,
    .cart-table tbody {
        display: block;
        width: 100%;
    }

    .cart-table tbody {
        height: 60vh; 
        max-height: 60vh;
        overflow-y: auto;
    }

    .cart-table thead tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .cart-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
{{-- cash modal --}}
<div class="modal fade" id="cashModal" tabindex="-1" role="dialog" aria-labelledby="cashModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="cashModalTitle">Cash Payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <h4 id="memberName"></h4>
            <h3><strong>Total:</strong> <span id="totalAmount">0</span>,-</h3>
            <div class="mb-3">
                <label for="cashReceived" class="form-label">Cash Received</label>
                <input type="number" class="form-control" id="cashReceived" min="0">
            </div>
            <div>
                <h5><strong>Change:</strong> <span id="cashChange">0</span>,-</h5>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="cashPayment" class="btn mb-2 btn-primary">Payment</button>
        </div>
        </div>
    </div>
</div>
{{-- credit modal --}} 
<div class="modal fade" id="creditModal" tabindex="-1" role="dialog" aria-labelledby="creditModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="creditModalTitle">Credit Payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">x</span>
            </button>
        </div>
        <div class="modal-body">
            <form>
                <div class="form-group row">
                    <label for="crMember" class="col-sm-3 col-form-label">Nama</label>
                    <div class="col-sm-9">
                        <h3><span id="crMember"></span></h3>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="crTotal" class="col-sm-3 col-form-label">Total</label>
                    <div class="col-sm-9">
                        <h3 id="crTotal"></h3>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="crTenor" class="col-sm-3 col-form-label">Tenor</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" id="crTenor">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="crTotal" class="col-sm-3 col-form-label">Angsuran</label>
                    <div class="col-sm-9">
                        <h3 id="crInterest"></h3>
                    </div>
                </div>
                <hr class="my-2">
                <small>*Kredit akan tercatat sebagain pinjaman anggota</small>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="creditPayment" class="btn mb-2 btn-primary">Payment</button>
        </div>
        </div>
    </div>
</div>
     
<div class="container-fluid py-3">
  <div class="row">
    <!-- Product List -->
    <div class="col-md-8">
        <h4>Products</h4>
        <input type="text" class="form-control" id="item_search" name="item_search" placeholder="Search item...">
        <hr class="my-4">
        <div id="productLoading" style="display: none; text-align:center; padding: 20px;">
            <div class="spinner-grow mr-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
      <div class="row" id="productList">

      </div>
    </div>

    <!-- Cart -->
    <div class="col-md-4">
        <h4>Cart Pelanggan</h4>
        <select id="memberSelect" name="member_id" class="form-control"></select>

      <table class="table cart-table table-bordered">
        <thead>
          <tr>
            <th>Barang</th>
            <th width="20%">Qty</th>
            <th width="25%">Price</th>
            <th width="10%"></th>
          </tr>
        </thead>
        <tbody id="cartBody"></tbody>
      </table>
      <h2>Total: <span id="total">0</span>,-</h2>
      <hr class="my-2">
      <button type="button" id="cashBtn" class="btn mb-2 btn-success btn-block"> C A S H </button>
      <hr class="my-2">
      <button type="button" id="creditBtn" class="btn mb-2 btn-outline-warning btn-block"> KREDIT </button>
    </div>
  </div>
</div>
 

@endsection

@section('page_script')
<script src="{{ asset('fedash/js/pos.js')}}"></script>

<script>
    loadProducts();
    $(document).ready(function () {
        $('#memberSelect').select2({
            placeholder: 'Search anggota...',
            theme: 'bootstrap4',
            minimumInputLength: 2,
            ajax: {
                url: '/api/members/search', // Your route
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                cache: true
            }
        }).on('change', function(e) {
            // Get the full selected data
            const selectedData = $(this).select2('data');
            if (selectedData.length > 0) {
                cust_name = selectedData[0].text
            }
        });

        $("body").bind('paste', function(e) {
            console.log("PASTE");
            $("#item_search").focus();
            console.log("PASTE. Sending input. Value before clearing input : " + $('#item_search').val());
        });

    });
</script>
  
@endsection
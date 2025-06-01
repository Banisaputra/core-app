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
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">Mauris lobortis efficitur ligula, et consectetur lectus maximus sed. 

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
      <button type="button" id="cashBtn"class="btn mb-2 btn-success btn-block" data-toggle="modal" data-target="#cashModal"> C A S H </button>
      <hr class="my-2">
      <button type="button" id="creditBtn"class="btn mb-2 btn-outline-warning btn-block" data-toggle="modal" data-target="#creditModal"> KREDIT </button>
    </div>
  </div>
</div>
 

@endsection

@section('page_script')
<script>
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
            const selectedData = $(this).select2('data')[0];
            cust_name = selectedData.text;
        });


    });
</script>
<script>
  const productList = document.getElementById("productList");
  const cartBody = document.getElementById("cartBody");
  const totalEl = document.getElementById("total");
  const searchBox = document.getElementById("item_search");
  const cashBtn = document.getElementById('cashBtn');
  const cashPayment = document.getElementById('cashPayment');
  const cashReceive = document.getElementById('cashReceived');
  const cart = {};
  var cust_name = "";

  function formatIDR(value, decimal) {
    return value.toLocaleString('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: decimal
    });
  }



cashBtn.addEventListener('click', function () {
    // Set total in modal
    const total = parseFloat(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
    document.getElementById('memberName').textContent = cust_name;
    document.getElementById('totalAmount').textContent = formatIDR(total,0);
    cashReceive.value = '';
    document.getElementById('cashChange').textContent = '0';
    const memberId = document.getElementById('memberSelect').value *1;
    // return, modal hide

});

cashReceive.addEventListener('input', function () {
    const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/[^\d]/g, ''));
    const received = parseFloat(this.value) || 0;
    const change = received - total;
    document.getElementById('cashChange').textContent = formatIDR(change,0);
});

// save sales
cashPayment.addEventListener('click', function () {
  const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/[^\d]/g, ''));
  const received = parseFloat(cashReceive.value);
  const memberId = document.getElementById('memberSelect').value *1;
    if (isNaN(received) || received < total) {
        alert('Nominal Tidak Sesui!');
        return;
    }
    if(memberId == 0){
        alert('Pelanggan Harus Dipilih!');
        return;
    }
  
    // Collect cart data
    var cartItems = [];
    cartItems = Object.entries(cart).map((item) => {
        const price = parseFloat(item[1].price ?? 0);
        const qty = parseInt(item[1].qty ?? 1);

        return {
            id: item[0],
            name: item[1].name,
            price: !isNaN(price) ? Math.max(0, price) : 0,
            qty: !isNaN(qty) ? Math.max(1, qty) : 1,
            subtotal: function() { return this.price * this.qty }
        };
    });

  // Send to backend
  fetch('/submit-sale', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content // if Laravel
    },
    body: JSON.stringify({
        member_id: memberId,
        items: cartItems,
        total: total,
        received: received,
        change: received - total,
        payment_type: 'CASH'
    })
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
        $('#cashModal').modal('hide');
      alert('Payment successful!\nChange:' + formatIDR(received - total, 0));
      
      // Clear cart
      document.getElementById('cartBody').innerHTML = '';
      document.getElementById('total').textContent = '0';

      // Optional: print receipt, show invoice, etc.
    } else {
      alert('Failed to submit sale.');
    }
  })
  .catch(error => {
    console.error(error);
    alert('Error submitting sale.w');
  });
});


// Load products from backend with optional search
async function loadProducts(query = '') {
    // Show loading
    document.getElementById('productLoading').style.display = 'block';
    try {
        const res = await fetch(`/api/items/search?q=${encodeURIComponent(query)}`);
        const data = await res.json();
        // Hide loading
        document.getElementById('productLoading').style.display = 'none';
        renderProducts(data);
    } catch (err) {
        console.error("Failed to load products", err);
    }
}

// Render products to the productList
function renderProducts(products) {
    productList.innerHTML = '';
    if (products.length === 0) {
        productList.innerHTML = '<p class="text-muted">No products found.</p>';
        return;
    }

    products.forEach(product => {
        const col = document.createElement("div");
        col.className = "col-md-3 mb-3";
        col.innerHTML = `
        <div class="p-3 text-center product-card" data-id="${product.id}" data-name="${product.item_name}" data-price="${product.sales_price}">
            <img src="/storage/${product.item_image}" alt="item_picture" width="150px">
            <h6>${product.item_name}</h6>
            <p>${formatIDR(parseFloat(product.sales_price), 0)}</p>
            <button class="btn btn-sm btn-primary btn-block add-to-cart">Add</button>
        </div>
        `;
        productList.appendChild(col);
    });
}

// Update cart UI
function updateCart() {
cartBody.innerHTML = "";
let total = 0;

for (const [id, item] of Object.entries(cart)) {
    const row = document.createElement("tr");
    row.innerHTML = `
    <td>${item.name}</td>
    <td width="20%"><input type="number" value="${item.qty}" min="1" class="form-control form-control-sm qty-input" data-name="${name}"></td>
    <td width="25%">${formatIDR(item.qty * item.price ,0)}</td>
    <td width="10%"><button class="btn btn-sm btn-danger remove-item" data-name="${name}">&times;</button></td>
    `;
    cartBody.appendChild(row);
    total += item.qty * item.price;
}

totalEl.textContent = formatIDR(total, 0);
}

// Handle clicks
document.addEventListener("click", e => {
    if (e.target.classList.contains("add-to-cart")) {
        const card = e.target.closest(".product-card");
        const id = card.dataset.id; 
        const name = card.dataset.name;
        const price = parseFloat(card.dataset.price);

        if (!cart[id]) {
        cart[id] = { name: name, qty: 1, price: price };
        } else {
        cart[id].qty++;
        }
        updateCart();
    }

    if (e.target.classList.contains("remove-item")) {
        const name = e.target.dataset.name;
        delete cart[id];
        updateCart();
    }
});

// Quantity change
cartBody.addEventListener("input", e => {
    if (e.target.classList.contains("qty-input")) {
        const id = e.target.dataset.id;
        const name = e.target.dataset.name;
        const qty = parseInt(e.target.value);
        if (qty > 0) {
        cart[id].qty = qty;
        } else {
        delete cart[id];
        }
        updateCart();
    }
});

// Search input with debounce
let searchTimer;
searchBox.addEventListener("input", () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        const query = searchBox.value.trim();
        loadProducts(query);
    }, 300);
});

// Load products on page load
loadProducts();
</script>
@endsection
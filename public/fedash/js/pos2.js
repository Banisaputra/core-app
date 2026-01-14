"use strict"
const productList = document.getElementById("productList");
const cartBody = document.getElementById("cartBody");
const totalEl = document.getElementById("total");
const searchBox = document.getElementById("item_search");
const cashBtn = document.getElementById('cashBtn');
const cashPayment = document.getElementById('cashPayment');
const creditBtn = document.getElementById('creditBtn');
const creditPayment = document.getElementById('creditPayment');
const cashReceive = document.getElementById('cashReceived');
const tenorReceive = document.getElementById('crTenor');
var cart = {};
var cust_name = "";

function formatIDR(value, decimal) {
   return value.toLocaleString('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: decimal
   });
}
function showLoader() {
   document.getElementById('loading-overlay').style.display = 'flex';
}
function hideLoader() {
   document.getElementById('loading-overlay').style.display = 'none';
}
 
cashBtn.addEventListener('click', function () {
   if (cust_name == '') {
      alert('Silakan pilih pelanggan terlebih dahulu!')
      document.getElementById('memberSelect').focus();
      return;
   }
   new bootstrap.Modal(document.getElementById('cashModal')).show();
   const total = parseFloat(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
   document.getElementById('memberName').textContent = cust_name;
   document.getElementById('totalAmount').textContent = formatIDR(total,0);
   cashReceive.value = '';
   document.getElementById('cashChange').textContent = '0';
});

creditBtn.addEventListener('click', function () {
   if (cust_name == '') {
      alert('Silakan pilih pelanggan terlebih dahulu!')
      document.getElementById('memberSelect').focus();
      return;
   }
   new bootstrap.Modal(document.getElementById('creditModal')).show();
   const total = parseFloat(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
   document.getElementById('crMember').textContent = cust_name;
   document.getElementById('crTotal').textContent = formatIDR(total,0);
   document.getElementById('crTenor').value = '';
   document.getElementById('crInterest').textContent = '0';
});

tenorReceive.addEventListener('input', function () {
   const total = parseFloat(document.getElementById('crTotal').textContent.replace(/[^\d]/g, ''));
   const tenor = parseFloat(this.value) || 1;
   const estInterest = total/tenor;
   document.getElementById('crInterest').textContent = formatIDR(estInterest,0);
});

cashReceive.addEventListener('input', function () {
   const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/[^\d]/g, ''));
   const received = parseFloat(this.value) || 0;
   const change = received - total;
   document.getElementById('cashChange').textContent = formatIDR(change,0);
});

// save sales
creditPayment.addEventListener('click', function () {
   creditPayment.disabled = true;
  const total = parseFloat(document.getElementById('crTotal').textContent.replace(/[^\d]/g, ''));
  const tenor = parseFloat(tenorReceive.value);
  const memberId = document.getElementById('memberSelect').value *1;
  const crType = "BARANG";
   if (isNaN(tenor)) {
      alert('Tenor Tidak Sesuai!');
      return;
   }
   if(memberId == 0){
      alert('Pelanggan Harus Dipilih!');
      return;
   }
   showLoader();
   // Collect cart data
   var cartItems = [];
   cartItems = Object.entries(cart).map((item) => {
      const price = parseFloat(item[1].price ?? 0);
      const disc = parseFloat(item[1].disc ?? 0);
      const qty = parseInt(item[1].qty ?? 1);

      return {
         id: item[0],
         name: item[1].name,
         price: !isNaN(price) ? Math.max(0, price) : 0,
         disc_price: !isNaN(disc) ? Math.max(0, disc) : 0,
         qty: !isNaN(qty) ? Math.max(1, qty) : 1,
         subtotal: function() { return (this.price - this.disc_price) * this.qty }
      };
   });
   
  // Send to backend
  fetch('/submit-sale', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
    },
    body: JSON.stringify({
        member_id: memberId,
        items: cartItems,
        total: total,
        tenor: tenor,
        crInterest: total/tenor,
        crType: crType,
        payment_type: 'CREDIT'
    })
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      hideLoader();
      $('#creditModal .close').trigger('click');
      alert('Payment successful!\nAngsuran: ' + formatIDR(total/tenor, 0) + ' selama '+ tenor +' bulan');
      
      // Clear cart
      document.getElementById('cartBody').innerHTML = '';
      document.getElementById('total').textContent = '0';
      $('#memberSelect').val(null).trigger('change');
      cart = {};

      // Optional: print receipt, show invoice
      window.location.reload()
      window.open('/sales/' + response.receipt.id + '/print', '_blank');
    } else {
      alert('Failed to submit sale.\nNote: '+ response.message);
    }
  })
  .catch(error => {
    console.error(error);
    alert('Error submitting sale.');
  })
  .finally(() => {
      creditPayment.disabled = false;
  });
});

cashPayment.addEventListener('click', function () {
   cashPayment.disabled = true;
 
   const total = parseFloat(document.getElementById('totalAmount').textContent.replace(/[^\d]/g, ''));
   const received = parseFloat(cashReceive.value);
   const memberId = document.getElementById('memberSelect').value *1;
   if (isNaN(received) || received < total) {
      alert('Nominal Tidak Sesuai!');
      cashPayment.disabled = false;
      return;
   }
   if(memberId == 0){
      alert('Pelanggan Harus Dipilih!');
      cashPayment.disabled = false;
      return;
   }
  
   showLoader();
    // Collect cart data
   var cartItems = [];
   cartItems = Object.entries(cart).map((item) => {
      const price = parseFloat(item[1].price ?? 0);
      const disc = parseFloat(item[1].disc ?? 0);
      const qty = parseInt(item[1].qty ?? 1);

      return {
         id: item[0],
         name: item[1].name,
         price: !isNaN(price) ? Math.max(0, price) : 0,
         disc_price: !isNaN(disc) ? Math.max(0, disc) : 0,
         qty: !isNaN(qty) ? Math.max(1, qty) : 1,
         subtotal: function() { return (this.price - this.disc_price) * this.qty }
      };
   });
 
  // Send to backend
  fetch('/submit-sale', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
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
      hideLoader();
        $('#cashModal .close').trigger('click');
        alert('Payment successful!\nChange:' + formatIDR(received - total, 0));
      
      // Clear cart
      document.getElementById('cartBody').innerHTML = '';
      document.getElementById('total').textContent = '0';
      $('#memberSelect').val(null).trigger('change');
      cart = {};
      cashPayment.disabled = false;

      // Optional: print receipt
      window.open('/sales/' + response.receipt.id + '/print', '_blank');
    } else {
      alert('Failed to submit sale.');
    }
  })
  .catch(error => {
    console.error(error);
    alert('Error submitting sale.');
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
   } else if (products.length === 1) {
      autoToCart(products[0]);
   }

   products.forEach(product => {
      const col = document.createElement("div");
      col.className = "col-12 mb-3";
      // col.innerHTML = `
      //    <div class="text-center product-card" data-id="${product.id}" data-stock="${product.stock}" data-name="${product.item_name}" data-price="${product.sales_price}" data-ppn="${product.effective_ppn}">
      //       <img src="/storage/${product.item_image}" class="mb-3"
      //          onerror="this.onerror=null; this.src='/images/default.jpg'" 
      //          alt="item_picture" width="150px" height="150px">
      //       <h6>${product.item_name.length > 20 ? product.item_name.substring(0, 20) + '...' : product.item_name}</h6>
      //       <span>${formatIDR(parseFloat(product.sales_price), 0)}</span><br>
      //       <span class="text-muted">Stock: ${product.stock}</span>
      //       <button class="btn btn-sm btn-primary btn-block add-to-cart">Add</button>
      //    </div>
      // `;
      col.innerHTML = `
         <div class="product-card product-item row" data-id="${product.id}" data-stock="${product.stock}" data-name="${product.item_name}" data-price="${product.sales_price}" data-ppn="${product.effective_ppn}">
            <div class="product-info col-12">
               <div class="product-name col-5">${product.item_name}</div>
               <div class="product-price col-3">${formatIDR(parseFloat(product.sales_price), 0)}</div>
               <div class="product-stock col-2">Stok: ${product.stock}</div>
               <div class="col-2">
                  <button class="btn btn-sm btn-primary btn-block add-to-cart"><i class="fas fa-cart-plus me-1"></i> Beli</button>
               </div>
            </div>
         </div> 
      `;
      productList.appendChild(col);
      searchBox.value = "";
   });
}

function updateCart() {
   cartBody.innerHTML = "";
   let total = 0;

   for (const [id, item] of Object.entries(cart)) {
      const row = document.createElement("div");
      row.className = 'mb-2';
      row.innerHTML = `
      <div class="detail-item">
         <div class="col-4">${item.name}</div>
         <div class="quantity-control col-4" style="justify-content:center;">
            <button class="btn btn-outline-primary btn-sm py-1 px-2 btnQty" type="button" id="plus-${id}"><i id="plus-${id}" class="fe fe-plus btnQty"></i></button>
            <input type="number" id="qty-input-${id}" class="form-control qty-input px-1 mx-2" data-name="${item.name}" data-id="${id}" min="1" value="${item.qty}">
            <button class="btn btn-outline-secondary btn-sm py-1 px-2 btnQty" type="button" id="minus-${id}"><i id="minus-${id}" class="fe fe-minus btnQty"></i></button>
         </div>
   
         <div class="total-control col-3">${formatIDR(item.qty * (item.price - item.disc) ,0)}</div>
         <div class="edit-control"><button class="btn btn-sm btn-outline-info btn-edit-item" data-id="${id}" data-toggle="modal" data-target="#editItem"><span data-id="${id}" class="fe fe-info fe-16 edit-item"></span></button></div>
      </div>
      `;
      cartBody.appendChild(row);
      total += item.qty * (item.price - item.disc);
   }
   
   totalEl.textContent = formatIDR(total, 0);
}

function autoToCart(product) {
   const id = product.id; 
   const name = product.item_name;
   const price = parseFloat(product.sales_price);
   const stock = product.stock;
   const ppn = product.effective_ppn;

   if (stock == 0) return alert('stok barang kosong, restock barang diperlukan untuk transaksi!')
   if (!cart[id]) {
      cart[id] = { name: name, qty: 1, price: price, stock: stock, disc: 0, ppn: ppn};
   } else {
      if (cart[id].stock < (cart[id].qty + 1)) {
         return alert("stock barang tidak cukup!")
      }
      cart[id].qty++;
   }
   updateCart();
}

document.addEventListener("click", e => {
   if (e.target.classList.contains("add-to-cart")) {
      const card = e.target.closest(".product-card");
      const id = card.dataset.id; 
      const name = card.dataset.name;
      const price = parseFloat(card.dataset.price);
      const stock = card.dataset.stock;
      const ppn = card.dataset.ppn;

      if (stock == 0) return alert('stok barang kosong, restock barang diperlukan untuk transaksi!')
      if (!cart[id]) {
         cart[id] = { name: name, qty: 1, price: price, stock: stock, disc: 0, ppn: ppn};
      } else {
         if (cart[id].stock < (cart[id].qty + 1)) {
            return alert("stock barang tidak cukup!")
         }
         cart[id].qty++;
      }
      updateCart();
   }

   if (e.target.classList.contains("edit-item") || e.target.classList.contains("btn-edit-item")) {
      const id = e.target.dataset.id;
      var subTotal = cart[id]['price'] * cart[id]['qty'];
      var ppn_val = (subTotal * (cart[id]['ppn']*1 / 100))
      var total_with_ppn = subTotal + ppn_val; 

      $('#editItem #itemID').val(id);
      $('#editItem #itemName').html(cart[id]['name']);
      $('#editItem #itemQty').html(cart[id]['qty']);
      $('#editItem #itemPrice').html(formatIDR(cart[id]['price'], 0));
      $('#editItem #disc_price').val(cart[id]['disc']);
      $('#editItem #ppn').html(cart[id]['ppn'])
      $('#editItem #totalBase').html(formatIDR(subTotal, 0));
      $('#editItem #totalFinal').html(formatIDR(total_with_ppn, 0));
      $('#editItem #finalTotal').html(formatIDR(cart[id]['qty'] * (cart[id]['price']-cart[id]['disc']), 0));
   }
 
});

cartBody.addEventListener("click", e => {
    if (!e.target.classList.contains("btnQty")) return;
    
    const [type, id] = e.target.id.split('-');
    const item = cart[id];
    
    if (type === "plus") {
        if (item.stock <= item.qty) {
            return alert("Stok barang tidak cukup!");
        }
        item.qty++;
    } 
    else if (type === "minus") {
        if (--item.qty <= 0) {
            if (confirm("Hapus item dari keranjang?")) {
                delete cart[id];
            } else {
                item.qty = 1; // Reset to minimum
            }
        }
    }
    updateCart();
});

cartBody.addEventListener("input", debounce(e => {
   if (!e.target.classList.contains("qty-input")) return;
   
   const [,, id] = e.target.id.split('-');
   const item = cart[id];
   let newQty = parseInt(e.target.value) || 0;
   
   if (newQty > item.stock) {
      newQty = item.stock;
      alert("Stok hanya tersedia " + item.stock);
   }
   else if (newQty < 1) {
      if (confirm("Hapus item dari keranjang?")) {
         delete cart[id];
      } else {
         newQty = 1;
      }
   }
   
   item.qty = newQty;
   e.target.value = newQty;
   updateCart();
}, 800));

// Debounce helper function
function debounce(func, delay) {
   let timeout;
   return function() {
      const context = this;
      const args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), delay);
   };
}

let searchTimer;
searchBox.addEventListener("input", () => {
   clearTimeout(searchTimer);
   searchTimer = setTimeout(() => {
      const query = searchBox.value.trim();
      loadProducts(query);
   }, 500);
});

$('#editItem #disc_price').on('input', function () {
   let total = $('#totalBase').html().replace(/[^\d]/g, '');
   let qty = $('#itemQty').html().replace(/[^\d]/g, '');
   let disc = $(this).val()*1;
   let final = total - (disc * qty);
   $('#editItem #finalTotal').html(formatIDR(final, 0));
});

$('#editItem #saveChange').on('click', function() {
   let id = $('#editItem #itemID').val();
   let disc = $('#editItem #disc_price').val()*1;
   cart[id]['disc'] = disc;
   $('#editItem').modal('hide');
   updateCart();
});

$('#editItem').on('hidden.bs.modal', function () {
   $('#itemID').val('');
   $('#disc_price').val('');
   $('#itemName').html('');
   $('#itemQty').html('');
   $('#itemPrice').html('');
   $('#disc_price').val('');
   $('#totalBase').html('');
   $('#finalTotal').html('');
});

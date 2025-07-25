"use strict"

let rowIdx = 1;

document.querySelector('form').addEventListener('submit', function (e) {
   let valid = true;
   let message = '';
   let rows = document.querySelectorAll('#itemsBody tr');

   rows.forEach((row, index) => {
         const item = row.querySelector('select[name^="items"][name$="[item_id]"]');
         const qty = row.querySelector('input[name^="items"][name$="[qty]"]');
         const price = row.querySelector('input[name^="items"][name$="[price]"]');

         if (!item.value) {
            valid = false;
            message += `Baris ${index + 1}: Item belum dipilih.\n`;
         }
         if (!qty.value || qty.value <= 0) {
            valid = false;
            message += `Baris ${index + 1}: Qty tidak valid.\n`;
         }
         if (!price.value || price.value < 0) {
            valid = false;
            message += `Baris ${index + 1}: Harga tidak valid.\n`;
         }
   });

   if (!valid) {
         e.preventDefault();
         alert('Validasi gagal:\n\n' + message);
   }
});

$(document).on('input', '.qty, .price', function () {
   const row = $(this).closest('tr');
   const qty = parseFloat(row.find('.qty').val()) || 0;
   const price = parseFloat(row.find('.price').val()) || 0;
   const subtotal = qty * price;
   row.find('.subtotal').attr('data-value', subtotal);
   row.find('.subtotal').html(formatIDR(subtotal,0));
   calculateTotal();
});

function formatIDR(value, decimal) {
   return value.toLocaleString('id-ID', {
   style: 'currency',
   currency: 'IDR',
   minimumFractionDigits: decimal
   });
}

function initSelectItem() {
   $('.itemSelect').select2({
      placeholder: 'Search item...',
      theme: 'bootstrap4',
      minimumInputLength: 2,
      ajax: {
         url: '/api/items/search',
         dataType: 'json',
         delay: 250,
         data: function (params) {
               return {
                  q: params.term
               };
         },
         processResults: function (data) {
               return {
                  results: data.map(function (item) {
                     return {
                           id: item.id,
                           text: item.item_name
                     };
                  })
               };
         },
         cache: true
      }
   });
}

function addRow() {
   const newRow = `<tr>
      <td><select name="items[${rowIdx}][item_id]" class="form-control itemSelect" required></select></td>
      <td><input type="number" name="items[${rowIdx}][qty]" class="form-control qty" required></td>
      <td><input type="number" name="items[${rowIdx}][price]" class="form-control price" required></td>
      <td><span name="items[${rowIdx}][subtotal]" class="form-control subtotal" data-value=""></span></td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">x</button></td>
   </tr>`;
   $('#itemsBody').append(newRow);
   initSelectItem();
   rowIdx++;
}

function removeRow(button) {
   // $(button).prop('id')
   $(button).closest('tr').remove();
   calculateTotal();
}

function calculateTotal() {
   let total = 0;
   $('.subtotal').each(function () {
         total += ($(this).attr('data-value')*1) || 0;
         
   });
   $('#total').html(formatIDR(total,0));
}
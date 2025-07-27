"use strict"

let rowIdx = 1;

document.querySelector('form').addEventListener('submit', function (e) {
   let valid = true;
   let message = '';
   let rows = document.querySelectorAll('#itemsBody tr');

   rows.forEach((row, index) => {
         const item = row.querySelector('select[name^="items"][name$="[item_id]"]');
         const qty = row.querySelector('input[name^="items"][name$="[qty]"]');

         if (!item.value) {
            valid = false;
            message += `Baris ${index + 1}: Item belum dipilih.\n`;
         }
         if (!qty.value || qty.value <= 0) {
            valid = false;
            message += `Baris ${index + 1}: Qty tidak valid.\n`;
         }
   });

   if (!valid) {
      e.preventDefault();
      alert('Validasi gagal:\n\n' + message);
   }
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
                           text: item.item_name + " - [stock: "+ item.stock +"]"
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
      <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">x</button></td>
   </tr>`;
   $('#itemsBody').append(newRow);
   initSelectItem();
   rowIdx++;
}

function removeRow(button) {
   // $(button).prop('id')
   $(button).closest('tr').remove();
}

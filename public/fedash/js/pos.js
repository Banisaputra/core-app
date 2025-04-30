"use strict"
const formatter = new Intl.NumberFormat('id-ID')
let cart = [];
let invoice = 0;

$('#add_cart').click(function () {
   $(this).prop('disabled', true)
   let item_id = $('.search-item #item').val();
   let item_qty = $('.search-item #qty_item').val();
   $.ajax({
      url: urlSearch,
      dataType: 'json',
      data: {
         'item_id': item_id,
      },
      success: function (result) {
         // add cart
         cart.push(result['item_id']);
         invoice += (item_qty * 45000);
         $('.cart-container #cart-list tbody').append("<tr>\
         <td width='40%'>" + result['item_name'] + "</td>\
         <td width='10%'>" + result['item_brand_id'] + "</td>\
         <td width='10%'>" + item_qty + "</td>\
         <td width='15%'>" + formatter.format(45000) + "</td>\
         <td width='15%'>" + formatter.format(item_qty * 45000) + "</td>\
         <td width='10%'><button class='btn btn-outline-danger' onClick='itemDelete(this)' data-subtotal='"+ (item_qty * 45000) + "' data-id='" + result['item_id'] + "'><i class='fe fe-trash'></i></button></td>\
         </tr>");

         $('#add_cart').prop('disabled', false)
         $('select#item').val('').trigger('change')
         $('input#qty_item').val('')
         $('.invoice-item #total-invoice').text(formatter.format(invoice) + ',-')
         $('.invoice-item strong').text(cart.length)
      }
   })
})

$('#payment').click(function () {
   alert('saving transaction and view invoice')
})

$('#reset').click(function () {
   alert('delete all item on cart')
   location.reload();
})

function itemDelete(btn) {
   let item_id = $(btn).data('id')
   let subtotal = $(btn).data('subtotal')
   var row = btn.parentNode.parentNode;
   row.parentNode.removeChild(row);
   cart.splice(cart.indexOf(item_id), 1);
   invoice -= subtotal;
   $('.invoice-item #total-invoice').text(formatter.format(invoice) + ',-')
   $('.invoice-item strong').text(cart.length)
}

$('.js-data-item').change(function () {
   var data = $(this).val();
   alert('item selected with id = ' + data);
});

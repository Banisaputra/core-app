@extends('layouts.main')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Tambah Pembelian</h2>
        </div>
        
      </div>
      <form action="{{ route('purchases.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <hr class="my-4">
        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
          @foreach ($errors->all() as $error)
            <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ $error }} <br>           
          @endforeach
        </div>
        @endif
        @if (session()->has('error'))
          <div class="alert alert-danger" role="alert">
            <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ session('error') }} <br>           
          </div>
        @endif
        @if (session()->has('success'))
          <div class="alert alert-success" role="alert">
            <span class="fe fe-help-circle fe-16 mr-2"></span> {{ session('success') }} <br>           
          </div>
        @endif
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="svDate">Kode</label>
            <h5>{{ $pr_code }}</h5>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="simple-select2">Supplier</label>
            <select id="supplierSelect" name="supplier_id" class="form-control"></select>
          </div>
          <div class="form-group col-md-3">
            <label for="simple-select2">Nomor Faktur</label>
            <input type="text" class="form-control" id="ref_doc" name="ref_doc" value="{{old('ref_doc')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="pr_date">Tanggal Pembelian</label>
            <input type="date" class="form-control" id="pr_date" name="pr_date" value="{{old('pr_date')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="proof_of_payment">Invoice Photo</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="proof_of_payment" name="proof_of_payment">
              <label class="custom-file-label" for="proof_of_payment" id="label_photo">Choose file</label>
              <small>*Format file jpg/jpeg,png dengan ukuran max:2MB</small>
            </div>
            <!-- Preview container -->
            <div class="mt-2">
                <img id="preview-image" src="" alt="Preview" style="max-width: 300px;" hidden>
            </div>
          </div>
          <div class="form-group col-md-3">
            <label for="pr_date">Jatuh Tempo</label>
            <input type="date" class="form-control" id="over_due" name="over_due" value="{{old('over_due')}}">
          </div>
        </div>
        <hr class="my-4">

        <!-- Tabel Item -->
        <div class="table-responsive">
            <table class="table table-bordered" id="purchaseTable">
                <thead class="thead-dark">
                    <tr>
                        <th width="35%">Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th width="20%">Subtotal</th>
                        <th>
                            <button type="button" class="btn btn-sm btn-success" onclick="addRow()">+</button>
                        </th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr>
                        <td><select name="items[0][item_id]" class="form-control itemSelect" required></select></td>
                        <td><input type="number" name="items[0][qty]" class="form-control qty" required></td>
                        <td><input type="number" name="items[0][price]" class="form-control price" required></td>
                        <td><span name="items[0][subtotal]" class="form-control subtotal" data-value=""></span></td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">x</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td colspan="2"><span name="total" id="total" class="form-control"></span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
 
        <div class="form-row">
          <div class="col-md-6">
            <small>*Kode dibuat otomatis oleh sistem</small>
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script>
  $(document).ready(function () {
    initSelectItem();
     $('#supplierSelect').select2({
        placeholder: 'Search supplier...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        ajax: {
            url: '/api/supplier/search', // Your route
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
    });

    $('#proof_of_payment').on('change', function(event) {
      const file = event.target.files[0];
      const preview = $('#preview-image');
      const fileNameDisplay = $('#label_photo');
      fileNameDisplay.html( file ? file.name.substr(1, 70) : 'Choose file');
      if (file) {
        const reader = new FileReader(); 
        reader.onload = function(e) {
            preview.prop('src', e.target.result);
            preview.prop('hidden', false);
        }
        reader.readAsDataURL(file);
      } else {
        preview.prop('src' , '');
        preview.prop('hidden' , true);
      }
    });

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


  });
</script>
<script>
  let index = 1;

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
      const newRow = `
      <tr>
          <td><select name="items[${index}][item_id]" class="form-control itemSelect" required></select></td>
          <td><input type="number" name="items[${index}][qty]" class="form-control qty" required></td>
          <td><input type="number" name="items[${index}][price]" class="form-control price" required></td>
          <td><span name="items[${index}][subtotal]" class="form-control subtotal" data-value=""></span></td>
          <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">x</button></td>
      </tr>`;
      $('#itemsBody').append(newRow);
      initSelectItem();
      index++;
  }

  function removeRow(button) {
      // $(button).prop('id')
      $(button).closest('tr').remove();
      calculateTotal();
  }

  $(document).on('input', '.qty, .price', function () {
      const row = $(this).closest('tr');
      const qty = parseFloat(row.find('.qty').val()) || 0;
      const price = parseFloat(row.find('.price').val()) || 0;
      const subtotal = qty * price;
      row.find('.subtotal').attr('data-value', subtotal);
      row.find('.subtotal').html(formatIDR(subtotal,0));
      calculateTotal();
  });

  function calculateTotal() {
      let total = 0;
      $('.subtotal').each(function () {
          total += ($(this).attr('data-value')*1) || 0;
          
      });
      $('#total').html(formatIDR(total,0));
  }

  function formatIDR(value, decimal) {
   return value.toLocaleString('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: decimal
   });
}
</script>
@endsection

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
           <div class="col mb-4">
              <h2 class="h3 mb-0 page-title">Margin Penjualan</h2>
            </div>
            <div class="row">
              <div class="col-md-12">
                <form action={{ route('business.sales') }} method="POST" id="form-member" enctype="multipart/form-data">
                  @csrf
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="svtSelect">Jenis Kategori</label>
                      <select id="svtSelect" name="sv_type_id[]" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="auto_day">Margin(%)</label>
                      <div class="input-group">
                        <input type="number" name="auto_day" id="auto_day" class="form-control" min="1" max="28" required>
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Submit</button>
                        </div>
                      </div>
                    </div>
                    <small>Note: Harga jual akan ditambah dengan margin dari HPP</small>
                  </div>
                </form>
              </div>
              <div class="col-md-12 my-4">
                <div class="card shadow">
                  <div class="card-body">
                    <h5 class="card-title">Daftar Margin Penjualan</h5>
                    <p class="card-text">Margin berlaku untuk kategori utama dan turunan.</p>
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Margin</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php $count = 0; ?>
                          @foreach ($salesPolicy as $svt)
                              <?php $count++ ?>
                              <tr>
                                  <td>{{ $count }}</td>
                                  <td>{{ $svt->name }}</td>
                                  <td>{{ $svt->auto_date}}</td>
                                  <td>Rp {{ number_format($svt->value, 0) }}</td>
                                  <td><span class="badge badge-pill badge-{{ $svt->is_transactional == 1 ? 'success' : 'danger' }}">
                                      {{ $svt->is_transactional == 1 ? "Active" : "Nonactive" }}</span></td>
                              </tr>
                              @endif
                          @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-4" style="border-top: 1px solid #919192;">
             
        </div>
    </div>
</div>

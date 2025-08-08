{{-- modal saving --}}
<div class="modal fade" id="savingTypeModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="savingTypeForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Form Jenis Simpanan</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="_method" id="method">
          <input type="hidden" id="type_id">
          <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label>Nominal</label>
            <input type="number" name="value" class="form-control" min="1" required></input>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn mb-2 btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- content --}}
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col">
                <h2 class="h3 mb-0 page-title">Jenis Simpanan</h2>
            </div>
            <div class="row align-items-center my-4">
                <div class="col">
                    <button class="btn btn-primary mb-3" id="btnAdd">Tambah Data</button>
                </div>
                <div class="col-auto">
                    {{-- other button --}}
                </div>
            </div>
        
            <div class="row my-4"> 
              <div class="col">
                <div class="card shadow">
                  <div class="card-body"> 
                    <table class="table datatables" id="savingType">
                      <thead>
                        <tr>
                          <th width="5%">No.</th> 
                          <th width="15%">Nama</th>
                          <th width="45%">Deskripsi</th>
                          <th width="20%">Nominal</th>
                          <th width="10%">Status</th>
                          <th width="5%">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($svTypes as $svt)
                          <tr data-id="{{ $svt->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $svt->name }}</td>
                            <td>{{ $svt->description }}</td>
                            <td>Rp {{ number_format($svt->value, 0) }}</td>
                            <td>{!! $svt->is_transactional == 1 ? "<span class='dot dot-lg bg-success mr-1'></span>Aktif" : "<span class='dot dot-lg bg-secondary mr-1'></span>Tidak Aktif" !!}</td>
                            <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="text-muted sr-only">Action</span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                  <button class="dropdown-item btn-edit">Edit</button>
                                  <form action="{{ route('saving-types.destroy', $svt->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" id="btnDelete" class="dropdown-item">{{ $svt->is_transactional==1 ? "Nonaktifkan" : "Aktifkan"}}</button>
                                  </form>
                              </div>
                            </td> 
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-4" style="border-top: 1px solid #919192;">
            <div class="col mb-4">
              <h2 class="h3 mb-0 page-title">Jadwalkan Simpanan</h2>
            </div>
            <div class="row">
              <div class="col-md-12">
                <form action={{ route('saving-types.schedule') }} method="POST" id="form-member" enctype="multipart/form-data">
                  @csrf
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="svtSelect">Jenis Simpanan</label>
                      <select id="svtSelect" name="sv_type_id[]" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="auto_day">Hari Autogenerate (1 - 28)</label>
                      <div class="input-group">
                        <input type="number" name="auto_day" id="auto_day" class="form-control" min="1" max="28" required>
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Submit</button>
                        </div>
                      </div>
                    </div>
                    <small>Note: Kosongkan untuk nonaktifkan semua penjadwalan</small>
                  </div>
                </form>
              </div>
              <div class="col-md-12 my-4">
                <div class="card shadow">
                  <div class="card-body">
                    <h5 class="card-title">Daftar Penjadwalan aktif</h5>
                    <p class="card-text">Penjadwalan berlaku untuk semua anggota aktif.</p>
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Tanggal Generate</th>
                          <th>Nominal</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php $count = 0; ?>
                          @foreach ($svTypes as $svt)
                              @if ($svt->is_auto == 1 && $svt->auto_date > 0)
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
        </div>
    </div>
</div>

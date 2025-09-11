
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col mb-4">
                <h2 class="h3 mb-0 page-title">Pengaturan Pinjaman</h2>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">
                  <strong class="card-title">Syarat Umum</strong>
                </div>
                <div class="card-body">
                    {{-- syarat umum --}}
                    <form action="{{ route('policy.general') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Tanggal Cut Off Periode</label>
                                    <input type="number" min="1" max='28' id="cut_off" name="cut_off" class="form-control">
                                </div> 
                                <hr class="my-4">
                                <button class="btn btn-primary" id="btnSaveGeneral" type="submit">Simpan Perubahan</button>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mb-3">
                                  <label for="simpleinputlabel">Tanggal Cut Off Periode</label>
                                  <input type="number" id="cut_off" class="form-control" value="{{ $generalPolicies['cut_off_bulanan']['value'] ?? 1 }}" readonly>
                              </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</div>

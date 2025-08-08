
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
                    <form action="{{ route('policy.loanUmum') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Bungan(%)</label>
                                    <input type="text" id="bunga" name="bunga" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="maxAngsuranPokok">Maksimal Angsuran Pokok</label>
                                    <input type="text" id="maxAngsuranPokok" name="maxAngsuranPokok" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="minAngsuranPokok">Minimal Angsuran Pokok</label>
                                    <input type="text" id="minAngsuranPokok" name="minAngsuranPokok" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="maxPotongStaff">Maksimal Potong Gaji Staff</label>
                                    <input type="text" id="maxPotongStaff" name="maxPotongStaff" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="maxPotongOperator">Maksimal Potong Gaji Operator</label>
                                    <input type="text" id="maxPotongOperator" name="maxPotongOperator" class="form-control">
                                </div>
                                <hr class="my-4">
                                <button class="btn btn-primary" id="btnSaveUmum" type="submit">Simpan Perubahan</button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Bungan(%)</label>
                                    <input type="text" id="current_bunga" class="form-control" value="{{ number_format((float) ($loanPolicies['bunga_pinjaman']['value']??0),2) }}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="maxAngsuranPokok">Maksimal Angsuran Pokok</label>
                                    <input type="text" id="current_maxAngsuranPokok" class="form-control" value="{{ number_format((int) ($loanPolicies['max_pokok_angsuran']['value']??0))}}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="minAngsuranPokok">Minimal Angsuran Pokok</label>
                                    <input type="text" id="current_minAngsuranPokok" class="form-control" value="{{ number_format((int) ($loanPolicies['min_pokok_angsuran']['value']??0)) }}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="maxPotongStaff">Maksimal Potong Gaji Staff</label>
                                    <input type="text" id="current_maxPotongStaff" class="form-control" value="{{ number_format((int) ($loanPolicies['max_potong_gaji_staff']['value']??0)) }}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="maxPotongOperator">Maksimal Potong Gaji Operator</label>
                                    <input type="text" id="current_maxPotongOperator" class="form-control" value="{{ number_format((int) ($loanPolicies['max_potong_gaji_operator']['value']??0)) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4" style="border-top: 1px solid #919192;">
            <div class="card shadow mb-4">
                <div class="card-header">
                  <strong class="card-title">Syarat Khusus</strong>
                </div>
                <div class="card-body">
                    {{-- setting syarat khusus --}}
                    <form action="{{ route('policy.loanKhusus')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="exAgunan01">Maksimal Pinjaman kurang dari 1 Tahun</label>
                                    <input type="text" name="exAgunan01" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="exAgunan15">Maksimal Pinjaman kurang dari 5 Tahun</label>
                                    <input type="text" name="exAgunan15" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="exAgunan50">Maksimal Pinjaman lebih dari 5 Tahun</label>
                                    <input type="text" name="exAgunan50" class="form-control">
                                </div>
                                
                                <hr class="my-4">
                                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="exAgunan01">Maksimal Pinjaman kurang dari 1 Tahun</label>
                                    <input type="text" id="exAgunan01" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="exAgunan15">Maksimal Pinjaman kurang dari 5 Tahun</label>
                                    <input type="text" id="exAgunan15" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="exAgunan50">Maksimal Pinjaman lebih dari 5 Tahun</label>
                                    <input type="text" id="exAgunan50" class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">
                  <strong class="card-title">Syarat Agunan</strong>
                </div>
                <div class="card-body">
                    {{-- setting syarat agunan --}}
                    <form action="{{ route('policy.loanAgunan') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6"> 
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="bpkbMotor">BPKB Motor</label>
                                        <input type="text" class="form-control" name="bpkbMotor" placeholder="Maksimal pinjaman">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="startBPM">Mulai Tahun</label>
                                        <input type="number" class="form-control" name="startBPM" min="0" id="validationCustom03">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="endBPM">Sampai Tahun</label>
                                        <input type="number" class="form-control" name="endBPM" min="0" id="validationCustom03">
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="bpkbMobil">BPKB Mobil</label>
                                        <input type="text" class="form-control" name="bpkbMobil" placeholder="Maksimal pinjaman">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="startBPC">Mulai Tahun</label>
                                        <input type="number" class="form-control" name="startBPC" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="endBPC">Sampai Tahun</label>
                                        <input type="number" class="form-control" name="endBPC" min="0">
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sertify">Sertifikat Tanah</label>
                                        <input type="text" class="form-control" name="sertify" placeholder="Maksimal pinjaman">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="startSertify">Mulai Tahun</label>
                                        <input type="number" class="form-control" min="0" name="startSertify">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="endSertify">Sampai Tahun</label>
                                        <input type="number" class="form-control" min="0" name="endSertify">
                                    </div>
                                </div>
                                <hr class="my-4">
                                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow">
                                    <div class="card-body">
                                    <h5 class="card-title">Daftar Agunan</h5>
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th>Tahun</th>
                                            <th>Nominal</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count = 0; ?>
                                            @foreach ($svTypes as $svt)
                                                <?php $count++ ?>
                                                <tr>
                                                    <td>{{ $count }}</td>
                                                    <td>{{ $svt->name }}</td>
                                                    <td>{{ $svt->auto_date}}</td>
                                                    <td>Rp {{ number_format($svt->value, 0) }}</td>
                                                    <td><button type="button" class="btn mb-2 btn-outline-danger"><span class="fe fe-trash-2 fe-16"></span></button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

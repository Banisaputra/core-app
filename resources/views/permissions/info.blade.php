@extends('layouts.main')

@section('page_css')
    
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
              <h2 class="page-title">Roles Information</h2>
              <div class="row">
                <div class="col-md-12 my-4">
                  <div class="card shadow">
                    <div class="card-body">
                      <h5 class="card-title">Role access</h5>
                      <p class="card-text">Menu yang dapat diakses oleh role terdaftar</p>
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Access</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1.</td>
                            <td>Administrator</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Master & Pengaturan</li>
                              <li>Usaha</li>
                              <li>Koperasi</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>Kepala Toko</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Master & Pengaturan</li>
                              <li>Usaha</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>Kepala Koperasi</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Master & Pengaturan</li>
                              <li>Koperasi</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>Admin Toko</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Usaha</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>Bendahara</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Usaha</li>
                              <li>Koperasi</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>Badan Pengawas</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                          <tr>
                            <td>7.</td>
                            <td>Member</td>
                            <td><ul>
                              <li>Dashboard</li>
                              <li>Laporan</li>
                            </ul></td>
                            <td><span class="badge badge-pill badge-success">active</span></td>
                          </tr>
                              
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
    
@endsection
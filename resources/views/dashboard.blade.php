@extends('layouts.main')

@section('title')
    <title>Dashboard - Monitoring</title>
@endsection

@section('page_css')
    
@endsection
@section('content')
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-12">
            <div class="row">
               <div class="col-md-6 col-xl-3 mb-4">
                  <div class="card shadow border-0">
                     <div class="card-body">
                        <div class="row align-items-center">
                           <div class="col-3 text-center">
                              <span class="circle circle-sm bg-primary-light">
                                 <i class="fe fe-16 fe-shopping-bag text-white mb-0"></i>
                              </span>
                           </div>
                           <div class="col pr-0">
                              <p class="small text-muted mb-0">Monthly Sales</p>
                              <span class="h3 mb-0">{{ number_format($sales,0,',','.')}}</span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6 col-xl-3 mb-4">
                  <div class="card shadow border-0">
                     <div class="card-body">
                        <div class="row align-items-center">
                        <div class="col-3 text-center">
                           <span class="circle circle-sm bg-primary">
                              <i class="fe fe-16 fe-shopping-cart text-white mb-0"></i>
                           </span>
                        </div>
                        <div class="col pr-0">
                           <p class="small text-muted mb-0">Orders</p>
                           <span class="h3 mb-0">{{ number_format($purchase,0,',','.')}}</span>
                        </div>
                        </div>
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
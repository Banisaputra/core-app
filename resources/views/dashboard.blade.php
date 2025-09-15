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
                           <p class="small text-muted mb-0">Purchase</p>
                           <span class="h3 mb-0">{{ number_format($purchase,0,',','.')}}</span>
                        </div>
                        </div>
                     </div>
                  </div>
               </div> 
            </div>
            <div class="row">
               <div class="col-md-6 mb-4">
                  <div class="card shadow">
                     <div class="card-header">
                        <strong class="card-title mb-0">Sales & Purchase Chart</strong>
                       
                     </div>
                     <div class="card-body">
                        <canvas id="spChart" width="400" height="300"></canvas>
                     </div> 
                  </div> 
                </div>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('page_script')
    <script>
      var ChartData = { 
         labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], 
         datasets: [
            { 
               label: "Sales", 
               barThickness: 10, 
               backgroundColor: "rgba(51, 161, 81, 1)", 
               borderColor: "rgba(51, 161, 81, 1)", 
               pointRadius: !1, 
               pointColor: "#0dc516ff", 
               pointStrokeColor: "rgba(51, 161, 81, 1)", 
               pointHighlightFill: "#fff", 
               pointHighlightStroke: "rgba(51, 161, 81, 1)", 
               data: [28, 48, 40, 19, 64, 27, 90, 85, 92, 56, 75, 40], 
               fill: "",
               borderWidth: 2,
               lineTension: .1 
            }, 
            {
               label: "Purchase", 
               barThickness: 10, 
               backgroundColor: "rgba(210, 214, 222, 1)",
               borderColor: "rgba(210, 214, 222, 1)", 
               pointRadius: !1, 
               pointColor: "rgba(210, 214, 222, 1)", 
               pointStrokeColor: "#c1c7d1", 
               pointHighlightFill: "#fff", 
               pointHighlightStroke: "rgba(220,220,220,1)", 
               data: [65, 59, 80, 42, 43, 55, 40, 36, 68, 34, 77, 80], 
               fill: "", 
               borderWidth: 2, 
               lineTension: .1 
            },
         ] 
      }
      var ChartOptions = { 
         maintainAspectRatio: !1, 
         responsive: !0, 
         legend: { display: !1 }, 
         scales: { 
            xAxes: [{ 
               gridLines: { 
                  display: !1 
               } 
            }], 
            yAxes: [{ 
               gridLines: { 
                  display: !1, 
                  color: colors.borderColor, 
                  zeroLineColor: colors.borderColor 
               } 
            }] 
         } 
      }
      spChart=document.getElementById("spChart");
      spChart&&new Chart(spChart,{type:"bar",data:ChartData,options:ChartOptions});
    </script>
@endsection
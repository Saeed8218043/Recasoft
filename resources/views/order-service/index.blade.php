@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-css/1.4.6/select2-bootstrap.css" integrity="sha512-7/BfnxW2AdsFxJpEdHdLPL7YofVQbCL4IVI4vsf9Th3k6/1pu4+bmvQWQljJwZENDsWePEP8gBkDKTsRzc5uVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="m-grid__item m-grid__item--fluid m-wrapper">
                   <div class="m-subheader ">
        <div class="row">
            <div class="col-lg-4 col-md-4">
            	
                <h3 class="m-subheader__title "></h3>
            </div>                      
           
            <div class="col-lg-8 col-md-8">
				<div class="d-flex justify-content-end">
					 {{-- Buttons Group --}}
                        <div class="btn-group" role="group" aria-label="Large button group">
                            <button type="button" id="Order_Service" data-toggle="modal" data-target="#m_modal_5" class="m-btn btn btn-outline-primary active">Order Service</button>
                            <button type="button" id="log" data-toggle="modal" data-target="#m_modal_5" class="m-btn btn btn-outline-primary">Log</button>
                        </div>
                        {{-- Buttons Group ends --}}
				</div>
			</div>
        </div>

         
    </div>
					<!-- END: Subheader -->

                    <div class="m-content">
						<div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4">
							<h4 class="p-2 p-md-0 px-3 px-md-0 font-weight-bold">
								Order Service
							</h4>
                      

                            <div class="">
                                   
                                    <div class="modal-body">
                                        @php
                                        $compID = isset($currentCompany->company_id)?$currentCompany->company_id:'-';
                                        @endphp
                                        <form method="post" action="{{route('sendOrderService',['company_id'=>$compID])}}" enctype="multipart/form-data">
                                            @csrf
											<div class="col-lg-12 mb-4">
                                            
                                                <label>
                                                    Project ID
                                                </label>
                                                <input type="text" class="form-control" name="company_id" value="{{$currentCompany->company_id??''}}" readonly>
                                            </div>

											<div class="col-lg-12 mb-4">
                                            
                                                <label>
                                                    Project Name
                                                </label>
                                                <input type="text" class="form-control" name="company_name" value="{{$currentCompany->name??''}}" readonly>
                                            </div>
											<div class="col-lg-12 mb-4">
                                            
                                                <label>
                                                    Phone Number
                                                </label>
                                                <input type="text" class="form-control" name="phone_number" required>
                                            </div>

                                            <!--Bootstrap Select-->
											<div class="col-lg-12 mb-4">
                                            
                                                <label>
                                                    Select Device(s)
                                                </label>
                                                 <select name="devices[]" id="chooseFileInput" class="form-control m-bootstrap-select m_selectpicker" style="font-weight:bold;" multiple required>
                                                    @foreach($equipments as $row)
                                                            <option class="select_device" value="{{ $row->device_id }}">{{ $row->name }}</option>
                                                @endforeach
                                                {{-- <optgroup label="Not connected" >
                                                    @foreach ($other_equipments as $item)
                                                        @if ( $item->event_type=='equipment')
                                                        <option class="select_device" value="{{ $item->device_id }}">{{(isset($item->name) && $item->name!='')?$item->name:$item->device_id}}</option>
                                                        @endif
                                                        @endforeach
                                                    </optgroup> --}}
                                                    </select>
                                                </select>
                                            </div>
                                            <!--Bootstrap Select ends-->
											<div class="col-lg-12 mb-4">
                                            
                                                <input type="file" class="form-control" name="order_service_file" style="width: 100%; height: auto;">
                                            </div>
                                            
											<div class="col-lg-12 mb-4">
                                            
                                                <label>
                                                    Comments
                                                </label>
                                                <textarea name="description" class="form-control" style="height: 140px;font-weight:600; font-family: 'Open Sans';"></textarea>
                                            </div>
                                            <div>
                                                <input type="checkbox" id="urgent" name="urgent" value="Yes">
                                                <label for="urgent">Urgent</label>
                                            </div>
                                            <div class="d-flex justify-content-center justify-content-md-end">
                                                <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary mx-1">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                    </div>
                        </div>
        </div>
        <script>
                    $('.m_selectpicker').selectpicker('show');
        var company_id = "{{$company_id??''}}";
            $(document).on('click', '#Order_Service', function() {
                $('#log').removeClass('active');
                  window.location.href = '{{url("sendOrder-service")}}'+'/'+company_id;
                  
            });
            $(document).on('click', '#log', function() {
                $('#Order_Service').removeClass('active');
                  window.location.href = '{{url("order-service-logs")}}'+'/'+company_id;

            });
        </script>
        @endsection
       
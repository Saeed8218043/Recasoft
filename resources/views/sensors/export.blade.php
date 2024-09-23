@extends('layouts.app')

@section('content')
<style type="text/css">

@keyframes spin {
  0% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}

@media screen and (max-width: 767px) {
    .splabel {
        display: none;
    }
}    
</style>
<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="row">
                
<!-- mm/dd/yyyy -->

            <div class="col-xl-7 col-lg-7">
                {{-- <form action="{{route('export-csv-by-date')}}" method="post">
                    @csrf --}}
                    <input type="hidden" name="company_id" value="{{$company_id??0}}">
                    <div class="row gutter-10">
                        <div class="col-xl-5 col-lg-4 col-md-5">
                            <div class="form-group mb-2 mb-sm-0">
                                <!-- <label>Start Date</label> -->
                                <input type="text" name="startdate" id="startdate" placeholder="Start Date" class="form-control" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-4 col-md-5">
                            <div class="form-group mb-2 mb-sm-0">
                                <!-- <label>End Date</label> -->
                                <input type="text" name="enddate" id="enddate" placeholder="End Date" class="form-control" required="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-2">
                            <div class="form-group mb-2 mb-sm-0">
                                <!-- <label class="splabel" style="visibility:hidden;">Button</label> -->
                                <button class="btn btn-primary btn-block" onclick="openCSVModal()">Export CSV</button>

                            </div>
                        </div>
                    </div>
                {{-- </form> --}}
            </div>                
        </div>
    </div>

    <!-- END: Subheader -->
    <div class="m-content">

        <!--Begin::Section-->

        <div class="m-portlet panel-has-radius table-borderless mb-4 custom-p-5">
         <div class="row mb-3">
                    <div class="col-md-4">
                        <h4 class="m-subheader__title mb-3"><strong>File Export</strong></h4>
                    <div>
                <p class="m-0 text-muted">
                    Generate CSV or Excel files from your equipment data
                </p>
            </div> 
                    </div>
                 </div>
            <!--begin: Datatable -->
            <div class="table-responsive">
                <table class="table has-valign-middle table-hover">
                    <thead>
                        <tr>
                            <th>
                                TYPE
                            </th>
                            <th>
                                SENSORS & EQUIPMENTS
                            </th>  
                            <th>
                                START DATE
                            </th>
                            <th>
                                END DATE
                            </th>                                   
                            <th class="text-right">
                                ACTIONS
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($result) && count($result)>0)
                        @php
                        $counter=1;
                        @endphp
                        @foreach($result as $row)
                        @php
                        $sensor_id = isset($row->device_id)?$row->device_id:'';
                        $connected_equipment = \App\Device::where('sensor_id',$sensor_id)->first();
                        $sensor_name = !empty($row->name)?$row->name:$row->device_id;
                        if($sensor_name==''){
                            $sensor_name=$sensor_id;
                        }
                        @endphp
                        <tr>
                            <td>
                                @if ($connected_equipment == '' || $connected_equipment==null)
                                <svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                focusable="false" width="1em" height="1em"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"
                                class="iconify fs-22" data-icon="carbon:temperature"
                                style="vertical-align: -0.125em; transform: rotate(360deg);">
                                <path fill="currentColor"
                                    d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z">
                                </path>
                            </svg>
                            @else
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" width="1.5em" height="1.5em"
                            viewBox="0 0 459.359 459.359"
                            style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                            <g>
                                <path style="fill:#020202;"
                                    d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362 c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912 c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z" />
                                <path style="fill:#020202;"
                                    d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67 c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774 c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z" />
                                <path style="fill:#020202;"
                                    d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93 C153.145,312.05,157.593,307.619,163.061,307.619z" />
                                <path style="fill:#020202;"
                                    d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473 c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294 C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143 c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026 H23.075V236.214h65.787V361.026z" />
                                <path style="fill:#020202;"
                                    d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814 c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8 c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058 c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03 c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8 c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814 c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064 c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738 c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372 l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113 h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366 l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801 c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926 C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337 c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z" />
                                <path style="fill:#020202;"
                                    d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926 c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z" />
                            </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                            <g> </g>
                        </svg>

                                @endif
                            </td>
                            <td>
                                {{isset($connected_equipment)?$connected_equipment->name:$sensor_name}}
                            </td>
                            <td>
                                {{date('d-m-Y',strtotime($row->minDate))}}
                            </td>
                            <td>
                                {{date('d-m-Y',strtotime($row->maxDate))}}
                            </td>
                            <td align="right">
                                {{-- <button class="btn btn-primary btn-sm m-btn--pill" data-href="{{url('export-csv')}}/{{$company_id}}/{{$row->minDate}}/{{$row->maxDate}}/{{$row->device_id}}/{{preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '',$sensor_name).'.csv'}}" onclick="exportTasks(event.target);">
                                  <i class="fa flaticon-download"></i>  Download CSV
                                </button> --}}
                                <a href="#" onclick="openModal('{{url('export-csv')}}/{{$company_id}}/{{$row->minDate}}/{{$row->maxDate}}/{{$row->device_id}}/{{preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '',$sensor_name).'.csv'}}', 'csv')" >
                                        <button class="btn btn-sm m-btn--pill" >
                                  <i class="fa flaticon-eye"></i> 
                                </button>
                                       
                                </a>
                            </td>
                        </tr>
                        @php
                        $counter++;
                        @endphp
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        {{-- <div class="pull-right">
        {{ $result->links() }}
        </div> --}}

    </div>
    </div>

<style type="text/css">
@media screen and (min-width: 992px) {
    .modal-xl {
        max-width: 940px;
    }
}    
</style>

     <div class="modal fade" id="ImageModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <a id="downloadButton" class="btn btn-primary" download>Download</a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 4px;">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div style="align-items: center;">
                    <div id="mediaContainer" class="pulse-button" style="display: inherit;"></div>
                </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
 </div>
@endsection
@push('scripts')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.standalone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="{{asset('public/papaparse.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
      var dateToday = new Date();
      $('#startdate').datepicker(
        {
          format: 'yyyy-mm-dd',
          autoclose: true,
          weekStart: 1,
        }
        ).on('changeDate', function (selected) {
          var minDate = new Date(selected.date.valueOf());
          $('#enddate').datepicker('setStartDate', minDate).show();
        });
      $('#enddate').datepicker(
        {
          format: 'yyyy-mm-dd',
          autoclose: true,
          weekStart: 1,
          // endDate: dateToday
        }
      );
  });
                    //const modal = document.getElementById('ImageModal');
                    const mediaContainer = document.getElementById('mediaContainer');

                    function openModal(url, type) {
                        var mediaContainer = document.getElementById('mediaContainer');
                        mediaContainer.innerHTML = ''; // Clear any existing content

                        var downloadButton = document.getElementById('downloadButton');
                        downloadButton.setAttribute('href', url); // Set the download link to the provided URL

                            mediaContainer.classList.add('pulse-button');

                        if (type === 'pdf') {
                            var pdfViewer = document.createElement('iframe');
                            pdfViewer.src = url;
                            pdfViewer.style.width = '100%';
                            pdfViewer.style.height = '80vh';
                            pdfViewer.style.border = 'none';

                            mediaContainer.appendChild(pdfViewer);
                            downloadButton.style.display = 'block'; // Show the download button for PDFs
                        } else if (type === 'image') {
                            var imageElement = document.createElement('img');
                            imageElement.src = url;
                            imageElement.style.maxWidth = '100%';
                            imageElement.style.maxHeight = '100%';
                            imageElement.style.objectFit = 'contain'; // Adjusts the image to fit within the container without stretching

                            // Adjust the resolution of the image
                            imageElement.style.width = '800px';
                            imageElement.style.height = '600px';

                            mediaContainer.appendChild(imageElement);
                            downloadButton.style.display = 'block'; // Hide the download button for images
                        }else if (type === 'csv') {
                            // Load CSV file using Papa Parse
                            Papa.parse(url, {
                                download: true,
                                complete: function(results) {
                                    var csvData = results.data;
                                    var table = document.createElement('table');
                                    table.className = 'table table-responsive'; // Add 'table-responsive' class for responsiveness
                                    var tbody = document.createElement('tbody');

                                    // Create table rows and cells from the CSV data
                                    for (var i = 0; i < csvData.length; i++) {
                                        var row = document.createElement('tr');
                                        for (var j = 0; j < csvData[i].length; j++) {
                                            var cell = document.createElement(i === 0 ? 'th' : 'td');
                                            cell.textContent = csvData[i][j];
                                            row.appendChild(cell);
                                        }
                                        tbody.appendChild(row);
                                    }
                                    table.appendChild(tbody);
                                    mediaContainer.innerHTML = ''; // Clear loader
                                    mediaContainer.appendChild(table);
                                    mediaContainer.classList.remove('pulse-button');

                                    downloadButton.style.display = 'block'; // Show the download button for CSV files
                                },
                                error: function() {
                                    // Handle error condition
                                    mediaContainer.innerHTML = ''; // Clear loader
                                    var errorText = document.createElement('p');
                                    errorText.textContent = 'Error occurred while loading the CSV file.';
                                    mediaContainer.appendChild(errorText);
                                }
                            });
                        }

                        $('#ImageModal').modal('show');
                        }

                    function openCSVModal() {
                        // Get the values of the start date and end date inputs
                        var startDate = document.getElementById('startdate').value;
                        var endDate = document.getElementById('enddate').value;

                        // Create the CSV export URL with the company ID and date range
                        var url = "{{ route('export-csv-by-date') }}";
                       url += "?company_id={{ $company_id ?? 0 }}";
                        url += "&startdate=" + encodeURIComponent(startDate);
                        url += "&enddate=" + encodeURIComponent(endDate); 

                        // Call the openModal function with the CSV URL and type
                        openModal(url, 'csv');
                    }

</script>
@endpush
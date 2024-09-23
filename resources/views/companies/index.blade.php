@extends('layouts.app')

@section('content')
<div class="m-grid__item m-grid__item--fluid m-wrapper">

                    <!-- BEGIN: Subheader -->
                    <div class="m-subheader ">
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <h3 class="m-subheader__title ">Projects</h3>
                                <p class="m-0 text-muted">
                                    See project profile
                                </p>
                            </div>  
                            <div class="col-lg-8 col-md-8 d-flex justify-content-md-end align-items-center">
                                <div class="form-group m-form__group mt-3 mb-0">
                                    <div class="m-input-icon m-input-icon--right">
                                        <input type="text" class="form-control m-input search-devices-input" placeholder="Search for companies">
                                        <span class="m-input-icon__icon m-input-icon__icon--right"><span><i class="fa flaticon-search"></i></span></span>
                                    </div>
                                </div>

                                <a href="{{route('companies.create')}}"  class="btn btn-primary mt-3 ml-2">
                                    <i class="la la-plus-circle"></i> Create New Projects
                                </a>
                            </div>                      
                        </div>
                    </div>

                    <!-- END: Subheader -->
                    <div class="m-content">

                        <!--Begin::Section-->

                        <div class="m-portlet panel-has-radius mb-4 p-2">
                            <!--begin: Datatable -->
                            <table class="table has-valign-middle data-table">
                                <thead>
                                    <tr>
                                        <th>
                                            {{-- <input type="checkbox" name=""> --}}
                                        </th>
                                        <th>
                                            Project Name
                                        </th>
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Phone
                                        </th>
                                        <th>
                                            Created Date
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th class="text-center">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>        
                        </div>

                    </div>
                </div>
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">  
                <script type="text/javascript" src="{{asset('public/assets/js/datatables/datatables.min.js?v=1')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/js/selects/select2.min.js?v=1')}}"></script>
                <script type="text/javascript">
                    $(document).ready( function ()  {

        // Table setup
    // ------------------------------

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        lengthChange: false,
        searching: false,
        autoWidth: false,
        /*columnDefs: [{ 
            orderable: false,
            width: '100px',
            targets: [ 5 ]
        }],*/
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Filter:</span> _INPUT_',
            lengthMenu: '<span>Show:</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
            $('.data-table').css({opacity: 0.0, visibility: "visible"}).animate({opacity: 1}, 300);
            $('html,body').animate({ scrollTop: 0 }, 'fast');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
            
        }
    });


        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            order:[],
            ajax: "{{ route('companies.showData') }}",
            columns: [
                    // {data: 'avatar', name: 'avatar'},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name',width:'20%'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    
                    {data: 'created_at', name: 'created_at'},
                    {data: 'is_active', name: 'is_active'},
                    // {data: 'role_id', name: 'role_id'},
                    
                    
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    ]
                });

        // Enable Select2 select for the length option
        /*$('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });*/

    });
                </script>
@endsection
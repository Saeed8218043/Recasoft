@extends('layouts.app')

@section('content')
    <style>
        .role {
            padding: 7px;
            width: 110px
        }
    </style>
    @php
        $user_folder = \App\Document::where('user_id', \Auth::user()->id)->first();
    @endphp
    <div class="m-grid__item m-grid__item--fluid m-wrapper">

        <div class="m-subheader">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-4">
                    <h4 class="m-subheader__title "></h4>
                </div>
                <div class="col-lg-8 col-md-8">
                   <div class=" d-flex justify-content-md-end flex-wrap">
                        <div class="mb-1 d-flex align-items-center flex-wrap">
                            <button type="button" class="btn btn-primary d-sm-inline-block mb-2 mb-sm-0 mr-2"
                                data-toggle="modal" data-target="#folder-modal">Create folder</button>
                            <div class="form-group m-form__group mb-0">
                                <div class="m-input-icon m-input-icon--right">
                                    <form action="" method="get">
                                        <input type="text" class="form-control m-input search-devices-input"
                                            placeholder="Search for folders in Project" name="search"
                                            value="{{ $search ?? '' }}">
                                    </form>
                                    <span class="m-input-icon__icon m-input-icon__icon--right"><span><i
                                                class="fa flaticon-search"></i></span></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <div class="m-content">

            <!--Begin::Section-->

            <div class="m-portlet panel-has-radius mb-4 custom-p-5">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <h4 class="m-subheader__title mb-3"><strong>Files</strong></h4>
                    </div>
                    <!-- Tags -->
                    @if (\Session::has('message'))
                        {{-- <div class="alert alert-success">{{\Session::get('message')}}</div> --}}

                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: '{{ session('title') ?? '' }}',
                                text: '{{ session('message') }}'
                            });
                        </script>
                    @endif
                    @if (\Session::has('success'))
                        {{-- <div class="alert alert-success">{{\Session::get('message')}}</div> --}}

                        <script>
                            Swal.fire({
                                title: '{{ session('title') ?? '' }}',
                                icon: "success",
                                text: '{{ session('success') }}'
                            });
                        </script>
                    @endif
                </div>
                <!-- Tags end -->
                <!-- Tags -->
                <div class="mb-1 mt-3">

                </div>
                <!-- Tags end -->

                <!--begin: Datatable -->
                <div class="table-responsive p-4">
                    <table
                        class="table table-striped- table-bordered table-hover table-borderless table-checkable has-valign-middle"
                        id="m_table_1">
                        <thead>
                            <tr>

                                <th width="2%" style="text-align: center">TYPE</th>
                                <th width="55%">NAME</th>
                                <th>Created By</th>
                                <th>Copy or Sync</th>
                                <th>Files & Folders</th>
                                <th width="2%" style="text-align: center">Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $folder)
                                <tr>


                                    <td align="center"><a id="{{ $folder->id }}"
                                            class="iconHolder fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                            href="{{ url('documents') }}/{{ $company_id }}/{{ $folder->slug }}"
                                            style="color:#212529;text-decoration:none;display:block;">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="20px"
                                                height="20px">
                                                <path fill="currentColor"
                                                    d="M.54 3.87L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z" />
                                            </svg></a></td>

                                    <td><a id="{{ $folder->id }}" value="{{ $folder->id }}"
                                            href="{{ url('documents') }}/{{ $company_id }}/{{ $folder->slug }}"
                                            style="color:#212529;text-decoration:none;display:block;">{{ $folder->name }}</a>
                                    </td>
                                    @php
                                        $folders = \App\Document::where('slug', $folder->slug)->first();
                                        $roles = \App\CompanyMembers::where('user_id', $folders->user_id)->first();
                                        $role = isset($roles->role) ? $roles->role : '';
                                        
                                    @endphp
                                    <td>
                                        @if ($role == null)
                                            <span class="badge badge-pill badge-secondary role">Super Admin</span>
                                        @endif
                                        @if ($role == 1)
                                            <span class="badge badge-pill badge-secondary role">User</span>
                                        @endif
                                        @if ($role == 2)
                                            <span class="badge badge-pill badge-secondary role">Company Admin</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($folder->belongsTo == 0)
                                            <a><button type="button" folderId="{{ $folder->id }}"
                                                    class="btn btn-default btn-sm move_sensor" data-toggle="modal"
                                                    data-target="#transfer_modal">
                                                    <span class="mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                            height="1em" preserveAspectRatio="xMidYMid meet"
                                                            viewBox="0 0 24 24">
                                                            <path fill="currentColor"
                                                                d="m13 3l3.293 3.293l-7 7l1.414 1.414l7-7L21 11V3z"></path>
                                                            <path fill="currentColor"
                                                                d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z">
                                                            </path>
                                                        </svg></span> Copy to another Project
                                                </button></a>

                                            <a href="{{ route('documents.syncFolder', ['folder_id' => $folder->id]) }}">
                                                <button type="button" folderId="{{ $folder->id }}"
                                                    class="btn btn-default btn-sm">
                                                    <span class="mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                            height="1em" preserveAspectRatio="xMidYMid meet"
                                                            viewBox="0 0 512 512">
                                                            <g>
                                                                <path
                                                                    d="M256,0C146.9,0,55.8,67.4,18.7,162.7l39.3,16c31.1-79.7,106.5-136,198-136c69.2,0,130.8,33.9,169.3,85.3h-84v42.7h149.3
                                                                    V21.3H448v66C401.3,33.8,332.2,0,256,0z M454,333.3c-31.1,79.7-106.5,136-198,136c-69.9,0-131.3-34.4-170-85.3h84.7v-42.7H21.3
                                                                    v149.3H64v-66c46.7,52.9,115.1,87.3,192,87.3c109.1,0,200.2-67.4,237.3-162.7L454,333.3z" />
                                                            </g>
                                                        </svg>
                                                    </span> Sync
                                                </button>
                                            </a>
                                        @else
                                            <span class="badge badge-pill badge-danger role">Clone</span>
                                        @endif
                                    </td>

                                    <td style="text-align: center">
                                        {{ $folder->children_count }}
                                    </td>
                                    <td class="text-right">
                                        @if ($folder->user_id == \Auth::user()->id || \Auth::user()->id == 1)
                                            <span class="miniIcon">
                                                <i data-url="{{ route('documents.deletefolder', ['id' => $folder->id]) }}"
                                                    data-id="{{ $folder->id }}" class="deletefolder la la-trash"></i>
                                            </span>
                                            <span class="miniIcon">
                                                <i data-dismiss="modal" class="editfolder la la-edit" data-toggle="modal"
                                                    data-id="{{ $folder->id }}" data-target="#modal-edit-folder"></i>
                                            </span>
                                        @else
                                            @if ($role == 1 || $role == 2)
                                                @if (($folder->user_id == \Auth::user()->id && $role == 2) || $role == 1 || \Auth::user()->id == 1)
                                                    <span class="miniIcon">
                                                        <i data-url="{{ route('documents.deletefolder', ['id' => $folder->id]) }}"
                                                            data-id="{{ $folder->id }}"
                                                            class="deletefolder la la-trash"></i>
                                                    </span>
                                                    <span class="miniIcon">
                                                        <i data-dismiss="modal" class="editfolder la la-edit"
                                                            data-toggle="modal" data-id="{{ $folder->id }}"
                                                            data-target="#modal-edit-folder"></i>
                                                    </span>
                                                @endif
                                            @endif
                                        @endif
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Modal-->
    <div class="modal fade" id="folder-modal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel"><b>Create Folder</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- @php
                $compID = isset($currentCompany->company_id)?$currentCompany->company_id:'-';
                @endphp --}}
                    <form method="post" action="{{ route('documents.create') }}">
                        @csrf
                        <input type="text" name="company_id" value="{{ $company_id }}" hidden>
                        <div class="mb-3">
                            <label>
                                Name
                            </label>
                            <input type="text" class="form-control" name="name" required>
                        </div>


                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary mx-1">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!--begin::Modal-->
    <div class="modal fade" id="modal-delete-folder" tabindex="-1" role="dialog"
        aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Delete Folder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="GET">
                        @csrf
                        <p>Are you sure you want to delete this Folder?</p>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary mx-1" value="Delete">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->

    <!--start::Modal2-->

    <div class="modal fade" id="modal-edit-folder" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    @if (\Auth::user()->id == 1)
                        <h5 class="modal-title" id="addDocModalLabel2">Rename Folder</h5>
                    @endif
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('documents.editFolder') }}" enctype="multipart/form-data">
                        @csrf
                        <div id="modal-edit">

                        </div>
                        @if (isset($user_folder))
                            <div class="d-flex justify-content-center justify-content-md-end">
                                <button type="button" class="btn btn-secondary mx-1"
                                    data-dismiss="modal">Cancel</button>
                                <input type="submit" class="btn btn-primary mx-1" value="Update">
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal2-->



    @if (isset($can_manage_users) && $can_manage_users > 0)
        <div class="modal fade" id="transfer_modal" tabindex="-1" role="dialog" aria-labelledby="transfer_modalLabel"
            aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Copy Folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @php
                            
                            $currentID = App\Company::select('id')
                                ->where(['name' => $currentCompany->name, 'company_id' => $currentCompany->company_id])
                                ->get();
                            
                            $parent_ID = App\Company::select('parent_id')->get();
                            $id = \Auth::user()->id;
                            $companies2 = App\Company::where('user_id', $id)->get();
                            $flag = false;
                            
                            for ($i = 0; $i < sizeof($parent_ID); $i++) {
                                if ($parent_ID[$i]->parent_id == $currentID[0]->id) {
                                    $flag = true;
                                }
                            }
                            if ($user_id == 1) {
                                $companies = \App\Company::where('parent_id', 0)
                                    ->where('company_id', '!=', $company_id)
                                    ->select('id', 'name', 'company_id')
                                    ->orderBy('name', 'ASC')
                                    ->get();
                            } else {
                                $companies = \App\Company::where(function ($q) use ($cID, $selectedParent) {
                                    $q->where('id', $selectedParent);
                                    $q->orWhere('parent_id', $cID);
                                    if ($selectedParent > 0) {
                                        $q->orWhere('parent_id', $selectedParent);
                                    }
                                })
                                    ->where('company_id', '!=', $company_id)
                                    ->get();
                            }
                        @endphp


                        <form method="POST" action="{{ route('documents.copyFolder') }}">
                            @csrf
                            <input type="hidden" name="folder_id" id="folder_id">
                            <input type="hidden" name="current_company" id="current_company"
                                value="{{ isset($currentID[0]->company_id) ? $currentID[0]->company_id : '' }}">
                            <div class="form-group">
                                @if ($user_id > 1)
                                    <select name="transfer_company" id="transfer_company" class="form-control">
                                        <option value="">Select Project</option>

                                        @if (isset($companies) && count($companies) > 0)
                                            @foreach ($companies as $company2)
                                                @if ($company2->parent_id == 0)
                                                    <option
                                                        value="{{ isset($company2->company_id) ? $company2->company_id : '' }}">
                                                        {{ isset($company2->name) ? $company2->name : '' }} (Inventory
                                                        Account)
                                                    </option>
                                                @else
                                                    <option
                                                        value="{{ isset($company2->company_id) ? $company2->company_id : '' }}">
                                                        {{ isset($company2->name) ? $company2->name : '' }}</option>
                                                @endif
                                            @endforeach
                                        @endif

                                    </select>
                                @else
                                    <select name="transfer_company" id="transfer_company" class="form-control">
                                        <option value="">Select Project</option>
                                        @php
                                            $crtComp = isset($currentCompany->company_id) ? $currentCompany->company_id : '';
                                        @endphp
                                        @if (isset($companies) && count($companies) > 0)
                                            @foreach ($companies as $company)
                                                @php
                                                    if ($crtComp == $company->company_id) {
                                                        continue;
                                                    }
                                                    
                                                @endphp
                                                @if ($company->parent_id == 0)
                                                    <option
                                                        value="{{ isset($company->company_id) ? $company->company_id : '' }}">
                                                        {{ isset($company->name) ? $company->name : '' }}</option>
                                                @endif
                                            @endforeach
                                        @endif

                                    </select>
                                @endif
                                <p id="company_select_error" class="text-danger d-none">Please select any project</p>
                            </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        @if ($user_id > 1)
                            <button type="submit" class="btn btn-primary ">Copy Folder</button>
                        @else
                            <button type="submit" class="btn btn-primary ">Copy Folder</button>
                        @endif
                    </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        $(document).on('click', '.move_sensor', function() {
            var folder_id = $(this).attr('folderId');
            console.log(folder_id);
            $('#folder_id').val(folder_id);

        });
        // $('.move_sensor_button').on('click', function() {
        //     let company_id = $('#transfer_company').val();
        //     var folder_id = $('#folder_id').val()
        //     console.log(folder_id);
        //     let comp_ID = $('#comp_ID').val();
        //     let current_company = "{{ $currentCompany->company_id }}";
        //     // console.log(device_ids);
        //     if (company_id == '') {
        //         $('#company_select_error').removeClass('d-none');
        //     } else {
        //         $('#company_select_error').addClass('d-none');
        //         $.ajax({

        //             url: '{{ route('documents.copyFolder') }}',
        //             type: 'POST',
        //             dataType: 'JSON',
        //             data: {
        //                 current_company: current_company,
        //                 transfer_company: company_id,
        //                 folder_id:folder_id
        //             },
        //             // beforeSend:function(){
        //             //     $('#search-loader').show();
        //             // },
        //             success: function(data) {
        //                 console.log(data);
        //                 if (data.success && data.success == true) {
        //                     window.location.href = '{{ route('documents.copyFolder') }}';
        //                 }

        //                 // $('#search-loader').hide();
        //             },
        //             error: function(data) {
        //                 console.log(data);
        //             }
        //         });
        //     }
        // });
        $(document).on('click', '.deletefolder', function(e) {
            var id = $(this).attr('data-id');
            $('#modal-delete-folder').modal('show');
            $('#modal-delete-folder form').attr('action', $(this).attr('data-url'));
        });

        $(document).on('click', '#modal-edit-folder', function(e) {
            console.log(e);
            var id = $(this).attr('data-id');
        });

        $(document).on('click', '.editfolder', function(e) {
            console.log(e);
            var id = $(this).attr('data-id');
            e.preventDefault();

            $.ajax({
                type: 'get',
                data: {
                    id: id,
                },
                url: "{{ route('documents.viewFolderValue') }}",
                success: function(data) {

                    $('#modal-edit').html(data);

                }
            });


            $('#modal-edit-folder').modal('show');
        });
        $('#reset-filter').on('click', function() {
            window.location.href = '{{ url('documents/' . $company_id) }}';
        });
    </script>
@endsection

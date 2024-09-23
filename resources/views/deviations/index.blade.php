@extends('layouts.app')

@section('content')
    <style>
        .full-body {
            display: none
        }

        .showMore {
            color: blue !important;
        }

        .showLess {
            display: none;
            color: blue !important;
        }
    </style>

    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        <div class="m-subheader ">
            <div class="row">
               
                <div class="col-lg-12 col-md-8">
                    <div class="d-flex justify-content-end">
                        <button data-toggle="modal" data-target="#deviation_modal" class="btn btn-primary"><svg
                                xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M17 13h-4v4h-2v-4H7v-2h4V7h2v4h4m-5-9A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2Z">
                                </path>
                            </svg> Create report</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- END: Subheader -->
        <div class="m-content">

            <!--Begin::Section-->
            <style type="text/css">
                .limitBody {
                    width: 150px;
                    white-space: normal;
                }

                .limittext {
                    width: 100px;
                    float: left;
                    /* add this */
                    white-space: nowrap;
                    overflow: hidden;
                }

                .full-issue {
                    display: none
                }

                .showMore {
                    color: blue !important;
                }

                .showLess {
                    display: none;
                    color: blue !important;
                }

                .showMoreIssue {
                    color: blue !important;
                }

                .showLessIssue {
                    display: none;
                    color: blue !important;
                }

                .limitBody {
                    width: 240px;
                    white-space: normal;
                }
                .file{
                    text-align: center;
                }
            </style>
            <div class="m-portlet panel-has-radius table-borderless mb-4 custom-p-5">
            <div class="row mb-3">
                    <div class="col-md-4">
                        <h4 class="m-subheader__title mb-3"><strong>Errors</strong></h4>
                    </div>
                 </div>
                <!-- Table Starts -->
                <table
                    class="table has-valign-middle table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-responsive">
                    @if (\Session::has('message'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ \Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ \Session::get('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <thead>
                        <tr>
                            <th width="15%">
                                Name
                            </th>
                            <th width="25%">
                                issue
                            </th>
                            <th width="30%">
                                ACTIONS
                            </th>
                            <th width="8%">
                                STATUS
                            </th>
                            <th width="8%">
                                DATE
                            </th>
                            <th width="10%">files</th>

                            <th>EDIT</th>
                            <th>DOWNLOAD</th>

                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($deviations_data) && $deviations_data!=null)
                        @foreach ($deviations_data as $deviation)
                            <tr>
                                <td>{{ $deviation->name }}</td>
                                <td>
                                    <div class="limitBody">
                                        <span class="full-issue" id={{ 'showIssue' . $deviation->id }}>
                                            {!! nl2br($deviation->issue) !!}
                                        </span>

                                        <span class="long body" id={{ 'issue-text' . $deviation->id }}>
                                            {!! substr($deviation->issue, 0, 30) !!}
                                        </span>
                                         @if (strlen($deviation->issue) > 20)
                                        <a data-id={{ $deviation->id }} class="showMoreIssue"
                                            id={{ 'showMoreIssue' . $deviation->id }}>More</a>

                                        <a data-id={{ $deviation->id }} class="showLessIssue"
                                            id={{ 'showLessIssue' . $deviation->id }}>Less</a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="limitBody">
                                        <span class="full-body" id={{ 'showBody' . $deviation->id }}>
                                            {!! nl2br($deviation->actions) !!}
                                        </span>

                                        <span class="long body" id={{ 'history-text' . $deviation->id }}>
                                            {!! substr($deviation->actions, 0, 30) !!}
                                        </span>
                                        @if (strlen($deviation->actions) > 20)
                                        <a data-id={{ $deviation->id }} class="showMore"
                                            id={{ 'showMore' . $deviation->id }}>More</a>
                                        <a data-id={{ $deviation->id }} class="showLess"
                                            id={{ 'showLess' . $deviation->id }}>Less</a>
                                        @endif
                                    </div>

                                </td>
                                <td>{{ $deviation->status }}</td>
                                <td>{{ $deviation->date }}</td>
                                @php 
                                    $array = explode(',', $deviation->files);
                                @endphp

                                <td>
                                @if(isset($deviation->files) && $deviation->files!='')
                                    @foreach ($array as $file)
                                        <div class="limitBody">
                                @php
                                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                @endphp

                                @if ($fileExtension === 'pdf')

                                    <a href="#" onclick='openModal("{{ asset("storage/app/public/$file") }}", "pdf")' >
                                        Download
                                </a>
                                @elseif (in_array($fileExtension, ['png', 'jpg', 'jpeg', 'gif']))
                                   <a href="#" onclick='openModal("{{ asset("storage/app/public/$file") }}", "image")' >
                                        Download
                                </a>
                                @else
                                    <a href="#" onclick='openModal("{{ asset("storage/app/public/$file") }}", "pdf")' >Download
                                </a>
                                @endif
                                            {{-- <a href='{{ asset("storage/app/public/$file") }}' download></a> --}}
                                         
                                        </div>
                                    @endforeach
                                    @else
                                    <span>No files uploaded<span>
                                    @endif
                                </td>
                                <td>
                                    @if (\Auth::user()->id == 1)
                                        <span class="miniIcon">
                                            <i data-url="{{ route('deviations.delete', ['id' => $deviation->id]) }}"
                                                data-id="{{ $deviation->id }}" class="deleteDeviation la la-trash"></i>
                                        </span>
                                    @endif
                                    <span class="miniIcon">
                                        <i data-dismiss="modal" class="editdeviation la la-edit" d_id="{{ $deviation->id }}"
                                            d_name="{{ $deviation->name }}" d_actions="{{ $deviation->actions }}"
                                            d_status="{{ $deviation->status }}" d_issue="{{ $deviation->issue }}"
                                            d_date="{{ $deviation->date }}"  data-toggle="modal"
                                            data-id="{{ $deviation->id }}" data-target="#update-deviation-modal"></i>
                                    </span>
                                </td>

                                <td  class="file">
                                
                                    <a href="#" onclick="openModal('{{ route('downloadPdf', [$deviation->id]) }}', 'pdf')" style="color: black;">
                                        <span class="miniIcon">
                                            <i data-dismiss="modal" class="la la-download" data-toggle="modal"></i>
                                        </span>
                                    </a>

                                </td>

                                {{-- <td>{{ $history->device_id }} </td>
                            <td> {{ $history->email }} </td>

                            <td>{{ $history->updated_at }} </td> --}}
                            </tr>
                        @endforeach
                            @else
                            <tr class="text-center">
										<td colspan="7">No record found</td>
									</tr>
                            @endif
                    </tbody>
                </table>
                <!-- Table Ends -->
            </div>

        </div>

    </div>



    <div class="modal fade" id="deviation_modal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Add error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="post" action="{{ route('deviations.create') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="company_id" value="{{ $company_id }}" hidden>
                        <div class="mb-3">
                            <label>
                                Date
                            </label>
                            <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>"
                                style="font-weight:600; font-family: 'Open Sans';">
                        </div>

                        <div class="mb-3">
                            <label>
                                Your name
                            </label>
                            <input type="text" class="form-control" name="name"
                                style="font-weight:600; font-family: 'Open Sans';">
                        </div>
                        <div class="mb-3">
                            <label>
                                Describe the issue
                            </label>
                            <textarea type="text" class="form-control" name="issue"
                                style="height: 140px;font-weight:600; font-family: 'Open Sans';" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label>
                                Actions taken
                            </label>
                            <textarea name="actions" class="form-control" style="height: 140px;font-weight:600; font-family: 'Open Sans';"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>
                                Upload file
                            </label>
                            <input type="file" class="form-control" name="files[]" id="deviation_files" multiple>
                        </div>
                        <div class="mb-3">
                            <label>
                                Status
                            </label>
                            <select name="status" class="form-control">
                                <option value="open">Open</option>
                                <option value="closed">closed</option>
                            </select>
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

    <!--begin::Update-Deviation-Modal-->
    <div class="modal fade" id="update-deviation-modal" tabindex="-1" role="dialog"
        aria-labelledby="supportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Edit deviation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="post" action="{{ route('deviations.edit') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="company_id" value="{{ $company_id }}" hidden>
                        <input type="text" name="id" id="dev-id" value="" hidden>
                        <div class="mb-3">
                            <label>
                                Date
                            </label>
                            <input type="date" class="form-control" name="date" id="date"
                                value="<?php echo date('Y-m-d'); ?>" style="font-weight:600; font-family: 'Open Sans';">
                        </div>

                        <div class="mb-3">
                            <label>
                                Your name
                            </label>
                            <input type="text" class="form-control" name="name" id="name"
                                style="font-weight:600; font-family: 'Open Sans';">
                        </div>
                        <div class="mb-3">
                            <label>
                                Describe the issue
                            </label>
                            <textarea type="text" class="form-control" name="issue" id="issue"
                                style="height: 140px;font-weight:600; font-family: 'Open Sans';" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label>
                                Actions taken
                            </label>
                            <textarea name="actions" id="actions" class="form-control"
                                style="height: 140px;font-weight:600; font-family: 'Open Sans';"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>
                                Upload file
                            </label>
                            <input type="file" class="form-control" name="files[]" multiple>
                        </div>
                        <div class="mb-3">
                            <label>
                                Status
                            </label>
                            <select name="status" id="status" class="form-control">
                                <option value="open">Open</option>
                                <option value="closed">closed</option>
                            </select>
                        </div>


                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary mx-1">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Update-Deviation-Modal-->

 <div class="modal fade" id="ImageModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="mediaContainer"></div>
                </div>
                <div class="modal-footer">
                    <a id="downloadButton" class="btn btn-primary" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
 </div>

    <!--begin::Modal-->
    <div class="modal fade" id="modal-delete-deviation" tabindex="-1" role="dialog"
        aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Delete Deviation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">
                        <p>Are you sure you want to delete this deviation?</p>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Delete">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->


    <script>
        $(document).on('click', '.deleteDeviation', function(e) {
            $('#modal-delete-deviation').modal('show');
            $('#modal-delete-deviation form').attr('action', $(this).attr('data-url'));
        });

        $(document).on('click', '.editdeviation', function(e) {
            var id = $(this).attr('d_id');
            var issue = $(this).attr('d_issue');
            var actions = $(this).attr('d_actions');
            var name = $(this).attr('d_name');
            var status = $(this).attr('d_status');
            var date = $(this).attr('d_date');
            
            $('#issue').val(issue);
            $('#actions').val(actions);
            $('#name').val(name);
            $('#date').val(date);
            $('#dev-id').val(id);
        });
        $('.showMore').on('click', function() {
            var id = $(this).attr('data-id');

            $("#showBody" + id).attr("style", "display:block");
            $("#history-text" + id).attr("style", "display:none");
            $("#showMore" + id).attr("style", "display:none");
            $("#showLess" + id).attr("style", "display:block");
        });
        $('.showLess').on('click', function() {
            var id = $(this).attr('data-id');

            $("#showBody" + id).attr("style", "display:none");
            $("#history-text" + id).attr("style", "display:block");

            $('#showLess' + id).css('display', 'none');
            $('#showMore' + id).css('display', 'block');
        });


        $('.showMoreIssue').on('click', function() {
            var id = $(this).attr('data-id');

            $("#showIssue" + id).attr("style", "display:block");
            $("#issue-text" + id).attr("style", "display:none");
            $("#showMoreIssue" + id).attr("style", "display:none");
            $("#showLessIssue" + id).attr("style", "display:block");
        });
        $('.showLessIssue').on('click', function() {
            var id = $(this).attr('data-id');

            $("#showIssue" + id).attr("style", "display:none");
            $("#issue-text" + id).attr("style", "display:block");

            $('#showLessIssue' + id).css('display', 'none');
            $('#showMoreIssue' + id).css('display', 'block');
        });

       
                    //const modal = document.getElementById('ImageModal');
                    const mediaContainer = document.getElementById('mediaContainer');

                    function openModal(url, type) {
                        var mediaContainer = document.getElementById('mediaContainer');
                        mediaContainer.innerHTML = ''; // Clear any existing content

                        var downloadButton = document.getElementById('downloadButton');
                        downloadButton.setAttribute('href', url); // Set the download link to the provided URL

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
                        }

                        $('#ImageModal').modal('show');
                        }


    </script>
@endsection

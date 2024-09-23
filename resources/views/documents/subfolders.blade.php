@extends('layouts.app')

@section('content')
@php
$myuser=Session::get('newArray');
       $user_folder = \App\Document::where('user_id',\Auth::user()->id)->where('slug',$slug)->first();
       $folder = \App\Document::where('slug', $slug)->first();
    $role = \App\CompanyMembers::where('user_id', $folder->user_id)->first();
@endphp
    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        <div class="m-content">

            <!--Begin::Section-->
            {!! $html !!}
            
        </div>

    </div>
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
        <script>
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
                            downloadButton.style.display = 'none'; // Hide the download button for images
                        }

                        $('#ImageModal').modal('show');
                        }

                     


                </script>
    <!--begin::Modal-->
    <div class="modal fade" id="folder-modal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Create directory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- @php
                $compID = isset($currentCompany->company_id)?$currentCompany->company_id:'-';
                @endphp --}}
                    <form method="post"
                        action="{{ route('documents.createsub', ['company_id' => $company_id, 'slug' => $slug]) }}">
                        @csrf
                        <input type="text" name="company_id" value="{{ $company_id }}" hidden>
                        <input type="text" name="slug" value="{{ $slug }}" hidden>
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
    <div class="modal fade" id="file-modal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocModalLabel">Add Resource</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST"
                        action="{{ route('documents.createFile', ['company_id' => $company_id, 'slug' => $slug]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ isset($company_id) ? $company_id : '' }}">
                        <input type="hidden" name="slug" value="{{ isset($slug) ? $slug : '' }}">
                        <div class="mb-3">
                            <label>
                                Name
                            </label>
                            <input required type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label>
                                Upload
                            </label>

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="file" required=""
                                        style="width: 100%; height: auto;">
                                </div>
                            </div>

                        </div>
                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Upload">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->



    <!--begin::Modal-->
    <div class="modal fade" id="modal-delete-folder" tabindex="-1" role="dialog"
        aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Delete Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="GET">
                        @csrf
                        <input type="hidden" name="sID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">
                        <p>Are you sure you want to delete this note?</p>

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
                        <h5 class="modal-title" id="addDocModalLabel2">Rename</h5>
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

    <script>

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
    </script>
@endsection

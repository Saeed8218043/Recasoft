<!--begin::Modal-->
<div class="modal fade" id="m_modal_4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Projects</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="isloading" id="search-loader" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                    </div>

                    <!--if it is Device ID-->
                        <!--Top Message-->
                        <p class="text-muted mgk_ids">
                            Locate a device across projects based on ID (e.g. bs62m73jm90savu5qf50)
                        </p>
                        <!--Top Message ends-->
                    <!--if it is Device ID ends-->

                    <div class="d-flex">
                        <div class="input-group mb-3">
                          {{-- <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary dropdown-toggle color-7" type="button" data-toggle="dropdown" aria-expanded="false">Company</button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="#">Dropdown item</a>
                            </div>
                            </div> --}}
                            <select id="searchType" class="form-control">
                                <option value="1">Projects</option>
                                <option value="2">Device ID</option>
                            </select>
                            <input type="text" class="form-control" aria-label="Search" placeholder="Search" id="search-company">
                        </div>
                        @php
                        $is_shown=1;
                        $is_modal=0;
                        $company_id = isset($company_id)?$company_id:'-';
                        $email = \Auth::user()->email;
                        $user_id = \Auth::user()->id;
                        $sql2 = \DB::table('company_members')
                        ->where('user_id',$user_id)
                        ->where('company_id',$company_id)
                        ->where('role',2)
                        // ->whereIn('role',[1])
                        ->get();
                        // $sql2 = \DB::table('company_members')->where('user_id',$user_id)->whereIn('role',[0,2])->get();
                        if($email=='admin@recasoft.com'){
                                $is_shown=0;
                        }elseif(isset($sql2) && count($sql2)>0){
                                $is_shown=1;
                                $is_modal=1;

                        }else{
                                $is_shown=0;
                        }
                        @endphp
                        @if($is_shown==1)
                        <div class="mb-3 ml-3 mgk_projects">
                                <button type="button" class="btn btn-primary px-2 px-sm-3" data-toggle="modal" data-target="#m_modal_3" data-dismiss="modal">
                                    <i class="la la-plus-circle"></i>   <span class="d-none d-sm-inline-block">New</span> Project
                                </button>
                        </div>
                        @endif
                    </div>

                    <div class="mgk_projects"  style="display: none;">
                        <a id="gotoDetails" href="javascript:;" class="btn btn-block btn-primary  px-2 px-sm-3">Go to details</a>
                    </div>
                    <div class="mgk_projects" id="projectList">
                        <div class="table-responsive">
                            <table class="table sensors-popup-table">
                                <thead>
                                    <tr>
                                        <th>Project Name</th>
                                        <th class="text-center">Cloud Connectors</th>
                                        <th class="text-center">Sensors</th>
                                    </tr>
                                </thead>
                                <tbody id="loadCompaniesList"></tbody>
                            </table>
                        </div>
                    </div>


                    <!--if it is Device ID-->
                        <!--ID info line-->
                        <div class="mgk_ids" id="searchDevice">
                            

                        </div>
                        <!--ID info line ends-->
                    <!--if it is Device ID ends-->
                </form>
            </div>
    {{-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Submit</button>
    </div> --}}
        </div>
    </div>
</div>


<!--begin::Modal-->
<div class="modal fade" id="m_modal_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: auto !important;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create New Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="alert" role="alert" id="create-company-msg" style="display:none;"></div>
                @if ($is_modal == 1)
                @php
                    $email = \Auth::user()->email;
                    $sqlCompany_member = \DB::table('companies')->where('company_id',$company_id)->first();
                    $sql_Id = $sqlCompany_member->id;
                    function getRandomString2($length = 8) {
                        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $string = '';
                        for ($i = 0; $i < $length; $i++) {
                            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
                        }
                        return $string;
                    }
                    $randStr = getRandomString2();
                @endphp
                @if ($message = Session::get('warning'))
                <div class="alert alert-warning alert-block">
                    <button type="button" class="close" data-dismiss="alert"></button>	
                    <strong>{{ $message }}</strong>
                </div>
                @endif
                {{-- @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
                @endif --}}
                @if(Session::has('message'))
                <script>
                $(document).ready(function(){
                    $('#modal').modal({show: true});
                });
                </script>
                @endif
                
                <form action="{{route("companies.store2", [$sql_Id])}}" method="post" id="create-company2">
                    <div class="isloading" id="loader" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg> 
                    </div>
                    @csrf
                    <div class="mb-3">
                        <label>Project Name</label>
                        <input type="text" name="company_name" class="form-control"  required="required">
                    </div>
                    <div class="mb-3">
                        <label>Organization Name</label>
                        <input type="text" name="organization_name" class="form-control"  required="required">
                    </div>
                    <div class="mb-3">
                        <label>Organization Number</label>
                        <input type="text" name="organization_no" class="form-control" >
                    </div>
                    <div class="mb-3">
                        <label>Project Email Address</label>
                        <input type="email" name="company_email" class="form-control"  required="required">
                    </div>
                    <div class="mb-3">
                        <label>Project Phone</label>
                        <input type="text" name="company_phone" class="form-control"  required="required">
                    </div>
                    <div class="mb-3">
                        <label>Project ID</label>
                        <input type="text" name="company_id" class="form-control" value="{{$randStr}}" readonly>
                        
                        
                        
                    <div class="d-flex justify-content-end border-top pt-3">
                        <input type="submit" name="" class="btn btn-primary mr-3" value="Create" id="company-create-btn2">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    </div>
                    
                </form>
                    {{-- @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                    @endif --}}
                @else
                <form action="" method="post" id="create-company">
                    <div class="isloading" id="loader" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                    </div>
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="company_name" class="form-control" placeholder="Company Name" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="organization_name" class="form-control" placeholder="Organization Name" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="organization_no" class="form-control" placeholder="Organization Number">
                    </div>
                    <div class="mb-3">
                        <input type="email" name="company_email" class="form-control" placeholder=" Company Email Address" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="company_phone" class="form-control" placeholder=" Company Phone" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="company_id" class="form-control" placeholder="Project ID" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="email" name="service_account_email" class="form-control" placeholder="Service Account Email" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="service_account_id" class="form-control" placeholder="Secret Key" required="required">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="key_id" class="form-control" placeholder="Key ID" required="required">
                    </div>
                    <div class="d-flex justify-content-end border-top pt-3">
                        <input type="submit" name="" class="btn btn-primary mr-3" value="Create" id="company-create-btn">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    </div>
                    
                </form>
                @endif
                
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" id="upload-image-msg" style="display:none;"></div>
                <form action="" method="post" id="upload-company-image" enctype="multipart/form-data">
                    <div class="isloading" id="image-loader" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                    </div>
                    @csrf
                    <input type="hidden" name="company_id" value="{{request()->segment(2)??''}}">
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required="required">
                    </div>
                    <div class="d-flex">
                        <input type="submit" name="" class="btn btn-primary mr-3" value="Upload" id="company-upload-image-btn">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the image ?</p>
                <form action="{{url('delete-company-image')}}" method="post">
                    @csrf
                    <input type="hidden" name="company_id" value="{{request()->segment(2)??''}}">
                    <div class="d-flex">
                        <input type="submit" name="" class="btn btn-primary mr-3" value="Yes">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<style type="text/css">
#searchType {
    flex-grow: 0;
    width: 90px;
}    
@media screen and (min-width:  576px) {
    #searchType {
        flex-grow: 0;
        width: 120px;
    }    
}
</style>
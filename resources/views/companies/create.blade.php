@extends('layouts.app')

@section('content')
<div class="m-grid__item m-grid__item--fluid m-wrapper">

                    <!-- BEGIN: Subheader -->
                    <div class="m-subheader ">
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <h3 class="m-subheader__title ">Projects</h3>
                                <p class="m-0 text-muted">
                                    See Project profile
                                </p>
                            </div>  
                            <div class="col-lg-8 col-md-8 d-flex justify-content-md-end align-items-center">
                                {{-- <div class="form-group m-form__group mt-3 mb-0">
                                    <div class="m-input-icon m-input-icon--right">
                                        <input type="text" class="form-control m-input search-devices-input" placeholder="Search for companies">
                                        <span class="m-input-icon__icon m-input-icon__icon--right"><span><i class="fa flaticon-search"></i></span></span>
                                    </div>
                                </div> --}}

                                <a href="{{route('companies.create')}}" class="btn btn-primary mt-3 ml-2" >
                                    <i class="la la-plus-circle"></i> Create New Project
                                </a>
                            </div>                      
                        </div>
                    </div>

                    <!-- END: Subheader -->
                    <div class="m-content">

                        <!--Begin::Section-->

                        <div class="m-portlet panel-has-radius mb-4 p-4">
                            <!--begin: Datatable -->
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <form method="post" action="{{route('companies.store')}}">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="company_id" value="{{old('company_id')}}" class="form-control" placeholder="Company ID (Disruptive URL ID)">
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="name" value="{{old('name')}}" class="form-control" placeholder="Enter Company Name">
                                </div>
                                <div class="mb-3">
                                    <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="Enter Company Email Address">
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="phone" value="{{old('phone')}}" class="form-control" placeholder="Enter Company Phone">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" placeholder="About Company" style="height: 100px;" name="description"></textarea>
                                </div>
                                {{-- <div class="mb-4 d-flex align-items-center">
                                    <figure class="user-thumb-1 user-thumb mb-0 mr-3">
                                        <img class="img-fluid" src="assets/app/media/img/users/c.jpg" alt="">
                                    </figure>
                                    <input type="file" name="" class="form-control ml-3 height-auto">
                                </div> --}}
                                <div class="d-flex">
                                    <input type="submit" name="" class="btn btn-primary mr-3" value="Create">
                                  {{--   <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button> --}}
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                
@endsection
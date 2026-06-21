@extends('layouts.app')

@section('content')

<link href="{{ asset('assets/select_bo/css/dropZone.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">

<style>
    .gallery { display: inline-block; margin-top: 10px; width: 100%; }
    .image_block { border-radius: 3px; padding: 5px; margin: 15px; background: #f9f9f9; border: 1px solid #ddd; transition: 0.3s; }
    .image_block:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .image_block_inner { position: relative; margin-bottom: 5px; text-align: center; }
    .checkbox_wrapper { position: absolute; top: 5px; left: 5px; z-index: 10; background: rgba(255,255,255,0.8); padding: 2px; border-radius: 2px; }
    .img-container { height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #eee; }
    .img-container img { max-height: 100%; width: auto; }
    .gallery_image_reload { display: block; margin: 10px 0; color: #337ab7; font-weight: bold; }
</style>

<div class="header dashboard_from">
    <h1 class="page-title">Update Information</h1>
    <ul class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li><a href="#">/ Master Data</a> / </li>
        <li><a href="{{ url('admin/master-data/floor') }}">Floor</a></li>
        <li><a href="{{ route('admin.floor.additional-images.edit',[$data->floor_code]) }}">/ Update Floor</a></li>
    </ul>
</div>

@php
    $path = request()->path();
    $lastPart = collect(explode('/', $path))->last();
@endphp
<div class="container">
    <div class="card shadow">
        <div class="card-body">
            <!-- Nav Tabs -->
            @include('admin.floor.tab')
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="main-content">

                <div class="panel panel-default" style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-12">
                            {{-- Dropzone Upload Form --}}
                            <form action="{{ route('admin.floor.additional-images.store') }}" 
                                enctype="multipart/form-data" 
                                class="dropzone" 
                                id="image-upload">
                                @csrf
                                <input type="hidden" name="floorCode" value="{{ $data->floor_code ?? null }}">
                            </form>

                            <a href="javascript:location.reload();" class="gallery_image_reload">
                                <i class="fa fa-refresh"></i> Refresh page to see newly uploaded files
                            </a>
                            <hr>
                        </div>
                    </div>

                    <div class="gallery">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover custom-table" id="datatable">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th>SL</th>
                                            <th>File Name</th>
                                            <th>File Type</th>
                                            <th>Show</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if (isset($files))
                                            @foreach ($files as $value)
                                                @php
                                                $filePath = asset('storage/assets/admin/images/floor/' . $data->floor_image);
                                                $ext = strtolower($value->file_type);
                                                $isImage = in_array($ext, ['jpg','jpeg','png','gif','bmp']);
                                            @endphp

                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>

                                                <td class="text-center">
                                                    {{ $value->original_name ?? $value->file_name }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $value->file_type }}
                                                </td>

                                                {{-- ✅ SHOW --}}
                                                <td class="text-center">
                                                    <a href="{{ $filePath }}" target="_blank"
                                                    style="color: blue; text-decoration: underline;">
                                                        View File
                                                    </a>
                                                </td>

                                                {{-- ✅ DELETE --}}
                                                <td class="text-center">
                                                    <form action="{{ route('admin.floor.additional-images.destroy', $value->id) }}" 
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm">
                                                            <i class="fa fa-trash" style="color:red;font-size: 18px;"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            @endforeach
                                        @else
                                            {{-- <tr>
                                                <td class="text-center">No Data Found</td>
                                            </tr> --}}
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/select_bo/js/dropZone.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>

<script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            pageLength: 10,
            ordering: true,
            searching: true,
            columnDefs: [
                {
                    defaultContent: "-",
                    targets: "_all"
                }
            ],
        });

        // Initialize Fancybox
        $(".fancybox").fancybox({
            openEffect: "elastic",
            closeEffect: "elastic"
        });

        // Dropzone Config
        Dropzone.options.imageUpload = {
            maxFilesize: 3.5, // MB
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
            dictDefaultMessage: "Drop files here to upload",
            success: function(file, response) {
                console.log("Successfully uploaded");
            }
        };
    });

    function submitDelete() {
        var selectedIds = [];
        
        // Find all checked checkboxes and get their data-id
        $('.img-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });

        if (selectedIds.length > 0) {
            if(confirm('Are you sure you want to delete ' + selectedIds.length + ' item(s)?')) {
                $('#imageIdPost').val(selectedIds.join(','));
                $('#imagePostForm').submit();
            }
        } else {
            alert('Please select at least one image to delete.');
        }
    }
</script>
@endpush
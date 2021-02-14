@extends('admin.app')

@section('title' , __('messages.add_new_service'))

@push('scripts')
    <script>
        var lang = "{{ Config::get('app.locale') }}",
            select = "{{ __('messages.select') }}"
            
        $("#typeSelect").on('change', function() {
            var type = $(this).val()
            $("#categorySelect").parent(".form-group").show()
            $("#categorySelect").html("")
            $.ajax({
                url : "/admin-panel/services/fetchcategoriesbytype/" + type,
                type : 'GET',
                success : function (data) {
                    $("#categorySelect").append(`
                        <option disabled selected>${select}</option>
                        `)
                    data.forEach(function (row) {
                        var title = row.title_en
                        if (lang == 'ar') {
                            title = row.title_ar
                        }
                        $("#categorySelect").append(`
                        <option value="${row.id}">${title}</option>
                        `)
                    })
                }
            })
        })
    </script>
@endpush

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_new_service') }}</h4>
                 </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data" >
            @csrf
                        
            <div class="form-group mb-4">
                <label for="title_en">{{ __('messages.title_en') }}</label>
                <input required type="text" name="title_en" class="form-control" id="title_en" placeholder="{{ __('messages.title_en') }}" value="" >
            </div>
            <div class="form-group mb-4">
                <label for="title_ar">{{ __('messages.title_ar') }}</label>
                <input required type="text" name="title_ar" class="form-control" id="title_ar" placeholder="{{ __('messages.title_ar') }}" value="" >
            </div>

            <div class="form-group mb-4">
                <label for="type">{{ __('messages.type') }}</label>
                <select id="typeSelect" required name="type" class="form-control" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                    <option value="doctor" >{{ __('messages.doctor') }}</option>
                    <option value="lawyer" >{{ __('messages.lawyer') }}</option>
                </select>
            </div>  
            
            <div style="display: none" class="form-group mb-4">
                <label for="type">{{ __('messages.category') }}</label>
                <select id="categorySelect" required name="category_id" class="form-control" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                </select>
            </div> 
            
            <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
        </form>
    </div>
@endsection
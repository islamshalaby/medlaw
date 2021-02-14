@extends('dashboard.app')

@section('title' , __('messages.update_profile'))

@push('scripts')
<script>
    var ss = $(".tags").select2({
        tags: true,
    });
    var f4 = flatpickr($(".flatpickr"), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "13:45"
    });
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
                    if (lang == 'en') {
                        title = row.title_ar
                    }
                    $("#categorySelect").append(`
                    <option value="${row.id}">${title}</option>
                    `)
                })
            }
        })
    })

    $("#categorySelect").on("change", function() {
        var category = $(this).val()
        console.log(category)
        $("#servicesSelect").parent(".form-group").show()
        $("#servicesSelect").html("")

        $.ajax({
            url : "/admin-panel/doctorslawyers/fetchservicesbycategory/" + category,
            type : 'GET',
            success : function (data) {
                
                data.forEach(function (row) {
                    var title = row.title_en
                    if (lang == 'ar') {
                        title = row.title_ar
                    }
                    $("#servicesSelect").append(`
                    <option value="${row.id}">${title}</option>
                    `)
                })
            }
        })

    })
    
    $(".day_work").change(function(){
        var day = $(this).data('day')
        if($(this).is(':checked')){
            $("." + day).css({"display" : "flex"})
            if ($("#reservation_type").val() == "attendance") {
                $(this).parents(".row").next().find('.count').show()
            }else {
                $(this).parents(".row").next().find('.count').hide()
            }
        }else{
            $("." + day).css({"display" : "none"})
        }
    })

    $(".time-range").on("click", '.addtime', function() {
        $(this).hide()
        var cloned = $(this).parent(".time-range").clone()
        $(this).show()

        $(this).parent(".time-range").after(cloned)
    })
    
    

    // submit form on click finish
    $(".actions ul").find('li').eq(2).on("click", 'a[href="#finish"]', function () {
        $("form").submit()
    })
</script>
    
@endpush


@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.update_profile') }}</h4>
             </div>
    </div>
    
    @if (session('status'))
        <div class="alert alert-danger mb-4" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">x</button>
            <strong>Error!</strong> {{ session('status') }} </button>
        </div> 
    @endif

    <form method="post" action="" >
     @csrf
     <div class="form-group mb-4">
        <label for="">{{ __('messages.current_image') }}</label><br>
        <img src="https://res.cloudinary.com/ddcmwwmwk/image/upload/w_100,q_100/v1581928924/{{ $data['drlaw']['personal_image'] }}"  />
    </div>
    <div class="custom-file-container" data-upload-id="myFirstImage">
        <label>{{ __('messages.upload') }} ({{ __('messages.personal_image') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
        <label class="custom-file-container__custom-file" >
            <input type="file"  name="personal_image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
            <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
            <span class="custom-file-container__custom-file__custom-file-control"></span>
        </label>
        <div class="custom-file-container__image-preview"></div>
    </div>
    <div class="form-group mb-4">
        <label for="first_name">{{ __('messages.first_name') }}</label>
        <input required type="text" name="first_name" class="form-control" id="first_name" placeholder="{{ __('messages.first_name') }}" value="{{ $data['drlaw']['first_name'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="last_name">{{ __('messages.last_name') }}</label>
        <input required type="text" name="last_name" class="form-control" id="last_name" placeholder="{{ __('messages.last_name') }}" value="{{ $data['drlaw']['last_name'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="app_name_en">{{ __('messages.app_name_en') }}</label>
        <input required type="text" name="app_name_en" class="form-control" id="app_name_en" placeholder="{{ __('messages.app_name_en') }}" value="{{ $data['drlaw']['app_name_en'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="app_name_ar">{{ __('messages.app_name_ar') }}</label>
        <input required type="text" name="app_name_ar" class="form-control" id="app_name_ar" placeholder="{{ __('messages.app_name_ar') }}" value="{{ $data['drlaw']['app_name_ar'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="password">{{ __('messages.password') }}</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="{{ __('messages.password') }}" value="" >
    </div>
    <div class="form-group mb-4">
        <label for="type">{{ __('messages.type') }}</label>
        <select id="typeSelect" required name="type" class="form-control" >
            <option selected disabled >{{ __('messages.select') }}</option>
            <option {{ $data['drlaw']['type'] == 'doctor' ? 'selected' : '' }} value="doctor" >{{ __('messages.doctor') }}</option>
            <option {{ $data['drlaw']['type'] == 'lawyer' ? 'selected' : '' }} value="lawyer" >{{ __('messages.lawyer') }}</option>
        </select>
    </div>
    <div class="form-group mb-4">
        <label for="type">{{ __('messages.category') }}</label>
        <select id="categorySelect" required name="category_id" class="form-control" >
            <option selected disabled >{{ __('messages.select') }}</option>
            @foreach ($data['categories'] as $cat)
                <option {{ $cat->id == $data['drlaw']['category_id'] ? 'selected' : '' }} value="{{ $cat->id }}">{{ App::isLocale('en') ? $cat->title_en : $cat->title_ar }}</option>
            @endforeach
        </select>
    </div> 
    <div class="form-group mb-4">
        <label for="email">{{ __('messages.email') }}</label>
        <input required type="text" name="email" class="form-control" id="email" placeholder="{{ __('messages.email') }}" value="{{ $data['drlaw']['email'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="phone">{{ __('messages.phone') }}</label>
        <input required type="text" name="phone" class="form-control" id="phone" placeholder="{{ __('messages.phone') }}" value="{{ $data['drlaw']['phone'] }}" >
    </div>
    <div class="form-group mb-4">
        <div class="col-12" >
            <label> {{ __('messages.gender') }} </label>
        </div>
        <div class="col-md-3" >
                <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-text checkbox-primary">
                    <input {{ $data['drlaw']['gender'] == 1 ? 'checked' : '' }}  type="radio" name="gender" value="1" class="new-control-input all-permisssions">
                    <span class="new-control-indicator"></span><span class="new-chk-content">{{ __('messages.male') }}</span>
                </label>
            </div>     
        </div>
        <div class="col-md-3" >
            <div class="n-chk">
                <label class="new-control new-checkbox new-checkbox-text checkbox-primary">
                    <input {{ $data['drlaw']['gender'] == 0 ? 'checked' : '' }}  type="radio" name="gender" value="0" class="new-control-input all-permisssions">
                    <span class="new-control-indicator"></span><span class="new-chk-content">{{ __('messages.female') }}</span>
                </label>
            </div>     
        </div>
        <div class="form-group mb-4">
            <label for="">{{ __('messages.current_image') }}</label><br>
            <img src="https://res.cloudinary.com/ddcmwwmwk/image/upload/w_100,q_100/v1581928924/{{ $data['drlaw']['image_professional_title'] }}"  />
        </div>
        <div class="custom-file-container" data-upload-id="myThirdddImage">
            <label>{{ __('messages.upload') }} ({{ __('messages.image_professional_title') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
            <label class="custom-file-container__custom-file" >
                <input type="file"  name="image_professional_title" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                <span class="custom-file-container__custom-file__custom-file-control"></span>
            </label>
            <div class="custom-file-container__image-preview"></div>
        </div>
        <div class="form-group mb-4">
            <label for="">{{ __('messages.current_image') }}</label><br>
            <img src="https://res.cloudinary.com/ddcmwwmwk/image/upload/w_100,q_100/v1581928924/{{ $data['drlaw']['image_profession_license'] }}"  />
        </div>
        <div class="custom-file-container" data-upload-id="mySecondImage">
            <label>{{ __('messages.upload') }} ({{ __('messages.image_profession_license') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
            <label class="custom-file-container__custom-file" >
                <input type="file"  name="image_profession_license" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                <span class="custom-file-container__custom-file__custom-file-control"></span>
            </label>
            <div class="custom-file-container__image-preview"></div>
        </div>
        <div class="form-group mb-4">
            <label for="professional_title_en">{{ __('messages.professional_title_en') }}</label>
            <input required type="text" name="professional_title_en" class="form-control" id="professional_title_en" placeholder="{{ __('messages.professional_title_en') }}" value="{{ $data['drlaw']['professional_title_en'] }}" >
        </div>
        <div class="form-group mb-4">
            <label for="professional_title_ar">{{ __('messages.professional_title_ar') }}</label>
            <input required type="text" name="professional_title_ar" class="form-control" id="professional_title_ar" placeholder="{{ __('messages.professional_title_ar') }}" value="{{ $data['drlaw']['professional_title_ar'] }}" >
        </div>
        <div class="form-group" >
            <div class="col-12" >
                <label> {{ __('messages.services') }} </label>
            </div>
            <select id="servicesSelect" name="service_id[]" class="form-control tags" multiple="multiple">
                @foreach ($data['services'] as $service)
                    <option {{ in_array($service->id, $data['drlaw_services']) ? 'selected' : '' }} value="{{ $service->id }}" >{{ App::isLocale('en') ? $service->title_en : $service->title_ar }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group mb-4 arabic-direction">
            <label for="about_en">{{ __('messages.about_en') }}</label>
            <textarea id="about_en" required name="about_en" class="form-control" rows="5">{{ $data['drlaw']['about_en'] }}</textarea>
        </div>
        <div class="form-group mb-4 arabic-direction">
            <label for="about_ar">{{ __('messages.about_ar') }}</label>
            <textarea id="about_ar" required name="about_ar" class="form-control" rows="5">{{ $data['drlaw']['about_ar'] }}</textarea>
        </div>
        <div class="form-group mb-4">
            <label for="reservation_type">{{ __('messages.reservation_type') }}</label>
            <select id="reservation_type" required name="reservation_type" class="form-control" >
                <option selected disabled >{{ __('messages.select') }}</option>
                <option {{ $data['drlaw']['reservation_type'] == 'attendance' ? 'selected' : '' }} value="attendance" >{{ __('messages.attendance') }}</option>
                <option {{ $data['drlaw']['reservation_type'] == 'intime' ? 'selected' : '' }} value="intime" >{{ __('messages.intime') }}</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label for="recieving_reservation_phone">{{ __('messages.recieving_reservation_phone') }}</label>
            <input required type="text" name="recieving_reservation_phone" class="form-control" id="recieving_reservation_phone" placeholder="{{ __('messages.recieving_reservation_phone') }}" value="{{ $data['drlaw']['recieving_reservation_phone'] }}" >
        </div>
        <div class="form-group mb-4">
            <label for="reservation_cost">{{ __('messages.reservation_cost') }}</label>
            <input required type="number" step="any" min="0" name="reservation_cost" class="form-control" id="reservation_cost" placeholder="{{ __('messages.reservation_cost') }}" value="{{ $data['drlaw']['reservation_cost'] }}" >
        </div>
    </div>
    <br>
    <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
</form>
</div>

@endsection
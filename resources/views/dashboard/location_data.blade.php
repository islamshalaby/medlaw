@extends('dashboard.app')

@section('title' , __('messages.update_location_data'))


@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.update_location_data') }}</h4>
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
        <label for="city_en">{{ __('messages.city_en') }}</label>
        <input required type="text" name="city_en" class="form-control" id="city_en" placeholder="{{ __('messages.city_en') }}" value="{{ $data['drlaw']['city_en'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="city_ar">{{ __('messages.city_ar') }}</label>
        <input required type="text" name="city_ar" class="form-control" id="city_ar" placeholder="{{ __('messages.city_ar') }}" value="{{ $data['drlaw']['city_ar'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="address_en">{{ __('messages.address_en') }}</label>
        <input required type="text" name="address_en" class="form-control" id="address_en" placeholder="{{ __('messages.address_en') }}" value="{{ $data['drlaw']['address_en'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="address_ar">{{ __('messages.address_ar') }}</label>
        <input required type="text" name="address_ar" class="form-control" id="address_ar" placeholder="{{ __('messages.address_ar') }}" value="{{ $data['drlaw']['address_ar'] }}" >
    </div>
    <div class="form-group mb-4">
        <label for="location_link">{{ __('messages.location_link') }}</label>
        <input required type="text" name="location_link" class="form-control" id="location_link" placeholder="{{ __('messages.location_link') }}" value="" >
    </div>
    <div class="form-group mb-4">
        <label for="">{{ __('messages.current_images') }}</label><br>
        <div class="row">
        @if (count($data['drlaw']->placeImages) > 0)
            @foreach ($data['drlaw']->placeImages as $image)
            <div style="position : relative" class="col-md-2 product_image">
                <a onclick="return confirm('{{ __('messages.are_you_sure') }}')" style="position : absolute; right : 20px" href="#" class="close">x</a>
                <img style="width: 100%" src="https://res.cloudinary.com/ddcmwwmwk/image/upload/w_100,q_100/v1581928924/{{ $image->image }}"  />
            </div>
            @endforeach
        @endif
        </div>
    </div>
    <div class="custom-file-container" data-upload-id="myFirstImage">
        <label>{{ __('messages.upload') }} ({{ __('messages.place_images') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
        <label class="custom-file-container__custom-file" >
            <input type="file" name="place_image[]" class="custom-file-container__custom-file__custom-file-input" multiple accept="image/*">
            <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
            <span class="custom-file-container__custom-file__custom-file-control"></span>
        </label>
        <div class="custom-file-container__image-preview"></div>
    </div>
    <br>
    <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
</form>
</div>

@endsection
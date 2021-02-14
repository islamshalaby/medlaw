@extends('admin.app')

@section('title' , 'Admin Panel Add New Ad')

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_new_ad') }}</h4>
                 </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data" >
            @csrf
            <div class="custom-file-container" data-upload-id="myFirstImage">
                <label>{{ __('messages.upload') }} ({{ __('messages.single_image') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                <label class="custom-file-container__custom-file" >
                    <input type="file" required name="image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                </label>
                <div class="custom-file-container__image-preview"></div>
            </div>

            <div class="form-group mb-4">
                <label for="adplace">{{ __('messages.adplace') }}</label>
                <select required name="place" class="form-control" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                    <option value="1" >{{ __('messages.mainpage') }}</option>
                    <option value="2" >{{ __('messages.secondpage') }}</option>
                </select>
            </div>


            <div class="form-group mb-4">
                <label for="adownertype">{{ __('messages.adownertype') }}</label>
                <select required name="adownertype" class="form-control adownertype" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                    <option value="doctor" >{{ __('messages.doctor') }}</option>
                    <option value="lawyer" >{{ __('messages.lawyer') }}</option>
                </select>
            </div>


            <div class="form-group mb-4">
                <label for="type">{{ __('messages.type') }}</label>
                <select required name="type" class="form-control ad-type-select" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                    <option value="id" >{{ __('messages.insideapp') }}</option>
                    <option value="link" >{{ __('messages.outsideapp') }}</option>
                </select>
            </div>

            
            <div class="form-group mb-4 doctor-select">
                <label for="adowner">{{ __('messages.adowner') }}</label>
                <select required name="content" class="form-control" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                    @for($i = 0; $i < count($data['doctors']); $i++)
                        <option value="{{$data['doctors'][$i]['id']}}" >{{$data['doctors'][$i]['app_name_en']}}</option>
                    @endfor
                </select>
            </div>
            
            <div class="form-group mb-4 lawyer-select">
                <label for="adowner">{{ __('messages.adowner') }}</label>
                <select required name="content" class="form-control" >
                    <option selected disabled >{{ __('messages.select') }}</option>
                    @for($i = 0; $i < count($data['lawyers']); $i++)
                        <option value="{{$data['lawyers'][$i]['id']}}" >{{$data['lawyers'][$i]['app_name_en']}}</option>
                    @endfor
                </select>
            </div>


            <div class="form-group mb-4 link-value">
                <label for="link">{{ __('messages.link') }}</label>
                <input required type="text" name="content" class="form-control" id="link" placeholder="{{ __('messages.link') }}" value="" >
            </div>
            <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
        </form>
    </div>
@endsection
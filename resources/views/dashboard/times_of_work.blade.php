@extends('dashboard.app')

@section('title' , __('messages.update_times_of_work'))

@push('styles')
<style>
    .wizard > .content > .body .select2-search input {
        border : none
    }
    input[disabled] {
        background-color: #eeeeee !important;
    }
    input[name="final_price[]"],
    input[name="total_amount[]"],
    input[name="remaining_amount[]"],
    input[name="barcodes[]"],
    input[name="stored_numbers[]"],
    input[disabled] {
        font-size: 10px
    }
    #properties-items .col-sm-5 {
        margin-bottom: 20px
    }
    .time-range,
    .wizard > .content > .body input[type="checkbox"],
    .add-range {
        display: none
    }
    .addtime,
    .deletetime {
        font-size: 50px;
        cursor: pointer;
    }
    .time-range .col-lg-3
    {
        margin-bottom: 20px
    }
</style>
    
@endpush

@push('scripts')
    <script>
        $(".day_work").change(function(){
            var day = $(this).data('day'),
                reservationType = "{{ $data['drlaw']['reservation_type'] }}"
                
            if($(this).is(':checked')){
                $("." + day).css({"display" : "flex"})
                if (reservationType == "attendance") {
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
            $(this).after(`
            <a class="deletetime">x</a>
            `)
            var cloned = $(this).parent(".time-range").clone(true).find("input").val("").end()
            $(this).show()
            $(this).next('.deletetime').hide()
    
            $(this).parent(".time-range").after(cloned)
        })

        $(".time-range").on("click", ".deletetime", function() {
            $(this).parent(".time-range").remove()
        })
    
    </script>
@endpush

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.update_times_of_work') }}</h4>
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
     {{-- sunday --}}
     <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.sunday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mr-2">
                <input data-day="sunday" class="day_work" {{ $data['sunday'] == 1 ? 'checked' : '' }} name="sunday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    @php
        $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 0 && $time->holiday == 0)
        {{-- sunday work time --}}
        <div style="display: flex" class="row time-range sunday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="sunday_from">{{ __('messages.from') }}</label>
                <input  type="time" name="sunday_from[]" class="form-control" id="sunday_from"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="sunday_to">{{ __('messages.to') }}</label>
                <input  type="time" name="sunday_to[]" class="form-control" id="sunday_to"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="sunday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
        </div>
        @php
        $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['sunday'] == 0)
    <div class="row time-range sunday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="sunday_from">{{ __('messages.from') }}</label>
            <input  type="time" name="sunday_from[]" class="form-control" id="sunday_from"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="sunday_to">{{ __('messages.to') }}</label>
            <input  type="time" name="sunday_to[]" class="form-control" id="sunday_to"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="sunday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif

    


    {{-- monday --}}
    <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.monday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                <input data-day="monday" {{ $data['monday'] == 1 ? 'checked' : '' }} class="day_work" name="monday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
        
    </div>
    @php
    $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 1 && $time->holiday == 0)
        {{-- monday work time --}}
        <div style="display: flex" class="row time-range monday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="monday_from">{{ __('messages.from') }}</label>
                <input  type="time" name="monday_from[]" class="form-control"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="monday_to">{{ __('messages.to') }}</label>
                <input  type="time" name="monday_to[]" class="form-control"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="monday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
        </div>
        @php
        $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['monday'] == 0)
    <div class="row time-range monday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="monday_from">{{ __('messages.from') }}</label>
            <input  type="time" name="monday_from[]" class="form-control"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="monday_to">{{ __('messages.to') }}</label>
            <input  type="time" name="monday_to[]" class="form-control"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="monday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif
    



    {{-- tuesday --}}
    <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.tuesday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                <input data-day="tuesday" {{ $data['tuesday'] == 1 ? 'checked' : '' }} class="day_work" name="tuesday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    @php
    $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 2 && $time->holiday == 0)
        {{-- tuesday work time --}}
        <div style="display: flex" class="row time-range tuesday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="tuesday_from">{{ __('messages.from') }}</label>
                <input  type="time" name="tuesday_from[]" class="form-control"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="tuesday_to">{{ __('messages.to') }}</label>
                <input  type="time" name="tuesday_to[]" class="form-control"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="tuesday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
        </div>
        @php
        $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['tuesday'] == 0)
    <div class="row time-range tuesday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="tuesday_from">{{ __('messages.from') }}</label>
            <input  type="time" name="tuesday_from[]" class="form-control"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="tuesday_to">{{ __('messages.to') }}</label>
            <input  type="time" name="tuesday_to[]" class="form-control"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="tuesday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif
    

    {{-- wednesday --}}
    <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.wednesday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                <input data-day="wdnesday" {{ $data['wdnesday'] == 1 ? 'checked' : '' }} class="day_work" name="wednesday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    @php
    $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 3 && $time->holiday == 0)
        {{-- wednesday work time --}}
        <div style="display: flex" class="row time-range wdnesday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="wednesday_from">{{ __('messages.from') }}</label>
                <input   type="time" name="wednesday_from[]" class="form-control"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="wednesday_to">{{ __('messages.to') }}</label>
                <input  type="time" name="wednesday_to[]" class="form-control"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="wednesday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
            
        </div>
        @php
            $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['wdnesday'] == 0)
    <div class="row time-range wdnesday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="wednesday_from">{{ __('messages.from') }}</label>
            <input   type="time" name="wednesday_from[]" class="form-control"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="wednesday_to">{{ __('messages.to') }}</label>
            <input  type="time" name="wednesday_to[]" class="form-control"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="wednesday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif
    


    {{-- thursday --}}
    <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.thursday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                <input data-day="thrusday" {{ $data['thrusday'] == 1 ? 'checked' : '' }} class="day_work" name="thursday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    @php
    $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 4 && $time->holiday == 0)
        {{-- thursday work time --}}
        <div style="display: flex" class="row time-range thrusday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="thursday_from">{{ __('messages.from') }}</label>
                <input  type="time" name="thursday_from[]" class="form-control"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="thursday_to">{{ __('messages.to') }}</label>
                <input  type="time" name="thursday_to[]" class="form-control"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="thursday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
        </div>
        @php
        $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['thrusday'] == 0)
    <div class="row time-range thrusday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="thursday_from">{{ __('messages.from') }}</label>
            <input  type="time" name="thursday_from[]" class="form-control"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="thursday_to">{{ __('messages.to') }}</label>
            <input  type="time" name="thursday_to[]" class="form-control"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="thursday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif
    


    {{-- friday --}}
    <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.friday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                <input data-day="friday" {{ $data['friday'] == 1 ? 'checked' : '' }} class="day_work" name="friday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    @php
    $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 5 && $time->holiday == 0)
        {{-- friday work time --}}
        <div style="display: flex" class="row time-range friday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="friday_from">{{ __('messages.from') }}</label>
                <input   type="time" name="friday_from[]" class="form-control"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="friday_to">{{ __('messages.to') }}</label>
                <input   type="time" name="friday_to[]" class="form-control"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="friday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
        </div>
        @php
        $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['friday'] == 0)
    <div class="row time-range friday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="friday_from">{{ __('messages.from') }}</label>
            <input   type="time" name="friday_from[]" class="form-control"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="friday_to">{{ __('messages.to') }}</label>
            <input   type="time" name="friday_to[]" class="form-control"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="friday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif
    


    {{-- saturday --}}
    <div class="row" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            {{ __('messages.saturday') }}
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                <input data-day="saturday" {{ $data['saturday'] == 1 ? 'checked' : '' }} class="day_work" name="saturday_work" type="checkbox" >
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    @php
    $i = 0;
    @endphp
    @foreach ($data['drlaw']->times as $time)
        @if($time->day == 6 && $time->holiday == 0)
        {{-- saturday work time --}}
        <div style="display: flex" class="row time-range saturday" >
            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="saturday_from">{{ __('messages.from') }}</label>
                <input   type="time" name="saturday_from[]" class="form-control"  value="{{ $time->from }}" >    
            </div>    

            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                <label for="saturday_to">{{ __('messages.to') }}</label>
                <input   type="time" name="saturday_to[]" class="form-control"  value="{{ $time->to }}" >    
            </div>
            @if($data['drlaw']['reservation_type'] == 'attendance')
            <div class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                <label for="">{{ __('messages.visits_count') }}</label>
                <input  type="number" name="saturday_count[]" class="form-control"  value="{{ $time->count }}" >    
            </div>
            @endif
            @if($i > 0)
            <a class="deletetime">x</a>
            @else
            <a class="addtime">+</a>
            @endif
        </div>
        @php
        $i ++;
        @endphp
        @endif
    @endforeach

    @if($data['saturday'] == 0)
    <div class="row time-range saturday" >
        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="saturday_from">{{ __('messages.from') }}</label>
            <input   type="time" name="saturday_from[]" class="form-control"  value="" >    
        </div>    

        <div class="col-lg-3 col-md-3 col-sm-4 col-6">
            <label for="saturday_to">{{ __('messages.to') }}</label>
            <input   type="time" name="saturday_to[]" class="form-control"  value="" >    
        </div>
        <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
            <label for="">{{ __('messages.visits_count') }}</label>
            <input  type="number" name="saturday_count[]" class="form-control"  value="" >    
        </div>
        <a class="addtime">+</a>
    </div>
    @endif
    <br>
    <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
</form>
</div>

@endsection
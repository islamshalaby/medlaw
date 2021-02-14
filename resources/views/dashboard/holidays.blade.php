@extends('dashboard.app')

@section('title' , __('messages.show_holidays'))

@push('scripts')
    <script>
        var f1 = flatpickr(document.getElementById('rangeCalendarFlatpickr'));
    </script>
@endpush

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.show_holidays') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="post" action="" >
                @csrf
                <div class="form-group mb-4">
                   <label for="city_en">{{ __('messages.day') }}</label>
                   <input id="rangeCalendarFlatpickr" class="form-control flatpickr flatpickr-input active" type="text" name="date" placeholder="Select Date..">
               </div>
               <br>
                <input type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">
            </form>
            <div class="table-responsive"> 
                <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>{{ __('messages.date') }}</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['holidays'] as $holiday)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ $holiday->date }}</td>
                                
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>  

@endsection
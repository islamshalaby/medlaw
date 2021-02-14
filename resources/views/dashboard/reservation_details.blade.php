@extends('dashboard.app')

@section('title' , __('messages.reservation_details'))

@section('content')

        <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.reservation_details') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive"> 
                <table class="table table-bordered mb-4">
                    <tbody>
                            <tr>
                                <td class="label-table" > {{ __('messages.user_name') }}</td>
                                <td>{{ $data['reservation']['user_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.user_phone') }} </td>
                                <td>{{ $data['reservation']['phone'] }}</td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.attend_status') }} </td>
                                <td> {{ $data['reservation']['user_confirm'] == 1 ? __('messages.confirm_attendence') : __('messages.confirm_not_attend') }} </td>
                            </tr>
                           
                            <tr>
                                <td class="label-table" > {{ __('messages.reservation_for') }} </td>
                                <td> {{ $data['reservation']['reservation_for'] == 'accountowner' ? __('messages.account_owner') : __('messages.another_person') }} </td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.date') }} </td>
                                <td> {{ $data['reservation']['date'] }} </td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.time') }} </td>
                                <td> {{ $data['reservation']['time'] }} </td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.resrvation_period') }} </td>
                                <td> {{ __("messages.from") . ": " . $data['reservation']->workTime->from . " " . __('messages.to') . ": " . $data['reservation']->workTime->to }} </td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.reservation_cost') }} </td>
                                <td> {{ $data['reservation']['cost'] . " " . __('messages.dinar') }} </td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.payment_method') }} </td>
                                <td> {{ $data['reservation']['payment_method'] }} </td>
                            </tr>
                            <tr>
                                <td class="label-table" > {{ __('messages.status') }} </td>
                                <td> 
                                    @if($data['reservation']['status'] == 1)
                                    {{ __('messages.reserved') }}
                                    @elseif($data['reservation']['status'] == 2)
                                    {{ __('messages.reservation_completed') }}
                                    @elseif($data['reservation']['status'] == 3)
                                    {{ __('messages.rated') }}
                                    @elseif($data['reservation']['status'] == 4)
                                    {{ __('messages.user_not_come') }}
                                    @elseif($data['reservation']['status'] == 5)
                                    {{ __('messages.user_cancel') }}
                                    @elseif($data['reservation']['status'] == 6)
                                    {{ __('messages.dr_law_cancel') }}
                                    @endif
                                </td>
                            </tr>
                            @if($data['reservation']['status'] == 5)
                            <tr>
                                <td class="label-table" > {{ __('messages.cancel_reason') }} </td>
                                <td> {{ $data['reservation']['user_cancell_reason'] }} </td>
                            </tr>
                            @endif
                            
                    </tbody>
                </table>
            </div>

        </div>
    </div>  

@endsection




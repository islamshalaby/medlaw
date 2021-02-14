@extends('dashboard.app')

@section('title' , __('messages.show_reservations'))

@push('scripts')
<script>
    var dTbls = $('#order-tbl').DataTable( {
        dom: 'Blfrtip',
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn', footer: true, exportOptions: {
                    columns: ':visible',
                    rows: ':visible'
                }},
                { extend: 'csv', className: 'btn', footer: true, exportOptions: {
                    columns: ':visible',
                    rows: ':visible'
                } },
                { extend: 'excel', className: 'btn', footer: true, exportOptions: {
                    columns: ':visible',
                    rows: ':visible'
                } },
                { extend: 'print', className: 'btn', footer: true, 
                    exportOptions: {
                        columns: ':visible',
                        rows: ':visible'
                    },customize: function(win) {
                        {{--  $(win.document.body).prepend(`<br /><h4 style="border-bottom: 1px solid; padding : 10px">test</h4>`); //before the table  --}}
                      }
                }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page _PAGE_ of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
           "sLengthMenu": "Results :  _MENU_",
        },
        "stripeClasses": [],
        "lengthMenu": [50, 100, 1000, 10000, 100000, 1000000, 2000000, 3000000, 4000000, 5000000],
        "pageLength": 50  
    } );
</script>
<script>
    var price = dTbls.column(2).data(),
        dinar = "{{ __('messages.dinar') }}"
    var totalPrice = parseFloat(price.reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0)).toFixed(2)

    $("#order-tbl tfoot").find('th').eq(2).text(`${totalPrice} ${dinar}`);
    
</script>
    
@endpush

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="container">
                    <div class="row">
                
                        <form style="width: 100%" id="areaForm" method="">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="user_name">{{ __('messages.user_name') }}</label>
                                    <input type="text" name="user_name" class="form-control" id="user_name" placeholder="{{ __('messages.user_name') }}" value="{{ isset($data['username']) ? $data['username'] : '' }}" >
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label for="dr_lawyer">{{ __('messages.status') }}</label>
                                    <select name="status" class="form-control" >
                                        <option selected disabled >{{ __('messages.select') }}</option>
                                        <option {{ isset($data['status']) && $data['status'] == 1 ? 'selected' : '' }} value="1" >{{ __('messages.reserved') }}</option>
                                        <option {{ isset($data['status']) && $data['status'] == 2 ? 'selected' : '' }} value="2" >{{ __('messages.reservation_completed') }}</option>
                                        <option {{ isset($data['status']) && $data['status'] == 3 ? 'selected' : '' }} value="3" >{{ __('messages.rated') }}</option>
                                        <option {{ isset($data['status']) && $data['status'] == 4 ? 'selected' : '' }} value="4" >{{ __('messages.user_not_come') }}</option>
                                        <option {{ isset($data['status']) && $data['status'] == 5 ? 'selected' : '' }} value="5" >{{ __('messages.user_cancel') }}</option>
                                        <option {{ isset($data['status']) && $data['status'] == 6 ? 'selected' : '' }} value="6" >{{ __('messages.dr_law_cancel') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-5">
                                    <label for="from">{{ __('messages.from') }}</label>
                                    <input type="date" name="from" class="form-control" id="from" placeholder="{{ __('messages.from') }}" value="{{ isset($data['from']) ? $data['from'] : '' }}" >
                                </div>
                                <div class="form-group col-md-5">
                                    <label for="to">{{ __('messages.to') }}</label>
                                    <input type="date" name="to" class="form-control" id="to" placeholder="{{ __('messages.to') }}" value="{{ isset($data['to']) ? $data['to'] : '' }}" >
                                </div>
                                <div class="form-group col-md-2">
                                    <input style="margin-top: 35px;" type="submit" value="{{ __('messages.filter') }}" class="btn btn-primary">
                                </div>
                            </div>
                                
                        </form>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.show_reservations') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive"> 
                <table id="order-tbl" class="table table-hover non-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.user_name') }}</th>
                            <th>{{ __('messages.reservation_cost') }}</th>
                            <th>{{ __('messages.payment_method') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.date') }}</th> 
                            <th class="text-center">{{ __('messages.details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['reservations'] as $reservation)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ $reservation->user_name }}</td>
                                <td>{{ $reservation->cost . " " . __('messages.dinar') }}</td>
                                <td>
                                    {{ $reservation->payment_method }}
                                </td>
                                <td>
                                    @if($reservation->status == 1)
                                    {{ __('messages.reserved') }}
                                    @elseif($reservation->status == 2)
                                    {{ __('messages.reservation_completed') }}
                                    @elseif($reservation->status == 3)
                                    {{ __('messages.rated') }}
                                    @elseif($reservation->status == 4)
                                    {{ __('messages.user_not_come') }}
                                    @elseif($reservation->status == 5)
                                    {{ __('messages.user_cancel') }}
                                    @elseif($reservation->status == 6)
                                    {{ __('messages.dr_law_cancel') }}
                                    @endif
                                </td>
                                <td>{{ $reservation->date }}</td>
                                
                                <td class="text-center blue-color" ><a href="{{ route('dashboard.services.details', $reservation->id) }}" ><i class="far fa-eye"></i></a></td>
                                
                                                             
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                          <th>{{ __('messages.total_cost') }}:</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        {{-- <div class="paginating-container pagination-solid">
            <ul class="pagination">
                <li class="prev"><a href="{{$data['categories']->previousPageUrl()}}">Prev</a></li>
                @for($i = 1 ; $i <= $data['categories']->lastPage(); $i++ )
                    <li class="{{ $data['categories']->currentPage() == $i ? "active" : '' }}"><a href="/admin-panel/categories/show?page={{$i}}">{{$i}}</a></li>               
                @endfor
                <li class="next"><a href="{{$data['categories']->nextPageUrl()}}">Next</a></li>
            </ul>
        </div>   --}}
        
    </div>  

@endsection
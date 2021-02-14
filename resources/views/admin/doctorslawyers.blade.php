@extends('admin.app')

@section('title' , __('messages.show_doctors&lawyers'))

@section('content')
    <div id="tableSimple" class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.show_doctors&lawyers') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive"> 
                <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.phone') }}</th>
                            <th>{{ __('messages.specialization') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            @if(Auth::user()->update_data) 
                                <th class="text-center">{{ __('messages.edit') }}</th>                          
                            @endif
                            @if(Auth::user()->delete_data) 
                                <th class="text-center">{{ __('messages.delete') }}</th>                          
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($data['doctorslawyers'] as $doclaw)
                            <tr>
                                <td><?=$i;?></td>
                                <td>{{ $doclaw->first_name . " " . $doclaw->last_name }}</td>
                                <td>{{ $doclaw->phone }}</td>
                                <td>{{ App::isLocale('en') ? $doclaw->professional_title_en : $doclaw->professional_title_ar }}</td>
                                <td>{{ App::isLocale('en') ? $doclaw->category->title_en : $doclaw->category->title_ar }}</td>
                                <td>{{ $doclaw->active == 1 ? __('messages.actived') : __('messages.blocked') }} 
                                    @if($doclaw->active == 1)
                                    <a onclick="return confirm('Are you sure?');" href="{{ route('doctors&lawyers.adminApprove', [$doclaw->id, 0]) }}">
                                        <span class="badge badge-danger">{{ __('messages.block') }}</span>
                                    </a>
                                    @else
                                    <a onclick="return confirm('Are you sure?');" href="{{ route('doctors&lawyers.adminApprove', [$doclaw->id, 1]) }}">
                                        <span class="badge badge-success">{{ __('messages.active') }}</span>
                                    </a>
                                    @endif
                                </td>
                                @if(Auth::user()->update_data) 
                                    <td class="text-center blue-color" ><a href="{{ route('doctors&lawyers.edit', $doclaw->id) }}" ><i class="far fa-edit"></i></a></td>
                                @endif
                                @if(Auth::user()->delete_data) 
                                    <td class="text-center blue-color" ><a onclick="return confirm('Are you sure you want to delete this item?');" href="/admin-panel/categories/delete/{{ $doclaw->id }}" ><i class="far fa-trash-alt"></i></a></td>
                                @endif                                
                                <?php $i++; ?>
                            </tr>
                        @endforeach
                    </tbody>
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
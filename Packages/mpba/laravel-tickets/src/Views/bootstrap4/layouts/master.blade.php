@extends($master)

@section('content')
    @include('ticket::shared.header')

    <div class="container">
        <div class="card mb-3">
            <div class="card-body">
                @include('ticket::shared.nav')
            </div>
        </div>
        @if(View::hasSection('ticket_content'))
            <div class="card">
                <h5 class="card-header d-flex justify-content-between align-items-baseline flex-wrap">
                    @if(View::hasSection('page_title'))
                        <span>@yield('page_title')</span>
                    @else
                        <span>@yield('page')</span>
                    @endif

                    @yield('ticket_header')
                </h5>
                <div class="card-body @yield('ticket_content_parent_class')">
                    @yield('ticket_content')
                </div>
            </div>
        @endif
        @yield('ticket_extra_content')
    </div>
@stop

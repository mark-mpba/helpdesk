@extends('ticket::layouts.master')

@section('page', trans('ticket::admin.config-index-title'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.configuration.create',
    trans('ticket::admin.btn-create-new-config'), null,
    ['class' => 'btn btn-primary'])
!!}
@stop

@section('ticket_content_parent_class', 'pl-0 pr-0 pb-0')

@section('ticket_content')
<!-- configuration -->
    @if($configurations->isEmpty())
        <div class="text-center">{{ trans('ticket::admin.config-index-no-settings') }}</div>
    @else
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#init-configs">{{ trans('ticket::admin.config-index-initial') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ticket-configs">{{ trans('ticket::admin.config-index-tickets') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#email-configs">{{ trans('ticket::admin.config-index-notifications') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#perms-configs">{{ trans('ticket::admin.config-index-permissions') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#editor-configs">{{ trans('ticket::admin.config-index-editor') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#other-configs">{{ trans('ticket::admin.config-index-other') }}</a></li>
        </ul>

        <div class="tab-content">
            <div id="init-configs" class="tab-pane fade show active">
                @include('ticket::admin.configuration.tables.init_table')
            </div>
            <div id="ticket-configs" class="tab-pane fade">
                @include('ticket::admin.configuration.tables.ticket_table')
            </div>
            <div id="email-configs" class="tab-pane fade">
                @include('ticket::admin.configuration.tables.email_table')
            </div>
            <div id="perms-configs" class="tab-pane fade">
                @include('ticket::admin.configuration.tables.perms_table')
            </div>
            <div id="editor-configs" class="tab-pane fade">
                @include('ticket::admin.configuration.tables.editor_table')
            </div>
            <div id="other-configs" class="tab-pane fade">
                @include('ticket::admin.configuration.tables.other_table')
            </div>
        </div>
    @endif
<!-- // Configuration -->
@endsection

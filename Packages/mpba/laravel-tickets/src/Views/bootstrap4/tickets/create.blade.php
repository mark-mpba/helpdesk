@extends('ticket::layouts.master')
@section('page', trans('ticket::lang.create-ticket-title'))
@section('page_title', trans('ticket::lang.create-new-ticket'))

@section('ticket_content')
    {!! CollectiveForm::open(['route'=>$setting->grab('main_route').'.store','method' => 'POST', 'files' => true]) !!}
    <div class="form-group row">
        {!! CollectiveForm::label('subject', trans('ticket::lang.subject') . trans('ticket::lang.colon'), ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col">
            {!! CollectiveForm::text('subject', null, ['class' => 'form-control', 'required' => 'required']) !!}
            <small class="form-text text-muted">{!! trans('ticket::lang.create-ticket-brief-issue') !!}</small>
        </div>
    </div>
    <div class="form-group row">
        {!! CollectiveForm::label('content', trans('ticket::lang.description') . trans('ticket::lang.colon'), ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col">
            {!! CollectiveForm::textarea('content', null, ['class' => 'form-control summernote-editor', 'rows' => '5', 'required' => 'required']) !!}
            <small class="form-text text-muted">{!! trans('ticket::lang.create-ticket-describe-issue') !!}</small>
        </div>
    </div>
    <div class="form-group row ml-1">
        {!! CollectiveForm::label('priority', trans('ticket::lang.priority') . trans('ticket::lang.colon'), ['class' => 'col-form-label']) !!}
        <div class="col-3">
            {!! CollectiveForm::select('priority_id', $priorities, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        {!! CollectiveForm::label('category', trans('ticket::lang.category') . trans('ticket::lang.colon'), ['class' => 'col-form-label']) !!}
        <div class="col-3">
            {!! CollectiveForm::select('category_id', $categories, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        {!! CollectiveForm::label('project', trans('ticket::lang.project') . trans('ticket::lang.colon'), ['class' => 'col-form-label']) !!}
        <div class="col-3">
            {!! CollectiveForm::select('project_id', $projects, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    {!! CollectiveForm::hidden('agent_id', 'auto') !!}
    <br>
    <div style="float: right;" class="form-group row">
        {!! link_to_route($setting->grab('main_route').'.index', trans('ticket::lang.btn-back'), null, ['class' => 'btn btn-lin k']) !!}
        {!! CollectiveForm::submit(trans('ticket::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
    </div>
    <div class="mb-2" style="max-width: 300px;">
        <label class="btn btn-primary rounded-pill px-3 py-2 mb-2">
            <i class="fa fa-paperclip me-1"></i> Attachments
            <input type="file" name="attachments[]" class="d-none" multiple>
        </label>
        <input type="file" name="attachments[]"
               class="form-control form-control-lg"
               multiple>
        <small class="text-muted">You can select multiple files</small>
    </div>
    {!! CollectiveForm::close() !!}
@endsection

@section('footer')
    @include('ticket::tickets.partials.summernote')
@append

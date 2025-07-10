@extends($master)
@section('page', trans('ticket::lang.create-ticket-title'))

@section('content')
@include('ticket::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::open([
                        'route'=>$setting->grab('main_route').'.store',
                        'method' => 'POST',
                        'class' => 'form-horizontal'
                        ]) !!}
            <legend>{!! trans('ticket::lang.create-new-ticket') !!}</legend>
            <div class="form-group">
                {!! CollectiveForm::label('subject', trans('ticket::lang.subject') . trans('ticket::lang.colon'), ['class' => 'col-lg-2 control-label']) !!}
                <div class="col-lg-10">
                    {!! CollectiveForm::text('subject', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    <span class="help-block">{!! trans('ticket::lang.create-ticket-brief-issue') !!}</span>
                </div>
            </div>
            <div class="form-group">
                {!! CollectiveForm::label('content', trans('ticket::lang.description') . trans('ticket::lang.colon'), ['class' => 'col-lg-2 control-label']) !!}
                <div class="col-lg-10">
                    {!! CollectiveForm::textarea('content', null, ['class' => 'form-control summernote-editor', 'rows' => '5', 'required' => 'required']) !!}
                    <span class="help-block">{!! trans('ticket::lang.create-ticket-describe-issue') !!}</span>
                </div>
            </div>
            <div class="form-inline row">
                <div class="form-group col-lg-4">
                    {!! CollectiveForm::label('priority', trans('ticket::lang.priority') . trans('ticket::lang.colon'), ['class' => 'col-lg-6 control-label']) !!}
                    <div class="col-lg-6">
                        {!! CollectiveForm::select('priority_id', $priorities, null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    {!! CollectiveForm::label('category', trans('ticket::lang.category') . trans('ticket::lang.colon'), ['class' => 'col-lg-6 control-label']) !!}
                    <div class="col-lg-6">
                        {!! CollectiveForm::select('category_id', $categories, null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
                {!! CollectiveForm::hidden('agent_id', 'auto') !!}
            </div>
            <br>
            <div class="form-group">
                <div class="col-lg-10 col-lg-offset-2">
                    {!! link_to_route($setting->grab('main_route').'.index', trans('ticket::lang.btn-back'), null, ['class' => 'btn btn-default']) !!}
                    {!! CollectiveForm::submit(trans('ticket::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        {!! CollectiveForm::close() !!}
    </div>
@endsection

@section('footer')
    @include('ticket::tickets.partials.summernote')
@append

@extends('ticket::layouts.master')
@section('page', trans('ticket::lang.create-ticket-title'))
@section('page_title', trans('ticket::lang.create-new-ticket'))

@section('ticket_content')
    {!! CollectiveForm::open(['route'=>'test.store','method' => 'POST']) !!}

    <div class="form-group row">
        {!! CollectiveForm::label('step','Step', ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col-1">
            {!! CollectiveForm::number('step', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! CollectiveForm::label('name','Test Name', ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col-8">
            {!! CollectiveForm::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! CollectiveForm::label('details','Test Details', ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col-8">
            {!! CollectiveForm::textarea('details', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>


    <div class="form-group row">
        {!! CollectiveForm::label('outcome','Expected Outcome', ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col-8">
            {!! CollectiveForm::textarea('outcome', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! CollectiveForm::label('actual','Actual Results', ['class' => 'col-lg-2 col-form-label']) !!}
        <div class="col-8">
            {!! CollectiveForm::textarea('actual', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="form-group row align-items-center">
        {!! CollectiveForm::label('passed', 'Passed', ['class' => 'col-lg-2 col-form-label']) !!}

        <div class="col-lg-2">
            {{-- Hidden input ensures 0 is submitted if switch is off --}}
            <input type="hidden" name="passed" value="0">

            <div class="form-check form-switch d-flex align-items-center">
                <input type="checkbox"
                       class="form-check-input me-2"
                       id="passed"
                       name="passed"
                       value="1"
                    {{ old('passed') == 1 ? 'checked' : '' }}>

                <label class="form-check-label mb-0" for="actual">
                    <span id="actual-label">{{ old('passed') == 1 ? 'Pass' : 'Fail' }}</span>
                </label>
            </div>
        </div>
    </div>

    <div style="float: right;" class="form-group row">
        {!! link_to_route($setting->grab('main_route').'.index', trans('ticket::lang.btn-back'), null, ['class' => 'btn btn-lin k']) !!}
        {!! CollectiveForm::submit(trans('ticket::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
    </div>
    {!! CollectiveForm::close() !!}
@endsection



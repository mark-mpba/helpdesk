@extends('ticket::layouts.master')
@section('page', trans('ticket::admin.config-create-subtitle'))

@section('ticket_header')
{!! link_to_route(
    $setting->grab('admin_route').'.configuration.index',
    trans('ticket::admin.btn-back'), null,
    ['class' => 'btn btn-secondary'])
!!}
@stop

@section('ticket_content')
    {!! CollectiveForm::open(['route' => $setting->grab('admin_route').'.configuration.store']) !!}

        <!-- Slug Field -->
        <div class="form-group row">
            {!! CollectiveForm::label('slug', trans('ticket::admin.config-edit-slug') . trans('ticket::admin.colon'), ['class' => 'col-sm-3 col-form-label']) !!}
            <div class="col-sm-9">
                {!! CollectiveForm::text('slug', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <!-- Default Field -->
        <div class="form-group row">
            {!! CollectiveForm::label('default', trans('ticket::admin.config-edit-default') . trans('ticket::admin.colon'), ['class' => 'col-sm-3 col-form-label']) !!}
            <div class="col-sm-9">
                {!! CollectiveForm::text('default', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <!-- Value Field -->
        <div class="form-group row">
            {!! CollectiveForm::label('value', trans('ticket::admin.config-edit-value') . trans('ticket::admin.colon'), ['class' => 'col-sm-3 col-form-label']) !!}
            <div class="col-sm-9">
                {!! CollectiveForm::text('value', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <!-- Lang Field -->
        <div class="form-group row">
            {!! CollectiveForm::label('lang', trans('ticket::admin.config-edit-language') . trans('ticket::admin.colon'), ['class' => 'col-sm-3 col-form-label']) !!}
            <div class="col-sm-9">
                {!! CollectiveForm::text('lang', null, ['class' => 'form-control']) !!}

            </div>
        </div>

        <!-- Submit Field -->
        <div class="form-group row">
            <div class="col-sm-10 offset-sm-3">
              {!! CollectiveForm::submit(trans('ticket::admin.btn-submit'), ['class' => 'btn btn-primary']) !!}
            </div>
        </div>

    {!! CollectiveForm::close() !!}
@stop

@section('footer')
    <script>
      $(document).ready(function() {
        $("#slug").bind('change', function() {
          var slugger = $('#slug').val();
              slugger = slugger
              .replace(/\W/g, '.')
              .toLowerCase();
          $("#slug").val(slugger);
        });

        $("#default").bind('keyup blur keypress change', function() {
          var duplicate = $('#default').val();
          $("#value").val(duplicate);
        });
      });
    </script>
@append

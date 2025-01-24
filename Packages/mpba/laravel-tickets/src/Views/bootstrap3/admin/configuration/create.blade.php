@extends($master)

@section('page')
    {{ trans('ticket::admin.config-create-subtitle') }}
@stop

@section('content')
    @include('ticket::shared.header')
     <div class="panel panel-default">
      <div class="panel-heading">
        <h3>{{ trans('ticket::admin.config-create-title') }}
          <div class="panel-nav pull-right" style="margin-top: -7px;">
              {!! link_to_route(
                  $setting->grab('admin_route').'.configuration.index',
                  trans('ticket::admin.btn-back'), null,
                  ['class' => 'btn btn-default'])
              !!}
          </div>
        </h3>
      </div>
      <div class="panel-body">
        <div class="form-horizontal">
{!! CollectiveForm::open(['route' => $setting->grab('admin_route').'.configuration.store']) !!}

            <!-- Slug Field -->
            <div class="form-group">
                {!! CollectiveForm::label('slug', trans('ticket::admin.config-edit-slug') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-9">
                    {!! CollectiveForm::text('slug', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Default Field -->
            <div class="form-group">
                {!! CollectiveForm::label('default', trans('ticket::admin.config-edit-default') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-9">
                    {!! CollectiveForm::text('default', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Value Field -->
            <div class="form-group">
                {!! CollectiveForm::label('value', trans('ticket::admin.config-edit-value') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-9">
                    {!! CollectiveForm::text('value', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Lang Field -->
            <div class="form-group">
                {!! CollectiveForm::label('lang', trans('ticket::admin.config-edit-language') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-9">
                    {!! CollectiveForm::text('lang', null, ['class' => 'form-control']) !!}

                </div>
            </div>

            <!-- Submit Field -->
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  {!! CollectiveForm::submit(trans('ticket::admin.btn-submit'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div>

          {!! CollectiveForm::close() !!}
        </div>
      </div>
      <div class="panel-footer">
      </div>
    </div>

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

@stop

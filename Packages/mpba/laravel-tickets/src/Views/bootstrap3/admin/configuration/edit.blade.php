@extends($master)

@section('page')
    {{ trans('ticket::admin.config-edit-subtitle') }}
@stop

@section('content')
    @include('ticket::shared.header')
     <div class="panel panel-default">
      <div class="panel-heading">
        <h3>{{ trans('ticket::admin.config-edit-title') }}
          <div class="panel-nav pull-right" style="margin-top: -7px;">
              {!! link_to_route(
                  $setting->grab('admin_route').'.configuration.index',
                  trans('ticket::admin.btn-back'), null,
                  ['class' => 'btn btn-default'])
              !!}
              {{--
              {!! link_to_route(
                  $setting->grab('admin_route').'.configuration.create',
                  trans('ticket::admin.btn-create-new-config'), null,
                  ['class' => 'btn btn-primary'])
              !!}
              --}}
          </div>
        </h3>
      </div>
      <div class="panel-body">
        <div class="form-horizontal">
{!! CollectiveForm::model($configuration, ['route' => [$setting->grab('admin_route').'.configuration.update', $configuration->id], 'method' => 'patch']) !!}
             <div class="well">
                 <b>{{ trans('ticket::admin.config-edit-tools') }}</b><br>
                 <a href="https://www.functions-online.com/unserialize.html" target="_blank">
                     {{ trans('ticket::admin.config-edit-unserialize') }}
                 </a>
                 <br>
                 <a href="https://www.functions-online.com/serialize.html" target="_blank">
                     {{ trans('ticket::admin.config-edit-serialize') }}
                 </a>
             </div>

            @if(trans("ticket::settings." . $configuration->slug) != ("ticket::settings." . $configuration->slug) && trans("ticket::settings." . $configuration->slug))
                <div class="panel panel-info">
                    <div class="panel-body">{!! trans("ticket::settings." . $configuration->slug) !!}</div>
                </div>
            @endif

              <!-- ID Field -->
              <div class="form-group">
                  {!! CollectiveForm::label('id', trans('ticket::admin.config-edit-id') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                  <div class="col-sm-9">
                      {!! CollectiveForm::text('id', null, ['class' => 'form-control', 'disabled']) !!}
                  </div>
              </div>

              <!-- Slug Field -->
              <div class="form-group">
                  {!! CollectiveForm::label('slug', trans('ticket::admin.config-edit-slug') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                  <div class="col-sm-9">
                      {!! CollectiveForm::text('slug', null, ['class' => 'form-control', 'disabled']) !!}
                  </div>
              </div>

              <div class="form-group">
                  {!! CollectiveForm::label('default', trans('ticket::admin.config-edit-default') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                  <div class="col-sm-9">
                      @if(!$default_serialized)
                          {!! CollectiveForm::text('default', null, ['class' => 'form-control', 'disabled']) !!}
                      @else
                          <pre>{{var_export(unserialize($configuration->default), true)}}</pre>
                      @endif
                  </div>
              </div>


              <!-- Value Field -->
              <div class="form-group">
                  {!! CollectiveForm::label('value', trans('ticket::admin.config-edit-value') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                  <div class="col-sm-9">
                      @if(!$should_serialize)
                            {!! CollectiveForm::text('value', null, ['class' => 'form-control']) !!}
                      @else
                          {!! CollectiveForm::textarea('value', var_export(unserialize($configuration->value), true), ['class' => 'form-control']) !!}
                      @endif
                  </div>
              </div>

            <!-- Serialize Field -->
            <div class="form-group">
                {!! CollectiveForm::label('serialize', trans('ticket::admin.config-edit-should-serialize') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-9">
                    {!! CollectiveForm::checkbox('serialize', 1, $should_serialize, ['class' => 'form-control', 'onchange' =>  'changeSerialize(this)',]) !!}
                    <span class="help-block" style="color: red;">@lang('ticket::admin.config-edit-eval-warning') <code>eval('$value = serialize(' . $value . ');')</code></span>
                </div>
            </div>

            <!-- Password Field -->
            <div id="serialize-password" class="form-group">
                {!! CollectiveForm::label('password', trans('ticket::admin.config-edit-reenter-password') . trans('ticket::admin.colon'), ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-9">
                    {!! CollectiveForm::password('password', ['class' => 'form-control']) !!}
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
    </div>

    <script>
        function changeSerialize(e){
            document.querySelector("#serialize-password").style.display = e.checked ? 'block' : 'none';
            document.querySelector(".help-block").style.display = e.checked ? 'block' : 'none';
        }

        changeSerialize(document.querySelector("input[name='serialize']"));


    </script>


    @if($should_serialize)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/{{ mpba\Tickets\Helpers\Cdn::CodeMirror }}/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/{{ mpba\Tickets\Helpers\Cdn::CodeMirror }}/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/{{ mpba\Tickets\Helpers\Cdn::CodeMirror }}/mode/php/php.min.js"></script>


    <script>

        loadCSS({!! '"'.asset('https://cdnjs.cloudflare.com/ajax/libs/codemirror/' . mpba\Tickets\Helpers\Cdn::CodeMirror . '/codemirror.min.css').'"' !!});
        loadCSS({!! '"'.asset('https://cdnjs.cloudflare.com/ajax/libs/codemirror/' . mpba\Tickets\Helpers\Cdn::CodeMirror . '/theme/monokai.min.css').'"' !!});

        window.addEventListener('load', function(){
            CodeMirror.fromTextArea( document.querySelector("textarea[name='value']"), {
                lineNumbers: true,
                mode: 'text/x-php',
                theme: 'monokai',
                indentUnit: 2,
                lineWrapping: true
            });
        });

    </script>
    @endif

@stop

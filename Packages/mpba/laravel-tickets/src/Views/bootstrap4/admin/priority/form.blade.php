<div class="form-group">
    {!! CollectiveForm::label('name', trans('ticket::admin.priority-create-name') . trans('ticket::admin.colon'), ['class' => '']) !!}
    {!! CollectiveForm::text('name', isset($priority->name) ? $priority->name : null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! CollectiveForm::label('color', trans('ticket::admin.priority-create-color') . trans('ticket::admin.colon'), ['class' => '']) !!}

    {!! CollectiveForm::custom('color', 'color', isset($priority->color) ? $priority->color : "#000000", ['class' => 'form-control']) !!}
</div>

{!! link_to_route($setting->grab('admin_route').'.priority.index', trans('ticket::admin.btn-back'), null, ['class' => 'btn btn-link']) !!}
@if(isset($priority))
    {!! CollectiveForm::submit(trans('ticket::admin.btn-update'), ['class' => 'btn btn-primary']) !!}
@else
    {!! CollectiveForm::submit(trans('ticket::admin.btn-submit'), ['class' => 'btn btn-primary']) !!}
@endif

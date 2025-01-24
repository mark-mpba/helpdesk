<div class="form-group">
    {!! CollectiveForm::label('name', trans('ticket::admin.status-create-name') . trans('ticket::admin.colon'), ['class' => '']) !!}
    {!! CollectiveForm::text('name', isset($status->name) ? $status->name : null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! CollectiveForm::label('color', trans('ticket::admin.status-create-color') . trans('ticket::admin.colon'), ['class' => '']) !!}
    {!! CollectiveForm::custom('color', 'color', isset($status->color) ? $status->color : "#000000", ['class' => 'form-control']) !!}
</div>

{!! link_to_route($setting->grab('admin_route').'.status.index', trans('ticket::admin.btn-back'), null, ['class' => 'btn btn-link']) !!}
@if(isset($status))
    {!! CollectiveForm::submit(trans('ticket::admin.btn-update'), ['class' => 'btn btn-primary']) !!}
@else
    {!! CollectiveForm::submit(trans('ticket::admin.btn-submit'), ['class' => 'btn btn-primary']) !!}
@endif

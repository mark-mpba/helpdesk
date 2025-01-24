<div class="form-group">
    {!! CollectiveForm::label('title', trans('ticket::projects.project.create-name') . trans('ticket::admin.colon'), ['class' => '']) !!}
    {!! CollectiveForm::text('title', isset($project->title) ? $project->title : null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! CollectiveForm::label('color', trans('ticket::projects.project.create-color') . trans('ticket::admin.colon'), ['class' => '']) !!}

    {!! CollectiveForm::custom('color', 'color', isset($project->color) ? $project->color : "#000000", ['class' => 'form-control']) !!}
</div>

{!! link_to_route($setting->grab('admin_route').'.projects.index', trans('ticket::admin.btn-back'), null, ['class' => 'btn btn-link']) !!}
@if(isset($project))
    {!! CollectiveForm::submit(trans('ticket::admin.btn-update'), ['class' => 'btn btn-primary']) !!}
@else
    {!! CollectiveForm::submit(trans('ticket::admin.btn-submit'), ['class' => 'btn btn-primary']) !!}
@endif

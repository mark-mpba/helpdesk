<div class="modal fade" id="ticket-edit-modal" tabindex="-1" role="dialog" aria-labelledby="ticket-edit-modal-Label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! CollectiveForm::model($ticket, [
                 'route' => [$setting->grab('main_route').'.update', $ticket->id],
                 'method' => 'PATCH',
                 'class' => 'form-horizontal'
             ]) !!}
            <div class="modal-header">
                <h5 class="modal-title" id="ticket-edit-modal-Label">{{ $ticket->subject }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">{{ trans('ticket::lang.flash-x') }}</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! CollectiveForm::text('subject', $ticket->subject, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    <textarea class="form-control summernote-editor" rows="5" required name="content" cols="50">{!! htmlspecialchars($ticket->html) !!}</textarea>
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('project_id', trans('ticket::lang.project') . trans('ticket::lang.colon'), ['class' => '']) !!}
                    {!! CollectiveForm::select('project_id', $project_lists, $ticket->project_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('priority_id', trans('ticket::lang.priority') . trans('ticket::lang.colon'), ['class' => '']) !!}
                    {!! CollectiveForm::select('priority_id', $priority_lists, $ticket->priority_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('agent_id', trans('ticket::lang.agent') . trans('ticket::lang.colon'), [
                        'class' => ''
                    ]) !!}
                    {!! CollectiveForm::select(
                        'agent_id',
                        $agent_lists,
                        $ticket->agent_id,
                        ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('category_id',  trans('ticket::lang.category') . trans('ticket::lang.colon'), [
                        'class' => ''
                    ]) !!}
                    {!! CollectiveForm::select('category_id', $category_lists, $ticket->category_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('status_id', trans('ticket::lang.status') . trans('ticket::lang.colon'), [
                        'class' => ''
                    ]) !!}
                    {!! CollectiveForm::select('status_id', $status_lists, $ticket->status_id, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('ticket::lang.btn-close') }}</button>
                {!! CollectiveForm::submit(trans('ticket::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
            </div>
            {!! CollectiveForm::close() !!}
        </div>
    </div>
</div>

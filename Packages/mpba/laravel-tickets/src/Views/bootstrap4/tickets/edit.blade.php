<div class="modal fade" id="ticket-edit-modal" tabindex="-1" role="dialog" aria-labelledby="ticket-edit-modal-Label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! CollectiveForm::model($ticket, [
                 'route'  => [$setting->grab('main_route').'.update', $ticket->id],
                 'method' => 'PATCH',
                 'class'  => 'form-horizontal',
                 'files'  => true  {{-- IMPORTANT: allow file uploads --}}
             ]) !!}
            <div class="modal-header">
                <h5 class="modal-title" id="ticket-edit-modal-Label">{{ $ticket->subject }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">{{ trans('ticket::lang.flash-x') }}</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    {!! CollectiveForm::text('subject', $ticket->subject, ['class' => 'form-control', 'required']) !!}
                </div>

                <div class="form-group">
                    <textarea class="form-control summernote-editor" rows="5" required name="content" cols="50">
                        {!! htmlspecialchars($ticket->html) !!}
                    </textarea>
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('project_id', trans('ticket::lang.project') . trans('ticket::lang.colon')) !!}
                    {!! CollectiveForm::select('project_id', $project_lists, $ticket->project_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('priority_id', trans('ticket::lang.priority') . trans('ticket::lang.colon')) !!}
                    {!! CollectiveForm::select('priority_id', $priority_lists, $ticket->priority_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('agent_id', trans('ticket::lang.agent') . trans('ticket::lang.colon')) !!}
                    {!! CollectiveForm::select('agent_id', $agent_lists, $ticket->agent_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('category_id', trans('ticket::lang.category') . trans('ticket::lang.colon')) !!}
                    {!! CollectiveForm::select('category_id', $category_lists, $ticket->category_id, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! CollectiveForm::label('status_id', trans('ticket::lang.status') . trans('ticket::lang.colon')) !!}
                    {!! CollectiveForm::select('status_id', $status_lists, $ticket->status_id, ['class' => 'form-control']) !!}
                </div>

                {{-- 🔹 Attachments upload --}}
                <div class="form-group">
                    <label class="form-label fw-bold">Attachments</label>
                    <label class="btn btn-primary rounded-pill px-3 py-2">
                        <i class="fa fa-upload me-1"></i> Choose Files
                        <input type="file" name="attachments[]" class="d-none" multiple>
                    </label>
                    <small class="form-text text-muted">You can select multiple files.</small>
                </div>

                {{-- 🔹 Show already attached files --}}
                @if($ticket->hasMedia('attachments'))
                    <ul class="list-group mt-3">
                        @foreach($ticket->getMedia('attachments') as $media)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fa fa-paperclip me-2"></i> {{ $media->file_name }}
                                </span>
                                <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank"
                                   download>
                                    <i class="fa fa-download me-1"></i> Download
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('ticket::lang.btn-close') }}</button>
                {!! CollectiveForm::submit(trans('ticket::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
            </div>

            {!! CollectiveForm::close() !!}
        </div>
    </div>
</div>

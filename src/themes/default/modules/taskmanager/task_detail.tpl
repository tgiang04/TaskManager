<!-- BEGIN: main -->
<div class="taskmanager-task-detail">
    <div class="page-header">
        <h1>{TASK.title}</h1>
        <div class="d-flex align-items-center mt-2">
            <span class="badge mr-2" style="background-color: {TASK.status_color}">{TASK.status_name}</span>
            <span class="badge badge-info mr-2">{TASK.priority}</span>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{LANG.task_description}</h5>
                </div>
                <div class="card-body">
                    {TASK.description}
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{LANG.task_progress}</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 30px;">
                        <div class="progress-bar" role="progressbar" style="width: {TASK.progress}%" 
                             aria-valuenow="{TASK.progress}" aria-valuemin="0" aria-valuemax="100">
                            {TASK.progress}%
                        </div>
                    </div>
                    <input type="range" class="form-control-range" min="0" max="100" value="{TASK.progress}" 
                           id="progressRange" onchange="updateProgress(this.value)">
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{LANG.task_comments}</h5>
                </div>
                <div class="card-body">
                    <!-- BEGIN: comment -->
                    <div class="comment-item mb-3 p-3 border-left border-primary">
                        <div class="d-flex justify-content-between">
                            <strong>{COMMENT.full_name}</strong>
                            <small class="text-muted">{COMMENT.time_format}</small>
                        </div>
                        <div class="mt-2">{COMMENT.content}</div>
                    </div>
                    <!-- END: comment -->
                    
                    <!-- BEGIN: no_comments -->
                    <p class="text-muted">{LANG.no_data}</p>
                    <!-- END: no_comments -->
                    
                    <div class="mt-4">
                        <h6>{LANG.comment_add}</h6>
                        <form method="post" action="">
                            <div class="form-group">
                                <textarea name="comment_content" class="form-control" rows="4" 
                                          placeholder="{LANG.comment_content}"></textarea>
                            </div>
                            <button type="submit" name="add_comment" class="btn btn-primary">
                                {LANG.comment_add}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{LANG.task_history}</h5>
                </div>
                <div class="card-body">
                    <!-- BEGIN: history_item -->
                    <div class="history-item mb-2">
                        <small>
                            <strong>{HISTORY.username}</strong> {HISTORY.action} 
                            <span class="text-muted">{HISTORY.time_format}</span>
                        </small>
                    </div>
                    <!-- END: history_item -->
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6>{LANG.task_status}</h6>
                </div>
                <div class="card-body">
                    <select class="form-control" onchange="updateStatus(this.value)">
                        <!-- BEGIN: status_option -->
                        <option value="{STATUS.status_key}"{STATUS.selected}>{STATUS.status_name}</option>
                        <!-- END: status_option -->
                    </select>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>{LANG.task_detail}</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{LANG.task_creator}:</strong><br>
                        {TASK.creator_username}
                    </p>
                    <p class="mb-2">
                        <strong>{LANG.task_assigned_to}:</strong><br>
                        {TASK.assigned_username}
                    </p>
                    <p class="mb-2">
                        <strong>{LANG.task_deadline}:</strong><br>
                        {TASK.deadline}
                    </p>
                    <p class="mb-2">
                        <strong>{LANG.task_created_time}:</strong><br>
                        {TASK.created_time}
                    </p>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>{LANG.task_attachments}</h6>
                </div>
                <div class="card-body">
                    <!-- BEGIN: attachment -->
                    <div class="attachment-item mb-2">
                        <i class="fa fa-file"></i> 
                        <a href="{ATTACHMENT.filepath}">{ATTACHMENT.filename}</a>
                        <small class="d-block text-muted">
                            {ATTACHMENT.filesize_format} - {ATTACHMENT.time_format}
                        </small>
                    </div>
                    <!-- END: attachment -->
                    
                    <button class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fa fa-upload"></i> {LANG.attachment_upload}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    // AJAX call to update status
    console.log('Update status to: ' + status);
}

function updateProgress(progress) {
    // AJAX call to update progress
    console.log('Update progress to: ' + progress);
}
</script>
<!-- END: main -->

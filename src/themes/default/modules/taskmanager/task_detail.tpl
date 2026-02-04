<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/modules/taskmanager/style.css">

<div class="taskmanager-task-detail">
    <input type="hidden" id="taskId" value="{TASK.id}">
    
    <div class="task-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1>{TASK.title}</h1>
                <div class="task-badges">
                    <span class="badge badge-light">
                        <i class="fa fa-folder"></i> {TASK.project_title}
                    </span>
                    <span class="badge" style="background-color: {TASK.status_color}">
                        {TASK.status_name}
                    </span>
                    <span class="badge task-priority {TASK.priority}">
                        <i class="fa fa-flag"></i> {TASK.priority_name}
                    </span>
                </div>
            </div>
            <!-- BEGIN: task_actions -->
            <div class="btn-group">
                <button type="button" class="btn btn-light" onclick="window.print()">
                    <i class="fa fa-print"></i>
                </button>
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editTaskModal">
                    <i class="fa fa-edit"></i>
                </button>
            </div>
            <!-- END: task_actions -->
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 sidebar-card">
                <div class="card-header">
                    <h5><i class="fa fa-align-left"></i> {LANG.task_description}</h5>
                </div>
                <div class="card-body">
                    {TASK.description}
                </div>
            </div>
            
            <div class="card mb-4 sidebar-card">
                <div class="card-header">
                    <h5><i class="fa fa-tasks"></i> {LANG.task_progress}</h5>
                </div>
                <div class="card-body progress-slider-wrapper">
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: {TASK.progress}%" 
                             aria-valuenow="{TASK.progress}" aria-valuemin="0" aria-valuemax="100">
                            {TASK.progress}%
                        </div>
                    </div>
                    <input type="range" class="progress-slider" min="0" max="100" value="{TASK.progress}" 
                           id="progressSlider">
                    <div class="progress-value" id="progressValue">{TASK.progress}%</div>
                </div>
            </div>
            
            <div class="card mb-4 sidebar-card">
                <div class="card-header">
                    <h5><i class="fa fa-comments"></i> {LANG.task_comments} ({COMMENT_COUNT})</h5>
                </div>
                <div class="card-body">
                    <!-- BEGIN: comment -->
                    <div class="comment-item">
                        <div class="comment-header">
                            <div class="comment-author">
                                <i class="fa fa-user-circle"></i> {COMMENT.full_name}
                            </div>
                            <div class="comment-time">
                                <i class="fa fa-clock-o"></i> {COMMENT.time_format}
                            </div>
                        </div>
                        <div class="comment-content">{COMMENT.content}</div>
                    </div>
                    <!-- END: comment -->
                    
                    <!-- BEGIN: no_comments -->
                    <div class="empty-state py-3">
                        <i class="fa fa-comments-o"></i>
                        <p class="mb-0">{LANG.no_comments}</p>
                    </div>
                    <!-- END: no_comments -->
                    
                    <div class="comment-form mt-4">
                        <h6 class="mb-3"><i class="fa fa-pencil"></i> {LANG.comment_add}</h6>
                        <form id="commentForm">
                            <div class="form-group">
                                <textarea id="comment_content" name="comment_content" class="form-control" rows="4" 
                                          placeholder="{LANG.comment_content_placeholder}"></textarea>
                            </div>
                            <button type="submit" class="btn btn-gradient">
                                <i class="fa fa-send"></i> {LANG.comment_add}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4 sidebar-card">
                <div class="card-header">
                    <h5><i class="fa fa-history"></i> {LANG.task_history}</h5>
                </div>
                <div class="card-body">
                    <div class="history-timeline">
                        <!-- BEGIN: history_item -->
                        <div class="history-item">
                            <div class="history-content">
                                <span class="history-user">{HISTORY.username}</span> {HISTORY.action}
                                <span class="history-time">{HISTORY.time_format}</span>
                            </div>
                        </div>
                        <!-- END: history_item -->
                        
                        <!-- BEGIN: no_history -->
                        <p class="text-muted mb-0">{LANG.no_history}</p>
                        <!-- END: no_history -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-3 sidebar-card">
                <div class="card-header">
                    <h6><i class="fa fa-info-circle"></i> {LANG.task_status}</h6>
                </div>
                <div class="card-body">
                    <select class="form-control status-select" id="statusSelect">
                        <!-- BEGIN: status_option -->
                        <option value="{STATUS.status_key}"{STATUS.selected}>{STATUS.status_name}</option>
                        <!-- END: status_option -->
                    </select>
                </div>
            </div>
            
            <div class="card mb-3 sidebar-card">
                <div class="card-header">
                    <h6><i class="fa fa-info"></i> {LANG.task_detail}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="d-block mb-1">
                            <i class="fa fa-user"></i> {LANG.task_creator}:
                        </strong>
                        <span class="text-muted">{TASK.creator_username}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="d-block mb-1">
                            <i class="fa fa-user-plus"></i> {LANG.task_assigned_to}:
                        </strong>
                        <span class="text-muted">{TASK.assigned_username}</span>
                    </div>
                    <div class="mb-3">
                        <strong class="d-block mb-1">
                            <i class="fa fa-calendar"></i> {LANG.task_deadline}:
                        </strong>
                        <span class="text-muted">{TASK.deadline_format}</span>
                    </div>
                    <div class="mb-0">
                        <strong class="d-block mb-1">
                            <i class="fa fa-clock-o"></i> {LANG.task_created_time}:
                        </strong>
                        <span class="text-muted">{TASK.created_time_format}</span>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3 sidebar-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fa fa-paperclip"></i> {LANG.task_attachments}</h6>
                    <span class="badge badge-secondary">{ATTACHMENT_COUNT}</span>
                </div>
                <div class="card-body">
                    <!-- BEGIN: attachment -->
                    <div class="attachment-item">
                        <div class="attachment-icon">
                            <i class="fa fa-file"></i>
                        </div>
                        <div class="attachment-info">
                            <div class="attachment-name">
                                <a href="{ATTACHMENT.filepath}" target="_blank">{ATTACHMENT.filename}</a>
                            </div>
                            <div class="attachment-meta">
                                {ATTACHMENT.filesize_format} • {ATTACHMENT.time_format}
                            </div>
                        </div>
                    </div>
                    <!-- END: attachment -->
                    
                    <!-- BEGIN: no_attachments -->
                    <div class="empty-state py-3">
                        <i class="fa fa-paperclip"></i>
                        <p class="mb-0">{LANG.no_attachments}</p>
                    </div>
                    <!-- END: no_attachments -->
                    
                    <!-- BEGIN: upload_button -->
                    <button class="btn btn-outline-primary btn-block mt-3" data-toggle="modal" data-target="#uploadModal">
                        <i class="fa fa-upload"></i> {LANG.attachment_upload}
                    </button>
                    <!-- END: upload_button -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload File -->
<!-- BEGIN: upload_modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="fa fa-upload"></i> {LANG.attachment_upload}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="attachment_file">{LANG.attachment_filename}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachment_file" name="attachment_file" required>
                            <label class="custom-file-label" for="attachment_file">{LANG.choose_file}</label>
                        </div>
                        <small class="form-text text-muted">
                            {LANG.max_file_size}: 10MB
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> {LANG.cancel}
                    </button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa fa-upload"></i> {LANG.upload}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: upload_modal -->

<script src="{NV_BASE_SITEURL}themes/default/modules/taskmanager/script.js"></script>
<!-- END: main -->

<!-- BEGIN: main -->
<div class="taskmanager-project-detail">
    <div class="page-header">
        <h1>{PROJECT.title}</h1>
        <p class="lead">{PROJECT.description}</p>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h6>{LANG.project_total_tasks}</h6>
                    <h3>{STATS.total_tasks}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h6>{LANG.project_completed_tasks}</h6>
                    <h3>{STATS.completed_tasks}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h6>{LANG.project_pending_tasks}</h6>
                    <h3>{STATS.in_progress_tasks}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h6>{LANG.project_progress}</h6>
                    <h3>{STATS.progress}%</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{LANG.task_list}</h5>
                    <!-- BEGIN: is_owner -->
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i> {LANG.task_add}
                    </a>
                    <!-- END: is_owner -->
                </div>
                <div class="card-body">
                    <!-- BEGIN: task -->
                    <div class="task-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6><a href="{TASK.link}">{TASK.title}</a></h6>
                                <small class="text-muted">
                                    {LANG.task_assigned_to}: {TASK.assigned_username} | 
                                    {LANG.task_deadline}: {TASK.deadline_format}
                                </small>
                            </div>
                            <span class="badge ml-2" style="background-color: {TASK.status_color}">
                                {TASK.status_name}
                            </span>
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: {TASK.progress}%" 
                                 aria-valuenow="{TASK.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <!-- END: task -->
                    
                    <!-- BEGIN: no_tasks -->
                    <p class="text-muted">{LANG.no_data}</p>
                    <!-- END: no_tasks -->
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>{LANG.project_members}</h5>
                </div>
                <div class="card-body">
                    <!-- BEGIN: member -->
                    <div class="member-item mb-2 d-flex align-items-center">
                        <div class="flex-grow-1">
                            <strong>{MEMBER.full_name}</strong>
                            <small class="d-block text-muted">@{MEMBER.username}</small>
                        </div>
                        <span class="badge badge-secondary">{MEMBER.role}</span>
                    </div>
                    <!-- END: member -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: main -->

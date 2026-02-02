<!-- BEGIN: main -->
<div class="taskmanager-home">
    <div class="page-header">
        <h1>{LANG.main}</h1>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">{LANG.stats_total_projects}</h5>
                    <h2>{STATS.total_projects}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">{LANG.stats_total_tasks}</h5>
                    <h2>{STATS.total_tasks}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">{LANG.stats_completed}</h5>
                    <h2>{STATS.completed_tasks}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">{LANG.stats_overdue}</h5>
                    <h2>{STATS.overdue_tasks}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{LANG.project_list}</h5>
                </div>
                <div class="card-body">
                    <!-- BEGIN: project -->
                    <div class="project-item mb-3 p-3 border rounded">
                        <h6><a href="{PROJECT.link}">{PROJECT.title}</a></h6>
                        <p class="text-muted mb-1">{PROJECT.description}</p>
                        <small>{LANG.project_total_tasks}: {PROJECT.total_tasks}</small>
                    </div>
                    <!-- END: project -->
                    
                    <!-- BEGIN: no_projects -->
                    <p class="text-muted">{LANG.no_data}</p>
                    <!-- END: no_projects -->
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{LANG.my_tasks}</h5>
                </div>
                <div class="card-body">
                    <!-- BEGIN: task -->
                    <div class="task-item mb-3 p-3 border rounded">
                        <h6><a href="{TASK.link}">{TASK.title}</a></h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge" style="background-color: {TASK.status_color}">{TASK.status_name}</span>
                            <span class="{TASK.priority_class}">{TASK.priority_name}</span>
                        </div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: {TASK.progress}%" aria-valuenow="{TASK.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <!-- END: task -->
                    
                    <!-- BEGIN: no_tasks -->
                    <p class="text-muted">{LANG.no_data}</p>
                    <!-- END: no_tasks -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: main -->

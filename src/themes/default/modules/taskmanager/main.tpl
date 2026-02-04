<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/modules/taskmanager/style.css">

<div class="taskmanager-home">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fa fa-dashboard"></i> {LANG.main}</h1>
            <!-- BEGIN: quick_actions -->
            <div class="btn-group">
                <a href="{BASE_URL}&amp;{NV_OP_VARIABLE}=projects" class="btn btn-gradient">
                    <i class="fa fa-folder"></i> {LANG.projects}
                </a>
                <a href="{BASE_URL}&amp;{NV_OP_VARIABLE}=tasks" class="btn btn-gradient">
                    <i class="fa fa-tasks"></i> {LANG.tasks}
                </a>
            </div>
            <!-- END: quick_actions -->
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-primary stats-card">
                <div class="card-body position-relative">
                    <h5 class="card-title">{LANG.stats_total_projects}</h5>
                    <h2>{STATS.total_projects}</h2>
                    <i class="fa fa-folder position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-info stats-card">
                <div class="card-body position-relative">
                    <h5 class="card-title">{LANG.stats_total_tasks}</h5>
                    <h2>{STATS.total_tasks}</h2>
                    <i class="fa fa-tasks position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-success stats-card">
                <div class="card-body position-relative">
                    <h5 class="card-title">{LANG.stats_completed}</h5>
                    <h2>{STATS.completed_tasks}</h2>
                    <i class="fa fa-check-circle position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-danger stats-card">
                <div class="card-body position-relative">
                    <h5 class="card-title">{LANG.stats_overdue}</h5>
                    <h2>{STATS.overdue_tasks}</h2>
                    <i class="fa fa-exclamation-triangle position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card project-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-folder-open"></i> {LANG.project_list}</h5>
                    <a href="{BASE_URL}&amp;{NV_OP_VARIABLE}=projects" class="btn btn-sm btn-light">
                        {LANG.view_all} <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <!-- BEGIN: project -->
                    <div class="task-item">
                        <h6><a href="{PROJECT.link}"><i class="fa fa-folder"></i> {PROJECT.title}</a></h6>
                        <p class="text-muted mb-2 small">{PROJECT.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-info">
                                <i class="fa fa-tasks"></i> {PROJECT.total_tasks} {LANG.tasks}
                            </span>
                            <small class="text-muted">
                                <i class="fa fa-calendar"></i> {PROJECT.created_time_format}
                            </small>
                        </div>
                    </div>
                    <!-- END: project -->
                    
                    <!-- BEGIN: no_projects -->
                    <div class="empty-state py-5">
                        <i class="fa fa-folder-open"></i>
                        <h5>{LANG.no_projects}</h5>
                        <p>{LANG.no_projects_desc}</p>
                        <!-- BEGIN: add_project_link -->
                        <a href="{BASE_URL}&amp;{NV_OP_VARIABLE}=projects" class="btn btn-gradient">
                            <i class="fa fa-plus"></i> {LANG.project_add}
                        </a>
                        <!-- END: add_project_link -->
                    </div>
                    <!-- END: no_projects -->
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card project-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-user"></i> {LANG.my_tasks}</h5>
                    <a href="{BASE_URL}&amp;{NV_OP_VARIABLE}=my-tasks" class="btn btn-sm btn-light">
                        {LANG.view_all} <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <!-- BEGIN: task -->
                    <div class="task-item">
                        <h6><a href="{TASK.link}">{TASK.title}</a></h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge" style="background-color: {TASK.status_color}">{TASK.status_name}</span>
                            <span class="task-priority {TASK.priority}">
                                <i class="fa fa-flag"></i> {TASK.priority_name}
                            </span>
                        </div>
                        <div class="task-progress-wrapper">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">{LANG.progress}</small>
                                <small class="text-muted">{TASK.progress}%</small>
                            </div>
                            <div class="task-progress-bar">
                                <div class="task-progress-fill" style="width: {TASK.progress}%"></div>
                            </div>
                        </div>
                    </div>
                    <!-- END: task -->
                    
                    <!-- BEGIN: no_tasks -->
                    <div class="empty-state py-5">
                        <i class="fa fa-check-circle"></i>
                        <h5>{LANG.no_tasks}</h5>
                        <p>{LANG.no_tasks_desc}</p>
                    </div>
                    <!-- END: no_tasks -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{NV_BASE_SITEURL}themes/default/modules/taskmanager/script.js"></script>
<!-- END: main -->

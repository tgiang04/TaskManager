<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/modules/taskmanager/style.css">

<div class="taskmanager-tasks">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fa fa-list-ul"></i> {LANG.task_list}</h1>
        <!-- BEGIN: add_button -->
        <div class="btn-group">
            <button type="button" class="btn btn-gradient" data-toggle="modal" data-target="#taskModal">
                <i class="fa fa-plus"></i> {LANG.task_add}
            </button>
            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                <i class="fa fa-filter"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#"><i class="fa fa-tasks"></i> {LANG.filter_all}</a>
                <a class="dropdown-item" href="#"><i class="fa fa-user"></i> {LANG.filter_my_tasks}</a>
                <a class="dropdown-item" href="#"><i class="fa fa-clock-o"></i> {LANG.filter_overdue}</a>
                <a class="dropdown-item" href="#"><i class="fa fa-check"></i> {LANG.filter_completed}</a>
            </div>
        </div>
        <!-- END: add_button -->
    </div>
    
    <div class="card task-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 35%">
                            <i class="fa fa-tasks"></i> {LANG.task_title}
                        </th>
                        <th style="width: 15%">
                            <i class="fa fa-folder"></i> {LANG.task_project}
                        </th>
                        <th style="width: 12%">
                            <i class="fa fa-user"></i> {LANG.task_assigned_to}
                        </th>
                        <th style="width: 10%">
                            <i class="fa fa-info-circle"></i> {LANG.task_status}
                        </th>
                        <th style="width: 10%">
                            <i class="fa fa-calendar"></i> {LANG.task_deadline}
                        </th>
                        <th style="width: 13%">
                            <i class="fa fa-chart-line"></i> {LANG.task_progress}
                        </th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: task -->
                    <tr>
                        <td>
                            <a href="{TASK.link}" class="font-weight-bold">{TASK.title}</a>
                            <span class="task-priority {TASK.priority} ml-2">
                                <i class="fa fa-flag"></i>
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{TASK.project_title}</span>
                        </td>
                        <td>
                            <i class="fa fa-user-circle"></i> {TASK.assigned_username}
                        </td>
                        <td>
                            <span class="badge" style="background-color: {TASK.status_color}">
                                {TASK.status_name}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="fa fa-clock-o"></i> {TASK.deadline_format}
                            </small>
                        </td>
                        <td>
                            <div class="task-progress-bar">
                                <div class="task-progress-fill" style="width: {TASK.progress}%"></div>
                            </div>
                            <small class="text-muted">{TASK.progress}%</small>
                        </td>
                        <td class="text-right">
                            <a href="{TASK.link}" class="btn btn-sm btn-info" title="{LANG.task_view}">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <!-- END: task -->
                </tbody>
            </table>
        </div>
        
        <!-- BEGIN: empty -->
        <div class="card-body">
            <div class="empty-state">
                <i class="fa fa-tasks"></i>
                <h5>{LANG.no_tasks}</h5>
                <p class="text-muted">{LANG.no_tasks_desc}</p>
                <!-- BEGIN: add_task_button -->
                <button type="button" class="btn btn-gradient" data-toggle="modal" data-target="#taskModal">
                    <i class="fa fa-plus"></i> {LANG.task_add_first}
                </button>
                <!-- END: add_task_button -->
            </div>
        </div>
        <!-- END: empty -->
    </div>
    
    <!-- BEGIN: generate_page -->
    <div class="text-center mt-4">
        {GENERATE_PAGE}
    </div>
    <!-- END: generate_page -->
</div>

<script src="{NV_BASE_SITEURL}themes/default/modules/taskmanager/script.js"></script>
<!-- END: main -->

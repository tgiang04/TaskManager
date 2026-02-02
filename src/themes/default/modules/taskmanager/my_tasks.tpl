<!-- BEGIN: main -->
<div class="taskmanager-my-tasks">
    <div class="page-header">
        <h1>{LANG.my_tasks}</h1>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{LANG.task_title}</th>
                    <th>{LANG.task_project}</th>
                    <th>{LANG.task_status}</th>
                    <th>{LANG.task_priority}</th>
                    <th>{LANG.task_deadline}</th>
                    <th>{LANG.task_progress}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: task -->
                <tr>
                    <td>
                        <a href="{TASK.link}">{TASK.title}</a>
                        <!-- BEGIN: overdue -->
                        <span class="badge badge-danger ml-2">{LANG.task_overdue}</span>
                        <!-- END: overdue -->
                        <!-- BEGIN: due_soon -->
                        <span class="badge badge-warning ml-2">{LANG.task_due_soon}</span>
                        <!-- END: due_soon -->
                    </td>
                    <td>{TASK.project_title}</td>
                    <td>
                        <span class="badge" style="background-color: {TASK.status_color}">
                            {TASK.status_name}
                        </span>
                    </td>
                    <td>{TASK.priority}</td>
                    <td>{TASK.deadline_format}</td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {TASK.progress}%" 
                                 aria-valuenow="{TASK.progress}" aria-valuemin="0" aria-valuemax="100">
                                {TASK.progress}%
                            </div>
                        </div>
                    </td>
                </tr>
                <!-- END: task -->
            </tbody>
        </table>
        
        <!-- BEGIN: empty -->
        <div class="alert alert-info">
            {LANG.no_data}
        </div>
        <!-- END: empty -->
    </div>
    
    <!-- BEGIN: generate_page -->
    <div class="text-center mt-4">
        {GENERATE_PAGE}
    </div>
    <!-- END: generate_page -->
</div>
<!-- END: main -->

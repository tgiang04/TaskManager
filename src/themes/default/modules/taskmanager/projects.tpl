<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/modules/taskmanager/style.css">

<div class="taskmanager-projects">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fa fa-folder-open"></i> {LANG.projects}</h1>
        <!-- BEGIN: add_button -->
        <button type="button" class="btn btn-gradient" data-toggle="modal" data-target="#projectModal">
            <i class="fa fa-plus"></i> {LANG.project_add}
        </button>
        <!-- END: add_button -->
    </div>
    
    <div class="row g-4">
        <!-- BEGIN: project -->
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="card project-card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <a href="{PROJECT.link}">{PROJECT.title}</a>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{PROJECT.description}</p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fa fa-calendar"></i> {PROJECT.start_date_format} - {PROJECT.end_date_format}
                        </small>
                    </div>
                    <div class="project-meta">
                        <div>
                            <span class="badge badge-info">
                                <i class="fa fa-tasks"></i> {PROJECT.total_tasks} {LANG.tasks}
                            </span>
                            <span class="badge badge-secondary ml-2">{PROJECT.status_name}</span>
                        </div>
                        <!-- BEGIN: project_actions -->
                        <div class="btn-group btn-group-sm">
                            <a href="{PROJECT.link}" class="btn btn-info" title="{LANG.project_view}">
                                <i class="fa fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-delete-project" data-id="{PROJECT.id}" title="{LANG.project_delete}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <!-- END: project_actions -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END: project -->
        
        <!-- BEGIN: empty -->
        <div class="col-12">
            <div class="empty-state">
                <i class="fa fa-folder-open"></i>
                <h5>{LANG.no_projects}</h5>
                <p class="text-muted">{LANG.no_projects_desc}</p>
                <!-- BEGIN: add_project_button -->
                <button type="button" class="btn btn-gradient" data-toggle="modal" data-target="#projectModal">
                    <i class="fa fa-plus"></i> {LANG.project_add_first}
                </button>
                <!-- END: add_project_button -->
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

<!-- Modal thêm/sửa dự án -->
<!-- BEGIN: project_modal -->
<div class="modal fade" id="projectModal" tabindex="-1" role="dialog" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalLabel">
                    <i class="fa fa-folder"></i> {LANG.project_add}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="projectForm">
                <div class="modal-body">
                    <input type="hidden" id="project_id" name="id" value="0">
                    
                    <div class="form-group">
                        <label for="project_title">{LANG.project_title} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="project_title" name="title" required placeholder="{LANG.project_title_placeholder}">
                    </div>
                    
                    <div class="form-group">
                        <label for="project_description">{LANG.project_description}</label>
                        <textarea class="form-control" id="project_description" name="description" rows="4" placeholder="{LANG.project_description_placeholder}"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_start_date">{LANG.project_start_date}</label>
                                <input type="text" class="form-control datepicker" id="project_start_date" name="start_date" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_end_date">{LANG.project_end_date}</label>
                                <input type="text" class="form-control datepicker" id="project_end_date" name="end_date" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="project_is_public" name="is_public" value="1">
                            <label class="custom-control-label" for="project_is_public">
                                {LANG.project_is_public}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> {LANG.cancel}
                    </button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fa fa-save"></i> {LANG.save}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: project_modal -->

<script src="{NV_BASE_SITEURL}themes/default/modules/taskmanager/script.js"></script>
<!-- END: main -->

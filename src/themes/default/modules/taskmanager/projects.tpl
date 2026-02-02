<!-- BEGIN: main -->
<div class="taskmanager-projects">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>{LANG.projects}</h1>
        <!-- BEGIN: add_button -->
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#projectModal">
            <i class="fa fa-plus"></i> {LANG.project_add}
        </a>
        <!-- END: add_button -->
    </div>
    
    <div class="row">
        <!-- BEGIN: project -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{PROJECT.link}">{PROJECT.title}</a>
                    </h5>
                    <p class="card-text">{PROJECT.description}</p>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fa fa-calendar"></i> {PROJECT.start_date_format} - {PROJECT.end_date_format}
                        </small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-info">{PROJECT.total_tasks} {LANG.tasks}</span>
                        <span class="badge badge-secondary">{PROJECT.status_name}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: project -->
        
        <!-- BEGIN: empty -->
        <div class="col-12">
            <div class="alert alert-info">
                {LANG.no_data}
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
<!-- END: main -->

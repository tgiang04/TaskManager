/**
 * TaskManager Module JavaScript
 * NukeViet CMS
 */

var TaskManager = {
    /**
     * Khởi tạo module
     */
    init: function() {
        this.initProgressSlider();
        this.initStatusChange();
        this.initCommentForm();
        this.initProjectForm();
        this.initTaskForm();
        this.initDeleteActions();
        this.initDatePickers();
        this.initUploadForm();
        this.initFileInput();
    },

    /**
     * Khởi tạo slider tiến độ
     */
    initProgressSlider: function() {
        var progressSlider = document.getElementById('progressSlider');
        if (progressSlider) {
            var progressValue = document.getElementById('progressValue');
            var progressBar = document.getElementById('progressBar');
            
            progressSlider.addEventListener('input', function() {
                var value = this.value;
                progressValue.textContent = value + '%';
                progressBar.style.width = value + '%';
            });
            
            progressSlider.addEventListener('change', function() {
                TaskManager.updateProgress(this.value);
            });
        }
    },

    /**
     * Cập nhật tiến độ công việc
     */
    updateProgress: function(progress) {
        var taskId = document.getElementById('taskId').value;
        
        $.ajax({
            type: 'POST',
            url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=task-detail&id=' + taskId,
            data: {
                update_progress: 1,
                progress: progress
            },
            dataType: 'json',
            beforeSend: function() {
                $('#progressSlider').prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 'OK') {
                    TaskManager.showNotification(response.message, 'success');
                    // Reload để cập nhật lịch sử
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    TaskManager.showNotification(response.message, 'error');
                }
            },
            error: function() {
                TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
            },
            complete: function() {
                $('#progressSlider').prop('disabled', false);
            }
        });
    },

    /**
     * Khởi tạo thay đổi trạng thái
     */
    initStatusChange: function() {
        var statusSelect = document.getElementById('statusSelect');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                TaskManager.updateStatus(this.value);
            });
        }
    },

    /**
     * Cập nhật trạng thái công việc
     */
    updateStatus: function(status) {
        var taskId = document.getElementById('taskId').value;
        
        $.ajax({
            type: 'POST',
            url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=task-detail&id=' + taskId,
            data: {
                update_status: 1,
                status: status
            },
            dataType: 'json',
            beforeSend: function() {
                $('#statusSelect').prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 'OK') {
                    TaskManager.showNotification(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    TaskManager.showNotification(response.message, 'error');
                }
            },
            error: function() {
                TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
            },
            complete: function() {
                $('#statusSelect').prop('disabled', false);
            }
        });
    },

    /**
     * Khởi tạo form bình luận
     */
    initCommentForm: function() {
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            
            var content = $('#comment_content').val();
            var taskId = $('#taskId').val();
            
            if (!content.trim()) {
                TaskManager.showNotification('Vui lòng nhập nội dung bình luận', 'warning');
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: {
                    add_comment: 1,
                    comment_content: content
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#commentForm button[type="submit"]').prop('disabled', true).html('<span class="task-loading"></span> Đang gửi...');
                },
                success: function(response) {
                    if (response.status === 'OK') {
                        TaskManager.showNotification(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        TaskManager.showNotification(response.message, 'error');
                        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-send"></i> Gửi bình luận');
                    }
                },
                error: function() {
                    TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
                    $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-send"></i> Gửi bình luận');
                }
            });
        });
    },

    /**
     * Khởi tạo form dự án
     */
    initProjectForm: function() {
        $('#projectForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                save: 1,
                id: $('#project_id').val(),
                title: $('#project_title').val(),
                description: CKEDITOR.instances.project_description ? CKEDITOR.instances.project_description.getData() : $('#project_description').val(),
                start_date: $('#project_start_date').val(),
                end_date: $('#project_end_date').val(),
                is_public: $('#project_is_public').is(':checked') ? 1 : 0
            };
            
            if (!formData.title.trim()) {
                TaskManager.showNotification('Vui lòng nhập tên dự án', 'warning');
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=projects',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#projectForm button[type="submit"]').prop('disabled', true).html('<span class="task-loading"></span> Đang lưu...');
                },
                success: function(response) {
                    if (response.status === 'OK') {
                        TaskManager.showNotification(response.message, 'success');
                        $('#projectModal').modal('hide');
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        TaskManager.showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
                },
                complete: function() {
                    $('#projectForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu dự án');
                }
            });
        });
    },

    /**
     * Khởi tạo form công việc
     */
    initTaskForm: function() {
        $('#taskForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                save: 1,
                id: $('#task_id').val(),
                project_id: $('#task_project_id').val(),
                title: $('#task_title').val(),
                description: CKEDITOR.instances.task_description ? CKEDITOR.instances.task_description.getData() : $('#task_description').val(),
                priority: $('#task_priority').val(),
                assigned_to: $('#task_assigned_to').val(),
                deadline: $('#task_deadline').val()
            };
            
            if (!formData.title.trim()) {
                TaskManager.showNotification('Vui lòng nhập tên công việc', 'warning');
                return;
            }
            
            $.ajax({
                type: 'POST',
                url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=tasks',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#taskForm button[type="submit"]').prop('disabled', true).html('<span class="task-loading"></span> Đang lưu...');
                },
                success: function(response) {
                    if (response.status === 'OK') {
                        TaskManager.showNotification(response.message, 'success');
                        $('#taskModal').modal('hide');
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        TaskManager.showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
                },
                complete: function() {
                    $('#taskForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu công việc');
                }
            });
        });
    },

    /**
     * Khởi tạo xóa
     */
    initDeleteActions: function() {
        $('.btn-delete-project').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Bạn có chắc chắn muốn xóa dự án này?')) {
                return;
            }
            
            var projectId = $(this).data('id');
            
            $.ajax({
                type: 'POST',
                url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=projects',
                data: {
                    delete: 1,
                    id: projectId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'OK') {
                        TaskManager.showNotification(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        TaskManager.showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
                }
            });
        });
    },

    /**
     * Khởi tạo date picker
     */
    initDatePickers: function() {
        if ($.fn.datepicker) {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                language: 'vi'
            });
        }
    },

    /**
     * Hiển thị thông báo
     */
    showNotification: function(message, type) {
        type = type || 'info';
        
        var className = 'alert-info';
        if (type === 'success') {
            className = 'alert-success';
        } else if (type === 'error') {
            className = 'alert-danger';
        } else if (type === 'warning') {
            className = 'alert-warning';
        }
        
        var notification = $('<div class="alert ' + className + ' alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    },

    /**
     * Load dự án vào modal để sửa
     */
    editProject: function(projectId) {
        // Implement logic to load project data and populate modal
        $('#projectModal').modal('show');
    },

    /**
     * Load công việc vào modal để sửa
     */
    editTask: function(taskId) {
        // Implement logic to load task data and populate modal
        $('#taskModal').modal('show');
    },

    /**
     * Khởi tạo form upload
     */
    initUploadForm: function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('upload_file', '1');
            
            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#uploadForm button[type="submit"]').prop('disabled', true).html('<span class="task-loading"></span> Đang tải...');
                },
                success: function(response) {
                    if (response.status === 'OK') {
                        TaskManager.showNotification(response.message, 'success');
                        $('#uploadModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        TaskManager.showNotification(response.message, 'error');
                        $('#uploadForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-upload"></i> Tải lên');
                    }
                },
                error: function() {
                    TaskManager.showNotification('Có lỗi xảy ra, vui lòng thử lại', 'error');
                    $('#uploadForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-upload"></i> Tải lên');
                }
            });
        });
    },

    /**
     * Khởi tạo file input
     */
    initFileInput: function() {
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });
    }
};

// Khởi tạo khi document ready
$(document).ready(function() {
    TaskManager.init();
});

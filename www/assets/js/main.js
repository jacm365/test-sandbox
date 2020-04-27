$(document).ready(function(){

    const version = 'v1';
    const BasePath = `api/${version}/tasks`;
    const taskEdit = { id: null };
    //Utils
    const formatTask = function(task) {
        let checked = '';
        let disabled = '';
        if(!!task.is_done) {
            checked =  'checked';
            disabled =  'disabled';
        }
        let taskHtml = `
        <div id="task_${task.id}" class="notification has-background-info has-text-centered is-widescreen task">
            <input id="task_checkbox_${task.id}" type="checkbox" ${checked} ${disabled} data-taskid="${task.id}" class="checkbox task-checkbox is-pulled-left">
            <span id="task_ltext_${task.id}" class="task-text" data-taskid="${task.id}">
                ${task.task}
            </span>
            <input class="input input-task hidden" id="task_text_${task.id}" value="${task.task}" type="text" maxlength="250">
            <a class="delete delete-task" data-taskid="${task.id}"></a>
        </div>
        `;
        return taskHtml;
    };

    const validateForm = function(text) {
        if(text === '') {
            $('.form-error').css('display', 'block');
            return false;
        }
        return true;
    };

    const clearFormErrors = function() {
        $('.form-error').hide();
    }

    const showMessage = function(text, type) {
        $('.message').addClass(type);
        $('.message').text(text);
        $('.message').fadeIn(300, function() {
            setTimeout(function() {
                $('.message').fadeOut(600);
                $('.message').removeClass(type);
            }, 1500);
        });
    };

    const closeEditor = function(taskId) {
        $('#task_text_' + taskId).hide();
        $('#task_ltext_' + taskId).show();
        taskEdit.id = null;
    };

    //API endpoints
    const deleteTask = function(taskId) {
        $.ajax(BasePath + '/' + taskId , { type: 'DELETE' })
            .done(function() {
                $('#task_' + taskId).remove();
                let text = 'Task deleted successfully.';
                showMessage(text, 'success');
            })
            .fail(function() {
                let text = 'Error deleting task. Try again.';
                showMessage(text, 'error');
            });
    };

    const updateTask = function(taskId, task, is_done) {
        $.ajax(BasePath + '/' + taskId, {
                type: 'PUT',
                data: { task: task, is_done: is_done ? 1 : 0 }
            })
            .done(function() {
                let text = 'Task updated successfully.';
                if(is_done) {
                    $('#task_checkbox_' + taskId).prop('checked', true);
                    $('#task_checkbox_' + taskId).prop('disabled', true);
                }
                if(!!taskEdit.id) {
                    $('#task_ltext_' + taskId).text(task);
                    closeEditor(taskEdit.id);
                }
                showMessage(text, 'success');
            })
            .fail(function() {
                let text = 'Error updating task. Try again.';
                showMessage(text, 'error');
            });
    };

    const addTask = function(task) {
        $.post( BasePath, { task: task })
            .done(function(task) {
                $('.task-list').append(formatTask(task));
                let text = 'Task added successfully.';
                showMessage(text, 'success');
            })
            .fail(function() {
                let text = 'Error adding task. Try again.';
                showMessage(text, 'error');
            });
    };

    $.get( BasePath, function( data ) {
        console.log(data)
        if(!!data && data.length > 0) {
            data.forEach(task => {
                $('.task-list').append(formatTask(task));
            });
        }
    });

    //Events
    $('.task-list').on('click', '.delete-task', function() {
        let taskId = $(this).data('taskid');
        if(confirm("Do you really want to delete this task?")) {
            deleteTask(taskId);
        }
    });
    $('.task-list').on('click', '.task-checkbox', function(event) {
        let taskId = $(this).data('taskid');
        let task = $('#task_text_' + taskId).val();
        let is_done = $('#task_checkbox_' + taskId).is(':checked');
        if(confirm("Do you really want to finish this task?")) {
            updateTask(taskId, task, is_done);
            $(this).prop('checked', false);
        } else {
            if($(this).is(':checked')) {
                $(this).prop('checked', false);
            }
        }
    });
    $('.task-button').on('click', function() {
        clearFormErrors();
        let task = $.trim($('#task-text').val());
        if(validateForm(task)) {
            addTask(task);
        }
    });
    $('.task-list').on('click', '.task-text', function() {
        let taskId = $(this).data('taskid');
        if($('#task_checkbox_' + taskId).is(':checked')) return false;
        $(this).hide();
        taskEdit.id = taskId;
        $('#task_text_' + taskId).show();
    });
    $(document).on('keyup', function(e){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == 13 && !!taskEdit.id){//enter
            let text = $.trim($('#task_text_'+ taskEdit.id).val());
            let lastText = $('#task_ltext_' + taskEdit.id);
            if(validateForm($('#task_text_'+ taskEdit.id)) &&
               lastText != text) {
                let is_done = $('#task_checkbox_' + taskEdit.id).is(':checked');
                updateTask(taskEdit.id, text, is_done);
            } else {
                closeEditor(taskEdit.id);
            }
        }
        else if(keycode === 27 && !!taskEdit.id){//esc
            closeEditor(taskEdit.id);
        }
    });
});
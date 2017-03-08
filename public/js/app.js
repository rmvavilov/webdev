$(document).ready(function () {
    "use strict";

    var $body = $('body'),
        $messages = $('#messages'),
        GET_ALL_MESSAGE = 0,
        MESSAGE_CREATE = 2,
        MESSAGE_EDIT = 3;
    
    var $btn_str = "<div class='content'>" +
        "<button id='add-comment' type='button' class='btn btn-primary btn-xs' data-form-visible='false'>reply</button>" +
        "</div>";

    var $edit_btn_str = "<button id='edit-comment' type='button' class='btn btn-primary btn-xs' data-form-visible='false'>edit</button>";
    
    // Create new message/comment
    // messageType: 0 - message; 1 - comment to message/comment to comment;
    $body.on('submit', 'form', function (e) {
        e.preventDefault();

        var data = {},
            operation_add = $(this).data('operationAdd'),
            action = (operation_add) ? MESSAGE_CREATE : MESSAGE_EDIT,
            parent_id = 0,
            $text_area = $(this).find('textarea'),
            type = $text_area.data('messageType'),
            text = $text_area.val();

        if (type == 1) {
            parent_id = $(this).parent().parent().parent().attr('id');
        }
        
        data = {
            action: action,
            parent_id: parent_id,
            type: type,
            text: text
        };

        $.ajax({
            type: "POST",
            url: '/messages',
            data: data,
            success: function(res) {
                if ( action == MESSAGE_CREATE ) {
                    createNewMessageSuccess(res, $text_area);
                } else if (action == MESSAGE_EDIT) {
                    updateMessageSuccess(res);
                }
            },
            error: function (xhr) {
                var ar = JSON.parse(xhr.responseText);
                if (ar.hasOwnProperty('auth') && ar.auth == false) {
                    window.location.reload();
                }
            }
        });
    });

    function createNewMessageSuccess(res, $text_area) {
        var ar = JSON.parse(res),
            type = ar.type,
            guest = false,
            editable = true,
            full_name = ar.first_name + ' ' + ar.last_name,
            append = (ar.type == 1);
        
        $text_area.val('');
        insertMessage(ar.id, ar.parent_id, type, full_name, ar.text, ar.created_at, guest, editable, append);
        hideCommentForm();
    }
    
    function updateMessageSuccess(res) {
        var ar = JSON.parse(res),
            id = ar.id,
            $message = $('#' + id + '.media').find('.comment-text:first'),
            text = ar.text;
        
        $message.text(text);
        hideCommentForm();
    }
    
    // Remove all previous existing 'add comment form'
    function hideCommentForm() {
        var $visible_form = $('#comment-form');
        
        $visible_form.parent().find('#add-comment').text('reply');
        $visible_form.parent().find('#add-comment').data('formVisible', false);
        $visible_form.parent().find('#edit-comment').text('update');
        $visible_form.parent().find('#edit-comment').data('formVisible', false);
        
        $visible_form.remove();
    }

    // Show/hide 'add/edit comment form' (dynamically created form)
    $body.on('click', '#add-comment, #edit-comment', function () {
        var form_visible = $(this).data('formVisible'),
            id = $(this).attr('id'),
            $parent = $(this).parent(),
            $text_div = $parent.prev('.comment-text'),
            str = (form_visible == 1) ? 'reply' : 'cancel',
            add = (id == 'add-comment'),
            placeholder_str = (add) ? 'add comment...' : 'edit comment...',
            textarea_str = (add) ? '' : $text_div.text(),
            btn_str = (add) ? 'Add' : 'Update',
            $add_comment_form = '';

        if (!form_visible) {
            hideCommentForm();
            $add_comment_form = "" +
                "<form id='comment-form' method='post' action='messages.php' data-operation-add='" + add + "' data-toggle='validator' role='form'>" +
                    "<div class='form-group'>" +
                        "<textarea id='new-message' class='form-control' cols='10' rows='3' placeholder='" + placeholder_str +"' required data-message-type='1'>" +
                            textarea_str + 
                        "</textarea>" +
                    "</div>" +
                    "<button type='submit' class='btn btn-default btn-xs'>" + btn_str + "</button>" +
                    "<button type='reset' class='btn btn-default btn-xs'>Clear</button>" +
                "</form>";
            $parent.append($add_comment_form);
        } else {
            $parent.find('form').remove();
        }
        
        $(this).text(str);
        $(this).data('formVisible', !form_visible);
    });

    function insertMessage(id, parent_id, type, name, text, created_at, guest, editable, append) {
        var $new_message = undefined,
            top_element_open = '',
            top_element_close = '',
            $message_element_str = '';
        
        if (type == 1) {
            top_element_open = "<div id='" + id + "' class='media'>";
            top_element_close = "</div>";
        } else {
            top_element_open = "<li id='" + id + "' class='media'>";
            top_element_close = "</li>";
        }

        $message_element_str = top_element_open +
            "<div class='media-left'>" +
                "<a href='#'>" +
                    "<img class='media-object' src='public/img/default_user_img.svg' alt='...'>" +
                "</a>" +
            "</div>" +
            "<div class='media-body'>" +
                "<div  class='media-heading'>" +
                    "<h4>" + name + ' ' + created_at + "</h4>" +
                "</div>" + 
                "<div class='comment-text'>" + text + "</div>" +
            "</div>" + top_element_close;
        
        if (type == 1) {
            var $msg = $messages.find('#' + parent_id + '>.media-body');
            $msg.append($message_element_str);
            // add comment button for auth users
            if (!guest) {
                $new_message = $("div#" + id);
                $new_message.find('.comment-text').after($btn_str);
                
                if( editable ){
                    $new_message.find('#add-comment').after($edit_btn_str);
                }
            }
        } else {
            if (append) {
                $messages.append($message_element_str);
            } else {
                $messages.prepend($message_element_str);
            }
            // add comment button for auth users
            if (!guest) {
                $new_message = $("li#" + id);
                $new_message.find('.comment-text').after($btn_str);

                if( editable ){
                    $new_message.find('#add-comment').after($edit_btn_str);
                }
            }
        }
    }
    
    function drawMessage(messages, guest, user_id) {
        var i = 0,
            message = {},
            editable = false,
            append = false,
            full_name = '';
        
        for (i; i < messages.length; i++) {
            message = messages[i];
            full_name = message.first_name + ' ' + message.last_name;
            editable = (message.user_id == user_id);
            append = (message.type == 1);
            if (message.hasOwnProperty('children') && message.children.length != 0) {
                insertMessage(message.id, message.parent_id, message.type, full_name, message.text, message.created_at, guest, editable, append);
                drawMessage(message.children, guest, user_id);
            } else {
                insertMessage(message.id, message.parent_id, message.type, full_name, message.text, message.created_at, guest, editable, append);
            }
        }
    }

    function getAllMessages() {
        $.ajax({
            type: "POST",
            url: '/messages',
            data: {
                action: GET_ALL_MESSAGE
            },
            success: getAllMessagesSuccess
        });
    }

    function getAllMessagesSuccess(res) {
        var data = JSON.parse(res),
            message_tree = data.message_tree,
            user_id = Number(data.user_id),
            guest = data.guest;

        drawMessage(message_tree, guest, user_id);
    }
    
    // APP start
    getAllMessages();
});
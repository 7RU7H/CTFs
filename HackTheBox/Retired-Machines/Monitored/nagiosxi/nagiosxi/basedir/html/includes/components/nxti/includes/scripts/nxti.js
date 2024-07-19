    var targetInput;

    $("table").on("focus", "input", function() {
        window.targetInput = $(this).data("id");
    });

    $('.default-populator').on('click', function () {
        var to_populate = $(this).parent().children('input');
        to_populate.val(to_populate.attr('placeholder'));
    });

    function appendMacro(macroVariable) {

        var txtarea = null;

        if ($('#copy-trap-modal').is(":visible")) {
            txtarea = $('#copy-trap-modal').find('[data-id=' + window.targetInput + ']');
        } else if ($('#edit-trap-modal').is(":visible")) {
            txtarea = $('#edit-trap-modal').find('[data-id=' + window.targetInput + ']');
        } else if ($('#add-trap-modal').is(":visible")) {
            txtarea = $('#add-trap-modal').find('[data-id=' + window.targetInput + ']');
        }

        if (!txtarea || txtarea.attr('name') !== 'exec[]') {
            return;
        }

        var start = txtarea[0].selectionStart;
        var end = txtarea[0].selectionEnd;
        var sel = txtarea[0].value.substring(start, end);
        var finText = txtarea[0].value.substring(0, start) + macroVariable + sel + txtarea[0].value.substring(end);
        txtarea[0].value = finText;
        txtarea[0].focus();
        txtarea[0].selectionEnd= end + macroVariable.length;
    }

    $('#test-btn').click(function() {
        insertAtCaret(targetInput);
    });

    function closeModal() {

        if ($('#copy-trap-modal').is(":visible")) {
            $('#copy-trap-modal').hide();
        } else if ($('#edit-trap-modal').is(":visible")) {
            $('#edit-trap-modal').hide();
        } else if ($('#add-trap-modal').is(":visible")) {
            $('#add-trap-modal').hide();
        }

        $('#macro-table-modal').hide();

        clear_whiteout();
    }

    window.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });

    function showMacroTable(modalSelector) {
        $('#macro-table-modal').toggle();

        $('#macro-table-modal').css('top', $(modalSelector).offset().top);
        $('#macro-table-modal').css('left', $(modalSelector).offset().left + $(modalSelector).outerWidth() + 0);

        $(window).resize(function() {
            $('#macro-table-modal').hide();
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $('#macro-table-modal').css('left', $(modalSelector).offset().left + $(modalSelector).outerWidth());
                $('#macro-table-modal').css('top', $(modalSelector).offset().top);

                if ($(modalSelector).is(":visible")) {
                    $('#macro-table-modal').show();
                }
            }, 200);
        });
    }

    function hasWhiteSpace(eventValue) {
        return /\s/g.test(eventValue);
    }

    <!-- Script that performs validation and sanitization -->
    function validateForm() {
        $('.hideOnResubmit').hide();
        var trapId = document.forms["editTrap"]["id"].value;
        var event = document.forms['editTrap']["event"].value;
        var errors = validateTrapForm('editTrap');
        // edit trap validates the event name already existing a bit differently as we must exclude the trap that we are editing
        errors += validateFormField(event, '.validationError', '.eventNameExists', 'eventNameAlreadyExists', {trapId: trapId});

        if (errors > 0) {
            $('#edit-trap-modal').scrollTop(0);
            return false;
        } else {
            return true;
        }
    }

    function validateAddForm() {
        $('.hideOnResubmit').hide();
        var event = document.forms['addTrap']["event"].value;
        var errors = validateTrapForm('addTrap');
        errors += validateFormField(event, '.validationError', '.eventNameExists', 'eventNameAlreadyExists');

        if (errors > 0) {
            $('#add-trap-modal').scrollTop(0);
            return false;
        } else {
            return true;
        }
    }

    function validateCopyForm() {
        $('.hideOnResubmit').hide();
        var event = document.forms['copyTrap']["event"].value;
        var errors = validateTrapForm('copyTrap');
        errors += validateFormField(event, '.validationError', '.eventNameExists', 'eventNameAlreadyExists');

        if (errors > 0) {
            $('#copy-trap-modal').scrollTop(0);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validation function for the trap form. Returns 1 or 0 to allow caller of function to
     * aggregate an error count.
     *
     * @param formValue
     * @param errorMessageSelector
     * @param validationMessageSelector
     * @param validationType
     * @param options
     * @returns {number}
     */
    function validateFormField(formValue, errorMessageSelector, validationMessageSelector, validationType, options = {}) {
        switch (validationType) {
            case 'hasWhiteSpace':
                if (hasWhiteSpace(formValue) == true) {
                    $(errorMessageSelector).show();
                    $(validationMessageSelector).show();
                    return 1;
                }
                break;
            case 'notEmpty':
                if (formValue == "") {
                    $(errorMessageSelector).show();
                    $(validationMessageSelector).show();
                    return 1;
                }
                break;
            case 'eventNameAlreadyExists':
                var form_values = '&mode=id_from_event_name&event=' + formValue;
                var url = "index.php?" + form_values;
                var hasError = false;

                $.ajax({
                    url: url,
                    async: false,
                    dataType: 'text',
                    success: function(existingTrapId) {

                        if ('trapId' in options && options.trapId !== existingTrapId && existingTrapId != 0) {

                            // used for the edit form
                            $(errorMessageSelector).show();
                            $(validationMessageSelector).show();
                            hasError = true;
                        }

                        if (!('trapId' in options) && existingTrapId != 0) {

                            // used for create and copy
                            $(errorMessageSelector).show();
                            $(validationMessageSelector).show();
                            hasError = true;
                        }
                    }
                });

                if (hasError) {
                    return 1;
                }

                break;
        }

        return 0;
    }

    function validateTrapForm(formName) {

        var event    = document.forms[formName]["event"].value;
        var oid      = document.forms[formName]["oid"].value;
        var severity = document.forms[formName]["severity"].value;
        var category = document.forms[formName]["category"].value;
        var errors = 0;

        errors += validateFormField(event,    '.validationError', '.eventSpaceMessage',    'hasWhiteSpace');
        errors += validateFormField(oid,      '.validationError', '.oidSpaceMessage',      'hasWhiteSpace');
        errors += validateFormField(severity, '.validationError', '.severitySpaceMessage', 'hasWhiteSpace');
        errors += validateFormField(event,    '.validationError', '.eventRequiredMessage',    'notEmpty');
        errors += validateFormField(oid,      '.validationError', '.oidRequiredMessage',      'notEmpty');
        errors += validateFormField(severity, '.validationError', '.severityRequiredMessage', 'notEmpty');
        errors += validateFormField(category, '.validationError', '.categoryRequiredMessage', 'notEmpty');

        return errors;
    }
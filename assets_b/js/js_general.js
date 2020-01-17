/* global bootbox, yii */
var applicationName = "ESB FNB";
var modalOpen = false;

$(document).on('pjax:complete', function (e) {
    // Run init every pjax finished
    initInput();
    init();
    var title = document.title;
    if (!title.startsWith(applicationName + " - ")) {
        document.title = applicationName + " - " + title;
    }

    if (modalOpen) {
//        // Activate select2 on modal popup
//        $('select').each(function () {
//            $(this).select2({
//                theme: 'krajee',
//                width: '100%',
//            });
//            //Remove title on select2
//            $('.select2-selection__rendered').removeAttr('title');
//        });
    }

    $('.tooltip').remove();
});

$(document).ready(function () {
    // Run init every time document is ready
    initInput();
    init();

    $('form').on('beforeValidate', function () {
        $(this).find(':submit').prop('disabled', true);
    });

    $('form').on('afterValidate', function (event, messages, errorAttributes) {
        if (errorAttributes.length > 0) {
            $(this).find(':submit').prop('disabled', false);
        }
    });

    $('form').on('beforeSubmit', function () {
        $(this).find(':submit').prop('disabled', true);
    });

    // Remove masked on numeric input
    $('.form-active, .form-filter').on('beforeSubmit', function () {
        $('.input-decimal, .input-integer').each(function () {
            if ($(this).val() !== "") {
                if ($(this).inputmask('hasMaskedValue')) {
                    var newValue = getNumericValue($(this).val());
                    $(this).inputmask('remove');
                    $(this).val(newValue);
                    $(this).removeClass('inputInitiated');
                }
            }
        });
    });

    // Auto focus in form to the first input, textarea, or select
    var input = $('form').find('input[type=text],input[type=password],textarea,select').filter(':enabled:visible:first:not(.krajee-datepicker)');
    var tagName = input.prop("tagName");
    if (tagName === "SELECT") {
        $(input).next(".select2").find(".select2-selection").focus();
    } else {
        $(input).focus();
    }

    // Handle enter on form focus to next input
    $(document).on('keydown', '.form-active input', function (e) {
        var self = $(this);
        var form = self.parents('form:eq(0)');
        var focusable = form.find('input, textarea, span.select2-selection, a, button').filter(':visible');
        var next = focusable.eq(focusable.index(this) + 1);
        if (e.keyCode === 13) {
            if (next.length) {
                e.preventDefault();
                next.focus();
            }
        }
    });

    $(document).on('click', '.sidebar-toggle', function () {
        if ($('body').hasClass("sidebar-collapse") && $('body').hasClass("sidebar-open")) {
            $('body').removeClass("sidebar-collapse");
        }
    });
});

$(window).on('load', function() {
    /* START DIRRTY TO CHECK FORM CHANGING */

    $('.form-active').dirrty({
        preventLeaving: true
    });

//    $('.form-active #btnCancel').on('click', function () {
//        if ($(".form-active").dirrty('isDirty')) {
//            bootbox.confirm("Unsaved data will be discarded. Are you sure?", function (confirmed) {
//                if (confirmed === true) {
//                    window.location = $('.form-active #btnCancel').attr('href');
//                }
//            });
//            return false;
//        }
//    });
    /* END DIRRTY TO CHECK FORM CHANGING */

    /* START HANDLE ENTER ON FORM */
    $('.form-active').on('keydown', 'input', function (e) {
        var self = $(this);
        var form = self.parents('form:eq(0)');
        var focusable = form.find('input, textarea, span.select2-selection, a, button').filter(':visible');
        var next = focusable.eq(focusable.index(this) + 1);
        if (e.keyCode === 13) {
            if (next.length) {
                e.preventDefault();
                next.focus();
            }
        }
    });
    /* END HANDLE ENTER ON FORM */
});

function initInput() {
    /* START CUSTOMIZE TOOLTIP */
    // Activate tooltip
    $('[title]:not([data-template])').tooltip({container: 'body'});
    // Rename tooltip for kartik DateControl
    $('.kv-date-remove').attr('data-original-title', 'Clear Field');
    $('.kv-date-calendar').attr('data-original-title', 'Select Date');
    /* END CUSTOMIZE TOOLTIP */

    // Activate select2
    $('select').each(function () {
        $(this).select2({
            theme: 'krajee',
            width: '100%'
        });
        //Remove title on select2
        $('.select2-selection__rendered').removeAttr('title');
    });

    //Activate mask for integer input
    $('.input-integer:not(.inputInitiated)').inputmask({
        alias: 'decimal',
        digits: 0,
        groupSeparator: '.',
        radixPoint: ',',
        autoGroup: true,
        removeMaskOnSubmit: false,
        rightAlign: true
    });

    //Activate mask for numeric text input
    $('.input-numeric:not(.inputInitiated)').inputmask({
        alias: 'decimal',
        digits: 0,
        groupSeparator: '',
        radixPoint: '',
        autoGroup: false,
        removeMaskOnSubmit: false,
        rightAlign: false
    });

    //Activate mask for decimal input
    $('.input-decimal:not(.inputInitiated)').inputmask({
        alias: 'decimal',
        digits: 4,
        groupSeparator: '.',
        radixPoint: ',',
        'allowMinus': true,
        autoGroup: true,
        removeMaskOnSubmit: false,
        rightAlign: false
    });

    $('.input-integer:not(.inputInitiated), .input-decimal:not(.inputInitiated)').click(function () {
        $(this).select();
    });

    //Activate mask for phone input
    $('.input-phone:not(.inputInitiated)').inputmask({
        removeMaskOnSubmit: false,
        mask: '+9[9]-[9][9][9][9][9][9][9][9][9][9][9]'
    });

    //Activate mask for email
    $('.input-email:not(.inputInitiated)').inputmask({
        alias: 'email'
    });

    //Activate mask for npwp input
    $('.input-npwp:not(.inputInitiated)').inputmask({
        removeMaskOnSubmit: false,
        mask: '9[9].9[9][9].9[9][9].9-[9][9][9].9[9][9]'
    });

    //Activate mask for vat invoice input
    $('.input-vat-invoice:not(.inputInitiated)').inputmask({
        removeMaskOnSubmit: false,
        mask: '9[9][9].9[9][9]-9[9].9[9][9][9][9][9][9][9]'
    });
    
    $('.input-integer, .input-numeric, .input-decimal, .input-phone, .input-email, .input-npwp, .input-vat-invoice')
            .addClass('inputInitiated');

    // Stay focus after close selections
    $(document).on('select2:close', 'select', function () {
        $(this).siblings('.select2-container').find('.select2-selection').focus();
    });

    // Handle click row to click checkbox
    $(document).on('click', 'table.table-index tbody tr:has(input[type=checkbox])', function () {
        var checked = $(this).find('input[type=checkbox]').prop('checked');
        $(this).find('input[type=checkbox]').prop('checked', !checked).trigger('change');
    });

    $(document).on('click', 'table.table-index tbody tr:has(input[type=checkbox]) a, table.table-index tbody tr input[type=checkbox]', function (e) {
        e.stopPropagation();
    });
}

function initDatePicker(selector) {
    $(selector).each(function () {
        $(this).kvDatepicker({
            "autoclose": true,
            "format": "dd-mm-yyyy"
        });
    });
}

function initDateControl(selector) {
    $(selector).each(function () {
//        $(this).parent().siblings('input[type=hidden]').datecontrol('destroy');
        $(this).datecontrol(eval($(this).data('krajee-datecontrol')));
        $(this).parent().kvDatepicker(eval($(this).data('krajee-kvdatepicker')));
        initDPRemove($(this).attr('id'));
        initDPAddon($(this).attr('id'));
    });
}

if (!modalOpen) {
    /* START MODAL BUTTON HANDLER */
    var input_selector = '.input-group:has(.input-group-btn):has(.ModalDialogButton) input';
    if ($(input_selector).length > 0) {
        $(input_selector).css('cursor', 'pointer');
    }

    $(document).on('keypress click', input_selector, function (e) {
        if ($(this).siblings('.input-group-btn').find('a.ModalDialogButton').length > 0) {
            e.preventDefault();
            $(this).siblings('.input-group-btn').find('a.ModalDialogButton:first').trigger('click');
            $('table tr:has(.ModalDialogButton)').css('cursor', 'pointer');
        }
    });

    $(document).on("click", ".ModalDialogButton", function (event) {
        event.preventDefault();
        var self = this;
        var href = $(this).attr("href");
        showLoading();
        $.ajax({
            url: href,
            success: function (data) {
                var smallBox = false;

                $.each($(self).data(), function (i) {
                    var value = $(self).data(i);
                    if (i.startsWith('target')) {
                        $('#ModalDialogBody').data(i, value);
                    }

                    if (i === 'nextFocus') {
                        $('#ModalDialogBody').data(i, value);
                    }

                    if (i === 'small-box') {
                        smallBox = true;
                    }
                });

                $('#ModalDialogContainer div:first-child').removeClass('modal-lg');
                $('#ModalDialogContainer div:first-child').removeClass('modal-sm');

                if (smallBox) {
                    $('#ModalDialogContainer div:first-child').addClass('modal-sm');
                } else {
                    $('#ModalDialogContainer div:first-child').addClass('modal-lg');
                }

                data = replaceAll(data, 'btnCancel', 'btnCancelModal');
                $('#ModalDialogBody').data("sender", self);
                $('#ModalDialogBody').html(data);
                $('#ModalDialogBody .form-active').dirrty({
                    preventLeaving: false
                });

                //initInput();
                $('#ModalDialogBody table tr:has(.ModalDialogSelect)').css('cursor', 'pointer');
                $('#ModalDialogBody select').each(function () {
                    $(this).select2({
                        dropdownParent: $('#ModalDialogBody'),
                        theme: 'krajee',
                        width: '100%'
                    });
                });

                hideLoading();
                modalOpen = true;
                $('#ModalDialogContainer').modal();
            },
            error: function (response) {
                $.unblockUI();
                smallAlert(response.responseJSON.message);
            }
        });
    });

    $(document).on('click', 'tr:has(.ModalDialogSelect)', function (e) {
        e.preventDefault();
        var self = $(this).find('.ModalDialogSelect');
        var result = {};
        $.each($(self).data(), function (i) {
            if (i.startsWith("return")) {
                var target = i.replace("return", "target");
                var selector = $('#ModalDialogBody').data(target);
                var value = $(self).data(i);
                var arrayKey = i.replace("return", "");
                $(selector).val(value).trigger('change');
                result[arrayKey] = value;
            }
        });

        $('#ModalDialogBody').data("result", JSON.stringify(result));
        $('#ModalDialogBody').data("success", true);
        $("#ModalDialogContainer").modal("hide");
    });

    $(document).on("click", ".ModalDialogMultiSelectButton", function (event) {
        event.preventDefault();
        var gridId = $(this).data("gridId");
        var keys = $(gridId).yiiGridView('getSelectedRows');
        var target = $('#ModalDialogBody').data("targetKeys");
        var result = [];

        keys.forEach(function (key, index) {
            var data = $(gridId + ' table > tbody > tr[data-key="' + key + '"] .ModalDialogCheckbox').data("result");
            result.push(data);
        });
        $(target).val(keys).trigger('change');
        $('#ModalDialogBody').data("success", true);
        $('#ModalDialogBody').data("result", JSON.stringify(result));
        $("#ModalDialogContainer").modal("hide");
    });

    $(document).on("click", ".ModalDialogFormSelectButton", function (event) {
        event.preventDefault();
        var result = $(this).data('result');
        $('#ModalDialogBody').data('success', true);
        $('#ModalDialogBody').data('result', JSON.stringify(result));
        $('#ModalDialogContainer').modal('hide');
    });

    $(document).on("click", "#btnCancelModal", function (event) {
        event.preventDefault();
        $("#ModalDialogContainer").modal("hide");
    });

    $('#ModalDialogBody').on('beforeSubmit', 'form', function (e) {
        var form = $(this);
        showLoading();
        $('#ModalDialogBody form .input-decimal, .input-integer').each(function () {
            var newValue = getNumericValue($(this).val());
            $(this).inputmask('remove');
            $(this).val(newValue);
            $(this).removeClass('inputInitiated');
        });

        var formData = form.serialize();
        formData = formData + "&IS_MODAL_SUBMIT=true";
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                var result = {};

                for (var key in data) {
                    var value = data[key];
                    var target = "target" + key.charAt(0).toUpperCase() + key.slice(1);
                    var selector = $('#ModalDialogBody').data(target);
                    $(selector).val(value).trigger('change');
                    result[key] = value;
                }
                hideLoading();
                $('#ModalDialogBody').data("success", true);
                $('#ModalDialogBody').data("result", JSON.stringify(result));
                $("#ModalDialogContainer").modal("hide");
                $(document).trigger("MODAL_CREATE_SUCCESS");
            },
            error: function () {
                hideLoading();
                yii.alert("Save data failed");
            }
        });
    }).on('submit', 'form', function (e) {
        e.preventDefault();
    });

    $('#ModalDialogContainer').on('shown.bs.modal', function () {
        $("#ModalDialogBody input:text:first").focus();
        modalOpen = true;
    });

    $('#ModalDialogContainer').on('hide.bs.modal.prevent', function (e) {
        if (e.target.id === 'ModalDialogContainer') {
            if ($("#ModalDialogBody").data("success")) {
                return true;
            }
        
            var confirmDirty = $("#ModalDialogBody").data("confirmDirty");

            if (!confirmDirty && $("#ModalDialogBody .form-active").dirrty('isDirty')) {
                yii.confirm("Unsaved data will be discarded. Are you sure?", function () {
                    $("#ModalDialogBody").data("confirmDirty", true);
                    $("#ModalDialogContainer").modal("hide");
                });
                return false;
            } else {
                return true;
            }   
        } else {
            return false;
        }
    });

    $('#ModalDialogContainer').on('hidden.bs.modal', function () {
        var success = $('#ModalDialogBody').data("success");
        var sender = $('#ModalDialogBody').data("sender");
        var nextFocus = $('#ModalDialogBody').data("nextFocus");
        var result = $('#ModalDialogBody').data("result");

        $.each($('#ModalDialogBody').data(), function (i) {
            $('#ModalDialogBody').removeData(i);
        });

        $("#ModalDialogBody").find("*").off();
        $('#ModalDialogBody').html("");

        if (success === true) {
            $(nextFocus).focus();
            $(sender).trigger("MODAL_DIALOG_SUCCESS", {"data": JSON.parse(result)});
            $(this).data('bs.modal', null);
            modalOpen = false;
        }
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
        if ($('.modal').hasClass('in')) {
            modalOpen = true;
            $('body').addClass('modal-open');
        }
    });
    /* END MODAL BUTTON HANDLER */
}

function init() {
    // Disabled button submit every submit to prevent double submit
//    $(document).on('click', '.btn-submit' , function() {
//        $(this).prop('disabled', true);
//        $('form').submit();
//    });

    // Handle WindowDialogBrowse TextBox to emulate button click on TextBox click.
    var input_selector = '.input-group:has(.input-group-btn):has(.WindowDialogBrowse) input';
    $(document).on('keypress click', input_selector, function (e) {
        if ($(this).siblings('.input-group-btn').find('a.WindowDialogBrowse').length > 0) {
            e.preventDefault();
            $(this).css('cursor', 'pointer');
            $(this).siblings('.input-group-btn').find('a.WindowDialogBrowse').trigger('click');
            $('table tr:has(.WindowDialogSelect)').css('cursor', 'pointer');
        }
    });

    initGridButton();
}

function initGridButton() {
    // Handle button click on index page
    $('[data-status]').on('click', function (e) {
        var action = $(this).data('action');
        var status = $(this).data('status');
        var extraStatus = $(this).data('extra-status');
        var statusMsg = $(this).data('msg');
        var extraStatusMsg = $(this).data('extra-msg');
        var transType = $(this).data('transaction');

        if (transType == 'ms_branch' || transType == 'ms_userrole' || transType == 'tr_endperiod' || transType == 'ms_currency' || transType == 'tr_assetdata') {
            if (extraStatus == 1) {
                e.preventDefault();
                regularAlert(extraStatusMsg);
                return false;
            }
        } else {
            if (status !== 0) {
                if (action === 'update' && (status != 1 && status != 2 && status != 3)) {
                    if (transType == 'tr_goodsreceipthead' || transType == 'tr_purchaseinvoicehead' || transType == 'tr_goodsdeliveryhead' || transType == 'tr_purchasehead' || transType == 'tr_productsaleshead') {
                        if (jQuery.inArray(status, extraStatus) != -1) {
                            e.preventDefault();
                            regularAlert(extraStatusMsg);
                            return false;
                        }
                    } else {
                        e.preventDefault();
                        regularAlert(statusMsg);
                        return false;
                    }
                } else if (action === 'delete' && (status != 1 && status != 2 && status != 3)) {
                    e.preventDefault();
                    if (transType === 'tr_goodsreceipthead' || transType === 'tr_purchaseinvoicehead' || transType == 'tr_purchasehead' || transType == 'tr_productsaleshead') {
                        if (jQuery.inArray(status, extraStatus) != -1) {
                            e.preventDefault();
                            regularAlert(extraStatusMsg);
                            return false;
                        }
                    } else {
                        e.preventDefault();
                        regularAlert(statusMsg);
                        return false;
                    }
                } else if (action === 'authorize' && (status != 1)) {
                    e.preventDefault();
                    regularAlert(statusMsg);
                    return false;
                } else if (action === 'finish' && (status != 3 && status != 4 && status != 6)) {
                    e.preventDefault();
                    regularAlert(statusMsg);
                    return false;
                } else if (action === 'close' && (status != 3 && status != 4 && status != 6)) {
                    e.preventDefault();
                    regularAlert(statusMsg);
                    return false;
                }

                if (action === 'update' && status == 3) {
                    if ((transType === 'tr_purchaserequesthead' || transType === 'tr_goodsdeliveryhead' || transType === 'tr_cashpurchasehead' || transType === 'tr_bankreconciliationhead' || transType === 'tr_cashcount' || transType === 'tr_goodsreceipthead' || transType === 'tr_memberdeposit' || transType === 'tr_advancepayment' || transType === 'tr_productionresulthead' || transType === 'tr_productionreturnhead' || transType === 'tr_materialdeliveryhead' || transType === 'tr_customeradvancepayment') && extraStatus == 1) {
                        e.preventDefault();
                        regularAlert(extraStatusMsg);
                        return false;
                    }
                }

                if (extraStatus === -1) {
                    e.preventDefault();
                    regularAlert(extraStatusMsg);
                    return false;
                }

                if (action === 'delete' && status == 3) {
                    if ((transType === 'tr_purchaserequesthead' || transType === 'tr_goodsdeliveryhead' || transType === 'tr_cashpurchasehead' || transType === 'tr_bankreconciliationhead' || transType === 'tr_cashcount' || transType === 'tr_goodsreceipthead' || transType === 'tr_memberdeposit' || transType === 'tr_advancepayment' || transType === 'tr_productionresulthead' || transType === 'tr_productionreturnhead' || transType === 'tr_materialdeliveryhead' || transType === 'tr_customeradvancepayment') && extraStatus == 1) {
                        e.preventDefault();
                        regularAlert(extraStatusMsg);
                        return false;
                    }
                }
            }
        }
    });

    //set button to disable state
    $('[data-status][data-action]').each(function () {
        var action = $(this).data('action');
        var status = $(this).data('status');
        var extraStatus = $(this).data('extra-status');
        var transType = $(this).data('transaction');
        if (transType == 'ms_branch' || transType == 'ms_userrole' || transType == 'tr_endperiod' || transType == 'ms_currency' || transType == 'tr_assetdata') {
            if (extraStatus == 1) {
                disableGridBtn(this);
            }
        } else {
            if (status !== 0) {
                if (action === 'update' && (status != 1 && status != 2 && status != 3)) {
                    if (transType === 'tr_goodsreceipthead' || transType === 'tr_purchaseinvoicehead' || transType == 'tr_purchasehead' || transType == 'tr_productsaleshead') {
                        if (jQuery.inArray(status, extraStatus) != -1) {
                            disableGridBtn(this);
                        }
                    } else {
                        disableGridBtn(this);
                    }
                } else if (action === 'delete' && (status != 1 && status != 2 && status != 3)) {
                    if (transType === 'tr_goodsreceipthead' || transType === 'tr_purchaseinvoicehead' || transType == 'tr_purchasehead' || transType == 'tr_productsaleshead') {
                        if (jQuery.inArray(status, extraStatus) != -1) {
                            disableGridBtn(this);
                        }
                    } else {
                        disableGridBtn(this);
                    }
                } else if (action === 'authorize' && (status != 1)) {
                    disableGridBtn(this);
                } else if (action === 'finish' && (status != 3 && status != 4 && status != 6)) {
                    disableGridBtn(this);
                } else if (action === 'close' && (status != 3 && status != 4 && status != 6)) {
                    disableGridBtn(this);
                }

                if (action === 'update' && status == 3) {
                    if ((transType === 'tr_purchaserequesthead' || transType === 'tr_goodsdeliveryhead' || transType === 'tr_cashpurchasehead' || transType === 'tr_bankreconciliationhead' || transType === 'tr_cashcount' || transType === 'tr_goodsreceipthead' || transType === 'tr_memberdeposit' || transType === 'tr_advancepayment' || transType === 'tr_productionresulthead' || transType === 'tr_productionreturnhead' || transType === 'tr_materialdeliveryhead' || transType === 'tr_customeradvancepayment') && extraStatus == 1) {
                        disableGridBtn(this);
                    }
                }

                if (extraStatus === -1) {
                    disableGridBtn(this);
                }

                if (action === 'delete' && status == 3) {
                    if ((transType === 'tr_purchaserequesthead' || transType === 'tr_goodsdeliveryhead' || transType === 'tr_cashpurchasehead' || transType === 'tr_bankreconciliationhead' || transType === 'tr_cashcount' || transType === 'tr_goodsreceipthead' || transType === 'tr_memberdeposit' || transType === 'tr_advancepayment' || transType === 'tr_productionresulthead' || transType === 'tr_productionreturnhead' || transType === 'tr_materialdeliveryhead' || transType === 'tr_customeradvancepayment') && extraStatus == 1) {
                        disableGridBtn(this);
                    }
                }
            }
        }
    });

    function disableGridBtn(btn) {
        $(btn).css('cursor', 'not-allowed');
        $(btn).css('color', '#aaa');
        $(btn).tooltip('destroy');
        return true;
    }
}

function removeAllTableRow(obj) {
    $(obj).each(function () {
        $('tr', this).each(function () {
            $(this).remove();
        })
    });
}

function replaceAll(string, find, stringReplace, ignoreCase) {
    if (stringReplace != undefined) {
        stringReplace = stringReplace.toString();
        stringReplace = stringReplace.replace("'", "`");
    }
    return string.replace(new RegExp(find.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"), (ignoreCase ? "gi" : "g")), (typeof (stringReplace) === "string") ? stringReplace.replace(/\$/g, "$$$$") : stringReplace);
}

function isNumeric(number) {
    return !isNaN(parseFloat(number)) && isFinite(number);
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function getRawValue(value) {
    var newValue = value;
    newValue = replaceAll(newValue, '.', '');
    newValue = replaceAll(newValue, '-', '');
    newValue = replaceAll(newValue, '+', '');
    newValue = replaceAll(newValue, ' ', '');
    return newValue;
}

function formatNumber(nStr) {
    nStr += '';
    x = nStr.split(',');
    x1 = x[0];
    x2 = x.length > 1 ? ',' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}

function getNumericValue(value) {
    var newValue = value;
    var padDecimal = "0000";
    newValue = replaceAll(newValue, '.', '');
    newValue = replaceAll(newValue, ',', '.');
    if (newValue.indexOf('.') >= 0) {
        var decimalDigit = newValue.split('.')[1].length;
        newValue = newValue + padDecimal.substring(0, padDecimal.length - decimalDigit);
        if (decimalDigit == 3) {
            //newValue = newValue + padDecimal.substring(0, padDecimal.length - decimalDigit);
        }
    }
    return newValue;
}

function getCompleteDisplayValue(value) {
    var newValue = value;
    var padDecimal = "0000";
    newValue = parseFloat(newValue);
    if (newValue > 0) {
        newValue = newValue.toString();
        if (newValue.split('.')[1] === undefined) {
            newValue = newValue + ',0000';
        } else {
            var decimalDigit = newValue.split('.')[1].length;
            newValue = replaceAll(newValue, ".", ",");
            newValue = formatNumber(newValue);
            newValue = newValue + padDecimal.substring(0, padDecimal.length - decimalDigit);
        }
    }

    return newValue;
}

function getNumericDisplayValue(value) {
    var newValue = value;
    newValue = parseFloat(newValue);
    newValue = newValue.toString();
    newValue = replaceAll(newValue, ".", ",");
    newValue = formatNumber(newValue);

    return newValue;
}

function getDisplayValue(value) {
    var newValue = value;
    newValue = parseFloat(newValue);
    newValue = newValue.toString();
    newValue = replaceAll(newValue, ".", ",");
    newValue = formatNumber(newValue);

    return newValue;
}

function disableForm(formId) {
    var f = document.forms[formId].getElementsByTagName('input');
    for (var i = 0; i < f.length; i++) {
        if (f[i].type !== 'button') {
            f[i].disabled = true;
        }
    }

    var f = document.forms[formId].getElementsByTagName('select');
    for (var i = 0; i < f.length; i++)
        f[i].disabled = true;

    var f = document.forms[formId].getElementsByTagName('textarea');
    for (var i = 0; i < f.length; i++)
        f[i].disabled = true;

    $('#' + formId + ' .kv-date-remove').each(function () {
        $(this).addClass('hidden');
    });

    $('#' + formId + " .file-input .btn-file").addClass("hidden");
    $('#' + formId + ' .kv-date-calendar').each(function () {
        $(this).addClass('hidden');
        $(this).parent().removeClass("input-group");
    });

    $('#' + formId + ' .kv-date-remove').each(function () {
        $(this).addClass('hidden');
    });
}

var maximumCounterListID = [];
function getMaximumCounter() {
    while (true) {
        var uuid = Math.floor((Math.random() * 100000000) + 1);
        ;
        if (maximumCounterListID.indexOf(uuid) === -1) {
            maximumCounterListID.push(uuid);
            return uuid;
        }
    }
}

/* START BOOTBOX CONFIGURATION */
yii.confirm = function (message, ok, cancel) {
    bootbox.setDefaults("locale", 'id');
    bootbox.confirm({
        message: message,
        buttons: {
            confirm: {
                label: 'Yes',
                className: 'btn-primary'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (confirmed) {
            if (confirmed) {
                !ok || ok();
            } else {
                !cancel || cancel();
            }
        }
    });
    // confirm will always return false on the first call
    // to cancel click handler
    return false;
};

function smallAlert(message) {
    bootbox.alert({
        message: message,
        size: 'small',
        backdrop: true
    });
}

function regularAlert(message) {
    bootbox.alert({
        message: message,
        backdrop: true
    });
}
/* END BOOTBOX CONFIGURATION */

function showLoading() {
    $.blockUI({
        css: {border: "3px solid #3C8DBC", padding: "15px"},
        message: "Loading ...",
        baseZ: 100010
    });
}

function hideLoading() {
    $.unblockUI();
}

$(document).on('click', '.WindowDialogBrowse', function (e) {
    if ($(this).is('[disabled]')) {
        return false;
    }
    e.preventDefault();
    var filterInput = $(this).attr('data-filter-Input');
    var filterInput2 = $(this).attr('data-filter-Input2');
    var filterInput3 = $(this).attr('data-filter-Input3');
    var targetValueField = $(this).attr('data-target-value');
    var targetTextField = $(this).attr('data-target-text');
    var targetWidth = $(this).attr('data-target-width');
    var targetHeight = $(this).attr('data-target-height');
    var filter = $(filterInput).val();
    var filter2 = $(filterInput2).val();

    if (filter == undefined || filter == '') {
        filter = -1;
    }

    if (filter2 == undefined || filter2 == '') {
        filter2 = '';
    }

    if (filterInput3 == undefined || filterInput3 == '') {
        filterInput3 = '';
    }

    var browseUrl = $(this).attr('href') + "?filter=" + filter + "&filter2=" + filter2 + "&filter3=" + filterInput3;

    if (targetWidth == undefined) {
        targetWidth = 800;
    }

    if (targetHeight == undefined) {
        targetHeight = 600;
    }
    $(this).focus();
    OpenPopupWindow(browseUrl, targetWidth, targetHeight, targetValueField, targetTextField);
});

$(document).on('click', '.WindowDialogSelect', function (e) {
    e.preventDefault();
    var returnValue = $(this).attr('data-return-value');
    var returnText = $(this).attr('data-return-text');
    ClosePopupWindow(returnValue, returnText);
});

$(document).on('click', 'tr:has(.WindowDialogSelect)', function (e) {
    e.preventDefault();
    var returnValue = $(this).find('.WindowDialogSelect').attr('data-return-value');
    var returnText = $(this).find('.WindowDialogSelect').attr('data-return-text');
    ClosePopupWindow(returnValue, returnText);
});

var popup;
function OpenPopupWindow(url, width, height, targetValueField, targetTextField) {
    popup = window.open(url, 'popUpWindow', "width=" + width + ",height=" + height + ",scrollbars=1,left=170,top=25");
    popup.focus();
    popup.valueField = targetValueField;
    popup.textField = targetTextField;
}

///////////////////// Multiple Select ClosePopupWindow \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

function ClosePopupWindowMultiple(value, text) {
    if (window.opener != null && !window.opener.closed) {
        var valueFieldID = window.valueField;
        var textFieldID = window.textField;
        window.opener.$(valueFieldID).val(value).trigger("change");
        window.opener.$(textFieldID).val(text).trigger("change");
    }
    window.close();
}

//////////////////// END Mutiple Select ClosePopupWindow \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

function ClosePopupWindow(value, text) {
    if (window.opener != null && !window.opener.closed) {
        var valueFieldID = window.valueField;
        var textFieldID = window.textField;
        window.opener.$(valueFieldID).val(value).trigger("change");

        text = JSON.parse(text);
        var i = 0;
        text.forEach(function (entry) {
            var id;
            if (i == 0) {
                id = textFieldID + ", " + textFieldID + "-" + i.toString();
            } else {
                id = textFieldID + "-" + i.toString();
            }
            window.opener.$(id).val(entry).trigger("change");
            i += 1;
        });
    }
    window.close();
}

// Untuk Purchase

$(document).on('click', '[data-modal]', function(e){
    e.preventDefault();
    let targetModal =  $(this).data('modal');
    $modal = $(targetModal);
    $body = $modal.find('.modal-body');
    
    $modal.modal('show');
    $modal.data('sender', this);
    $modal.data('target-value', $(this).data('target-value'));
    
    let href = $(this).attr('href');
    let method = $(this).data('method') || 'GET';
    
    let data = $(this).data();
    let params = {};
    
    let keys = Object.keys($(this).data());
    keys.forEach(function (key) {
        if (key.startsWith('param'))
        {
            let paramName = key.substr(5).toLowerCase();
            params[paramName] = data[key];
        }
        if (key.startsWith('inputparam'))
        {
            let paramName = key.substr(10).toLowerCase();
            $input = $(data[key]);
            
            if ($input.prop('type') == 'checkbox') params[paramName] = $input.prop('checked');
            else params[paramName] = $input.val();
        }
    });
    
    if (href)
    {
        $body.html(`<i class="fa fa-spinner fa-spin" style="font-size:24px; margin-left: 400px;"></i>`);
        $.ajax({
            url: href.updateQueryParamJson(params),
            type: method,
            success: function (data) {
                $body.html(data);
                
                $("tr:has(.modal-dialog-select)").css('cursor', 'pointer');
            },
            error: function (response) {
                bootbox.alert(response.responseJSON.message);
            }
        });
        
    }
});

$(document).on('click', ".modal-dialog-select", function(e){
    e.preventDefault();
    $modal = $(this).parents('.modal');
    
    let $targetValue = $($modal.data('target-value'));
    let value = $(this).data('value');
    let info = $(this).data('info');
    
    $targetValue[0].oldValue = $targetValue.val();
    $targetValue.val(value).change();
    
    let keys = Object.keys(info);
    keys.forEach((key, i) => {
        $(key)[0].oldValue = $(key).val();
        $(key).val(info[key]).change(); 
    });
    
    $(this).data('clicked', true);
    $modal.modal('hide');
});

$(document).on('click', "tr:has(.modal-dialog-select)", function(e){
    e.preventDefault();
    if( $(this).find('.modal-dialog-select').data('clicked')) return;
    
    $(this).find('.modal-dialog-select').click();
});

String.prototype.updateQueryParamJson = function (json) {
    return updateQueryParamJson(this, json)
};

function updateQueryParamJson(uri, json) {
    var keys = Object.keys(json);
    keys.forEach(function (key) {
        uri = updateQueryParam(uri, key, json[key]);
    });
    return uri;
}

String.prototype.updateQueryParam = function (key, value) {
    return updateQueryParam(this, json)
};

function updateQueryParam(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return encodeURI(uri + separator + key + "=" + value);
    }
}

String.prototype.replaceAll = function (find, replace, ignoreCase) {
    var str = this;
    return replaceAll(str, find, replace, ignoreCase);
};

String.prototype.replaces = function (keyValue)
{
    let str = this;
    let keys = Object.keys(keyValue);
    keys.forEach(function (key) {
        str = str.replaceAll(key, keyValue[key]);
    });
    
    return str;
}

String.prototype.toFloat = function () {
    let str = this;
    
    if (str && str.split(',').length == 2 || str.split('.')[str.split('.').length - 1].length == 3)
    {
        return parseFloat(str.replaces({' ': '', '.': '', ',':'.'}));
    }
    
    return parseFloat(str);
};

Number.prototype.toFloat = function () {
   return this;
};


Number.prototype.toDecimal = function (c, d, t) {
    var n = this;
    
    var defaultC = n.toString().split('.').length > 1 ? n.toString().split('.')[1].length : 0;
    
    var c = isNaN(c = Math.abs(c)) ? defaultC : c,
            d = d == undefined ? "," : d,
            t = t == undefined ? "." : t,
            s = n < 0 ? "-" : "",
            i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
            j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function formatDecimalNumber(value) {
    var newValue = value;
    if (newValue.toString().indexOf('.') >= 0) {
        var decimalDigit = newValue.toString().split('.')[1].length;
        if (decimalDigit === 3) {
            newValue = newValue.toFixed(4);
        }
    }

    return newValue;
}

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}
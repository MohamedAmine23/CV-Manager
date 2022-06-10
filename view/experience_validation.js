let script = document.createElement('script');
document.getElementsByTagName('head')[0].appendChild(script)

function getValidationConfig() {
    let value;
    $.ajax({
        type: "GET",
        async: false,
        url: "experience/get_validation_config/",
        success: function (data) {
            value = data;
        }
    });
    return value;
}
function getSliderConfig() {
    let value;
    $.ajax({
        type: "GET",
        async: false,
        url: "experience/get_slider_config/",
        success: function (data) {
            value = data;
        }
    });
    return value;
}
function validationPlugin() {
    if (getValidationConfig() == "true") {
        const validation = new JustValidate('#experienceForm');
        validation
            .addField("#start_date", [{
                plugin: JustValidatePluginDate(() => ({
                    require: true,
                    isAfter: getBirthdate(),
                })),
                errorMessage: "Start date can't be after the member's birthdate!",
            }, {
                plugin: JustValidatePluginDate(() => ({
                    require: true,
                    isBefore: ($("#stop_date").val().length == 0 ? new Date() : $("#stop_date").val()),
                })),
                errorMessage: "Start date can't exceed the stop date!",
            }], {successMessage: 'Start date is valid.'})
            .addField("#stop_date", [{
                plugin: JustValidatePluginDate(() => ({
                    isAfter: $("#start_date").val(),
                })),
                errorMessage: "Stop date can't be after the start date!",
            }, {
                plugin: JustValidatePluginDate(() => ({
                    isBefore: new Date(),
                })),
                errorMessage: "Stop date can't be after the current date!",
            }], {successMessage: 'Stop date is valid.'})
            .addField("#title", [
                {
                    rule: "required",
                    errorMessage: "Title is required",
                },
                {
                    rule: 'minLength',
                    value: 3,
                    errorMessage: "Title length must be greather than 3",
                },
            ], {successMessage: 'Title  is valid.'})
            .addField("#description", [
                {
                    rule: "minLength",
                    value: 10,
                    errorMessage: "Description length must be greater than 10 !"
                }, {
                    rule: "maxLength",
                    value: getMaxDescriptionLength(),
                    errorMessage: "Description length can't exceed " + getMaxDescriptionLength() + " characters!",
                }], {successMessage: ' Description is valid.'})
            .onSuccess(function (event) {
                event.target.submit();
            });
    }
}

function getMaxDescriptionLength() {
    var len
    $.ajax({
        type: "GET",
        async: false,
        url: "experience/get_max_length/",
        success: function (data) {
            $('#maxSize').text("/" + data);
            len = data
        }
    });
    return parseInt(len);
}

function getBirthdate() {
    var date
    $.ajax({
        type: "POST",
        url: "user/get_user_birthdate",
        async: false,
        data: {
            member: $("#member").val()
        },
        success: function (data) {
            date = data;
        }
    })
    return date;
}

function totalCharsDescr() {
    $('#totalChars').html("" + $('#description').val().length);
    if (getValidationConfig() == "false") {
        validateDescription();
    }

}

function validateForm() {
    if (getValidationConfig() == "false") {
        return validateDate() && validateTitle();
    }

}

function validateDate() {
    if (getValidationConfig() == "false") {
        return validateStartDate() && validateStopDate();
    }

}

function validateStartDate() {
    if (getValidationConfig() == "false") {
        const date = new Date($('#start_date').val());
        if (date > Date.now()) {
            $('#error_start').text("The start date can't be in the future. Back to the Future !").attr("class", "txt_delete");
            return false;
        }
        if (new Date($('#stop_date').val()) < date) {
            $('#error_start').text("The start date can't be after the stop date.").attr("class", "txt_delete");
            return false;
        }
        $(function () {
            $.ajax({
                type: "POST",
                url: "experience/valid_start_date",
                data: {
                    start_date: $("#start_date").val(),
                    member: $("#member").val()
                },
                success: function (data) {
                    if (data == "true") {
                        $('#error_start').text("The start date is valid.").attr("class", "txt_valid");
                        return true;
                    } else {
                        $('#error_start').text("The start date cant be before the birthdate.").attr("class", "txt_delete");
                        return false;
                    }
                }
            })
        });

    }
}

function validateStopDate() {
    if (getValidationConfig() == "false") {
        if (new Date($('#stop_date').val()) > Date.now()) {
            $('#error_stop').text("The stop date can't be in the future.").attr("class", "txt_delete");
            return false;
        }
        if (new Date($('#stop_date').val()) < new Date($('#start_date').val())) {
            $('#error_stop').text("The stop date can't be before the start date.").attr("class", "txt_delete");
            return false;
        }
        $('#error_stop').text("The stop date is valid.").attr("class", "txt_valid");
        return true;
    }

}

function validateTitle() {
    if (getValidationConfig() == "false") {
        if ($('#title').val().length < 3) {
            $('#error_title').text("The title must be at least 3 characters.").attr("class", "txt_delete");
            return false;
        }
        $('#error_title').text("The title is valid.").attr("class", "txt_valid");
        return true;
    }

}

function validateDescription() {
    if (getValidationConfig() == "false") {
        if ($('#description').val().length < 10) {
            $('#error_descr').text("The description must be at least 10 characters.").attr("class", "txt_delete");
            return false;
        }
        $.ajax({
            type: "GET",
            url: "experience/get_max_length/",
            success: function (data) {
                if ($('#description').val().length > data) {
                    $('#error_descr').text("The description must be less than " + data + " characters.").attr("class", "txt_delete");
                    $('#description').attr("maxlength", data);
                }
                $('#maxSize').text("/" + data);
            }
        })
        $('#error_descr').text("The description is valid.").attr("class", "txt_valid");
        return true;
    }

}

function clickable_skills(id) {
    $(".bg_blue").click(function () {
        var span = $("span[skill=" + $(this).attr("skill") + "]");
        $.ajax({
            type: "POST",
            url: "mastering/add_service",
            data: {
                level: 1,
                skill: $(this).attr("skill"),
                user: id
            },
            success: function (data) {
                span.removeClass("bg_blue");
                span.addClass("bg_violet");
            }
        });
    });
}

function skill_hover() {
    let temp;
    $(".bg_blue").hover(function () {
        temp = $(this).text();
        $(this).html($(this).text() + " <i class=\"fa fa-plus\" aria-hidden=\"true\"></i>");
    }, function () {
        $(this).text(temp);
    });
}

//Filter
function simpleFilter(id) {
    $("#filter").html("<h2>Filters</h2>" +
        "\nStart year: <input class=\"filter\" id=\"start_year\" type=\"number\" value=\"1900\"> - " +
        "End year: <input class=\"filter\" id=\"end_year\" type=\"number\" value=\"2099\"> " +
        "<span class=\"error_filter txt_delete\"></span>");

    $(".filter").on("change", function () {
        $.ajax({
            type: "POST",
            url: "experience/get_experiences_filtered",
            data: {
                filter_start: $("#start_year").val(),
                filter_end: $("#end_year").val(),
                user: id
            },
            success: function (request) {
                var arr = JSON.parse(request);
                $(".row").hide();
                $.each(arr, function (index, value) {
                    $(".row[experience=" + value.id + "]").show();
                });
                if (arr.length == 0) {
                    $("#filter_message").text("There is no experience with this filter");
                } else {
                    $("#filter_message").text("");
                }
            }
        });
    });
    $("#start_year").on("change", validateFilter);
    $("#end_year").on("change", validateFilter);
}

function validateFilter() {
    if ($("#start_year").val() > $("#end_year").val()) {
        $(".error_filter").text("The filter is not correct");
    } else {
        $(".error_filter").text("");
    }
}

function sliderFilter(id) {
    $(function () {
        $('<span id="values"></span>').insertBefore("#filter");
        $("#filter").slider({
            range: true,
            min: 1900,
            max: 2099,
            values: [1950, 2050],
            slide: function (event, ui) {
                $("#values").text("Filtered experience from " + $("#filter").slider("values", 0) + " to " + $("#filter").slider("values", 1));
                $.ajax({
                    type: "POST",
                    url: "experience/get_experiences_filtered",
                    data: {
                        filter_start: ui.values[0],
                        filter_end: ui.values[1],
                        user: id
                    },
                    success: function (request) {
                        var arr = JSON.parse(request);
                        $(".row").hide();
                        $.each(arr, function (index, value) {
                            $(".row[experience=" + value.id + "]").show();
                        });
                        if (arr.length == 0) {
                            $("#filter_message").text("There is no experience with this filter");
                        } else {
                            $("#filter_message").text("");
                        }
                    }
                });
            }
        });
        $("#values").text("Filtered experience from " + $("#filter").slider("values", 0) + " to " + $("#filter").slider("values", 1));
    });
}

//Delete modal Dialog
function init_modal_dialog() {
    $(".btn-outline-danger").removeAttr("href").text("Delete").on("click", function () {
        var experience = $(this).parent().parent().parent();
        deleteXPConfirm(experience);
        // console.log(experience.attr("experience"));
    });
}

function deleteXP(id, member, temp) {

    $.ajax(
        {
            type: "POST",
            //async: false,
            url: "experience/delete_service/",
            data: {id: id, member: member},
            success: function (data) {
                console.log(data);
                if (data == "true") {
                    temp.remove();
                } else {
                    alert_error();
                }
            }
        }
    );

}

function alert_error() {
    alert("delete Experience :Error");
}

function deleteXPConfirm(experience) {

    const expId = experience.attr("experience");
    const member = experience.attr("member");
    $('#message_to_delete_xp').text(experience.find(".experience_details").text());
    $('#confirmDialogDeleteXP').removeAttr("hidden");
    $('#confirmDialogDeleteXP').dialog({
        resizable: false,
        height: 300,
        width: 600,
        modal: true,
        autoOpen: true,
        buttons: {
            Confirm: function () {
                deleteXP(expId, member, experience);
                $(this).dialog("close");
            },
            Cancel: function () {
                $(this).dialog("close");
            }
        }
    });
    return false;
}
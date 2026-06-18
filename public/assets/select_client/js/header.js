/**
 * Mobile Number Validation
 * Logic: Starts with 8801 and follows specific operator codes
 * this code will go in header.js
 */
// function checkMobileNumber(mobileNumber, fieldId) {
//     if (mobileNumber.length === 13) {
//         var string = mobileNumber;
//         var re = new RegExp(
//             "^8801[3-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]",
//         );
//         if (re.test(string)) {
//             // Valid
//         } else {
//             sweetAlert(
//                 "Please enter valid mobile number...! eg. 88017XXXXXXXX",
//             );
//             document.getElementById(fieldId).value = "";
//             document.getElementById(fieldId).select();
//         }
//     } else {
//         sweetAlert("Please enter valid mobile number...! eg. 88017XXXXXXXX");
//         document.getElementById(fieldId).value = "";
//         document.getElementById(fieldId).select();
//     }
// }

function checkMobileNumber(mobileNumber, fieldId) {
    var re = /^8801\d{9}$/;

    if (!re.test(mobileNumber)) {
        sweetAlert("Please enter valid mobile number...! eg. 88017XXXXXXXX");
        document.getElementById(fieldId).value = "";
        document.getElementById(fieldId).select();
    }
}

function checkEmail(email, fieldId) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        $("#" + fieldId).val("");
        sweetAlert("Please enter valid email address...! eg. name@domain.com");
    }
}

function validMobileNoCheck(mobileNumber) {
    if (mobileNumber.length === 13) {
        var string = mobileNumber;
        var re = new RegExp(
            "^8801[3|5-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]",
        );
        if (re.test(string)) {
            return 1;
        } else {
            return 2;
        }
    } else {
        return 2;
    }
}

function validEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return 2;
    }
    return 1;
}

function getInputData(fieldsArr) {
    var jsonVariable = {};
    var inputArr = new Array();
    var requiredFlag = 1;
    for (var i = 0; i < fieldsArr.length; i++) {
        var inputTextValue = "";
        inputArr = fieldsArr[i].split("|");

        inputTextValue = $.trim($("#" + inputArr[0]).val());
        if (inputArr.length === 2) {
            if (inputTextValue === "") {
                requiredFlag = 0;
                $("#" + inputArr[1]).attr("class", "error");
            } else {
                $("#" + inputArr[1]).attr("class", "hidden error");
            }
        }
        jsonVariable[inputArr[0]] = inputTextValue;
    }
    if (requiredFlag === 0) {
        return false;
    }
    return jsonVariable;
}

function getReuiredFiledErrorMsg() {
    return "<strong><li>Fields are requried</li></strong>";
}

function hideErrorDiv() {
    $("#errorDiv").attr("class", "alert alert-danger hidden");
}

function showArrayErrorMsg(msgArray) {
    if (msgArray.length > 0) {
        showErrorMsg(msgArray.join(""));
    } else {
        hideErrorDiv();
    }
}

function showDivLoader(showLoaderDiv) {
    var loaderHtml =
        '<div id ="div-loader" class="div-loader-wrapper">\n\
                        <div class="loader">\n\
                            <div class="preloader pl-size-sm">\n\
                                <div class="spinner-layer pl-vroom-orange">\n\
                                    <div class="circle-clipper left">\n\
                                        <div class="circle"></div>\n\
                                    </div>\n\
                                    <div class="circle-clipper right">\n\
                                        <div class="circle"></div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                            <p>Please wait...</p>\n\
                        </div>\n\
                    </div>';
    $("#" + showLoaderDiv).html(loaderHtml);
}

function showDivLoaderXs(showLoaderDiv) {
    var loaderHtml =
        '<div id ="div-loader" class="div-loader-wrapper">\n\
                        <div class="loader">\n\
                            <div class="preloader pl-size-xs">\n\
                                <div class="spinner-layer pl-vroom-orange">\n\
                                    <div class="circle-clipper left">\n\
                                        <div class="circle"></div>\n\
                                    </div>\n\
                                    <div class="circle-clipper right">\n\
                                        <div class="circle"></div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                            <p>Please wait...</p>\n\
                        </div>\n\
                    </div>';
    $("#" + showLoaderDiv).html(loaderHtml);
}

function hideDivLoader(hideLoaderDiv) {
    $("#" + hideLoaderDiv).hide();
}

function showErrorMsg(errorMsg = "") {
    if (errorMsg !== "") {
        $("#errorDiv").attr("class", "alert alert-danger");
        document.getElementById("errorDiv").innerHTML = errorMsg;
        var etop = $("#divTop").offset().top;
        $("html, body").animate(
            {
                scrollTop: etop,
            },
            1000,
        );
    }
}

/**
 * Laravel Specific: Using url('/') instead of base_url()
 */
function successAlert(message = null, redirectUrl = null) {
    swal(
        {
            title: "Successful",
            text: message,
            type: "success",
            closeOnConfirm: false,
            confirmButtonText: "Ok",
            confirmButtonColor: "#A5DC86",
        },
        function () {
            // Updated to use Laravel home URL helper
            window.location.href = "{{ url('/') }}/" + redirectUrl;
        },
    );
}

function alertRedirect(message = null, redirectUrl = null) {
    alert(message);
    window.location.href = "{{ url('/') }}/" + redirectUrl;
}

function failAlert(message = null, redirectUrl = null, customTitle = "Failed") {
    swal(
        {
            title: customTitle,
            text: message,
            type: "warning",
            closeOnConfirm: false,
            confirmButtonText: "Ok",
            confirmButtonColor: "#ec6c62",
        },
        function () {
            window.location.href = "{{ url('/') }}/" + redirectUrl;
        },
    );
}

function showLoader() {
    $("#loader").show();
}

function hideLoader() {
    $("#loader").hide();
}

function getErrorBlockMsg(errorStr, msgArray) {
    if (jQuery.inArray(errorStr, msgArray) === -1) {
        msgArray.push(errorStr);
    }
    return msgArray;
}

function getTimeAmPmFormat(time = null) {
    if (time === null || time === "") {
        return "";
    }
    return new Date("1/01/2018 " + time)
        .toLocaleTimeString()
        .replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
}

function checkTime(time = null) {
    if (time === null || time === "") {
        return 2;
    }
    var date = new Date("1/01/2018 " + time);
    if (date.toString() === "Invalid Date") {
        return 0;
    }
    return 3;
}

function checkDateTime(dateTime = null) {
    if (dateTime === null || dateTime === "") {
        return 2;
    }
    var date = new Date(dateTime);
    if (date.toString() === "Invalid Date") {
        return 0;
    }
    return 3;
}

$(function () {
    // Material DatePicker initialization
    if ($(".dateInput").length > 0) {
        $(".dateInput").bootstrapMaterialDatePicker({
            setDate: new Date(),
            weekStart: 0,
            time: false,
        });
    }
});

$(document).ready(function () {

    $(document).on("change", "#setHeadline", function () {
        let status = $(this).is(":checked") ? "on" : "off";

        $.ajax({
            url: "/setting/toggleHeadline",
            method: "POST",
            data: { status: status },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    showTopAlert(res.message, "success");

                    if (status === "on") {
                        $(".animated-banner").removeClass("d-none");

                        // restart animation
                        isAnimating = false;
                        charIndex = 0;
                        startAnimation();

                    } else {
                        $(".animated-banner").addClass("d-none");

                        // stop animation
                        stopCursor();
                        $("#bannerText").stop(true, true).text("");
                        isAnimating = true;
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("❌ Toggle error:", error);
                showTopAlert("Error toggling headline." + error, "error");
            }
        });
        CommonCheckboxUtils.reloadPage(1000);
    });

    $(document).on("change", "#setVersion", function () {
        let status = $(this).is(":checked") ? "active" : "inactive";

        $.ajax({
            url: "/setting/toggleVersion",
            method: "POST",
            data: { status: status },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    showTopAlert(res.message, "success");
                }
            },
            error: function (xhr, status, error) {
                console.error("❌ Toggle error:", error);
                showTopAlert("Error toggling headline." + error, "error");
            }
        });
        CommonCheckboxUtils.reloadPage(1000);
    });

    $(document).on("click", "#clearCache", function () {
        const confirmMsg = "Are you sure you want to clear cache?<br>This action cannot be undone.";
        showTopAlert(confirmMsg, "warning", true, () => {
            showTopAlert("Deleting...", "info");

            $.ajax({
                url: "/setting/clearCache",    // <-- your route
                method: "POST",
                dataType: "json",
                success: function (response) {
                    showTopAlert(response.message, "success");
                    if (response.success) {
                        CommonCheckboxUtils.reloadPage(1000);
                    }
                },
                error: function () {
                    showTopAlert("Failed to clear cache.", "error");
                }
            });
        });
    });



    $(document).on("change", "#setDataOption", function () {
        let status = $(this).is(":checked") ? "on" : "off";

        $.ajax({
            url: "/setting/toggleDataOption",
            method: "POST",
            data: { status: status },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    showTopAlert(res.message, "success");
                }
                CommonCheckboxUtils.reloadPage(1000);
            },
            error: function (xhr, status, error) {
                console.error("❌ Toggle error:", error);
                showTopAlert("Error toggling Data Option." + error, "error");
            }
        });
    });

    // radio buttons
    // ✅ Toggle between text & select inputs
    $(".mode-radio").on("change", function () {
        const mode = $(this).val();

        if (mode === "text") {
            $(".value-input").removeClass("d-none");
            $(".value-select").addClass("d-none");
        } else {
            $(".value-select").removeClass("d-none");
            $(".value-input").addClass("d-none");
        }
    });

    // ✅ Focus on key input when modal opens
    $('#settingsModal').on('shown.bs.modal', function () {
        $("#settingKey").trigger("focus");
    });

    // ✅ Reset modal on close
    $('#settingsModal').on('hidden.bs.modal', function () {
        $("#settingsForm")[0].reset();

        $(".value-input").removeClass("d-none");
        $(".value-select").addClass("d-none");

        $("#settingId").val("");
        $('#settingStatus').prop("checked", true).text("Active").addClass("active").removeClass("inactive").val("active");
        $('#settingValueText').val("");
        $("#settingsForm").attr("action", "/setting/create");
        $("#settingSubmitBtn").prop("disabled", false).text("Submit");

        // ✅ Reset custom select
        $('#settingValueSelect').val("");
        $("#settingCustomSelect .custom-select__option").removeClass("selected");
        $("#settingSelectText").text("Select Value");
        $("#settingCustomSelect").removeClass("open");
    });


    $(document).on("click", "#settingCustomSelect .custom-select__option", function () {

        $("#settingCustomSelect .custom-select__option").removeClass("selected");

        $(this).addClass("selected");

        let selectedText = $(this).text().trim();
        let selectedValue = $(this).data("value");

        $("#settingCustomSelect .custom-select__selected").text(selectedText);

        $("#settingValueSelect").val(selectedValue);

        $("#settingCustomSelect").removeClass("open");
    });

    $(document).on("change", "#settingStatus", function () {
        const isChecked = $(this).is(":checked");

        const $indicator = $(".status-indicator");

        if (isChecked) {
            $indicator.text("Active").addClass("active").removeClass("inactive");
        } else {
            $indicator.text("Inactive").addClass("inactive").removeClass("active");
        }
    });


    // ✅ Submit form
    $('#settingsForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const actionUrl = form.attr("action");

        const settingKey = $.trim($('#settingKey').val());
        const mode = $("input[name='valueMode']:checked").val();

        const settingValue =
            mode === "text"
                ? $.trim($("#settingValueText").val())
                : $("#settingValueSelect").val();

        if (!settingKey || !settingValue) {
            showTopAlert("Key and Value are required.", "warning");
            return;
        }

        const payload = {
            settingKey: settingKey,
            settingValue: settingValue,
            settingId: $('#settingId').val(),
            settingStatus: $('#settingStatus').is(":checked") ? "active" : "inactive"
        };

        const submitBtn = $("#settingSubmitBtn");
        const originalText = submitBtn.text();

        submitBtn.prop('disabled', true)
            .html(`<span class="spinner-border spinner-border-sm me-1"></span>Saving...`);

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: payload,
            dataType: 'json',
            success(response) {
                if (response.success) {
                    showTopAlert(response.message, "success");
                    $('#settingsModal').modal('hide');
                    CommonCheckboxUtils.reloadPage(1200);
                } else {
                    showTopAlert(response.message || "Something went wrong!", "warning");
                }
            },
            error() {
                showTopAlert("Server error. Please try again.", "error");
            },
            complete() {
                submitBtn.prop("disabled", false).text(originalText);
            }
        });
        console.log(payload);
    });


    $(document).on('click', '.btn-edit-setting', function () {

        const id = $(this).data('id');
        const key = $(this).data('key');
        const settingValue = $(this).data('keyvalue');
        const settingStatus = $(this).data('status');


        $('#settingsModalTitle').text('Update Setting');
        $('#settingsModalIcon').text('edit_note');
        $('#settingSubmitBtn').text('Update Setting');

        $("#settingId").val(id);
        $('#settingKey').val(key);
        $('#settingStatus').val(settingStatus === "active" ? "active" : "inactive").prop("checked", settingStatus === "active").text(settingStatus === "active" ? "Active" : "Inactive").addClass(settingStatus === "active" ? "active" : "inactive").removeClass(settingStatus === "active" ? "inactive" : "active");
        $('#settingValueSelect').val(settingValue);
        $('#settingValueText').val(settingValue.charAt(0).toUpperCase() + settingValue.slice(1));


        $("#settingCustomSelect .custom-select__option").removeClass("selected");
        $("#settingCustomSelect .custom-select__option[data-value='" + settingValue + "']")
            .addClass("selected");

        // ✅ update shown text
        $("#settingCustomSelect .custom-select__selected").text(
            settingValue.charAt(0).toUpperCase() + settingValue.slice(1)
        );

        $("#settingsForm").attr("action", "/setting/update");
        $('#settingsModal').modal('show');
    });

    CommonCheckboxUtils.init('#selectAllSettings', '.settings-checkbox', function ($checkboxes) { });


    $(document).on("click", ".btn-delete-setting, #deleteSettingsSelected", function (e) {
        e.preventDefault();

        // Single Delete
        const id = $(this).data("id");

        // Bulk Delete
        const idsSelected = CommonCheckboxUtils.getSelected($('.settings-checkbox'));

        const idsToDelete = id ? [id] : idsSelected;

        if (idsToDelete.length === 0) {
            showTopAlert("Please select at least one setting to delete.", "warning");
            return;
        }

        const confirmMsg =
            `Are you sure you want to delete ${idsToDelete.length > 1 ? 'these ' + idsToDelete.length + ' selected settings together.' : 'this setting'
            }?<br>This action cannot be undone.`;

        showTopAlert(confirmMsg, "warning", true, () => {
            showTopAlert("Deleting...", "info");

            $.ajax({
                url: "/setting/deleteSelected",
                method: "POST",
                dataType: "json",
                data: { ids: idsToDelete },
                success: function (response) {
                    showTopAlert(response.message, response.success ? "success" : "warning");

                    if (response.success) {
                        CommonCheckboxUtils.reloadPage(1000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("❌ Delete error:", error);
                    showTopAlert("Error deleting setting(s).", "error");
                }
            });
        });
    });

});
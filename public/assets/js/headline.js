$(document).ready(function () {

    // =====================================================
    //  Define ALL FUNCTIONS + VARIABLES (Always Available)
    // =====================================================

    let msgs = [];
    let currentMsgIndex = 0;
    let charIndex = 0;
    let isAnimating = false;
    const $bannerText = $('#bannerText');
    let cursorInterval;

    function startCursor() {
        stopCursor();
        $bannerText.addClass('cursor');
        cursorInterval = setInterval(() => {
            $bannerText.toggleClass('cursor');
        }, 600);
    }

    function stopCursor() {
        if (cursorInterval) clearInterval(cursorInterval);
        $bannerText.removeClass('cursor');
    }

    function getFallbackMessages() {
        return [
            "Automation saves hours — keep going!",
            "Your work builds real impact.",
            "Small progress daily = big results.",
            "Smart tools create smart outcomes."
        ];
    }

    function startAnimation() {
        if (msgs.length === 0) return;
        isAnimating = false;
        charIndex = 0;
        typeCharacter();
    }

    function typeCharacter() {
        if (isAnimating) return;

        const currentMsg = msgs[currentMsgIndex];

        if (charIndex === 0) {
            $bannerText.text('');
            $bannerText.css({
                left: '50%',
                transform: 'translateX(-50%)'
            });
            startCursor();
        }

        if (charIndex < currentMsg.length) {
            $bannerText.text(currentMsg.substring(0, charIndex + 1));
            charIndex++;
            setTimeout(typeCharacter, 60);
        } else {
            stopCursor();
            setTimeout(slideLeftAndNext, 3000);
        }
    }

    function slideLeftAndNext() {
        if (isAnimating) return;
        isAnimating = true;

        const currentMsg = msgs[currentMsgIndex];
        $bannerText.text(currentMsg);

        const containerWidth = $('.animated-banner').width();
        const textWidth = $bannerText.width();
        const slideDistance = containerWidth / 2 + textWidth;

        $bannerText.animate({
            left: `-${slideDistance}px`
        }, 3000, 'linear', function () {

            $bannerText.css({
                left: (containerWidth + textWidth) + 'px',
                transform: 'translateX(0)'
            });

            $bannerText.animate({
                left: '50%'
            }, 3000, 'linear', () => {
                $bannerText.css('transform', 'translateX(-50%)');
                currentMsgIndex = (currentMsgIndex + 1) % msgs.length;
                charIndex = 0;
                isAnimating = false;
                setTimeout(typeCharacter, 600);
            });
        });
    }

    // =====================================================
    // ✅ CONDITIONAL: animation code only if banner ON
    // =====================================================

    if (!$(".animated-banner").hasClass("d-none")) {

        $.ajax({
            url: '/headline/list',
            method: 'GET',
            dataType: 'json',
            success(res) {
                msgs = (res.success && res.messages.length) ? res.messages : getFallbackMessages();
                startAnimation();
            },
            error() {
                msgs = getFallbackMessages();
                startAnimation();
            }
        });
    }

});


$(document).ready(function () {
    $(document).on("click", "#headlinesListSelect .custom-select__option", function () {

        $("#headlinesListSelect .custom-select__option").removeClass("selected");

        $(this).addClass("selected");

        let selectedText = $(this).text().trim();
        let selectedValue = $(this).data("value");

        $("#headlinesListSelect .custom-select__selected").text(selectedText);

        $("#headlineStatus").val(selectedValue);

        $("#headlinesListSelect").removeClass("open");
        console.log("✅ Selected status:", selectedValue);
    });

    $('#headlineModal').on('shown.bs.modal', function () {
        $("#headlineText").trigger("focus");
    });

    $('#headlineModal').on('hidden.bs.modal', function () {
        $("#headlineForm")[0].reset();
        $("#headlineId").val("");
        $('#headlineStatus').val("active");
        $("#headlineForm").attr("action", "/headline/create");
    });


    // $(document).on('click', '.btn-add-headline', function () {

    //     $('#headlineModalTitle').text('Create Headline');
    //     $('#headlineModalIcon').text('add_circle');
    //     $('#headlineSubmitBtn').text('Create Headline');

    //     $("#headlineForm").attr("action", "/headline/create")[0].reset();

    //     $('#headlineId').val('');
    //     $('#headlineModal').modal('show');

    // });

    // ✅ Edit mode
    $(document).on('click', '.btn-edit-headline', function () {

        const id = $(this).data('id');
        const text = $(this).data('text');
        const description = $(this).data('description');
        const status = $(this).data('status');

        $('#headlineModalTitle').text('Update Headline');
        $('#headlineModalIcon').text('edit_note');
        $('#headlineSubmitBtn').text('Update Headline');

        $("#headlineId").val(id);
        $('#headlineText').val(text);
        $('#headlineDescription').val(description);
        $('#headlineStatus').val(status);

        $("#headlinesListSelect .custom-select__option").removeClass("selected");
        $("#headlinesListSelect .custom-select__option[data-value='" + status + "']")
            .addClass("selected");

        // ✅ update shown text
        $("#headlinesListSelect .custom-select__selected").text(
            status.charAt(0).toUpperCase() + status.slice(1)
        );

        $("#headlineForm").attr("action", "/headline/update/");

        $('#headlineModal').modal('show');
    });

    // ✅ AJAX Submit
    $('#headlineForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const rawText = $.trim($('#headlineText').val());
        const submitBtn = $("#headlineSubmitBtn");
        const originalText = submitBtn.text();
        const actionUrl = form.attr("action");

        let headlines = extractHeadlines(rawText);

        if (headlines.length === 0) {
            showTopAlert("Please enter at least one headline.", "warning");
            return;
        }

        // Convert to JSON for backend
        let payload = {
            headlines: headlines,
            headlineId: $('#headlineId').val(),
            headlineDescription: $('#headlineDescription').val(),
            headlineStatus: $('#headlineStatus').val()
        };

        submitBtn.prop('disabled', true).html(`
        <span class="spinner-border spinner-border-sm me-1"></span>
        Saving...
    `);

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: payload,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showTopAlert(response.message, "success");
                    $('#headlineModal').modal('hide');
                    CommonCheckboxUtils.reloadPage(1200);
                } else {
                    showTopAlert(response.message || "Something went wrong!", "warning");
                }
            },
            error: function () {
                showTopAlert("Server error. Please try again.", "error");
            },
            complete: function () {
                submitBtn.prop("disabled", false).text(originalText);
            }
        });
    });


   

    CommonCheckboxUtils.init('#selectAllHeadlines', '.headline-checkbox', function ($checkboxes) {

        const selected = $checkboxes.filter(":checked");

        const $toggleBtn = $("#toggleStatusSelected");
        const $deleteBtn = $("#deleteHeadlinesSelected");

        if (selected.length === 0) {
            $toggleBtn.addClass("d-none");
            $deleteBtn.addClass("d-none");
            return;
        }

        $toggleBtn.removeClass("d-none");
        $deleteBtn.removeClass("d-none");

        let statuses = [];
        selected.each(function () {
            statuses.push($(this).data("status"));  // active / inactive
        });

        const allActive = statuses.every(s => s === "active");
        const allInactive = statuses.every(s => s === "inactive");

        if (allActive) {
            $("#toggleLabel").text("Make Inactive");
            $toggleBtn.data("mode", "set-inactive");
        }
        else if (allInactive) {
            $("#toggleLabel").text("Make Active");
            $toggleBtn.data("mode", "set-active");
        }
        else {
            $("#toggleLabel").text("Toggle Status Individually");
            $toggleBtn.data("mode", "toggle");
        }
        const selectedIds = CommonCheckboxUtils.getSelected($checkboxes);
        console.log("📧 Selected campaigns:", selectedIds);
    });


    $(document).on("click", ".btn-delete-headline, #deleteHeadlinesSelected", function (e) {
        e.preventDefault();

        // Single Delete
        const id = $(this).data("id");

        // Bulk Delete
        const idsSelected = CommonCheckboxUtils.getSelected($('.headline-checkbox'));

        const idsToDelete = id ? [id] : idsSelected;

        if (idsToDelete.length === 0) {
            showTopAlert("Please select at least one headline to delete.", "warning");
            return;
        }

        const confirmMsg =
            `Are you sure you want to delete ${idsToDelete.length > 1 ? 'these ' + idsToDelete.length + ' selected headlines together.' : 'this headline'
            }?<br>This action cannot be undone.`;

        showTopAlert(confirmMsg, "warning", true, () => {
            showTopAlert("Deleting...", "info");

            $.ajax({
                url: "/headline/deleteSelected",
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
                    showTopAlert("Error deleting headline(s).", "error");
                }
            });
        });
    });

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
                CommonCheckboxUtils.reloadPage(1000);
            },
            error: function (xhr, status, error) {
                console.error("❌ Toggle error:", error);
                showTopAlert("Error toggling headline." + error, "error");
            }
        });
    });

    $(document).on("click", "#toggleStatusSelected", function () {

        const selectedIds = CommonCheckboxUtils.getSelected($('.headline-checkbox'));

        if (selectedIds.length === 0) {
            return showTopAlert("Please select at least one headline.", "warning");
        }

        const mode = $(this).data("mode");

        const msgMap = {
            "set-active": "Set selected headlines to <b>Active</b>?",
            "set-inactive": "Set selected headlines to <b>Inactive</b>?",
            "toggle": "Toggle status will be applied for each selected headline. <br> And their status will be <b>Active</b> if all are <b>Inactive</b> and <b>Inactive</b> if all are <b>Active</b>.<br>Are you sure you want to Toggled(active/inactive)?"
        };

        showTopAlert(msgMap[mode], "warning", true, () => {

            $.ajax({
                url: "/headline/bulkToggleStatus",
                type: "POST",
                dataType: "json",
                data: {
                    ids: selectedIds,
                    mode: mode
                },
                success: function (response) {
                    showTopAlert(response.message, response.success ? "success" : "warning");

                    if (response.success) {
                        CommonCheckboxUtils.reloadPage(1000);
                    }
                },
                error: function () {
                    showTopAlert("Error updating statuses.", "error");
                }
            });

        });
    });



});


function extractHeadlines(rawInput) {
    if (!rawInput) return [];

    // Split by comma OR new line
    let parts = rawInput
        .split(/[\n,]+/)        // newline or comma
        .map(s => s.trim())     // cleanup
        .filter(s => s.length > 0); // remove blanks

    return parts;
}

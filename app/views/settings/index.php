<div class="container-fluid py-3 min-vh-75">
    <div class="row g-4 align-items-start justify-content-center pt-1">
        <!-- <div class="col-lg-11 mb-sm-3 mb-0"> -->

        <!-- ✅ LEFT SIDEBAR -->
        <div class="col-lg-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white  border-bottom-2 mb-0 py-2 px-3 border-dark border-2">
                    <h6 class="fw-bold m-0 p-0">Settings</h6>
                </div>

                <div class="list-group list-group-flush settings-menu">

                    <!-- ✅ Setting List -->
                    <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 active"
                        data-setting-target="#sectionSettings">
                        <i class="material-symbols-rounded">settings</i>
                        Setting List
                    </a>

                    <!-- ✅ Options -->
                    <a class="list-group-item list-group-item-action d-flex align-items-center gap-2"
                        data-setting-target="#sectionOptions">
                        <i class="material-symbols-rounded">tune</i>
                        Options
                    </a>
                    <a class="list-group-item list-group-item-action d-flex align-items-center gap-2"
                        data-setting-target="#linkSection">
                        <i class="material-symbols-rounded">link</i>
                        Links
                    </a>

                </div>
            </div>
        </div>

        <!-- ✅ RIGHT CONTENT PANEL -->
        <div class="col-lg-9">

            <!-- ✅ SECTION: MANAGE SETTINGS OPTIONS -->
            <div id="sectionSettings" class="settings-section">

                <div class="card shadow-sm border-0">

                    <!-- ✅ HEADER -->
                    <div class="card-header bg-orange text-white d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center gap-2">

                            <!-- Select All -->
                            <div class="form-check ps-0 p-2">
                                <input type="checkbox" id="selectAllSettings"
                                    class="form-check-input"
                                    style="width:20px;height:20px;">
                            </div>

                            <h6 class="mb-0 d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded">tune</i>
                                Manage Settings Options
                            </h6>

                            <span class="badge bg-light text-dark px-2 py-2" id="settingsCount">
                                <?= count($settings); ?>
                            </span>
                            <button id="deleteSettingsSelected" class="btn btn-danger btn-sm d-none">
                                <i class="material-symbols-rounded">delete</i> Delete
                            </button>

                            <button id="toggleSettingsSelected" class="btn btn-info btn-sm d-none">
                                <i class="material-symbols-rounded">sync_alt</i> Toggle Status
                            </button>

                        </div>

                        <!-- ADD NEW -->
                        <button class="btn btn-light text-orange fw-bold px-3 py-2 mb-0"
                            data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="material-symbols-rounded">add</i> Add New Setting
                        </button>
                    </div>

                    <!-- ✅ BODY -->
                    <div class="card-body p-1">
                        <div id="settingsListContainer"
                            class="scroll-wrapper"
                            style="max-height:350px;overflow-y:auto;">

                            <!-- ✅ LIST -->
                            <?php if (!empty($settings)) : ?>
                                <?php foreach ($settings as $s): ?>
                                    <div class="card shadow-xs border-radius-sm mb-2">
                                        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">

                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check ps-0">
                                                    <input type="checkbox"
                                                        class="form-check-input settings-checkbox"
                                                        value="<?= esc(encryptId($s['id'])) ?>">

                                                </div>
                                                <!-- <i class="material-symbols-rounded text-orange text-2xl">settings</i> -->
                                                <span class="text-warning m-0 p-0">|</span>

                                                <div>
                                                    <h6 class="mb-0 text-sm fw-bold pb-0">
                                                        <?= ucfirst(esc($s['key'])) ?>
                                                    </h6>

                                                    <?php if (!empty($s['created_at'])): ?>
                                                        <small class="text-muted mt-0 pt-0">
                                                            <?= esc($s['created_at']) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- RIGHT SIDE -->
                                            <div class="d-flex align-items-center gap-3">

                                                <span class="badge px-2"
                                                    style="background: <?= in_array(strtolower($s['value']), ['on']) ? '#198754' : '#dc3545' ?>;">
                                                    <?= ucfirst($s['value']) ?>
                                                </span>
                                                |
                                                <?php
                                                $status = isset($s['status']) ? strtolower($s['status']) : null;
                                                $color = match ($status) {
                                                    'active'   => '#198754',   // green
                                                    'inactive' => '#dc3545',   // red
                                                    default    => '#333232',   // dark for None or anything else
                                                };
                                                ?>
                                                <span class="badge px-2" style="background: <?= $color ?>;">
                                                    <?= $status ? ucfirst($status) : 'None' ?>
                                                </span>



                                                <div class="dropdown">
                                                    <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                        <i class="material-symbols-rounded">more_vert</i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end px-1 border-1 shadow border-secondary
                                                        bg-white overflow-auto border-lavender">

                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 btn-edit-setting"
                                                                href="#"
                                                                data-id="<?= esc(encryptId($s['id'])) ?>"
                                                                data-key="<?= esc($s['key']) ?>"
                                                                data-keyvalue="<?= esc($s['value']) ?>"
                                                                data-status="<?= esc($s['status']) ?>">
                                                                <i class="material-symbols-rounded text-orange">edit</i>
                                                                Edit
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 text-danger btn-delete-setting"
                                                                href="#"
                                                                data-id="<?= esc(encryptId($s['id'])) ?>">
                                                                <i class="material-symbols-rounded text-red">delete</i>
                                                                Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>

                            <?php else : ?>

                                <!-- ✅ EMPTY STATE -->
                                <div class="text-center py-4">
                                    <img src="<?= ROOT ?>/assets/images/empty.svg" width="180" class="opacity-75 mb-3">
                                    <h6 class="text-muted">No settings found</h6>
                                    <p class="text-secondary small">
                                        Click “Add New Setting” to create your first item.
                                    </p>
                                </div>

                            <?php endif ?>
                        </div>
                    </div>

                </div>

            </div>

            <div id="linkSection" class="settings-section d-none">
                <div class="card shadow-sm border-0">

                    <!-- Header -->
                    <div class="card-header bg-lavender d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 d-flex align-items-center text-dark gap-2">
                            <i class="material-symbols-rounded text-lavender">network_node</i>
                            Application Links
                        </h6>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 gap-2">
                                <a href="/headline" class="btn btn-lavender">
                                    <i class="material-symbols-rounded text-white">wb_incandescent</i>
                                    Go to Headlines
                                </a>

                                <a href="/setting/sessions" class="btn btn-lavender">
                                    <i class="material-symbols-rounded text-white">physical_therapy</i>
                                    Show Sessions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ SECTION: GENERAL OPTIONS -->
            <div id="sectionOptions" class="settings-section d-none">

                <div class="card shadow-sm border-0">

                    <!-- Header -->
                    <div class="card-header bg-purple d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 d-flex align-items-center text-white gap-2">
                            <i class="material-symbols-rounded">tune</i>
                            Application Options
                        </h6>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- ✅ Toggle: Headline -->
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="setHeadline"
                                        <?= isset($headlineEnabled) && $headlineEnabled === 'on' ? 'checked' : '' ?>>

                                    <label class="form-check-label fw-semibold" for="setHeadline">
                                        Show Headlines in Navbar
                                    </label>
                                </div>

                                <!-- ✅ Toggle: Data Options -->
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="setDataOption"
                                        <?= isset($dataOptionEnabled) && $dataOptionEnabled === 'on' ? 'checked' : '' ?>>

                                    <label class="form-check-label fw-semibold" for="setDataOption">
                                        Enable Data Options (Sheets / Filters)
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="setVersion"
                                        <?= isset($version_status) && $version_status === 'active' ? 'checked' : '' ?>>

                                    <label class="form-check-label fw-semibold" for="setVersion">
                                        Enable Version (Show Version)
                                    </label>
                                </div>
                            </div>
                            <hr class="vertical dark my-0 d-md-block">
                            <hr class="horizontal dark my-3 mx-0 d-md-none">
                            <div class="col-md-5">
                                <!-- //btn for clear cache -->
                                <?php if (isset($_SESSION['setting_cache'])): ?>
                                    <a role="button" href="#" id="clearCache" title="Clear Cache" class="btn btn-danger shadow-none btn-sm mb-0 border-radius-md py-1 px-1" style="border-radius: 4px;">
                                        <i class="material-symbols-rounded align-middle text-lg m-0 p-0">delete</i>
                                        <span class="align-middle text-sm m-0 p-0">Clear Cache</span>
                                    </a>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Add / Edit Headline Modal -->
<div class="modal fade"
    id="settingsModal"
    tabindex="-1"
    aria-labelledby="settingsModalLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="modal-header bg-orange text-white">
                <h6 class="modal-title fw-bold d-flex align-items-center" id="settingsModalLabel">
                    <i class="material-symbols-rounded me-2" id="settingsModalIcon">add_circle</i>
                    <span id="settingsModalTitle">Create New Setting</span>
                </h6>
                <button type="button" class="btn-close btn-close-white mb-0" data-bs-dismiss="modal"></button>
            </div>

            <form id="settingsForm" method="POST" action="/setting/create">

                <div class="modal-body">
                    <div class="row mb-3">

                        <!-- ✅ Left Column (Key) -->
                        <div class="col-md-5">
                            <div class="d-flex align-items-center mb-2">
                                <label for="settingKey" class="form-label fw-semibold fs-6 mb-0">Key</label>
                            </div>
                            <input class="form-control merge-input"
                                id="settingKey"
                                name="settingKey"
                                placeholder="Enter the key for this setting..." />
                        </div>

                        <!-- ✅ Right Column (Value + Controls) -->
                        <div class="col-md-7">
                            <div class="d-flex align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Value</label>

                                <!-- ✅ Radio Group -->
                                <div class="radio-group">
                                    <div class="form-check d-flex align-items-center gap-1">
                                        <input id="valueModeText" class="form-check-input mode-radio" type="radio" value="text" checked name="valueMode">
                                        <label for="valueModeText" class="form-check-label mb-0">Enter Manually</label>
                                    </div>

                                    <div class="form-check d-flex align-items-center gap-1">
                                        <input id="valueModeSelect" type="radio"
                                            name="valueMode"
                                            value="select"
                                            class="form-check-input mode-radio">
                                        <label for="valueModeSelect" class="form-check-label mb-0">Select From List</label>
                                    </div>
                                </div>
                            </div>

                            <!-- </div> -->

                            <!-- ✅ Text Input -->
                            <input type="text"
                                class="form-control merge-input value-input"
                                id="settingValueText"
                                name="settingValueText"
                                placeholder="Enter value manually..." />

                            <!-- ✅ Dropdown -->
                            <div id="settingCustomSelect" class="custom-select d-none value-select">

                                <div class="custom-select__trigger p-2 px-2 border" style="width: 100%;">
                                    <span class="custom-select__selected opacity-6" id="settingSelectText" style="font-size:13px">
                                        Select Value
                                    </span>
                                    <i class="custom-select__arrow material-symbols-rounded opacity-6">expand_more</i>
                                </div>

                                <div class="custom-select__options">
                                    <div class="custom-select__option" data-value="active">Active</div>
                                    <div class="custom-select__option" data-value="inactive">Inactive</div>
                                    <div class="custom-select__option" data-value="on">On</div>
                                    <div class="custom-select__option" data-value="off">Off</div>
                                </div>
                            </div>

                            <input type="hidden" id="settingValueSelect" name="settingValueSelect">
                            <input type="hidden" id="settingId" name="settingId">
                        </div>

                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                            type="checkbox"
                            id="settingStatus"
                            name="settingStatus" checked>
                        <label class="form-check-label fw-semibold mt-0" for="settingStatus">
                            Status (<span class="status-indicator active">Active</span>)
                        </label>
                    </div>
                </div>


                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-outline-secondary mb-0" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn bg-orange text-white fw-bold mb-0" id="settingSubmitBtn">
                        Create Setting
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
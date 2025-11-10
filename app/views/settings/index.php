<div class="container-fluid py-4">

    <div class="row">

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

                            <!-- Count -->
                            <span class="badge bg-light text-dark px-2 py-2" id="settingsCount">
                                <?= count($settings); ?>
                            </span>

                            <!-- Bulk Delete -->
                            <button id="deleteSettingsSelected" class="btn btn-danger btn-sm d-none">
                                <i class="material-symbols-rounded">delete</i> Delete
                            </button>

                            <!-- Bulk Toggle -->
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

                                            <!-- LEFT SIDE -->
                                            <div class="d-flex align-items-center gap-2">

                                                <div class="form-check ps-0">
                                                    <input type="checkbox"
                                                        class="form-check-input settings-checkbox"
                                                        value="<?= esc(encryptId($s['id'])) ?>"
                                                        data-key="<?= $s['key'] ?>">
                                                </div>

                                                <i class="material-symbols-rounded text-orange text-2xl">settings</i>
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
                                                    style="background:<?= $s['value'] == 'on' ? '#198754' : '#dc3545' ?>">
                                                    <?= ucfirst($s['value']) ?>
                                                </span>

                                                <div class="dropdown">
                                                    <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                        <i class="material-symbols-rounded">more_vert</i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end shadow">

                                                        <li>
                                                            <a class="dropdown-item btn-edit-setting"
                                                                href="#"
                                                                data-id="<?= esc(encryptId($s['id'])) ?>"
                                                                data-key="<?= esc($s['key']) ?>"
                                                                data-desc="<?= esc($s['value']) ?>">
                                                                <i class="material-symbols-rounded text-orange">edit</i>
                                                                Edit
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item text-danger btn-delete-setting"
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

                    </div>
                </div>

            </div>

        </div>


    </div>
</div>
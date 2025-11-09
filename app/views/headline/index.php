<div class="container-fluid py-3 min-vh-88">
    <div class="row ">
        <div class="col-lg-7 mb-sm-3 mb-0">
            <div class="card">
                <div class="card-header bg-orange border-radius-lg d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="text-white text-sm h6 mb-0 fw-bold pb-0 d-flex align-items-center">
                            <div class="form-check ps-0 me-2">
                                <input class="form-check-input" type="checkbox" id="selectAllHeadlines"
                                    style="width: 20px; height: 20px;">
                            </div>

                            <i class="material-symbols-rounded text-2xl ms-1">wb_incandescent</i>
                            <span class="text-white">Headlines</span>
                        </h6>
                        <span class="badge text-dark text-sm p-1 border-radius-md" style="background: wheat;" id="motivationCount">
                            <?php echo count($headlines); ?>
                        </span>
                        <a role="button" href="#" id="deleteHeadlinesSelected" title="Delete Selected"
                            class="btn btn-danger shadow-none btn-sm mb-0 border-radius-md d-none py-1 px-1" style="border-radius: 4px;">
                            <i class="material-symbols-rounded align-middle text-lg m-0 p-0">delete</i>
                            <span class="align-middle text-sm m-0 p-0">Delete</span>
                        </a>
                        <a role="button"
                            class="btn btn-info shadow-none btn-sm mb-0 border-radius-md d-none py-1 px-1"
                            id="toggleStatusSelected">
                            <i class="material-symbols-outlined align-middle text-md">sync_alt</i>
                            <span id="toggleLabel align-middle text-sm m-0 p-0">Toggle Status</span>
                        </a>

                    </div>
                    <button type="button" style="background: #c3bd9dff;" class="btn text-purple mb-0 gap-1 px-2 py-2 d-flex align-items-center btn-add-headline" data-bs-toggle="modal" data-bs-target="#headlineModal">
                        <i class="material-symbols-outlined text-md">add</i> Create New
                    </button>


                </div>
                <div class="card-body p-1">
                    <div class="px-0 py-0 scroll-wrapper" id="listContainer" style="max-height: 300px !important;">
                        <?php if (!empty($headlines)) : ?>
                            <?php foreach ($headlines as $headline) : ?>
                                <div class="card shadow-xs list-card border-radius-sm mb-1">
                                    <div class="card-body py-1 px-2 d-flex justify-content-between align-items-center">

                                        <!-- Left Section -->
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ps-0 p-2">
                                                <input class="form-check-input headline-checkbox"
                                                    type="checkbox"
                                                    data-status="<?= $headline['status'] ?>"
                                                    value="<?= esc(encryptId($headline['id'])) ?>">
                                            </div>
                                            <i class="material-symbols-rounded text-lg me-2 text-orange">wb_incandescent</i>

                                            <div class="d-flex flex-column">
                                                <h6 class="list-name mb-0 text-sm fw-bold text-dark">
                                                    <?= esc($headline['text'] ?? '') ?>
                                                </h6>
                                                <!-- Created Date -->
                                                <p class="text-xxs text-muted mb-0 mt-1">
                                                    Created on:
                                                    <?= isset($headline['created_at']) ? date('M d, Y h:i A', strtotime($headline['created_at'])) : ''; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Dropdown Menu -->
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- status -->
                                            <span class="badge text-white" style="font-size: 0.6rem; background: <?= $headline['status'] == 'active' ? '#198754' : '#dc3545'; ?>; padding: 5px;">
                                                <?= $headline['status'] == 'active' ? 'Active' : 'Inactive'; ?>
                                            </span>
                                            <div class="dropdown">
                                                <a class="text-secondary mb-0 btn-sm" href="#" role="button" id="dropdownMenu<?= $headline['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="material-symbols-rounded text-2xl">more_vert</i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-end px-1 py-1 border-1 shadow border-secondary" aria-labelledby="dropdownMenu<?= $headline['id']; ?>">
                                                    <li>
                                                        <a href="#"
                                                            data-id="<?= esc(encryptId($headline['id'])); ?>"
                                                            data-text="<?= esc($headline['text']); ?>"
                                                            data-status="<?= esc($headline['status']); ?>"
                                                            data-description="<?= esc($headline['description']); ?>"
                                                            class="dropdown-item border-radius-md d-flex align-items-center gap-2 px-1 btn-edit-headline">
                                                            <i class="material-symbols-rounded text-lg text-orange">edit</i>
                                                            Edit Headline
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#"
                                                            data-id="<?= esc(encryptId($headline['id'])); ?>"
                                                            class="dropdown-item border-radius-md d-flex align-items-center gap-2 px-1 btn-delete-headline">
                                                            <i class="material-symbols-rounded text-lg text-red">delete</i>
                                                            Delete Headline
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 py-1 user-select-none">
                                <img src="<?= ROOT; ?>/assets/images/empty.svg"
                                    width="200" height="200" class="opacity-75 user-select-none pointer-events-none" alt="Empty" draggable="false">
                                <h6 class="text-secondary fw-semibold mb-0 d-flex align-items-center gap-2">
                                    <i class="material-symbols-rounded text-secondary">wb_incandescent</i>
                                    No Headlines Found
                                </h6>
                                <p class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    Click the button above to add your first headline.
                                </p>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <!-- to set on or off the headline -->
            <div class="card shadow-xs border-radius-md">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Set Headline</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                            type="checkbox"
                            id="setHeadline"
                            <?= isset($headlineEnabled) && $headlineEnabled === 'on' ? 'checked' : '' ?>>

                        <label class="form-check-label" for="setHeadline">Set Headline</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add / Edit Headline Modal -->
<div class="modal fade"
    id="headlineModal"
    tabindex="-1"
    aria-labelledby="headlineModalLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="modal-header bg-orange text-white">
                <h6 class="modal-title fw-bold d-flex align-items-center" id="headlineModalLabel">
                    <i class="material-symbols-rounded me-2" id="headlineModalIcon">add_circle</i>
                    <span id="headlineModalTitle">Create New Headline</span>
                </h6>
                <button type="button" class="btn-close btn-close-white mb-0" data-bs-dismiss="modal"></button>
            </div>

            <form id="headlineForm" method="POST" action="/headline/create">

                <div class="modal-body">
                    <div class="row mb-2 d-flex align-items-center">
                        <div class="col-md-8">
                            <label for="headlineText" class="form-label fw-semibold">Headline</label>
                            <textarea style="resize:none;" rows="3" class="form-control merge-input" id="headlineText"
                                name="headlineText"
                                placeholder="Enter a headline or paste multiple headlines separated by commas or new lines..."></textarea>

                        </div>
                        <div class="col-md-4">
                            <label for="headlinesListSelect" class="form-label fw-semibold">Status</label>
                            <div class="custom-select p-0" id="headlinesListSelect">
                                <div class="custom-select__trigger p-2 px-2 border" style="width: 100%;">
                                    <span class="custom-select__selected opacity-6" style="font-size:13px">Active</span>
                                    <i class="custom-select__arrow material-symbols-rounded opacity-6">expand_more</i>
                                </div>
                                <div class="custom-select__options">
                                    <div class="custom-select__option selected" data-value="active">
                                        Active
                                    </div>
                                    <div class="custom-select__option" data-value="inactive">
                                        Inactive
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="headlineDescription" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control merge-input" id="headlineDescription"
                            name="headlineDescription" rows="2"
                            placeholder="Short description (optional)" style="resize:none;"></textarea>
                    </div>
                    <input type="hidden" id="headlineId" name="headlineId">
                    <input type="hidden" id="headlineStatus" name="headlineStatus" value="active">
                </div>

                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-outline-secondary mb-0" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn bg-orange text-white fw-bold mb-0" id="headlineSubmitBtn">
                        Create Headline
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
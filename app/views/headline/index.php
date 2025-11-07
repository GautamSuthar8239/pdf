<style>
    .card-header {
        background: linear-gradient(135deg, #a4a2c2 0%, #e89e4e 100%);
        color: white;
        border-radius: 14px 14px 0 0 !important;
        padding: 18px 22px;
        box-shadow: inset 0 0 12px rgba(255, 255, 255, 0.25);
    }

    .motivation-item {
        background: #ffffff;
        border-left: 4px solid #6c63ff;
        padding: 16px;
        margin-bottom: 12px;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all .25s ease;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .motivation-item:hover {
        transform: translateX(4px);
        background: #f7f7ff;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 72px;
        margin-bottom: 15px;
        opacity: 0.35;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        background: #6c63ff;
        color: #fff;
        border-radius: 12px 12px 0 0;
    }

    .modal-footer button {
        min-width: 110px;
    }
</style>

<div class="container-fluid py-2 min-vh-90">
    <div class="row mt-3">
        <div class="ms-1">
            <h3 class="text-primary mb-0 fw-bold">
                <i class="material-symbols-rounded align-middle text-4xl">wb_incandescent</i>
                Motivation Lines Manager
            </h3>
            <p class="mb-4">Add, edit, and manage your motivational messages</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- ADD NEW MOTIVATION -->
        <div class="col-lg-6 mx-auto">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-1"></i> Add New Motivation</h5>
                </div>
                <div class="card-body">
                    <form id="addMotivationForm">
                        <div class="input-group">
                            <input type="text" class="form-control merge-input" id="motivationInput"
                                placeholder="Enter your motivation line..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MOTIVATIONS LIST -->
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-list me-1"></i> Your Motivations</h5>
                    <span class="badge bg-light text-dark" id="motivationCount">0</span>
                </div>
                <div class="card-body" id="motivationsList">
                    <!-- Dynamic Items -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Motivation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMotivationForm">
                    <input type="hidden" id="editMotivationId">
                    <div class="mb-3">
                        <label for="editMotivationInput" class="form-label">Motivation Text</label>
                        <textarea class="form-control" id="editMotivationInput" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const ROOT = '<?= ROOT ?>';

        // Load motivations on page load
        loadMotivations();

        // Add new motivation
        $('#addMotivationForm').on('submit', function(e) {
            e.preventDefault();
            const text = $('#motivationInput').val().trim();

            if (text) {
                $.ajax({
                    url: ROOT + '/headline/add',
                    method: 'POST',
                    data: {
                        text: text
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#motivationInput').val('');
                            loadMotivations();
                            showToast('Motivation added successfully!', 'success');
                        } else {
                            showToast(response.message || 'Error adding motivation', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error adding motivation', 'error');
                    }
                });
            }
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const text = $(this).data('text');
            $('#editMotivationId').val(id);
            $('#editMotivationInput').val(text);
            $('#editModal').modal('show');
        });

        // Save edit
        $('#saveEditBtn').on('click', function() {
            const id = $('#editMotivationId').val();
            const text = $('#editMotivationInput').val().trim();

            if (text) {
                $.ajax({
                    url: ROOT + '/headline/update',
                    method: 'POST',
                    data: {
                        id: id,
                        text: text
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editModal').modal('hide');
                            loadMotivations();
                            showToast('Motivation updated successfully!', 'success');
                        } else {
                            showToast(response.message || 'Error updating motivation', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error updating motivation', 'error');
                    }
                });
            }
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            if (confirm('Are you sure you want to delete this motivation?')) {
                const id = $(this).data('id');
                $.ajax({
                    url: ROOT + '/headline/delete',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            loadMotivations();
                            showToast('Motivation deleted successfully!', 'success');
                        } else {
                            showToast(response.message || 'Error deleting motivation', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error deleting motivation', 'error');
                    }
                });
            }
        });

        // Load motivations
        function loadMotivations() {
            $.ajax({
                url: ROOT + '/headline/getAll',
                method: 'GET',
                dataType: 'json',
                success: function(motivations) {
                    displayMotivations(motivations);
                },
                error: function() {
                    console.error('Error loading motivations');
                }
            });
        }

        // Display motivations
        function displayMotivations(motivations) {
            const container = $('#motivationsList');
            container.empty();

            if (!motivations || motivations.length === 0) {
                container.html(`
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No motivations yet</h5>
                    <p>Add your first motivation line above!</p>
                </div>
            `);
                $('#motivationCount').text('0');
                return;
            }

            $('#motivationCount').text(motivations.length);

            motivations.forEach(function(item) {
                const html = `
                <div class="motivation-item">
                    <div class="flex-grow-1">
                        <i class="fas fa-quote-left text-muted me-2"></i>
                        <span>${escapeHtml(item.text)}</span>
                    </div>
                    <div class="btn-group btn-group-sm ms-2">
                        <button class="btn btn-outline-primary btn-edit" 
                                data-id="${item.id}" 
                                data-text="${escapeHtml(item.text)}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-delete" 
                                data-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
                container.append(html);
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Toast notification
        function showToast(message, type) {
            const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
            const toast = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div class="toast show ${bgColor} text-white" role="alert">
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
            $('body').append(toast);
            setTimeout(() => $('.toast').remove(), 3000);
        }
    });
</script>
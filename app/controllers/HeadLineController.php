<?php

class HeadlineController

{
    use Controller;

    public function index()
    {
        $data = [
            'title' => 'Headline',
            'breadcrumb' => ['Home / Headline'],
        ];

        $headlineModel = new HeadLine();

        $headlines = $headlineModel->findAll();

        $data['headlines'] = $headlines ? $headlines : [];

        $this->view("headline/index", $data);
    }

    public function list()
    {
        header('Content-Type: application/json');

        $model = new HeadLine();
        $rows = $model->where(['status' => 'active']);

        $messages = [];

        if ($rows) {
            foreach ($rows as $row) {
                $messages[] = $row['text'];
            }
        }

        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
        exit;
    }

    public function create()
    {
        $isAjax = $this->isAjaxRequest();

        // bulk headlines array
        $headlines = $_POST['headlines'] ?? [];

        if (empty($headlines)) {
            return $this->respond([
                'success' => false,
                'message' => 'No headline found.'
            ], $isAjax);
        }

        $description = trim($_POST['headlineDescription'] ?? '');
        $status      = trim($_POST['headlineStatus'] ?? 'active');

        $model = new HeadLine();
        $count = 0;

        foreach ($headlines as $headlineText) {

            if (trim($headlineText) === '') continue;

            $model->insert([
                'text'        => trim($headlineText),
                'description' => $description,
                'status'      => $status,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            $count++;
        }

        return $this->respond([
            'success' => true,
            'message' => $count . ' Headlines created successfully.'
        ], $isAjax);
    }

    public function update()
    {
        $isAjax = $this->isAjaxRequest();

        $id = decryptId($_POST['headlineId'] ?? 0);
        $text = trim($_POST['headlines'][0] ?? '');
        $description = trim($_POST['headlineDescription'] ?? '');
        $status = trim($_POST['headlineStatus'] ?? '');

        if ($text === '') {
            return $this->respond([
                'success' => false,
                'message' => 'Headline is required.'
            ], $isAjax);
        }

        $newData = [
            'text'        => $text,
            'description' => $description,
            'status'      => $status
        ];

        $model = new HeadLine();
        $old = $model->first(['id' => $id]);

        if (!$old) {
            return $this->respond([
                'success' => false,
                'message' => 'Headline not found.'
            ], $isAjax);
        }

        $changes = detectChanges($old, $newData, [
            'text',
            'description',
            'status'
        ]);

        if (!$changes) {
            return $this->respond([
                'success' => false,
                'message' => 'No changes detected. Try again.',
            ], $isAjax);
        }
        $changes['updated_at'] = date('Y-m-d H:i:s');
        if ($model->update($id, $changes)) {
            return $this->respond([
                'success' => true,
                'message' => 'Headline updated successfully.',
            ], $isAjax);
        }

        return $this->respond([
            'success' => false,
            'message' => 'Failed to update. Try again.',
        ], $isAjax);
    }

    public function bulkToggleStatus()
    {
        $isAjax = $this->isAjaxRequest();

        $ids = $_POST['ids'] ?? [];
        $mode = $_POST['mode'] ?? '';

        if (empty($ids) || !in_array($mode, ['set-active', 'set-inactive', 'toggle'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid request.'
            ], $isAjax);
        }

        $model = new HeadLine();

        foreach ($ids as $encId) {
            $id = decryptId($encId);
            if (!$id) continue;

            $row = $model->first(['id' => $id]);
            if (!$row) continue;

            // ✅ Determine new status
            if ($mode === "set-active") {
                $newStatus = "active";
            } elseif ($mode === "set-inactive") {
                $newStatus = "inactive";
            } elseif ($mode === "toggle") {
                $newStatus = $row['status'] === "active" ? "inactive" : "active";
            }

            $model->update($id, [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->respond([
            'success' => true,
            'message' => "Status updated successfully."
        ], $isAjax);
    }



    public function deleteSelected()
    {
        $isAjax = $this->isAjaxRequest();

        $ids = $_POST['ids'] ?? [];

        if (empty($ids)) {
            return $this->respond([
                'success' => false,
                'message' => 'No headlines selected.'
            ], $isAjax);
        }

        $model = new HeadLine();
        $count = 0;

        foreach ($ids as $encId) {
            $id = decryptId($encId);

            if ($id && $model->first(['id' => $id])) {
                $model->delete($id);
                $count++;
            }
        }

        return $this->respond([
            'success' => true,
            'message' => "$count headline(s) deleted successfully."
        ], $isAjax);
    }



    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * 🔧 Helper to send JSON or redirect based on request type
     */
    private function respond(array $response, bool $isAjax, ?string $redirectTo = null)
    {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Default redirect target = current page (no query params)
        if (!$redirectTo) {
            $redirectTo = strtok($_SERVER['REQUEST_URI'], '?');
        }

        Flash::set(
            'toast',
            $response['message'],
            $response['success'] ? 'success' : 'warning'
        );
        redirect($redirectTo);
    }
}

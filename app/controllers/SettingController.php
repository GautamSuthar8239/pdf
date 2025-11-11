<?php

class SettingController
{
    use Controller;

    public function index()
    {
        $data = [
            'title' => 'Settings',
            'breadcrumb' => ['Home / Settings']

        ];

        $settingsModel = new Setting();
        $data['settings'] = $settingsModel->findAll() ?? [];

        $this->view('settings/index', $data);
    }

    public function sessions()
    {
        $data = [
            'title' => 'Sessions',
            'breadcrumb' => ['Home / Sessions']

        ];

        $this->view('settings/sessions', $data);
    }

    public function create()
    {
        $isAjax = $this->isAjaxRequest();

        // bulk setting array
        $settingKey = $_POST['settingKey'] ?? [];

        if (empty($settingKey)) {
            return $this->respond([
                'success' => false,
                'message' => 'No Key found.'
            ], $isAjax);
        }

        $settingValue = trim($_POST['settingValue'] ?? '');
        $settingStatus      = trim($_POST['settingStatus'] ?? 'active');

        $model = new Setting();

        if ($model->first(['key' => $settingKey])) {
            return $this->respond([
                'success' => false,
                'message' => 'Key already exists.'
            ], $isAjax);
        }

        $model->insert([
            'key' => $settingKey,
            'value' => $settingValue,
            'status' => $settingStatus,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'success' => true,
            'message' => ' Setting created successfully.'
        ], $isAjax);
    }

    public function update()
    {
        $isAjax = $this->isAjaxRequest();

        // bulk setting array
        $settingKey = $_POST['settingKey'] ?? [];
        $id = decryptId($_POST['settingId'] ?? 0);

        if (empty($settingKey)) {
            return $this->respond([
                'success' => false,
                'message' => 'Key is required.'
            ], $isAjax);
        }

        $settingValue = trim($_POST['settingValue'] ?? '');
        $settingStatus      = trim($_POST['settingStatus'] ?? 'active');

        $newData = [
            'key' => $settingKey,
            'value' => $settingValue,
            'status' => $settingStatus
        ];


        $model = new Setting();

        $old = $model->first(['key' => $settingKey]);

        if (!$old) {
            return $this->respond([
                'success' => false,
                'message' => 'Setting not found.'
            ], $isAjax);
        }

        $changes = detectChanges($old, $newData, ['value', 'key', 'status']);

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
                'message' => 'Setting updated successfully.',
            ], $isAjax);
        }

        return $this->respond([
            'success' => false,
            'message' => 'Failed to update. Try again.',
        ], $isAjax);
    }


    public function deleteSelected()
    {
        $isAjax = $this->isAjaxRequest();

        $ids = $_POST['ids'] ?? [];

        if (empty($ids)) {
            return $this->respond([
                'success' => false,
                'message' => 'No setting(s) selected.'
            ], $isAjax);
        }

        $model = new Setting();
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
            'message' => "$count setting(s) deleted successfully."
        ], $isAjax);
    }


    public function toggleHeadline()
    {
        unset($_SESSION['setting_cache']['headline_status']);
        unset($_SESSION['cached_headlines']);

        $isAjax = $this->isAjaxRequest();
        $status = $_POST['status'] ?? null;

        if (!in_array($status, ['on', 'off'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid status.'
            ], $isAjax);
        }

        $model = new Setting();

        // Update or insert
        $existing = $model->first(['key' => 'headline_status']);

        if ($existing) {
            $model->update($existing['id'], [
                'value'      => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $model->insert([
                'key'   => 'headline_status',
                'value'      => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Headline visibility updated.'
        ], $isAjax);
    }

    public function clearCache()
    {
        // Clear all cached settings
        if (isset($_SESSION['setting_cache'])) {
            unset($_SESSION['setting_cache']);
            return $this->respond([
                'success' => true,
                'message' => 'Cache cleared successfully.'
            ], $this->isAjaxRequest());
        }

        return $this->respond([
            'success' => false,
            'message' => 'Cache not found.'
        ], $this->isAjaxRequest());
    }

    public function toggleVersion()
    {
        SettingCache::class::clear('version');
        SettingCache::class::clear('version_status');

        $isAjax = $this->isAjaxRequest();
        $status = $_POST['status'] ?? null;

        if (!in_array($status, ['active', 'inactive'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid status.'
            ], $isAjax);
        }

        $model = new Setting();

        // Update or insert
        $existing = $model->first(['key' => 'version']);

        if ($existing) {
            $model->update($existing['id'], [
                'status'      => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $model->insert([
                'key'   => 'version',
                'status'      => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Version visibility updated.'
        ], $isAjax);
    }

    public function toggleDataOption()
    {
        unset($_SESSION['setting_cache']['data_option']);
        $isAjax = $this->isAjaxRequest();
        $status = $_POST['status'] ?? null;

        if (!in_array($status, ['on', 'off'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid status.'
            ], $isAjax);
        }

        $model = new Setting();

        // Update or insert
        $existing = $model->first(['key' => 'data_option']);

        if ($existing) {

            $model->update($existing['id'], [
                'value'      => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $model->insert([
                'key'   => 'data_option',
                'value'      => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Data Option visibility updated.'
        ], $isAjax);
    }

    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }


    private function respond(array $response, bool $isAjax, ?string $redirectTo = null)
    {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

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

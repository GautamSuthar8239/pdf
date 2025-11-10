<?php

class SettingController
{
    use Controller;

    public function index()
    {
        $data = [
            'title' => 'Settings',
            'breadcrumb' => ['Settings']

        ];

        $settingsModel = new Setting();
        $data['settings'] = $settingsModel->findAll() ?? [];

        $this->view('settings/index', $data);
    }

    public function toggleHeadline()
    {
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

    public function toggleDataOption()
    {
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

<?php

class GlobalHeadlines
{
    public static function load()
    {
        // If disabled → always empty
        if (SettingCache::get('headline_status', 'on') !== 'on') {
            return [];
        }

        // Already cached?
        if (!empty($_SESSION['cached_headlines'])) {
            return $_SESSION['cached_headlines'];
        }

        // Load from DB
        $model = new Headline();
        $rows = $model->where(['status' => 'active']);

        $messages = [];

        foreach ($rows as $r) {
            $messages[] = $r['text'];
        }

        // default fallback
        if (!$messages) {
            $messages = [
                "Automation saves hours — keep going!",
                "Your work builds real impact.",
                "Small progress daily = big results.",
                "Smart tools create smart outcomes."
            ];
        }

        $_SESSION['cached_headlines'] = $messages;

        return $messages;
    }

    public static function clear()
    {
        unset($_SESSION['cached_headlines']);
    }
}

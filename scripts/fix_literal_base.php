<?php
$viewsDir = __DIR__ . '/../app/views';

function fixLiteralBase($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            $changed = false;
            
            // Fix single quote variants
            if (strpos($content, "'<?= BASE ?>/") !== false) {
                $content = str_replace("'<?= BASE ?>/", "BASE . '/", $content);
                $changed = true;
            }
            if (strpos($content, "'<?= BASE ?>'") !== false) {
                $content = str_replace("'<?= BASE ?>'", "BASE", $content);
                $changed = true;
            }
            // Fix double quote variants
            if (strpos($content, '"<?= BASE ?>/') !== false) {
                $content = str_replace('"<?= BASE ?>/', 'BASE . "/', $content);
                $changed = true;
            }
            if (strpos($content, '"<?= BASE ?>"') !== false) {
                $content = str_replace('"<?= BASE ?>"', 'BASE', $content);
                $changed = true;
            }

            // Fix possible header redirects in views (e.g. auth blocks)
            if (strpos($content, "header('Location: <?= BASE ?>/") !== false) {
                $content = str_replace("header('Location: <?= BASE ?>/", "header('Location: ' . BASE . '/", $content);
                $changed = true;
            }

            if ($changed) {
                file_put_contents($path, $content);
                echo "Fixed literal BASE tags in: " . $path . "\n";
            }
        }
    }
}

fixLiteralBase($viewsDir);
echo "Done fixing literal BASE strings.\n";

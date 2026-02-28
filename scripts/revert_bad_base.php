<?php
$viewsDir = __DIR__ . '/../app/views';

function revertBadBase($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            $changed = false;
            
            // Fix hrefs
            if (strpos($content, 'href=BASE . "/') !== false) {
                $content = str_replace('href=BASE . "/', 'href="<?= BASE ?>/', $content);
                $changed = true;
            }
            if (strpos($content, "href=BASE . '/") !== false) {
                $content = str_replace("href=BASE . '/", 'href="<?= BASE ?>/', $content);
                $changed = true;
            }

            // Fix actions
            if (strpos($content, 'action=BASE . "/') !== false) {
                $content = str_replace('action=BASE . "/', 'action="<?= BASE ?>/', $content);
                $changed = true;
            }
            if (strpos($content, "action=BASE . '/") !== false) {
                $content = str_replace("action=BASE . '/", 'action="<?= BASE ?>/', $content);
                $changed = true;
            }

            // Fix src
            if (strpos($content, 'src=BASE . "/') !== false) {
                $content = str_replace('src=BASE . "/', 'src="<?= BASE ?>/', $content);
                $changed = true;
            }
            if (strpos($content, "src=BASE . '/") !== false) {
                $content = str_replace("src=BASE . '/", 'src="<?= BASE ?>/', $content);
                $changed = true;
            }

            // Fix standalone BASE outputs
            if (strpos($content, "=BASE . '") !== false) {
                $content = str_replace("=BASE . '", "='<?= BASE ?>", $content);
                $changed = true;
            }

            if ($changed) {
                file_put_contents($path, $content);
                echo "Reverted bad BASE tags in: " . $path . "\n";
            }
        }
    }
}

revertBadBase($viewsDir);
echo "Done reverting bad BASE strings.\n";

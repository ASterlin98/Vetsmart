<?php
$viewsDir = __DIR__ . '/../app/views';
$controllersDir = __DIR__ . '/../app/controllers';

function doReplace($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            
            $changed = false;
            
            // Si es vista
            if (strpos($path, 'views') !== false) {
                if (strpos($content, '/vetsmart/') !== false) {
                    $content = str_replace('/vetsmart/', '<?= BASE ?>/', $content);
                    $changed = true;
                }
                if (strpos($content, '"/vetsmart"') !== false) {
                    $content = str_replace('"/vetsmart"', '"<?= BASE ?>"', $content);
                    $changed = true;
                }
                if (strpos($content, "'/vetsmart'") !== false) {
                    $content = str_replace("'/vetsmart'", "'<?= BASE ?>'", $content);
                    $changed = true;
                }
            }
            
            // Si es controlador
            if (strpos($path, 'controllers') !== false) {
                if (strpos($content, "Location: /vetsmart/") !== false) {
                     $content = str_replace("Location: /vetsmart/", "Location: ' . BASE . '/", $content);
                     $changed = true;
                }
                if (strpos($content, "Location: /vetsmart") !== false) {
                     $content = str_replace("Location: /vetsmart", "Location: ' . BASE . '", $content);
                     $changed = true;
                }
            }
            
            if ($changed) {
                file_put_contents($path, $content);
                echo "Replaced in: " . $path . "\n";
            }
        }
    }
}

doReplace($viewsDir);
doReplace($controllersDir);
echo "Done\n";

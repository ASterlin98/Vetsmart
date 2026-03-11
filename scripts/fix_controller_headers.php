<?php
$controllersDir = __DIR__ . '/../app/controllers';

function fixHeadersController($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            $changed = false;
            
            // Fix double-quoted strings with single quotes + BASE inside
            // header("Location: ' . BASE . '/something");
            if (strpos($content, 'header("Location: \' . BASE . \'/') !== false) {
                // Change to: header('Location: ' . BASE . '/something');
                // For instance: header("Location: ' . BASE . '/admin");
                // Will become: header("Location: " . BASE . "/admin");
                $content = str_replace('header("Location: \' . BASE . \'/', 'header("Location: " . BASE . "/', $content);
                $changed = true;
            }

            // Sometimes there are other variants
            if (strpos($content, 'header("Location: \'. BASE .\'/') !== false) {
                $content = str_replace('header("Location: \'. BASE .\'/', 'header("Location: " . BASE . "/', $content);
                $changed = true;
            }

            if ($changed) {
                file_put_contents($path, $content);
                echo "Fixed redirect headers in: " . $path . "\n";
            }
        }
    }
}

fixHeadersController($controllersDir);
echo "Done fixing redirect headers.\n";

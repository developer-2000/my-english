<?php

namespace App\Helpers;

class ViteHelper
{
    public static function asset($entry)
    {
        if (app()->environment('local')) {
            return "http://localhost:5173/{$entry}";
        }

        $manifestPath = public_path('build/.vite/manifest.json');
        
        if (!file_exists($manifestPath)) {
            return asset("build/{$entry}");
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        if (isset($manifest[$entry])) {
            return asset('build/' . $manifest[$entry]['file']);
        }

        return asset("build/{$entry}");
    }

    public static function css($entry)
    {
        if (app()->environment('local')) {
            return "http://localhost:5173/{$entry}";
        }

        $manifestPath = public_path('build/.vite/manifest.json');
        
        if (!file_exists($manifestPath)) {
            return asset("build/{$entry}");
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        if (isset($manifest[$entry]) && isset($manifest[$entry]['css'])) {
            $cssFiles = [];
            foreach ($manifest[$entry]['css'] as $css) {
                $cssFiles[] = asset('build/' . $css);
            }
            return $cssFiles;
        }

        // Если CSS не найден в entry, возвращаем все CSS файлы из build
        $cssFiles = [];
        $buildPath = public_path('build');
        if (is_dir($buildPath)) {
            $files = scandir($buildPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'css') {
                    $cssFiles[] = asset('build/' . $file);
                }
            }
        }
        
        return $cssFiles;
    }
}

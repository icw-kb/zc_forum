<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DummyPluginFileGenerator
{
    /**
     * Generate a dummy plugin zip file.
     */
    public static function generate(string $pluginName, string $version): string
    {
        // Check if file already exists
        $storagePath = 'plugins/' . $pluginName . '/' . $version . '/' . $pluginName . '-v' . $version . '.zip';
        if (Storage::exists($storagePath)) {
            return $storagePath;
        }
        
        $tempDir = sys_get_temp_dir() . '/' . uniqid('plugin_');
        $pluginDir = $tempDir . '/' . $pluginName;
        
        // Create temporary directory structure
        if (!file_exists($pluginDir)) {
            mkdir($pluginDir, 0777, true);
        }
        
        // Create dummy plugin files
        self::createDummyFiles($pluginDir, $pluginName, $version);
        
        // Create zip file
        $zipPath = $tempDir . '/' . $pluginName . '-v' . $version . '.zip';
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            self::addFolderToZip($pluginDir, $zip, $pluginName);
            $zip->close();
        }
        
        // Store in Laravel storage
        $storagePath = 'plugins/' . $pluginName . '/' . $version . '/' . basename($zipPath);
        
        // Ensure directory exists
        Storage::makeDirectory(dirname($storagePath));
        
        // Store the file
        Storage::put($storagePath, file_get_contents($zipPath));
        
        // Cleanup temporary files
        self::removeDirectory($tempDir);
        
        return $storagePath;
    }
    
    /**
     * Create dummy plugin files.
     */
    private static function createDummyFiles(string $dir, string $pluginName, string $version): void
    {
        // Create directories first
        mkdir($dir . '/includes/modules', 0777, true);
        mkdir($dir . '/includes/templates', 0777, true);
        mkdir($dir . '/admin', 0777, true);
        
        // Create a readme file
        file_put_contents($dir . '/readme.txt', <<<EOT
{$pluginName} Plugin for Zen Cart
Version: {$version}

This is a dummy plugin file for testing purposes.

Installation:
1. Upload the files to your Zen Cart installation
2. Configure the plugin in the admin panel
3. Enjoy!

For support, visit: https://example.com/support
EOT
        );
        
        // Create a dummy PHP file
        file_put_contents($dir . '/includes/modules/' . $pluginName . '.php', <<<EOT
<?php
/**
 * {$pluginName} Plugin
 * Version: {$version}
 */

class {$pluginName} {
    public function __construct() {
        // Plugin initialization
    }
    
    public function execute() {
        // Plugin execution logic
    }
}
EOT
        );
        
        // Create some dummy template files
        file_put_contents($dir . '/includes/templates/template_default.php', '<?php // Template file ?>');
        
        // Create an install SQL file
        file_put_contents($dir . '/install.sql', <<<EOT
-- Installation SQL for {$pluginName}
-- Version: {$version}

CREATE TABLE IF NOT EXISTS {$pluginName}_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(255),
    config_value TEXT
);

INSERT INTO configuration (configuration_key, configuration_value) 
VALUES ('{$pluginName}_VERSION', '{$version}');
EOT
        );
        
        // Create a changelog
        file_put_contents($dir . '/changelog.txt', <<<EOT
{$pluginName} Changelog
====================

Version {$version}
- Initial release
- Added core functionality
- Fixed minor bugs
EOT
        );
    }
    
    /**
     * Add folder contents to zip file recursively.
     */
    private static function addFolderToZip(string $folder, ZipArchive $zip, string $parentFolder = ''): void
    {
        $handle = opendir($folder);
        
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                $filePath = $folder . '/' . $file;
                $localPath = $parentFolder . '/' . $file;
                
                if (is_file($filePath)) {
                    $zip->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    $zip->addEmptyDir($localPath);
                    self::addFolderToZip($filePath, $zip, $localPath);
                }
            }
        }
        
        closedir($handle);
    }
    
    /**
     * Remove directory recursively.
     */
    private static function removeDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? self::removeDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}
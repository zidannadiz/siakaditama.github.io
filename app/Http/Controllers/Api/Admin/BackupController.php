<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Services\AuditLogService;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Get list of backups
     */
    public function index()
    {
        $backups = $this->getBackupList();
        
        return response()->json([
            'success' => true,
            'data' => $backups,
        ]);
    }

    /**
     * Create new backup
     */
    public function create()
    {
        try {
            $dbConnection = config('database.default');
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$timestamp}.sql";
            
            if ($dbConnection === 'sqlite') {
                $databasePath = config('database.connections.sqlite.database');
                
                if (!File::exists($databasePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Database file tidak ditemukan.',
                    ], 404);
                }
                
                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }
                
                try {
                    $sqlDump = $this->createSqliteDump($databasePath);
                    $backupPath = $backupDir . '/' . $backupName;
                    File::put($backupPath, $sqlDump);
                    
                    $sqliteBackupPath = $backupDir . '/backup_' . $timestamp . '.sqlite';
                    File::copy($databasePath, $sqliteBackupPath);
                    
                    AuditLogService::logCustom(
                        'backup',
                        null,
                        "Backup database berhasil dibuat: {$backupName}"
                    );
                    
                    return response()->json([
                        'success' => true,
                        'message' => "Backup berhasil dibuat: {$backupName}",
                        'data' => [
                            'filename' => $backupName,
                            'size' => File::size($backupPath),
                            'size_human' => $this->formatBytes(File::size($backupPath)),
                            'created_at' => Carbon::now()->toDateTimeString(),
                        ],
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Backup SQLite error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat backup: ' . $e->getMessage(),
                    ], 500);
                }
                
            } elseif (in_array($dbConnection, ['mysql', 'mariadb'])) {
                $config = config("database.connections.{$dbConnection}");
                
                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }
                
                $backupPath = $backupDir . '/' . $backupName;
                
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                    escapeshellarg($config['username']),
                    escapeshellarg($config['password']),
                    escapeshellarg($config['host']),
                    escapeshellarg($config['port'] ?? '3306'),
                    escapeshellarg($config['database']),
                    escapeshellarg($backupPath)
                );
                
                exec($command, $output, $returnCode);
                
                if ($returnCode !== 0 || !File::exists($backupPath) || File::size($backupPath) === 0) {
                    $error = implode("\n", $output);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat backup. Pastikan mysqldump tersedia. Error: ' . $error,
                    ], 500);
                }
                
                AuditLogService::logCustom(
                    'backup',
                    null,
                    "Backup database berhasil dibuat: {$backupName}"
                );
                
                return response()->json([
                    'success' => true,
                    'message' => "Backup berhasil dibuat: {$backupName}",
                    'data' => [
                        'filename' => $backupName,
                        'size' => File::size($backupPath),
                        'size_human' => $this->formatBytes(File::size($backupPath)),
                        'created_at' => Carbon::now()->toDateTimeString(),
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe database tidak didukung untuk backup.',
                ], 400);
            }
            
        } catch (\Exception $e) {
            \Log::error('Backup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat backup: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore from backup
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);

        try {
            $backupFile = $request->backup_file;
            $backupPath = storage_path('app/backups/' . $backupFile);
            
            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan.',
                ], 404);
            }
            
            $dbConnection = config('database.default');
            
            if ($dbConnection === 'sqlite') {
                $databasePath = config('database.connections.sqlite.database');
                
                $currentBackupPath = storage_path('app/backups/pre_restore_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sqlite');
                if (File::exists($databasePath)) {
                    if (!File::exists(storage_path('app/backups'))) {
                        File::makeDirectory(storage_path('app/backups'), 0755, true);
                    }
                    File::copy($databasePath, $currentBackupPath);
                }
                
                \DB::disconnect();
                
                if (pathinfo($backupFile, PATHINFO_EXTENSION) === 'sql') {
                    $sql = File::get($backupPath);
                    
                    if (File::exists($databasePath)) {
                        File::delete($databasePath);
                    }
                    
                    File::put($databasePath, '');
                    File::chmod($databasePath, 0666);
                    
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    
                    foreach ($statements as $statement) {
                        if (!empty($statement) && !str_starts_with(trim($statement), '--')) {
                            try {
                                \DB::unprepared($statement . ';');
                            } catch (\Exception $e) {
                                \Log::warning('SQL statement failed: ' . substr($statement, 0, 100));
                            }
                        }
                    }
                } else {
                    if (File::exists($databasePath)) {
                        File::delete($databasePath);
                    }
                    File::copy($backupPath, $databasePath);
                    File::chmod($databasePath, 0666);
                }
                
                AuditLogService::logCustom(
                    'restore',
                    null,
                    "Database berhasil di-restore dari backup: {$backupFile}"
                );
                
                return response()->json([
                    'success' => true,
                    'message' => "Database berhasil di-restore dari: {$backupFile}",
                ]);
                
            } elseif (in_array($dbConnection, ['mysql', 'mariadb'])) {
                $config = config("database.connections.{$dbConnection}");
                
                $command = sprintf(
                    'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
                    escapeshellarg($config['username']),
                    escapeshellarg($config['password']),
                    escapeshellarg($config['host']),
                    escapeshellarg($config['port'] ?? '3306'),
                    escapeshellarg($config['database']),
                    escapeshellarg($backupPath)
                );
                
                exec($command, $output, $returnCode);
                
                if ($returnCode !== 0) {
                    $error = implode("\n", $output);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal restore database. Error: ' . $error,
                    ], 500);
                }
                
                AuditLogService::logCustom(
                    'restore',
                    null,
                    "Database berhasil di-restore dari backup: {$backupFile}"
                );
                
                return response()->json([
                    'success' => true,
                    'message' => "Database berhasil di-restore dari: {$backupFile}",
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe database tidak didukung untuk restore.',
                ], 400);
            }
            
        } catch (\Exception $e) {
            \Log::error('Restore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat restore: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete backup
     */
    public function destroy($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan.',
                ], 404);
            }
            
            File::delete($backupPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dihapus.',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Delete backup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus backup: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getBackupList()
    {
        $backupDir = storage_path('app/backups');
        
        if (!File::exists($backupDir)) {
            return [];
        }
        
        $files = File::files($backupDir);
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'size_human' => $this->formatBytes($file->getSize()),
                'created_at' => Carbon::createFromTimestamp($file->getMTime())->toDateTimeString(),
            ];
        }
        
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $backups;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function createSqliteDump($databasePath)
    {
        try {
            $db = new \PDO('sqlite:' . $databasePath);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = "-- SQLite Backup\n";
            $sql .= "-- Generated: " . Carbon::now()->toDateTimeString() . "\n";
            $sql .= "-- Database: " . basename($databasePath) . "\n\n";
            $sql .= "BEGIN TRANSACTION;\n\n";
            
            $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(\PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                $createTable = $db->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'")->fetchColumn();
                if ($createTable) {
                    $sql .= "-- Table: {$table}\n";
                    $sql .= $createTable . ";\n\n";
                    
                    $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                    if (!empty($rows)) {
                        $columns = array_keys($rows[0]);
                        $columnNames = '`' . implode('`, `', $columns) . '`';
                        
                        foreach ($rows as $row) {
                            $values = array_map(function($value) use ($db) {
                                if ($value === null) {
                                    return 'NULL';
                                } elseif (is_numeric($value)) {
                                    return $value;
                                } else {
                                    return $db->quote($value);
                                }
                            }, array_values($row));
                            
                            $sql .= "INSERT INTO `{$table}` ({$columnNames}) VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sql .= "\n";
                    }
                }
            }
            
            $sql .= "COMMIT;\n";
            
            return $sql;
        } catch (\Exception $e) {
            \Log::error('SQLite dump error: ' . $e->getMessage());
            throw $e;
        }
    }
}

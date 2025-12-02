<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Services\AuditLogService;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupList();
        
        return view('admin.backup.index', compact('backups'));
    }

    public function create()
    {
        try {
            $dbConnection = config('database.default');
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$timestamp}.sql";
            
            if ($dbConnection === 'sqlite') {
                // Backup SQLite
                $databasePath = config('database.connections.sqlite.database');
                
                if (!File::exists($databasePath)) {
                    return back()->withErrors(['error' => 'Database file tidak ditemukan.']);
                }
                
                // Create backup directory if not exists
                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }
                
                // Create SQL dump for SQLite
                try {
                    $sqlDump = $this->createSqliteDump($databasePath);
                    $backupPath = $backupDir . '/' . $backupName;
                    File::put($backupPath, $sqlDump);
                    
                    // Also copy the SQLite file as backup
                    $sqliteBackupPath = $backupDir . '/backup_' . $timestamp . '.sqlite';
                    File::copy($databasePath, $sqliteBackupPath);
                    
                    // Log audit
                    AuditLogService::logCustom(
                        'backup',
                        null,
                        "Backup database berhasil dibuat: {$backupName}"
                    );
                    
                    return back()->with('success', "Backup berhasil dibuat: {$backupName}");
                } catch (\Exception $e) {
                    \Log::error('Backup SQLite error: ' . $e->getMessage());
                    return back()->withErrors(['error' => 'Gagal membuat backup: ' . $e->getMessage()]);
                }
                
            } elseif (in_array($dbConnection, ['mysql', 'mariadb'])) {
                // Backup MySQL/MariaDB
                $config = config("database.connections.{$dbConnection}");
                
                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }
                
                $backupPath = $backupDir . '/' . $backupName;
                
                // Build mysqldump command
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
                    return back()->withErrors(['error' => 'Gagal membuat backup. Pastikan mysqldump tersedia. Error: ' . $error]);
                }
                
                // Log audit
                AuditLogService::logCustom(
                    'backup',
                    null,
                    "Backup database berhasil dibuat: {$backupName}"
                );
                
                return back()->with('success', "Backup berhasil dibuat: {$backupName}");
            } else {
                return back()->withErrors(['error' => 'Tipe database tidak didukung untuk backup.']);
            }
            
        } catch (\Exception $e) {
            \Log::error('Backup error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat backup: ' . $e->getMessage()]);
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);

        try {
            $backupFile = $request->backup_file;
            $backupPath = storage_path('app/backups/' . $backupFile);
            
            if (!File::exists($backupPath)) {
                return back()->withErrors(['error' => 'File backup tidak ditemukan.']);
            }
            
            $dbConnection = config('database.default');
            
            if ($dbConnection === 'sqlite') {
                // Restore SQLite
                $databasePath = config('database.connections.sqlite.database');
                
                // Backup current database first (safety measure)
                $currentBackupPath = storage_path('app/backups/pre_restore_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sqlite');
                if (File::exists($databasePath)) {
                    if (!File::exists(storage_path('app/backups'))) {
                        File::makeDirectory(storage_path('app/backups'), 0755, true);
                    }
                    File::copy($databasePath, $currentBackupPath);
                }
                
                // Close any existing connections
                DB::disconnect();
                
                // If backup file is .sql file, restore from SQL dump
                if (pathinfo($backupFile, PATHINFO_EXTENSION) === 'sql') {
                    // Restore from SQL dump
                    $sql = File::get($backupPath);
                    
                    // Delete existing database
                    if (File::exists($databasePath)) {
                        File::delete($databasePath);
                    }
                    
                    // Create new database
                    File::put($databasePath, '');
                    File::chmod($databasePath, 0666);
                    
                    // Execute SQL statements one by one (some may fail, that's ok)
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    
                    foreach ($statements as $statement) {
                        if (!empty($statement) && !str_starts_with(trim($statement), '--')) {
                            try {
                                DB::unprepared($statement . ';');
                            } catch (\Exception $e) {
                                // Continue with next statement
                                \Log::warning('SQL statement failed: ' . substr($statement, 0, 100));
                            }
                        }
                    }
                } else {
                    // Restore from SQLite file copy
                    if (File::exists($databasePath)) {
                        File::delete($databasePath);
                    }
                    File::copy($backupPath, $databasePath);
                    File::chmod($databasePath, 0666);
                }
                
                // Log audit
                AuditLogService::logCustom(
                    'restore',
                    null,
                    "Database berhasil di-restore dari backup: {$backupFile}"
                );
                
                return back()->with('success', "Database berhasil di-restore dari: {$backupFile}");
                
            } elseif (in_array($dbConnection, ['mysql', 'mariadb'])) {
                // Restore MySQL/MariaDB
                $config = config("database.connections.{$dbConnection}");
                
                // Build mysql command to restore
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
                    return back()->withErrors(['error' => 'Gagal restore database. Error: ' . $error]);
                }
                
                // Log audit
                AuditLogService::logCustom(
                    'restore',
                    null,
                    "Database berhasil di-restore dari backup: {$backupFile}"
                );
                
                return back()->with('success', "Database berhasil di-restore dari: {$backupFile}");
            } else {
                return back()->withErrors(['error' => 'Tipe database tidak didukung untuk restore.']);
            }
            
        } catch (\Exception $e) {
            \Log::error('Restore error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat restore: ' . $e->getMessage()]);
        }
    }

    public function download($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (!File::exists($backupPath)) {
            abort(404, 'File backup tidak ditemukan.');
        }
        
        return response()->download($backupPath, $filename);
    }

    public function destroy($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!File::exists($backupPath)) {
                return back()->withErrors(['error' => 'File backup tidak ditemukan.']);
            }
            
            File::delete($backupPath);
            
            return back()->with('success', 'Backup berhasil dihapus.');
            
        } catch (\Exception $e) {
            \Log::error('Delete backup error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus backup: ' . $e->getMessage()]);
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
                'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                'path' => $file->getPathname(),
            ];
        }
        
        // Sort by created_at desc
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
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
            // Create SQL dump from SQLite database
            $db = new \PDO('sqlite:' . $databasePath);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = "-- SQLite Backup\n";
            $sql .= "-- Generated: " . Carbon::now()->toDateTimeString() . "\n";
            $sql .= "-- Database: " . basename($databasePath) . "\n\n";
            $sql .= "BEGIN TRANSACTION;\n\n";
            
            // Get all tables
            $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(\PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                // Get CREATE TABLE statement
                $createTable = $db->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'")->fetchColumn();
                if ($createTable) {
                    $sql .= "-- Table: {$table}\n";
                    $sql .= $createTable . ";\n\n";
                    
                    // Get table data
                    $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                    if (!empty($rows)) {
                        // Get column names
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

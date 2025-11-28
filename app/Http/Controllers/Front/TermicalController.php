<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TermicalController extends Controller
{
    public function index()
    {
        return view("terminal");
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command' => ['required', 'string'],
        ]);

        $userCommand = trim($request->input('command'));
        Log::channel('terminal')->info("Executing command: {$userCommand}");

        try {
            // if not local, set COMPOSER_HOME as env var if present
            if (app()->environment() !== 'local') {
                if ($composerHome = env('COMPOSER_HOME')) {
                    putenv("COMPOSER_HOME={$composerHome}");
                }
            }

            // shorthand helper to find binary via PATH
            $findBinary = function (string $bin) {
                $cmd = strtoupper(substr(PHP_OS_FAMILY, 0, 3)) === 'WIN' ? "where {$bin}" : "command -v {$bin}";
                try {
                    $proc = Process::fromShellCommandline($cmd, base_path());
                    $proc->setTimeout(4);
                    $proc->run();
                    if ($proc->isSuccessful()) {
                        $out = trim($proc->getOutput());
                        if ($out !== '') {
                            $lines = preg_split('/[\r\n]+/', $out);
                            return trim($lines[0]);
                        }
                    }
                } catch (\Throwable $e) {
                }
                return null;
            };

            // detect php binary
            $phpBinary = null;
            $candidates = [];
            if (!empty(PHP_BINARY)) $candidates[] = PHP_BINARY;
            $candidates[] = 'php'; // check PATH fallback
            // check candidate from where/which
            if ($path = $findBinary('php')) $candidates[] = $path;
            // check some common wamp location
            $candidates[] = base_path('..\\..\\php\\php'.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'\\php.exe');

            foreach ($candidates as $cand) {
                if (empty($cand)) continue;
                try {
                    $p = Process::fromShellCommandline('"'.$cand.'" -v', base_path());
                    $p->setTimeout(3);
                    $p->run();
                    if ($p->isSuccessful()) { $phpBinary = $cand; break; }
                } catch (\Throwable $e) { /* ignore */ }
            }

            if (empty($phpBinary)) {
                $msg = 'PHP CLI binary not found. Please install PHP CLI or add it to PATH.';
                Log::channel('terminal')->error($msg);
                return response()->json(['success' => false, 'output' => $msg], 500);
            }

            // helper to split args preserving quoted segments
            $splitArgs = function (string $raw = '') {
                if ($raw === '') return [];
                $parts = str_getcsv($raw, ' ');
                return array_values(array_filter($parts, fn($v) => $v !== null && $v !== ''));
            };

            $process = null;
            // Composer handling
            if (preg_match('/^\s*composer\b/i', $userCommand)) {
                $argsRaw = trim(preg_replace('/^\s*composer\b/i', '', $userCommand));
                $parts = $splitArgs($argsRaw);
                $composerPhar = base_path('composer.phar');
                $composerGlobal = $findBinary('composer');
                if (file_exists($composerPhar)) {
                    array_unshift($parts, $composerPhar);
                    array_unshift($parts, $phpBinary);
                    $process = new Process($parts, base_path());
                } elseif ($composerGlobal) {
                    array_unshift($parts, $composerGlobal);
                    $process = new Process($parts, base_path());
                } else {
                    $msg = 'composer.phar not found and composer not available globally. Upload composer.phar or enable composer in PATH.';
                    Log::channel('terminal')->error($msg);
                    return response()->json(['success' => false, 'output' => $msg], 400);
                }
            }

            // PHP artisan (php artisan / artisan / migrate) handling
            elseif (preg_match('/^\s*php\s+artisan\b/i', $userCommand) || preg_match('/^\s*artisan\b/i', $userCommand) || preg_match('/^\s*migrate\b/i', $userCommand)) {
                $argsRaw = $userCommand;
                // Remove leading 'php' if present
                $argsRaw = preg_replace('/^\s*php\s+/i', '', $argsRaw);
                // Remove leading 'artisan' if present
                $argsRaw = preg_replace('/^\s*artisan\b/i', '', $argsRaw);
                $argsRaw = trim($argsRaw);
                $parts = $splitArgs($argsRaw);
                array_unshift($parts, 'artisan');
                array_unshift($parts, $phpBinary);
                $process = new Process($parts, base_path());
            }

            // Generic 'php' commands
            elseif (preg_match('/^\s*php\b/i', $userCommand)) {
                $argsRaw = trim(preg_replace('/^\s*php\b/i', '', $userCommand));
                $parts = $splitArgs($argsRaw);
                array_unshift($parts, $phpBinary);
                $process = new Process($parts, base_path());
            }

            // For other commands, if they include shell operators, run as shell command, otherwise split into args
            else {
                $shellOperators = ['|','>','<',';','&','*','?','$','`'];
                $useShell = false;
                foreach ($shellOperators as $op) {
                    if (strpos($userCommand, $op) !== false) { $useShell = true; break; }
                }
                if ($useShell) {
                    $process = Process::fromShellCommandline($userCommand, base_path());
                } else {
                    $parts = $splitArgs($userCommand);
                    if (count($parts) === 0) {
                        return response()->json(['success' => false, 'output' => 'Empty command']);
                    }
                    $process = new Process($parts, base_path());
                }
            }

            // Fallback check
            if (!isset($process) || !$process instanceof Process) {
                $process = Process::fromShellCommandline($userCommand, base_path());
            }

            $process->setTimeout(3600);
            $process->run();

            if (!$process->isSuccessful()) {
                $errOut = trim($process->getErrorOutput());
                $stdOut = trim($process->getOutput());
                $message = $errOut ?: $stdOut ?: 'Command returned non-zero exit code.';
                Log::channel('terminal')->error('Process failed: ' . $message);
                return response()->json(['success' => false, 'output' => nl2br(e($message))], 500);
            }

            $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());
            Log::channel('terminal')->info("Output: {$output}");
            Log::channel('terminal')->info("*************************************");

            return response()->json(['success' => true, 'output' => nl2br(e($output))]);
        } catch (\Throwable $e) {
            Log::channel('terminal')->error("Command failed: {$userCommand}", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'output' => nl2br(e($e->getMessage()))], 500);
        }
    }
}

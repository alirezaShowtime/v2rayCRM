<?php

namespace App\Http\Controllers\Terminal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TerminalController extends Controller
{

    public function panel()
    {
        $path = "";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = shell_exec("echo %cd%");
        } else {
            $path = shell_exec("pwd");
        }

        return view('terminal.panel', [
            "output" => "",
            "path" => $path,
            "command" => "",
        ]);
    }

    public function runCommend(Request $request)
    {
        
        $path = "";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = shell_exec("echo %cd%");
        } else {
            $path = shell_exec("pwd");
        }
        
        return view('terminal.panel', [
            "output" => shell_exec($request->command),
            "path" => $path,
            "command" => $request->command,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Support\AssignmentDocuments;
use App\Support\AssignmentFiles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicAssignmentFileController extends Controller
{
    public function showById(Request $request, int $id): BinaryFileResponse|JsonResponse
    {
        $row = DB::table('assignement')->where('id', $id)->first();
        if (! $row || ($row->type ?? '') !== 'file') {
            return response()->json(['status' => 'error', 'message' => 'File not found'], 404);
        }

        $index = max(0, (int) $request->query('index', 0));
        $path = AssignmentDocuments::resolvePath((string) $row->document, $index);
        if (! $path) {
            return response()->json(['status' => 'error', 'message' => 'File missing on server'], 404);
        }

        $filename = AssignmentDocuments::resolveName((string) $row->document, $index);

        return response()->file($path, [
            'Content-Type' => AssignmentFiles::mimeType($path),
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function show(string $filename): BinaryFileResponse|JsonResponse
    {
        $filename = basename(urldecode($filename));

        return $this->fileResponse($filename);
    }

    private function fileResponse(string $filename): BinaryFileResponse|JsonResponse
    {
        $path = AssignmentFiles::resolvePath($filename);
        if (! $path) {
            return response()->json(['status' => 'error', 'message' => 'File missing on server'], 404);
        }

        return response()->file($path, [
            'Content-Type' => AssignmentFiles::mimeType($path),
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}

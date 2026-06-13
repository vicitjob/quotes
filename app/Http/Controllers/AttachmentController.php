<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GateEntryDetail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    //
	public function download($id,$fileno): StreamedResponse
    {
        $record = GateEntryDetail::findOrFail($id);
		$filename = 'file'.$fileno;
        // Optional: extra authorization check
        // abort_unless(auth()->user()->can('view', $record), 403);
		
		if(!isset($record->$filename))
		{
			abort(404);
		}

        if (!$record->$filename || !Storage::exists($record->$filename)) {
            abort(404);
        }

        // Download with original filename if you store it, else basename:
        $downloadName = basename($record->$filename);

        return Storage::download($record->$filename, $downloadName);
		// Let browser decide: show inline if it can (PDF/image), otherwise download
		// Absolute path on disk (local/private disk)
        //$path = Storage::path($record->$filename);
        //return response()->file($path);
    }
}

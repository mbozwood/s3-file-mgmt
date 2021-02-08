<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $files = File::query()->with('user')->get();
        return response()->view('admin.index', compact('files'));
    }

    /**
     * Download the file
     *
     * @param File $file
     * @return StreamedResponse
     */
    public function show(File $file)
    {
        $s3file = $file->downloadFromS3();

        $file->update(['download_count' => $file->download_count + 1]);

        return response()->streamDownload(function() use ($s3file) {
            echo $s3file['Body'];
        }, $file->original_filename);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param File $file
     * @return RedirectResponse
     */
    public function destroy(File $file)
    {
        $response = $file->deleteFromS3();
        if($response != null) {
            session()->flash('error', $response);
            return redirect()->route('admin.index');
        }

        $success = $file->original_filename . ' was successfully deleted';
        $file->forceDelete();

        session()->flash('success', $success);
        return redirect()->route('admin.index');
    }
}

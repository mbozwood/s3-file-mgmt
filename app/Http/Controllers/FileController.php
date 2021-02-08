<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = auth()->user()->files;
        return response()->view('files.index', compact('files'));
    }

    /**
     * Show the form
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('files.create');
    }


    /**
     * Upload the file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $ftu = $request->file('file');

            $file = new File;
            $file->s3_filename = Str::uuid();
            $file->original_filename = $ftu->getClientOriginalName();
            $file->endpoint_url = '';
            $file->bucketname = env('AWS_BUCKET', '');
            $file->content_type = $request->file('file')->getMimeType();
            $file->user_id = Auth::id();
            $file->save();

            $response = $file->uploadToS3($request->file('file')->getRealPath());
            if($response != null) {
                $file->forceDelete();
                session()->flash('error', $response);
                return redirect()->route('files.create');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'There was an error uploading the file');
            return redirect()->route('files.create');
        }

        $success = $file->original_filename . ' was successfully uploaded';
        session()->flash('success', $success);
        return redirect()->route('files.index');
    }

    /**
     * Download the file
     *
     * @param File $file
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(File $file)
    {
        if($file->user_id == Auth::id()) {
            $s3file = $file->downloadFromS3();

            $file->update(['download_count' => $file->download_count + 1]);

            return response()->streamDownload(function() use ($s3file) {
                echo $s3file['Body'];
            }, $file->original_filename);

        } else {
            $response = 'You must be the owner of the file to download it';
            session()->flash('error', $response);
            return redirect()->route('files.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param File $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(File $file)
    {
        $response = $file->deleteFromS3();
        if($response != null) {
            session()->flash('error', $response);
            return redirect()->route('files.index');
        }

        $success = $file->original_filename . ' was successfully deleted';
        $file->forceDelete();

        session()->flash('success', $success);
        return redirect()->route('files.index');
    }
}

<?php

namespace App\Http\Controllers;

use File;
use Image;
use Session;
use Storage;
use App\Http\Requests;
//use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediamanagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware(['auth', 'clearance'])->except('show');
        $this->middleware('auth');
        $this->middleware('clearance')->except('show');
    }

    /*
     * Store a new file into Storeage media.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png,gif,zip,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        // Make directory if not exist
        $autoPath = date('Y/m');
        $mediaPath = base_path().'/public/media/'.$autoPath;
        $imgThumbPath = base_path().'/public/media/'.$autoPath.'/thubnails';

        if (! File::isDirectory($mediaPath)) {
            File::makeDirectory($mediaPath, 0777, true, true);
        }
        if (! File::isDirectory($imgThumbPath)) {
            File::makeDirectory($imgThumbPath, 0777, true, true);
        }

        // zjisteni nazvu souboru
        //$fileName = $request->file('file')->getClientOriginalName();

        $originalImage = $request->file;
        $fileName = $originalImage->getClientOriginalName();

        // ocisteni nazvu souboru od ...
        $fileName = strip_tags($fileName);
        $fileName = preg_replace('/[\r\n\t ]+/', ' ', $fileName);
        $fileName = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $fileName);
        $fileName = strtolower($fileName);
        $fileName = html_entity_decode($fileName, ENT_QUOTES, 'utf-8');
        $fileName = htmlentities($fileName, ENT_QUOTES, 'utf-8');
        $fileName = preg_replace('/(&)([a-z])([a-z]+;)/i', '$2', $fileName);
        $fileName = str_replace(' ', '-', $fileName);
        $fileName = rawurlencode($fileName);
        $fileName = str_replace('%', '-', $fileName);

        // If common files
        $uploadFile = explode('.', $fileName);
        $mimeType = $uploadFile[1];
        $pureFileName = $uploadFile[0];
        if ($mimeType == 'zip' || $mimeType == 'pdf' || $mimeType == 'doc' || $mimeType == 'docx' || $mimeType == 'xls' || $mimeType == 'xlsx') {
            $request->file('file')->move(
                base_path().'/public/media/'.$autoPath.'/', $fileName
            );
        } else {
            // StandardImage
            $standardImage = Image::make($originalImage);
            $standardImage->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $standardImage->sharpen(15);
            $standardImage->save(base_path().'/public/media/'.$autoPath.'/'.$pureFileName.'.jpg');

            //Thumbnail
            $thumbnailImage = Image::make($originalImage);
            $thumbnailImage->resize(640, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $standardImage->sharpen(15);
            $thumbnailImage->save(base_path().'/public/media/'.$autoPath.'/thubnails/'.$fileName);

            //$thumbnailImage->save(base_path() . '/public/media/'.$autoPath.'/thubnails/'. $fileName);
        }

        Session::flash('flash_message', 'Soubor '.$mimeType.' byl uložen do adresře '.$autoPath);

        return redirect()->back();
    }

    /**
     * Show the media mainpage.
     *
     * @param  $year Year
     * @return Response
     */
    public function show($year = null)
    {
        $yearsDir = Storage::disk('media')->directories('/');

        $directory = $year;
        $files = Storage::disk('media')->allFiles($directory);

        return view('admin.media.index', compact('files', 'year', 'yearsDir'));
    }

    /**
     * Delete selected file.
     * @param $fileName Full path to file in Media Storage
     * @return Response
     */
    public function fileDelete($fileName)
    {
        $file = str_replace('::', '/', $fileName);

        Storage::disk('media')->delete($file);
        Session::flash('flash_message', 'Soubor '.$file.' byl smazán.');

        return redirect()->back();
    }
}

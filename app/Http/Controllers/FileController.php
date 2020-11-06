<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Date;
use Carbon\Carbon;
use App\Campaign;
use App\Smtp;
use App\Smtps_group;
use App\File;
use App\Type;
use Illuminate\Http\File as AppFile;

class FileController extends Controller
{

    function AttachFileAttachement(Request $request){
        $file = File::find($request->file);
        $campaign = Campaign::with('files')->where('id', $request->campaign)->first();
        if(!$campaign->files->contains($file->id))
        {
            $campaign->files()->attach($file->id);
            return response()->json(['campaign' => $campaign, 'message' => 'File attached!']);
        }
        else
            return response()->json(['campaign' => $campaign, 'message' => 'File already attached!']);
    }

    function detachFile(Request $request)
    {
        $file = File::find($request->file);
        $campaign = Campaign::with('files')->where('id', $request->campaign)->first();
        if($campaign->files->contains($file->id))
        {
            $campaign->files()->detach($file->id);
            return response()->json(['campaign' => $campaign, 'message' => 'File detached!']);
        }
        else
            return response()->json(['campaign' => $campaign, 'message' => 'File is not attached!']);
    }

    function addFile(Request $request)
    {
        $file = new File();
        $name = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME) . '_' . str_replace(["-"," ",":"], "_", Carbon::now()->toDateTimeString());
        $ext = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_EXTENSION);
        $path = Storage::putFileAs('/files', $request->file('file'), "$name.$ext");
        if ($request->name)
            $file->name = $request->name;
        else
            $file->name = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
        $file->path = $path;
        if ($request->type == 1)
            $file->lines = $this->remove_duplicates($path, $request->duplicate);
        else
            $file->lines = 0;
        $file->type_id = $request->type;
        $file->save();
        return response()->json(['file' => $file, 'message' => 'File added!']);
    }

    function updateFile(Request $request)
    {
        $file = File::find($request->file);
        $file->name = $request->name;
        $file->save();
        return response()->json(['file' => $file, 'message' => 'File name updated successfully!']);
    }

    function getFiles(){
        $files = File::all();
        return response()->json(['files' => $files]);
    }

    function remove_duplicates($path, $duplicate = 0)
    {
        $data = file_get_contents(storage_path('app/public/' . $path));
        $array = array_filter(array_map('trim', explode("\n", $data)));
        if ($duplicate)
            $array = array_unique($array);
        $size = sizeof($array);
        $str = implode("\n", $array);
        file_put_contents(storage_path('app/public/' . $path), $str);
        return $size;
    }

    function mergeFiles(Request $request)
    {
        $file = new File();
        $name = $request->name . '_' . str_replace(["-"," ",":"], "_", Carbon::now()->toDateTimeString());
        $file->name = $request->name;
        $file->lines = 0;
        $file->type_id = 1;
        $path = "files/$name.txt";
        file_put_contents(storage_path('app/public/' . $path), "");
        $file->path = $path;
        $file->save();

        $files = File::whereIn('id', $request->fs)->get();
        foreach($files as $f)
        {
            $data = file_get_contents(storage_path('app/public/' . $f->path));
            file_put_contents(storage_path('app/public/' . $path), $data."\n", FILE_APPEND | LOCK_EX);
            $file->lines += $f->lines;
            if ($request->deleteAction)
            {
                Storage::delete($f->path);
                $f->delete();
            }
            else
                $request->fs = array();
        }
        $file->save();
        return response()->json(['file' => $file,'old' => $request->fs ,'message' => 'Files Merged Successfully!']);
    }

    function deleteFile($id)
    {
        $file = File::find($id);
        Storage::delete($file->path);
        $file->delete();
        return response()->json(['message' => 'File Deleted Successfully!']);
    }

}

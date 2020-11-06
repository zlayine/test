<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Date;
use Carbon\Carbon;
use App\Smtp;
use App\Smtps_group;
use App\File;

class SmtpController extends Controller
{
    function getSmtps($id)
    {
        $group = Smtps_group::where('id', $id)->with('smtps')->first();
        return response()->json(['group' => $group]);
    }

    function getGroups()
    {
        $groups = Smtps_group::withCount('smtps')->get();
        return response()->json(['groups' => $groups]);
    }

    function addSmtp(Request $request)
    {
        $group = new Smtps_group();
        $group->name = $request->name;
        $group->save();
        $name = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME) . '_' . str_replace(["-"," ",":"], "_", Carbon::now()->toDateTimeString());
        $path = Storage::putFileAs('/files', $request->file('file'), "$name.txt");
        $file = file_get_contents(storage_path('app/public/' . $path));
        // $contents = explode(':', $xxxxx);
        // $lines = explode("\n", $xxxxx);
        $array = array_filter(array_map('trim', explode("\n", $file)));
        foreach ($array as $line) {
            $smtpfields = explode(':', $line);
            $smtp = new Smtp();
            $smtp->group_id = $group->id;
            $smtp->username = $smtpfields[0];
            $smtp->password = $smtpfields[1];
            $smtp->host = $smtpfields[2];
            $smtp->port = $smtpfields[3];
            $smtp->limit = 0;
            $smtp->hits = 0;
            $smtp->status = 1;
            $smtp->save();
        }
        Storage::delete($path);
        $group = Smtps_group::where('id', $group->id)->withCount('smtps')->first();
        return response()->json(['group' => $group , 'message' => 'Smtps added successfully!']);
    }

    function addGroup(Request $request)
    {
        $group = new Smtps_group();
        $group->name = $request->name;
        $group->save();
        return response()->json(['group' => $group, 'message' => 'Group created successfully!']);
    }

    function updateSmtp(Request $request)
    {

    }

    function updateGroup(Request $request)
    {
        $group = Smtps_group::find($request->smtp);
        $group->name = $request->name;
        $group->save();
        return response()->json(['message' => 'Smtp Group Updated successfully!']);
    }

    function deleteSmtp($id)
    {
        $smtp = Smtp::find($id);
        $smtp->delete();
        return response()->json(['message' => 'Smtp deleted!']);
    }

    function deleteGroup($id)
    {
        $group = Smtps_group::find($id);
        $group->smtps()->delete();
        $group->delete();
        return response()->json(['message' => 'Smtp Group deleted!']);
    }


}

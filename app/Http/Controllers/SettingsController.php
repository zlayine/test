<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;

class SettingsController extends Controller
{
    function getSettings()
    {
        $email = env('EMAIL_DEBUG');
        return response()->json(['email' => $email]);
    }

    function updateEmail(Request $request)
    {
        $this->updateDotEnv('EMAIL_DEBUG', $request->email);
        return response()->json(['email' => $request->email, 'message' => 'Debug email saved!']);
    }

    public function updateDotEnv($key, $newValue, $delim=''){

	    $path = base_path('.env');
	    $oldValue = env($key);

	    if ($oldValue === $newValue) {
	        return;
	    }

	    if (file_exists($path)) {
	        file_put_contents(
	            $path, str_replace(
	                $key.'='.$delim.$oldValue.$delim, 
	                $key.'='.$delim.$newValue.$delim, 
	                file_get_contents($path)
	            )
	        );
	    }
    }

    public function checkUser(){
        $user = User::all();
        return [$user];
    }

    public function createUser(Request $request){
        $user = new User();
        $user->name = "admin";
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
		$user->save();
		return '1';
    }
    
    public function test(){
        /*$size = Queue::size();
        echo $size;*/

        $handle = fopen(storage_path('app/public/files/emaillist_2020_02_14_15_32_03.txt'), "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                if (strlen($buffer) > 5){
                    if(strpos($buffer, ';') !== false){
                        $recp = explode(";", $buffer);
                        $recp_name = '';
                        if ($recp[1] && $recp[1] != "\n")
                            $recp_name = $recp[1];
                        if (!$recp_name)
                            $recp_name = $this->getNameFromEmail($recp[0]);
                        echo $recp_name . "-";
                    }
                }
            }
        }
    }

    function getNameFromEmail($email)
    {
        $email = trim($email);
        $name = explode( '@', $email)[0];
        if (strpos($name, '.'))
        {
            $tmp = $name;
            $name = str_replace('.', ' ', $name);
            $name = str_replace('-', ' ', $name);
            $name = preg_replace('/\d+/', '', $name);
            if (strlen($name) < 3)
                $name = $tmp;
            else
                $name[0] = strtoupper($name[0]);
        }
        return ($name);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use App\Models\User;

use App\Models\Credential;

class HomeController extends Controller
{
    public function home()
    {
        if (Auth::check()) {
            return redirect()->route('showCredentials');
        } else {
            return redirect()->route('login');
        }
    }

    public function registerUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required',
        ]);

        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $user->save();
        Auth::login($user);
        return redirect()->route('home');
    }

    public function loginUser(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('home');
        } else {
            return redirect()->route('login')->with('error', 'Login failed. Please check your credentials.'); // Redirect to login with an error message
        }
    }

    public function login()
    {
        return view('auth.login');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

    public function storeView(){
        return view('storeCredential');
    }

    public function storeCredential(Request $request){
        $data = [
            'user_id' => Auth::user()->id,
            'username' => $request->username,
            'title' => $request->title,
            'note' => $request->note,
            'password' => encrypt($request->password),
        ];

        Credential::create($data);
        $searchResults = Credential::where('user_id', Auth::user()->id)
        ->latest('created_at')
        ->get();
    
        $htmlContent = '';
    
        foreach ($searchResults as $item) {
            $password = decrypt($item->password);
            $visiblePart = substr($password, 0, 2);
            $remainingSpace = 100 - strlen($visiblePart);
            $hiddenPart = str_repeat('*', max(0, $remainingSpace));
            $maskedPassword = $visiblePart . $hiddenPart;
    
            $htmlContent .= '
            <div class="bg-gray-100 rounded-lg shadow-sm sm:mt-0 mt-5">
                <div class="px-5 py-3">
                    <div class="flex justify-between align-items-center">
                        <h4 class="font-semibold text-sm capitalize">'. $item->title .'</h4>
                        <div class="flex gap-4">
                            <a class="delete-button" data-credential-id="'. $item->id .'" href="#"><i style="font-size: 10px" class="fas fa-trash-alt"></i></a>
                            <a class="edit-button" data-credential-id="'. $item->id .'" href="#"><i style="font-size: 10px" class="fas fa-edit"></i></a>
                        </div>
                    </div>';
        
        if ($item->username !==null) {
            $htmlContent .= '<hr class="border my-1.5">';
        }else if( decrypt($item->password) !== null ){
            $htmlContent .= '<hr class="border my-1.5">';
        }else if( $item->note !== null ){
            $htmlContent .= '<hr class="border my-1.5">';
        }
        
        
        $htmlContent .= '
                    <div class="mb-1">
                        <h4 class="text-xs">'. $item->username .'</h4>
                    </div>';
        
        if ($password !== null) {
            $htmlContent .= '
                    <div class="flex justify-between">
                        <p class="overflow-hidden text-xs" id="password-'. $item->id .'">'. $maskedPassword .'</p>
                        <div class="flex justify-between gap-x-1 ml-2">
                            <div id="copy-button-'. $item->id .'">
                                <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                        onclick="copyPassword('. $item->id .', \''. $password .'\')">Copy</button>
                            </div>
                            <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                    data-password-visible="false"
                                    onclick="togglePasswordVisibility('. $item->id .', \''. $password .'\')">Show</button>
                        </div>
                    </div>';
        }
        
        $htmlContent .= '
                    <div class="mt-1">
                        <p style="font-size: 10px">'. $item->note .'</p>
                    </div>
                </div>
            </div>
        ';

        }
    
        return $htmlContent;
    }

    public function showCredentials(){
        $data = Credential::where('user_id', Auth::user()->id)
        ->latest('created_at')
        ->get();
        return view('allCredentials', compact('data'));
    }

    public function deleteCredentials($id) {
        Credential::where('id',$id)->delete();
        $searchResults = Credential::where('user_id', Auth::user()->id)
        ->latest('created_at')
        ->get();
    
        $htmlContent = '';
    
        foreach ($searchResults as $item) {
            $password = decrypt($item->password);
            $visiblePart = substr($password, 0, 2);
            $remainingSpace = 100 - strlen($visiblePart);
            $hiddenPart = str_repeat('*', max(0, $remainingSpace));
            $maskedPassword = $visiblePart . $hiddenPart;
    
            $htmlContent .= '
            <div class="bg-gray-100 rounded-lg shadow-sm sm:mt-0 mt-5">
                <div class="px-5 py-3">
                    <div class="flex justify-between align-items-center">
                        <h4 class="font-semibold text-sm capitalize">'. $item->title .'</h4>
                        <div class="flex gap-4">
                            <a class="delete-button" data-credential-id="'. $item->id .'" href="#"><i style="font-size: 10px" class="fas fa-trash-alt"></i></a>
                            <a class="edit-button" data-credential-id="'. $item->id .'" href="#"><i style="font-size: 10px" class="fas fa-edit"></i></a>
                        </div>
                    </div>';
        
        if ($item->username !==null) {
            $htmlContent .= '<hr class="border my-1.5">';
        }else if( decrypt($item->password) !== null ){
            $htmlContent .= '<hr class="border my-1.5">';
        }else if( $item->note !== null ){
            $htmlContent .= '<hr class="border my-1.5">';
        }
        
        
        $htmlContent .= '
                    <div class="mb-1">
                        <h4 class="text-xs">'. $item->username .'</h4>
                    </div>';
        
        if ($password !== null) {
            $htmlContent .= '
                    <div class="flex justify-between">
                        <p class="overflow-hidden text-xs" id="password-'. $item->id .'">'. $maskedPassword .'</p>
                        <div class="flex justify-between gap-x-1 ml-2">
                            <div id="copy-button-'. $item->id .'">
                                <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                        onclick="copyPassword('. $item->id .', \''. $password .'\')">Copy</button>
                            </div>
                            <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                    data-password-visible="false"
                                    onclick="togglePasswordVisibility('. $item->id .', \''. $password .'\')">Show</button>
                        </div>
                    </div>';
        }
        
        $htmlContent .= '
                    <div class="mt-1">
                        <p style="font-size: 10px">'. $item->note .'</p>
                    </div>
                </div>
            </div>
        ';
        

        }
    
        return $htmlContent;
    }

    public function editCredentials($id) {
        $data = Credential::where('id',$id)->first();
        $password = '';
        $username = '';
        $note = '';
        if(decrypt( $data->password ) !== null){
            $password = decrypt( $data->password );
        }
        if($data->username !== null){
            $username = $data->username;
        }
        if($data->note !== null){
            $note = $data->note;
        }
        $allData = [$data, $username, $password, $note];
        return $allData;
    }

    public function updateCredentials(Request $request)
    {
        $data = [
            'user_id' => Auth::user()->id,
            'username' => $request->username,
            'title' => $request->title,
            'note' => $request->note,
            'password' => encrypt($request->password),
        ];

        $credential = Credential::findOrFail($request->input('credential_id'));

        $credential->update($data);

        $searchResults = Credential::where('user_id', Auth::user()->id)
        ->latest('created_at')
        ->get();
    
        $htmlContent = '';
    
        foreach ($searchResults as $item) {
            $password = decrypt($item->password);
            $visiblePart = substr($password, 0, 2);
            $remainingSpace = 100 - strlen($visiblePart);
            $hiddenPart = str_repeat('*', max(0, $remainingSpace));
            $maskedPassword = $visiblePart . $hiddenPart;
    
            $htmlContent .= '
    <div class="bg-gray-100 rounded-lg shadow-sm sm:mt-0 mt-5">
        <div class="px-5 py-3">
            <div class="flex justify-between align-items-center">
                <h4 class="font-semibold text-sm capitalize">'. $item->title .'</h4>
                <div class="flex gap-4">
                    <a class="delete-button" data-credential-id="'. $item->id .'" href="#"><i style="font-size: 10px" class="fas fa-trash-alt"></i></a>
                    <a class="edit-button" data-credential-id="'. $item->id .'" href="#"><i style="font-size: 10px" class="fas fa-edit"></i></a>
                </div>
            </div>';

if ($item->username !==null) {
    $htmlContent .= '<hr class="border my-1.5">';
}else if( decrypt($item->password) !== null ){
    $htmlContent .= '<hr class="border my-1.5">';
}else if( $item->note !== null ){
    $htmlContent .= '<hr class="border my-1.5">';
}


$htmlContent .= '
            <div class="mb-1">
                <h4 class="text-xs">'. $item->username .'</h4>
            </div>';

if ($password !== null) {
    $htmlContent .= '
            <div class="flex justify-between">
                <p class="overflow-hidden text-xs" id="password-'. $item->id .'">'. $maskedPassword .'</p>
                <div class="flex justify-between gap-x-1 ml-2">
                    <div id="copy-button-'. $item->id .'">
                        <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                onclick="copyPassword('. $item->id .', \''. $password .'\')">Copy</button>
                    </div>
                    <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                            data-password-visible="false"
                            onclick="togglePasswordVisibility('. $item->id .', \''. $password .'\')">Show</button>
                </div>
            </div>';
}

$htmlContent .= '
            <div class="mt-1">
                <p style="font-size: 10px">'. $item->note .'</p>
            </div>
        </div>
    </div>
';

        
        }
    
        return $htmlContent;
    }

    public function searchCredentials(Request $request)
    {
        $searchQuery = $request->input('query');
        $searchResults = Credential::where('user_id', Auth::user()->id)
            ->where('title', 'LIKE', '%' . $searchQuery . '%')
            ->get();
    
        $htmlContent = '';
    
        foreach ($searchResults as $item) {
            $password = decrypt($item->password);
            $visiblePart = substr($password, 0, 2);
            $remainingSpace = 100 - strlen($visiblePart);
            $hiddenPart = str_repeat('*', max(0, $remainingSpace));
            $maskedPassword = $visiblePart . $hiddenPart;
    
            foreach ($searchResults as $item) {
                $password = decrypt($item->password);
                $visiblePart = substr($password, 0, 2);
                $remainingSpace = 100 - strlen($visiblePart);
                $hiddenPart = str_repeat('*', max(0, $remainingSpace));
                $maskedPassword = $visiblePart . $hiddenPart;
        
                $htmlContent .= '
                    <div class="bg-gray-100 rounded-lg shadow-sm sm:mt-0 mt-5">
                        <div class="px-5 py-3"">
                        <div class="flex justify-between align-items-center">
                        <h4 class="font-semibold text-sm capitalize">'. $item->title .'</h4>
                                <div class="flex gap-4">
                                    <a class="delete-button" data-credential-id="'. $item->id .'" href="#" ><i  style="font-size: 10px" class="fas fa-trash-alt"></i></a>
                                    <a class="edit-button" data-credential-id="'. $item->id .'" href="#"><i  style="font-size: 10px" class="fas fa-edit"></i></a>
                                </div>
                            </div>
                            <hr class="border my-1.5">
                            <div class="mb-1">
                                <h4 class="text-xs">'. $item->username .'</h4>
                            </div>
                            <div class="flex justify-between">
                                <p class="overflow-hidden text-xs" id="password-'. $item->id .'">'. $maskedPassword .'</p>
                                <div class="flex justify-between gap-x-1 ml-2">
                                    <div id="copy-button-'. $item->id .'">
                                        <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                                onclick="copyPassword('. $item->id .', \''. $password .'\')">Copy</button>
                                    </div>
                                    <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                            data-password-visible="false"
                                            onclick="togglePasswordVisibility('. $item->id .', \''. $password .'\')">Show</button>
                                </div>
                            </div>
                            <div class="mt-1">
                                 <p style="font-size: 10px">'. $item->note .'</p>
                            </div>
                        </div>
                    </div>
                ';
            }
        }
    
        return $htmlContent;
    }
    
    
}

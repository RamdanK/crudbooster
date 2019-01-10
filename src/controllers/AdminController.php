<?php namespace crocodicstudio\crudbooster\controllers;

use CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminController extends CBController
{
    function getIndex()
    {
        $data = [];
        $data['page_title'] = '<strong>Dashboard</strong>';

        return view('crudbooster::home', $data);
    }

    public function getLockscreen()
    {

        if (! CRUDBooster::myId()) {
            Session::flush();

            return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_session_expired'));
        }

        Session::put('admin_lock', 1);

        return view('crudbooster::lockscreen');
    }

    public function postUnlockScreen()
    {
        $id = CRUDBooster::myId();
        $password = Request::input('password');
        $users = DB::table(config('crudbooster.USER_TABLE'))->where('id', $id)->first();

        if (\Hash::check($password, $users->password)) {
            Session::put('admin_lock', 0);

            return redirect(CRUDBooster::adminPath());
        } else {
            echo "<script>alert('".trans('crudbooster.alert_password_wrong')."');history.go(-1);</script>";
        }
    }

    public function getLogin()
    {

        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        return view('crudbooster::login');
    }

    public function postLogin()
    {

        $validator = Validator::make(Request::all(), [
            'email' => 'required|email|exists:'.config('crudbooster.USER_TABLE'),
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
        }

        $email = Request::input("email");
        $password = Request::input("password");
        $users = DB::table(config('crudbooster.USER_TABLE'))->where("email", $email)->first();

        if (\Hash::check($password, $users->password)) {
            $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();

            $company = DB::table("conf_company")->where("id", $users->id_company)->first();

            $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', $users->id_cms_privileges)->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();

            $photo = ($users->photo) ? asset($users->photo) : asset('vendor/crudbooster/avatar.jpg');
            Session::put('admin_id', $users->id);
            Session::put('admin_is_superadmin', $priv->is_superadmin);
            Session::put('admin_name', $users->name);
            Session::put('admin_photo', $photo);
            Session::put('admin_privileges_roles', $roles);
            Session::put("admin_privileges", $users->id_cms_privileges);
            Session::put('admin_privileges_name', $priv->name);
            Session::put('admin_company_id', $company->id);
            Session::put('admin_company', $company->name);
            Session::put('admin_lock', 0);
            Session::put('theme_color', $priv->theme_color);
            Session::put("appname", CRUDBooster::getSetting('appname'));

            CRUDBooster::insertLog(trans("crudbooster.log_login", ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));

            $cb_hook_session = new \App\Http\Controllers\CBHook;
            $cb_hook_session->afterLogin();

            return redirect(CRUDBooster::adminPath());
        } else {
            return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_password_wrong'));
        }
    }

    public function getRegister()
    {
        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        return view('crudbooster::register');
    }

    public function getRegisterGroup() {
        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        $data['countries'] = DB::table('helper_country')->get();

        // echo json_encode($data);die;
        return view('crudbooster::register_company', $data);
    }

    public function postRegisterGroup()
    {
        $data = Request::all();
        $validator = Validator::make(Request::all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:cms_users'],
            'description' => ['required', 'string', 'max:1000'],
            'phone' => ['string', 'max:15','unique:conf_company'],
            'street' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:50'],
            'zip_code' => ['required', 'string', 'max:50'],
            'id_country' => ['required', 'numeric'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        if ($validator->fails()) {
            $message = $validator->errors()->all();
            $data['countries'] = DB::table('helper_country')->get();
            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger'])->withInput();
        }
        $data['company']['name'] = Request::input('name');
        $data['company']['description'] = Request::input('description');
        $data['company']['phone'] = Request::input('phone');
        $data['company']['street'] = Request::input('street');
        $data['company']['city'] = Request::input('city');
        $data['company']['zip_code'] = Request::input('zip_code');
        $data['company']['id_country'] = Request::input('id_country');

        $company_id = DB::table('conf_company')->insertGetId($data['company']);
        
        $data['users']['name'] = Request::input('name');
        $data['users']['email'] = Request::input('email');
        $data['users']['password'] = \Hash::make(Request::input('password'));
        $data['users']['street'] = Request::input('street');
        $data['users']['city'] = Request::input('city');
        $data['users']['zip_code'] = Request::input('zip_code');
        $data['users']['id_country'] = Request::input('id_country');
        $data['users']['id_company'] = $company_id;
        $data['users']['created_at'] = date('Y-m-d H:i:s');
        $data['users']['id_cms_privileges'] = 2;
        // echo json_encode($data);die;
        if ($company_id) {
            $userId = DB::table('cms_users')->insertGetId($data['users']);
            if($userId) {
                return redirect()->route('getLogin')->with(['message' => 'Please login to start your session.']);
            }
        }
    }

    public function getRegisterPersonal() {
        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        $data['countries'] = DB::table('helper_country')->get();

        // echo json_encode($data);die;
        return view('crudbooster::register_personal', $data);
    }

    public function postRegisterPersonal() {
        $data = Request::all();
        $validator = Validator::make(Request::all(), [
            'name' => ['required', 'string', 'max:50'],
            'phone' => ['string', 'max:15', 'unique:cms_users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:cms_users,email'],
            'salutation' => ['required', 'string', 'max:5'],
            'institution' => ['required', 'string', 'max:255'],
            'participation' => ['required', 'string', 'max:20'],
            'street' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:50'],
            'zip_code' => ['required', 'string', 'max:50'],
            'id_country' => ['required', 'numeric'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        if ($validator->fails()) {
            $message = $validator->errors()->all();
            $data['countries'] = DB::table('helper_country')->get();
            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger'])->withInput();
        }

        unset($data['_token']);
        unset($data['password_confirmation']);
        $data['password'] = \Hash::make(Request::input('password'));
        $data['id_cms_privileges'] = 3;
        

        if (\Illuminate\Support\Facades\Schema::hasColumn('cms_users', 'created_at')) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        // echo json_encode($data);die;
        $lastInsertId = DB::table('cms_users')->insertGetId($data);
        if ($lastInsertId) {
            return redirect(CRUDBooster::adminPath());
        }

    }

    public function getForgot()
    {
        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        return view('crudbooster::forgot');
    }

    public function postForgot()
    {
        $validator = Validator::make(Request::all(), [
            'email' => 'required|email|exists:'.config('crudbooster.USER_TABLE'),
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
        }

        // $rand_string = str_random(6);
        // $password = \Hash::make($rand_string);
        $valid_time = (string) CRUDBooster::getSetting('reset_password_token_expiration_time');
        // DB::table(config('crudbooster.USER_TABLE'))->where('email', Request::input('email'))->update(['password' => $password]);
        $appname = CRUDBooster::getSetting('appname');
        $user = CRUDBooster::first(config('crudbooster.USER_TABLE'), ['email' => g('email')]);
        $token_reset_password = $this->createTokenResetPassword($user->email,$valid_time);
        $link_reset_password = 'https://conference.ganeshaconnect.com/admin/reset-password?token='.$token_reset_password;
        DB::table('cms_users')->where('email', $user->email)->update(['token_reset_password' => $token_reset_password]);
        // $user->password = $rand_string;
        CRUDBooster::sendEmail(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend', 'link_reset_password' => $link_reset_password]);

        CRUDBooster::insertLog(trans("crudbooster.log_forgot", ['email' => g('email'), 'ip' => Request::server('REMOTE_ADDR')]));

        return redirect()->route('getLogin')->with('message', trans("crudbooster.message_forgot_password"));
    }

    public function getLogout()
    {

        $me = CRUDBooster::me();
        CRUDBooster::insertLog(trans("crudbooster.log_logout", ['email' => $me->email]));

        Session::flush();

        return redirect()->route('getLogin')->with('message', trans("crudbooster.message_after_logout"));
    }

    
    public function createTokenResetPassword($email = '', $valid_time = '30')
    {
        $dateNow = new \DateTime();
        $today = $dateNow->format('Y-m-d H:i:s');
        $validTime = $dateNow->add(date_interval_create_from_date_string($valid_time.' minutes'));
        $validUntil = $validTime->format('Y-m-d H:i:s');
        $isValid = '1';
        $tokenString = $email . '|' . $isValid . '|' . $today. '|' . $validUntil;
        $token = $this->Base64UrlEncode($tokenString);
        return $token;
    }

    public function getResetPassword() {
        $data['page_title'] = 'Reset Password';
        $data['token_reset_password'] = g('token');
        return view('crudbooster::reset_password', $data);
    }

    public function postResetPassword() {
        $validator = Validator::make(Request::all(), [
            'password' => 'required|string|min:6|confirmed',
            'token_reset_password' => 'required'
        ]);
        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
        }
        $token = g('token_reset_password');
        $decoded_token = $this->Base64UrlDecode($token);
        $data = explode('|',$decoded_token);
        $today = new \DateTime();
        $validDate = new \DateTime($data[3]);
        $is_valid = $data[1] === '1' ? true : false;
        
        if(!$is_valid) {
            return redirect()->back()->with('message','Sorry, the token is not valid anymore.');
        }

        if($today > $validDate) {
            return redirect()->back()->with('message','Sorry, the token is not valid anymore.');
        }
        $email = $data[0];
        $find = DB::table('cms_users')->where('email', $email);
        if($find->count() > 0) {
            $user = $find->first();
            if($user->token_reset_password !== $token) {
                return redirect()->back()->with('message','Sorry, the token is not valid anymore.');
            }
            $new_password = \Hash::make(Request::input('password'));
            $data[1] = '0';
            $update_token = $this->Base64UrlEncode((implode('|',$data)));
            $updated = DB::table('cms_users')->where('email',$email)->where('token_reset_password',$token)->update(array(
                'token_reset_password' => $update_token,
                'password' => $new_password
            ));
            if($updated) {
                return redirect()->route('getLogin')->with('message', 'Your password has been changed. Please login to start your session.');
            }
        } else {
            return redirect()->back()->with('message','Sorry, the email address is not found in our database.');
        }
        return redirect()->route('getLogin');
    }

    public function Base64UrlEncode(string $data, bool $usePadding = false): string
    {
        $encoded = \strtr(\base64_encode($data), '+/', '-_');
        return true === $usePadding ? $encoded : \rtrim($encoded, '=');
    }

    public function Base64UrlDecode(string $data): string
    {
        $decoded = \base64_decode(\strtr($data, '-_', '+/'), true);
        if ($decoded === false) {
            exit('Invalid data provided');
        }
        return $decoded;
    }

}

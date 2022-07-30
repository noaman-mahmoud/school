<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationsResource;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\SignUpDelegateRequest;
use App\Http\Requests\Api\Auth\SignUpProviderRequest;
use App\Http\Requests\Api\Auth\StoreComplaintRequest;
use App\Http\Requests\Api\Auth\UpdatePasswordRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\Auth\SignUpUserRequest;
use App\Http\Requests\Api\Auth\ResendCodeRequest;
use App\Http\Requests\Api\Auth\CheckCodeRequest;
use App\Http\Requests\Api\Auth\ActivateRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\ProviderWorkDay;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Illuminate\Support\Arr;
use App\Models\Complaint;
use App\Models\Provider;
use App\Models\Delegate;
use App\Traits\SmsTrait;
use App\Traits\PaginationTrait;
use App\Models\User;
use Carbon\Carbon;
use App;
use Auth;
use Hash;

class AuthController extends Controller {

    use ResponseTrait, SmsTrait, GeneralTrait ,PaginationTrait;

    public function signUpUser(SignUpUserRequest $request)
    {
        $exists = User::where('user_type','user')->where('phone',$request->phone)->count();
        if($exists) {
            return $this->response('fail', __('auth.invalid-registered'));
        }
        $user = User::create($request->validated()+['country_code'=>966]);
        $user->sendVerificationCode();

        $userData = new UserResource($user->refresh());
        return $this->response('success', __('auth.registered'), ['user' => $userData]);
    }

    public function signUpDelegate(SignUpDelegateRequest $request)
    {
        $exists = User::where('user_type','delegate')->where('phone',$request->phone)->count();
        if($exists) {
            return $this->response('fail', __('auth.invalid-registered'));
        }
        $user = User::create($request->validated()+['is_approved'=>0,'user_type'=>'delegate']);

        Delegate::create($request->validated()+['user_id'=>$user->id]);

        $user->sendVerificationCode();

        $userData = new UserResource($user->refresh());
        return $this->response('success', __('auth.registered'), ['user' => $userData]);
    }

    public function signUpProvider(SignUpProviderRequest $request)
    {

        $exists = User::where('user_type','provider')->where('phone',$request->phone)->count();
        if($exists) {
            return $this->response('fail', __('auth.invalid-registered'));
        }
        $user = User::create($request->validated()+['is_approved'=>0,'user_type'=>'provider']);

        Provider::create($request->validated()+['user_id'=>$user->id]);

        if (isset($request->work_days)){

            $days = json_decode($request->work_days,true);

            foreach ($days as $day){

                ProviderWorkDay::updateOrCreate([
                    'user_id' => $user->id,
                    'day'     => $day['day'],
                ],[
                    'user_id'   => $user->id,
                    'day'       => $day['day'],
                    'time_from' => !empty($day['time_from']) ? $day['time_from'] : null ,
                    'time_to'   => !empty($day['time_to'])   ? $day['time_to']   : null,
                    'status'    => $day['status'],
                ]);
            }
        }
        $user->sendVerificationCode();

        $userData = new UserResource($user->refresh());
        return $this->response('success', __('auth.registered'), ['user' => $userData]);
    }

    public function activate(ActivateRequest $request) {
        $user = User::where('phone', $request['phone'])
            ->where('country_code', 966)
            ->first();

        if (!$this->isCodeCorrect($user, $request->code)) {
            return $this->failMsg(trans('auth.code_invalid'));
        }

        return $this->response('success', __('auth.activated'), ['user' => $user->markAsActive()->login()]);
    }

    public function resendCode(ResendCodeRequest $request) {
        User::where('phone', $request['phone'])
            ->where('country_code', 966)
            ->first()->sendVerificationCode();

        return $this->response('success', __('auth.code_re_send'));
    }

    public function login(LoginRequest $request) {

        $user = User::where('phone', $request['phone'])
            ->where('country_code', 966)
            ->first();

        if (!Hash::check($request->password, $user->password)) {
            return $this->failMsg(__('auth.failed'));
        }

        if ($user->user_type != $request['user_type']){
            return $this->failMsg(__('auth.not_authorized_user_type') . '' .  trans('auth.'.$user->user_type));
        }

        if (!$user->is_approved){
            return $this->failMsg(__('auth.approved'));
        }

        if ($user->is_blocked) {
            return $this->blockedReturn($user);
        }

        if (!$user->active) {
            return $this->phoneActivationReturn($user);
        }

        return $this->response('success', __('apis.signed'), ['user' => $user->login()]);
    }

    public function logout(Request $request) {
        if ($request->bearerToken()) {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                $user->logout();
            }
        }

        return $this->response('success', __('apis.loggedOut'));
    }

    public function getProfile(Request $request) {
        $user         = auth()->user();
        $requestToken = ltrim($request->header('authorization'), 'Bearer ');
        $userData     = UserResource::make($user)->setToken($requestToken);

        return $this->successData(['user' => $userData]);
    }

    public function updateProfile(UpdateProfileRequest $request) {
        $user = auth()->user();
        $user->update($request->validated());

        if ($request->about_laundry && $user->user_type == 'provider'){
            $user->provider->update(['about_laundry'=>$request->about_laundry]);
        }

        $requestToken = ltrim($request->header('authorization'), 'Bearer ');
        $userData     = UserResource::make($user->refresh())->setToken($requestToken);
        return $this->response('success', __('apis.updated'), ['user' => $userData]);
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        $user = auth()->user();
        $user->update($request->validated());
        return $this->successMsg(__('apis.updated'));
    }

    public function forgetCheckCode(CheckCodeRequest $request) {

        $user = User::where('phone', $request['phone'])->first();


        if ($user->user_type != $request['user_type']){
            return $this->failMsg(__('auth.not_authorized_user_type') . '' .  trans('auth.'.$user->user_type));
        }

        if (!$this->isCodeCorrect($user, $request->code)) {
            return $this->failMsg(trans('auth.code_invalid'));
        }

        return $this->successMsg();
    }

    public function resetPassword(ForgetPasswordRequest $request) {
        $user = User::where('phone', $request['phone'])

            ->first();

        if (!$this->isCodeCorrect($user, $request->code)) {
            return $this->failMsg(trans('auth.code_invalid'));
        }

        $user->update(['password' => $request->password, 'code' => null, 'code_expire' => null]);
        return $this->successMsg(trans('auth.password_changed'));
    }

    public function changeLang(Request $request) {
        $user = auth()->user();
        $lang = in_array($request->lang, languages()) ? $request->lang : 'ar';
        $user->update(['lang' => $lang]);
        App::setLocale($lang);
        return $this->successMsg(__('apis.updated'));
    }

    public function switchNotificationStatus() {
        $user = auth()->user();
        $user->update(['is_notify' => !$user->is_notify]);
        return $this->response('success', __('apis.updated'), ['notify' => (bool) $user->refresh()->is_notify]);
    }

    public function getNotifications() {
        auth()->user()->unreadNotifications->markAsRead();
        $notifications =  NotificationsResource::collection(auth()->user()->notifications()->paginate(20));
        $pagination = $this->paginationModel($notifications);

        return $this->successData(['pagination' => $pagination,'notifications' => $notifications]);
    }

    public function countUnreadNotifications() {
        return $this->successData(['count' => auth()->user()->unreadNotifications->count()]);
    }

    public function deleteNotification($notification_id) {
        auth()->user()->notifications()->where('id', $notification_id)->delete();
        return $this->successMsg( __('site.notify_deleted'));
    }

    public function deleteNotifications() {
        auth()->user()->notifications()->delete();
        return $this->successMsg( __('apis.deleted'));
    }

    public function StoreComplaint(StoreComplaintRequest $Request) {
        Complaint::create($Request->validated() + (['user_id' => auth()->id()]));
        return $this->successMsg( __('apis.complaint_send'));
    }

}


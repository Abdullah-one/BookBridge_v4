<?php

namespace App\Http\Controllers\Api\Point;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function getPointInformation(): JsonResponse
    {
        try {
            if (Gate::denies('isPoint')) {
                return response()->json(['status' => 'fail', 'message' => 'غير مصرح لهذا الفعل']);
            }
            $account_id = auth()->user()->id;
            $result=DB::table('accounts')->where('accounts.id', $account_id)
                ->join('exchange_points', 'accounts.id', '=', 'exchange_points.account_id')
                ->select([
                    'phoneNumber',
                    'role',
                    'userName',
                    'email',
                    'maxPackages',
                    'no_packages',
                    'location',
                ])->first();
            return response()->json(['status'=>'success','data'=>$result]);
        }
        catch (\Exception $exception){
            return response()->json(['status' => 'fail', 'message' => 'هناك خطأ بالخادم']);
        }
    }

    public function checkValidityOfToken(): JsonResponse
    {
        try {
            if (Gate::denies('isPoint')) {
                return response()->json(['status' => 'fail', 'message' => 'غير مصرح لهذا الفعل']);
            }
            $account_id = auth()->user()->id;
            $result=DB::table('accounts')->where('accounts.id', $account_id)
                ->select([
                    'phoneNumber',
                ])->first();
            return response()->json(['status'=>'success','data'=>$result]);
        }
        catch (\Exception $exception){
            return response()->json(['status' => 'fail', 'message' => 'هناك خطأ بالخادم']);
        }
    }



    public function Login(Request $request): JsonResponse
    {
        $emailOrPhone = $request->emailOrPhone;
        $user = Account::where(function (Builder $builder) use ($emailOrPhone) {
            $builder->where('email', $emailOrPhone)
                ->orWhere(function (\Illuminate\Database\Eloquent\Builder $query) use ($emailOrPhone) {
                    $query->where('phoneNumber', $emailOrPhone);

                })
                ->whereIn('role',['point']);

        })->first();
        $device_name=$request->device_name;
        if (!$user ||!Hash::check($request->password, $user->password)) {
            return response()->json(['status'=>'fail' , 'message'=>'كلمة المرور أو البريد الإلكتروني غير صحيحة']);
        }
        $token=$user->createToken($device_name)->plainTextToken;
        return response()->json(['status'=>'success', 'data'=>['token'=>$token ,
             'userName'=>$user->userName ]]);

    }
}

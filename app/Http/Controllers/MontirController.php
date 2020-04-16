<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class MontirController extends Controller
{

    public function index()
    {
        $data["count"] = User::count();
        $user = array();

        foreach (User::all() as $p) {
            $item = [
                "id"                => $p->id,
                "nama_montir"       => $p->nama_montir,
                "email"    	        => $p->email,
                "kontak"    	    => $p->kontak,
                "nama_perusahaan"   => $p->nama_perusahaan,
                "alamat_perusahaan" => $p->alamat_perusahaan,
                "sertifikasi"       => $p->sertifikasi,
                "created_at"        => $p->created_at,
                "updated_at"        => $p->updated_at
            ];

            array_push($user, $item);
        }
        $data["user"] = $user;
        $data["status"] = 1;
        return response($data);
    }

    public function getAll($limit = 10, $offset = 0){
        $data["count"] = User::count();
        $user = array();

        foreach (User::take($limit)->skip($offset)->get() as $p) {
            $item = [
                "id"                => $p->id,
                "nama_montir"       => $p->nama_montir,
                "email"    	        => $p->email,
                "kontak"    	    => $p->kontak,
                "nama_perusahaan"   => $p->nama_perusahaan,
                "alamat_perusahaan" => $p->alamat_perusahaan,
                "sertifikasi"    	=> $p->sertifikasi,
                "created_at"        => $p->created_at,
                "updated_at"        => $p->updated_at
            ];
            array_push($user, $item);
        }
        $data["user"] = $user;
        $data["status"] = 1;
        return response($data);
    }
	//fungsi untuk login
	public function login(Request $request){
		$credentials = $request->only('email', 'password');

		try {
			if(!$token = JWTAuth::attempt($credentials)){
				return response()->json([
						'logged' 	=>  false,
						'message' 	=> 'Invalid email and password'
					]);
			}
		} catch(JWTException $e){
			return response()->json([
						'logged' 	=> false,
						'message' 	=> 'Generate Token Failed'
					]);
		}

		return response()->json([
					"logged"    => true,
                    "token"     => $token,
                    "message" 	=> 'Login berhasil'
		]);
	}

    public function delete($id)
    {
        try{
        	$delete = User::where("id", $id);
        	if($delete->first()->role != 'admin'){
        		$delete->delete();

        		if($delete){
	            	return response([
		            	"status"	=> 1,
		                "message"   => "Data berhasil dihapus."
		            ]);
	            } else {
	            	return response([
		            	"status"	=> 0,
		                "message"   => "Data gagal dihapus."
		            ]);
	            }

        	} else {
        		return response([
	            	"status"	=> 0,
	                "message"   => "User admin tidak boleh dihapus."
	            ]);
        	}
            
            
        } catch(\Exception $e){
            return response([
            	"status"	=> 0,
                "message"   => $e->getMessage()
            ]);
        }
    }

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'nama_montir'       => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:user',
            'password'          => 'required|string|min:6',
            'kontak'            => 'required|string|min:11',
            'nama_perusahaan'   => 'required|string|max:255',
            'alamat_perusahaan' => 'required|string|max:255',
            'sertifikasi'       => 'required|text'
		]);

		if($validator->fails()){
			return response()->json([
				'status'	=> 0,
				'message'	=> $validator->errors()
			]);
		}

		$user = new User();
		$user->nama_montir 	        = $request->nama_montir;
		$user->email 	            = $request->email;
        $user->password             = Hash::make($request->password);
        $user->kontak 	            = $request->kontak;
        $user->nama_perusahaan 	    = $request->nama_perusahaan;
        $user->alamat_perusahaan    = $request->alamat_perusahaan;
        $user->sertifikasi 	        = $request->sertifikasi;
		$user->save();

		$token = JWTAuth::fromUser($user);

		return response()->json([
			'status'	=> '1',
			'message'	=> 'Montir berhasil ditambahkan'
			//'user'		=> $user,
		], 201);
	}


	public function LoginCheck(){
		try {
			if(!$user = JWTAuth::parseToken()->authenticate()){
				return response()->json([
						'auth' 		=> false,
						'message'	=> 'Invalid token'
					]);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Token expired'
					], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Invalid token'
					], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Token absent'
					], $e->getStatusCode());
		}

		 return response()->json([
		 		"auth"      => true,
                "user"    => $user
		 ], 201);
	}

	public function logout(Request $request)
    {

        if(JWTAuth::invalidate(JWTAuth::getToken())) {
            return response()->json([
                "logged"    => false,
                "message"   => 'Logout berhasil'
            ], 201);
        } else {
            return response()->json([
                "logged"    => true,
                "message"   => 'Logout gagal'
            ], 201);
        }

    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pengaduan;
use JWTAuth;
use DB;
use Carbon\Carbon;

class PengaduanController extends Controller
{
    public $response;
    public $user;
    public function __construct(){
        $this->response = new ResponseHelper();

        $this->user = JWTAuth::parseToken()->authenticate();
    }
    
    public function getAllPengaduan($limit = NULL, $offset = NULL)
    {
        if($this->user->level == 'masyarakat'){
           $data["count"] = Pengaduan::where('id_user', '=', $this->user->id)->count();
           
           if($limit == NULL && $offset == NULL){
               $data["pengaduan"] = Pengaduan::where('id_user', '=', $this->user->id)->orderBy('tgl_pengaduan', 'desc')->with('kategori', 'tanggapan', 'user')->get();
            } else {
                $data["pengaduan"] = Pengaduan::where('id_user', '=', $this->user->id)->orderBy('tgl_pengaduan', 'desc')->with('kategori', 'tanggapan', 'user')->take($limit)->skip($offset)->get();
            }
        } else {
            $data["count"] = Pengaduan::count();
            
            if($limit == NULL && $offset == NULL){
                $data["pengaduan"] = Pengaduan::orderBy('tgl_pengaduan', 'desc')->with('kategori','tanggapan', 'user')->get();
            } else {
                $data["pengaduan"] = Pengaduan::orderBy('tgl_pengaduan', 'desc')->with('kategori', 'tanggapan', 'user')->take($limit)->skip($offset)->get();
            }
        }

        return $this->response->successData($data);
    }

    public function getId($id)
    {   
        $data["pengaduan"] = Pengaduan::where('id_pengaduan', $id)->with('kategori','tanggapan', 'user')->get();

        return $this->response->successData($data);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'tgl_pengaduan' => 'required|string',
			'isi_laporan' => 'required|string',
			'id_kategori' => 'required',
			'foto' => 'required',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

        $foto = rand().$request->file('foto')->getClientOriginalName();
        $request->file('foto')->move(base_path("./public/uploads"), $foto);

		$pengaduan = new Pengaduan();
		$pengaduan->id_user         = $this->user->id;
		$pengaduan->id_kategori     = $request->id_kategori;
		$pengaduan->tgl_pengaduan   = $request->tgl_pengaduan;
		$pengaduan->isi_laporan     = $request->isi_laporan;
        $pengaduan->foto            = $foto;
        $pengaduan->status          = 'terkirim';
		$pengaduan->save();

        $data = Pengaduan::where('id_pengaduan','=', $pengaduan->id)->first();
        return $this->response->successResponse('Data pengaduan berhasil terkirim');
    }


    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'id_pengaduan' => 'required',
			'status' => 'required|string',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$data          = Pengaduan::where('id_pengaduan', $request->id_pengaduan)->first();
		$data->tgl_pengaduan  = Carbon::now();
		$data->status  = $request->status;
		$data->save();

        return $this->response->successResponseData('Status berhasil diubah', $data);
    }

    public function destroy($id_pengaduan)
    {
        $delete = Pengaduan::where('id_pengaduan', $id_pengaduan)->delete();

        if($delete){
            return $this->response->successResponse('Data petugas berhasil dihapus');
        } else {
            return $this->response->errorResponse('Data petugas gagal dihapus');
        }
    }

}

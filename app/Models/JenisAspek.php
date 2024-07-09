<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use App\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class JenisAspek extends Model
{
    protected $primaryKey = 'uuid';
    protected $table = 'jenis_aspek';
    protected $guarded = [];

    public static function GetAllJenisAspek()
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $response = JenisAspek::select(
                'jenis_aspek.uuid as key',
                'jenis_aspek.name',
            )
            ->leftJoin('courses', 'courses.aspect_key', '=', 'jenis_aspek.uuid')
            ->where('jenis_aspek.deleted_at', null)
            ->get();

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function CreateOrUpdateJenisAspek($param)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');
            $uuid = Uuid::uuid4()->toString();

            DB::beginTransaction();

            if ($param->uuid == null) {
                JenisAspek::create([
                    'uuid'          => $uuid,
                    'name'          => $param->name,
                    'created_by'    => Auth::guard('api')->user()->id,
                    'created_at'    => $datenow,
                ]);
            } else {
                JenisAspek::where('uuid', $param->uuid)->update([
                    'name'          => $param->name,
                    'created_by'    => Auth::guard('api')->user()->id,
                    'created_at'    => $datenow,
                ]);
            }

            DB::commit();

            $data = Helper::responseData();
            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function DeleteJenisAspek($id)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');

            DB::beginTransaction();

            $data = JenisAspek::where('uuid', $id)->update([
                'deleted_by'    => Auth::guard('api')->user()->id,
                'deleted_at'    => $datenow,
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use Illuminate\Support\Facades\Auth;
use App\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class NilaiAkademik extends Model
{
    protected $primaryKey = 'uuid';
    protected $table = 'nilai_akademik';
    protected $guarded = [];

    static function GetNilaiAkademik()
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $response = NilaiAkademik::select(
                'nilai_akademik.uuid AS key',
                'student.uuid as student_id',
                'course.uuid as course_id',
                'nilai_akademik.semester',
                'bobot.uuid as bobot_id',
                'nilai_bobot'
            )
            ->leftjoin('bobot_akademik_new', 'bobot_key', '=', 'nilai_akademik.uuid')
            ->where('nilai_akademik.deleted_at', null)
            ->where('nilai_akademik.status', null)
            ->get();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function CreateOrUpdateNilaiAkademik($param)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');
            $uuid = Uuid::uuid4()->toString();


            if ($param->uuid == null) {
                $check_data = null;
            } else {
                $check_data = NilaiAkademik::where('uuid', $param->uuid)
                    ->whereNull('deleted_at')
                    ->first();
            }

            if ($check_data == null) {
                NilaiAkademik::create([
                    'uuid'              => $uuid,
                    'nilai_akademik'    => $param->nilai_akademik,
                    'semester'          => $param->semester,
                    'created_by'        => Auth::guard('api')->user()->id,
                    'created_at'        => $datenow,
                ]);
            } else {
                NilaiAkademik::where('uuid', $param->uuid)->update([
                    'nilai_akademik'    => $param->nilai_akademik,
                    'semester'          => $param->semester,
                    'created_by'        => Auth::guard('api')->user()->id,
                    'created_at'        => $datenow,
                ]);
            }

            $data = Helper::responseData();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function DeleteNilaiAkademik($id)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');

            $data = NilaiAkademik::where('uuid', $id)->update([
                'deleted_by'    => Auth::guard('api')->user()->id,
                'deleted_at'    => $datenow,
            ]);
            // DB::commit();

            return $data;
        } catch(\Throwable $th) {
            // DB::rollBack();
            throw $th;
        }
    }
}

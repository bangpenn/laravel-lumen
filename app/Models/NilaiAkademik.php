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

/**
 * @OA\Schema(
 *     schema="NilaiAkademik",
 *     type="object",
 *     properties={
 *         @OA\Property(
 *             property="key",
 *             type="string",
 *             description="UUID of the nilai akademik",
 *             example="7ec2e7c4-901d-4481-8a0a-cff3c9794c29"
 *         ),
 *         @OA\Property(
 *             property="student_id",
 *             type="string",
 *             description="UUID of the student",
 *             example="7ec2e7c4-901d-4481-8a0a-cff3c9794c29"
 *         ),
 *         @OA\Property(
 *             property="course_id",
 *             type="string",
 *             description="UUID of the course",
 *             example="7ec2e7c4-901d-4481-8a0a-cff3c9794c29"
 *         ),
 *         @OA\Property(
 *             property="semester",
 *             type="string",
 *             description="Semester of the nilai akademik",
 *             example="1"
 *         ),
 *         @OA\Property(
 *             property="bobot_id",
 *             type="string",
 *             description="UUID of the bobot akademik",
 *             example="313ce0eb-902d-4dd9-8bd9-1bb9effd974e"
 *         ),
 *         @OA\Property(
 *             property="nilai_bobot",
 *             type="number",
 *             format="float",
 *             description="Nilai bobot of the nilai akademik",
 *             example="98"
 *         )
 *     }
 * )
 */

class NilaiAkademik extends Model
{
    protected $primaryKey = 'uuid';
    protected $table = 'nilai_akademik_new';
    protected $guarded = [];

    static function GetNilaiAkademik()
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $response = NilaiAkademik::select(
                'nilai_akademik_new.uuid AS key',
                'student_key AS student_id',
                'course_key AS course_id',
                'nilai_akademik_new.semester',
                'nilai_akademik_new.key_bobot AS bobot_id',
                'nilai_akademik_new.nilai_bobot AS nilai_bobot'
            )
            ->leftjoin('bobot_akademik_new', 'bobot_akademik_new.uuid', '=', 'nilai_akademik_new.key_bobot')
            ->where('nilai_akademik_new.deleted_at', null)
            ->get();

            \Log::info('Request received for GetNilaiAkademik');
            \Log::info('Response data:', $response->toArray());

            return $response;
        } catch (\Throwable $th) {

            \Log::error('Error in GetNilaiAkademik', ['error' => $th->getMessage()]);

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

            // Untuk memasukkan key_bobot dari tabel bobot akademik
            $key_bobot = $param->key_bobot;
            if (!Uuid::isValid($key_bobot)) {
                // Handle error: invalid UUID format
                return Helper::responseFreeCustom(EC::HTTP_BAD_REQUEST, 'Format UUID tidak valid');
            }

            if ($check_data == null) {
                NilaiAkademik::create([
                    'uuid'              => $uuid,
                    'student_key'       => $uuid,
                    'course_key'        => $uuid,
                    'semester'          => $param->semester,
                    'key_bobot'         => $key_bobot,
                    'nilai_bobot'       => $param->nilai_bobot,
                    // 'created_by'        => Auth::guard('api')->user()->id,
                    'created_at'        => $datenow,
                ]);
            } else {
                NilaiAkademik::where('uuid', $param->uuid)->update([
                    'student_key'       => $uuid,
                    'course_key'        => $uuid,
                    'semester'          => $param->semester,
                    'key_bobot'         => $uuid,
                    'nilai_bobot'       => $param->nilai_bobot,
                    // 'created_by'        => Auth::guard('api')->user()->id,
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
                // 'deleted_by'    => Auth::guard('api')->user()->id,
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

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

/**
 *     @OA\Schema(
 *         schema="JenisAspek",
 *         type="object",
 *         properties={
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 format="int64",
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="key",
 *                 type="string",
 *                 description="UUID of the aspek akademik",
 *                 example="78993ca2-3406-424b-8aff-aa7a66f9c625"
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Name of the aspek akademik",
 *                 example="Aspek Akademik"
 *             )
 *         }
 *     )
 */
class JenisAspek extends Model
{
    protected $primaryKey = 'uuid';
    protected $table = 'jenis_aspek';
    // protected $fillable = [
    //     'uuid',
    //     'name',
    //     // tambahkan field lain yang bisa diisi di sini
    // ];
    protected $guarded = [];

    public static function GetAllJenisAspek()
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $response = JenisAspek::select(
                'jenis_aspek.uuid as key',
                'jenis_aspek.name',
            )
            // ->leftJoin('courses', 'courses.aspect_key', '=', 'jenis_aspek.uuid')
            ->where('jenis_aspek.deleted_at', null)
            ->get();

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function CreateOrUpdateJenisAspek($param)
    {

        \Log::info('CreateOrUpdateJenisAspek called', $param->all());

        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');
            $uuid = Uuid::uuid4()->toString();

            // $userId = Auth::guard('api')->user()->id;
            // \Log::info('Authenticated user ID', ['user_id' => $userId]);

            if ($param->uuid == null) {
                \Log::info('Creating new JenisAspek', ['uuid' => $uuid]);

                JenisAspek::create([
                    'uuid'          => $uuid,
                    'name'          => $param->name,
                    // 'created_by'    => $userId,
                    'created_at'    => $datenow,
                ]);
            } else {
                \Log::info('Updating existing JenisAspek', ['uuid' => $param->uuid]);

                JenisAspek::where('uuid', $param->uuid)->update([
                    'name'          => $param->name,
                    // 'created_by'    => Auth::guard('api')->user()->id,
                    'created_at'    => $datenow,
                ]);
            }

            $data = Helper::responseData();
            \Log::info('Response data', ['data' => $data]);

            return $data;
        } catch (\Throwable $th) {
            \Log::error('Error in CreateOrUpdateJenisAspek', ['error' => $th->getMessage()]);
            throw $th;
        }
    }

    public static function DeleteJenisAspek($id)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');

            // DB::beginTransaction();

            $data = JenisAspek::where('uuid', $id)->update([
                // 'deleted_by'    => Auth::guard('api')->user()->id,
                'deleted_at'    => $datenow,
            ]);

            // DB::commit();

            return $data;
        } catch (\Throwable $th) {
            // DB::rollBack();
            throw $th;
        }
    }
}

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
 *         schema="BobotAkademik",
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
 *                 description="UUID of the bobot akademik",
 *                 example="78993ca2-3406-424b-8aff-aa7a66f9c625"
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Name of the bobot akademik",
 *                 example="Bobot Akademik"
 *             ),
 *             @OA\Property(
 *                 property="persen_bobot",
 *                 type="number",
 *                 format="float",
 *                 description="persen bobot of the bobot akademik",
 *                 example="5"
 *             )
 *         }
 *     )
 */
class BobotAkademik extends Model
{
    protected $primaryKey = 'uuid';
    protected $table = 'bobot_akademik_new';
    protected $guarded = [];

    /**
     * Retrieve all Bobot Akademik data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function GetAllBobotAkademik()
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $response = BobotAkademik::select(
                'bobot_akademik_new.uuid as key',
                'bobot_akademik_new.nama as nama',
                'bobot_akademik_new.persen_bobot as persen_bobot',
            )
            ->where('bobot_akademik_new.deleted_at', null)
            ->get();

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Create or update Bobot Akademik.
     *
     * @param  object  $param  Data object with uuid, name, bobot_persen properties.
     * @return array
     */
    public static function CreateOrUpdateBobotAkademik($param)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');
            $uuid = Uuid::uuid4()->toString();

            if ($param->uuid == null) {
                BobotAkademik::create([
                    'uuid'          => $uuid,
                    'nama'          => $param->nama,
                    'persen_bobot'  => $param->persen_bobot,
                    // 'created_by'    => Auth::guard('api')->user()->id,
                    'created_at'    => $datenow,
                ]);
            } else {
                BobotAkademik::where('uuid', $param->uuid)->update([
                    'name'          => $param->name,
                    'persen_bobot'  => $param->persen_bobot,
                    // 'created_by'    => Auth::guard('api')->user()->id,
                    'created_at'    => $datenow,
                ]);
            }

            $data = Helper::responseData();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Delete Bobot Akademik by UUID.
     *
     * @param  string  $id  UUID of the Bobot Akademik to delete.
     * @return int
     */
    public static function DeleteBobotAkademik($id)
    {
        try {
            date_default_timezone_set("Asia/Bangkok");
            $datenow = date('Y-m-d H:i:s');

            $data = BobotAkademik::where('uuid', $id)->update([
                // 'deleted_by'    => Auth::guard('api')->user()->id,
                'deleted_at'    => $datenow,
            ]);

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}


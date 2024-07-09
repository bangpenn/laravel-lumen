<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller;
use App\Models\BobotAkademik;
use App\Helper;

use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use Illuminate\Support\Facades\Log;

class BobotAkademikController extends Controller
{
    public function __construct()
    {
        // Constructor logic can be added if needed
    }

    /**
     * Get all Bobot Akademik data.
     *
     * @OA\Get(
     *   tags={"Bobot Akademik"},
     *   path="/bobot-akademik/index",
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="Not Found"),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function GetBobotAkademik()
    {
        try {
            $jenisAspeks = BobotAkademik::GetAllBobotAkademik();

            return Helper::responseData($jenisAspeks);
        } catch (\Throwable $th) {
            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }
    }

    /**
     * Create or update Bobot Akademik.
     *
     * @OA\Post(
     *   tags={"Bobot Akademik"},
     *   path="/bobot-akademik/createorupdate",
     *   summary="Create or update Bobot Akademik",
     *   description="Create or update Bobot Akademik.",
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="uuid",
     *         title="uuid",
     *         description="UUID of bobot akademik (optional for update)",
     *         type="string",
     *         example=""
     *       ),
     *       @OA\Property(
     *         property="name",
     *         title="name",
     *         description="Name of bobot akademik",
     *         type="string",
     *         example="Aspek A"
     *       ),
     *       @OA\Property(
     *         property="bobot_persen",
     *         title="bobot_persen",
     *         description="Percentage weight of bobot akademik",
     *         type="number",
     *         example=20.5
     *       )
     *     ),
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=400, description="Bad Request"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not Found"),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function CreateOrUpdateBobotAkademik(Request $request)
    {
        try {
            $required_params = [];
            if (!$request->name) $required_params[] = 'name';
            if (!$request->bobot_persen) $required_params[] = 'bobot_persen';

            if (count($required_params) > 0) {
                $message = "Parameters must be provided: " . implode(", ", $required_params);
                return Helper::responseFreeCustom(EC::INSUF_PARAM, $message, array());
            }

            $result = BobotAkademik::CreateOrUpdateBobotAkademik($request);

            return Helper::responseData($result);
        } catch (\Throwable $th) {
            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }
    }

    /**
     * Delete Bobot Akademik by UUID.
     *
     * @OA\Delete(
     *   tags={"Bobot Akademik"},
     *   path="/bobot-akademik/{id}/delete",
     *   summary="Delete bobot akademik",
     *   description="Delete bobot akademik by UUID.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="UUID of bobot akademik to delete",
     *     @OA\Schema(
     *       type="string"
     *     ),
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=400, description="Bad Request"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not Found"),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function DestroyBobotAkademik($id)
    {
        try {
            $result = BobotAkademik::DeleteBobotAkademik($id);

            return Helper::responseData($result);
        } catch (\Throwable $th) {
            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }
    }
}

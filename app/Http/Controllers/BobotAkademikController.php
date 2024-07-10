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
     *   summary="Get list of Bobot Akademik",
     *   @OA\Response(
     *      response=200, 
     *      description="Successful operation",
     *      @OA\JsonContent(
     *          type="array",
     *             @OA\Items(ref="#/components/schemas/BobotAkademik")
     *      )
     *   ),
     *   @OA\Response(response=404, description="Not Found",
     *      @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bobot Akademik Tidak Ditemukan!"),
     *         )),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function GetBobotAkademik()
    {
        try {
            $bobotAkademik = BobotAkademik::GetAllBobotAkademik();

            return Helper::responseData($bobotAkademik);
        } catch (\Throwable $th) {
            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
            throw $th;

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
     *         property="nama",
     *         title="nama",
     *         description="nama of bobot akademik",
     *         type="string",
     *         example="Bobot Akademik"
     *       ),
     *       @OA\Property(
     *         property="persen_bobot",
     *         title="persen_bobot",
     *         description="Percentage weight of bobot akademik",
     *         type="number",
     *         example=20.5
     *       )
     *     ),
     *   ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="OK")
     *             ),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/BobotAkademik")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bad Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bobot Akademik Tidak Ditemukan!")
     *         )
     *     ),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function CreateOrUpdateBobotAkademik(Request $request)
    {
        try {

            \Log::info('Request received', $request->all());

            $required_params = [];
            if (!$request->nama) $required_params[] = 'nama';
            if (!$request->persen_bobot) $required_params[] = 'persen_bobot';

            if (count($required_params) > 0) {
                $message = "Parameters must be provided: " . implode(", ", $required_params);
                return Helper::responseFreeCustom(EC::INSUF_PARAM, $message, array());
            }

            \Log::info('Validation passed');


            $result = BobotAkademik::CreateOrUpdateBobotAkademik($request);

            \Log::info('Bobot Akademik saved', ['result' => $result]);


            return Helper::responseData($result);
        } catch (\Throwable $th) {
            \Log::error('Error occurred', ['error' => $th->getMessage()]);

            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
            throw $th;
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
    *   @OA\Response(
    *       response=200,
    *       description="OK",
    *       @OA\JsonContent(
    *           @OA\Property(
    *               property="meta",
    *               type="object",
    *               @OA\Property(
    *                   property="code",
    *                   type="integer",
    *                   example=200
    *               ),
    *               @OA\Property(
    *                   property="message",
    *                   type="string",
    *                   example="OK"
    *               )
    *           ),
    *           @OA\Property(
    *               property="data",
    *               type="integer",
    *               example=1
    *           )
    *       )
    *   ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bad Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bobot Akademik Tidak Ditemukan!")
     *         )
     *     ),
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
            throw $th;
        }
    }
}

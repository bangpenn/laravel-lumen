<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller;
use App\Models\JenisAspek;
use App\Helper;
use App\Models\UserLogDetail;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use Illuminate\Support\Facades\Log;

class JenisAspekController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @OA\Get(
     *   tags={"Jenis Aspek"},
     *   path="/jenis-aspek/index",
     *   summary="Get list of jenis aspek",
     *   @OA\Response(
     *      response=200, 
     *      description="Successful operation",
     *      @OA\JsonContent(
     *          type="array",
     *             @OA\Items(ref="#/components/schemas/JenisAspek")
     *      )
     *   ),
     *   @OA\Response(response=404, description="Not Found",
     *      @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Jenis Aspek Tidak Ditemukan!"),
     *         )),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function GetJenisAspek()
    {
        try {
            $jenisAspeks = JenisAspek::GetAllJenisAspek();

            \Log::info('Request received for GetJenisAspek');
            \Log::info('Response data:', $jenisAspeks->toArray());

            return Helper::responseData($jenisAspeks);
        } catch (\Throwable $th) {

            \Log::error('Error in GetJenisAspek', ['error' => $th->getMessage()]);

            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
            throw $th;

        }
    }

    /**
     * @OA\Post(
     *   tags={"Jenis Aspek"},
     *   path="/jenis-aspek/createorupdate",
     *   summary="Create or update jenis aspek",
     *   description="Create or update jenis aspek.",
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="uuid",
     *         title="uuid",
     *         description="UUID of jenis aspek (optional for update)",
     *         type="string",
     *         example=""
     *       ),
     *       @OA\Property(
     *         property="name",
     *         title="name",
     *         description="Name of jenis aspek",
     *         type="string",
     *         example="Aspek A"
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
     *                 @OA\Items(ref="#/components/schemas/JenisAspek")
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
     *             @OA\Property(property="message", type="string", example="Jenis Aspek Tidak Ditemukan!")
     *         )
     *     ),
     *   security={{ "apiAuth": {} }}
     * )
     */
    public function CreateOrUpdateJenisAspek(Request $request)
    {
        try {
            \Log::info('Request received', $request->all());

            $required_params = [];
            // if (!$request->uuid) $required_params[] = 'uuid';
            if (!$request->name) $required_params[] = 'name';

            if (is_countable($required_params) && count($required_params)) {
                $message = "Parameter berikut harus diisi: " . implode(", ", $required_params);
                return Helper::responseFreeCustom(EC::INSUF_PARAM, $message, array());
            }

            \Log::info('Validation passed');


            $result = JenisAspek::CreateOrUpdateJenisAspek($request);

            \Log::info('Jenis Aspek saved', ['result' => $result]);


            return Helper::responseData($result);
        } catch (\Throwable $th) {
            \Log::error('Error occurred', ['error' => $th->getMessage()]);

            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');

            throw $th;

        }
    }

    /**
     * @OA\Delete(
     *   tags={"Jenis Aspek"},
     *   path="/jenis-aspek/{id}/delete",
     *   summary="Delete jenis aspek",
     *   description="Delete jenis aspek by UUID.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="UUID of jenis aspek to delete",
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
    *   @OA\Response(response=400, description="Bad Request", @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Invalid ID supplied")
    *   )),
    *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Not authorized to delete this resource")
    *   )),
    *   @OA\Response(response=404, description="Not Found", @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean", example=false),
    *       @OA\Property(property="message", type="string", example="Jenis Aspek Tidak Ditemukan!")
    *   )),
    *   security={{ "apiAuth": {} }}
     * )
     */
    public function DestroyJenisAspek($id)
    {
        try {
            $result = JenisAspek::DeleteJenisAspek($id);

            return Helper::responseData($result);
        } catch (\Throwable $th) {

            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
            throw $th;
        }
    }
}

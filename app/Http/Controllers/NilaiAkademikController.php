<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller;
use App\Models\NilaiAkademik;
use App\Helper;

use App\Models\User;
use App\Models\UserLogDetail;

use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;

class NilaiAkademikController extends Controller
{
    public function __construct()
    {

    }

     /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */

     /**
     * Get all Nilai Akademik data.
     * 
     * @OA\Get(
     *   tags={"Nilai Akademik"},
     *   path="/nilai-akademik/index",
     *   summary="Get list of Nilai Akademik",

     *   @OA\Response(
     *      response=200, 
     *      description="Successful operation",
     *      @OA\JsonContent(
     *          type="array",
     *             @OA\Items(ref="#/components/schemas/NilaiAkademik")
     *      )
     *   ),
     *   @OA\Response(response=404, description="Not Found",
     *      @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Nilai Akademik Tidak Ditemukan!"),
     *         )),
     *   security={{ "apiAuth": {} }}
     * )
     */


    public function GetNilaiAkademik()
     {
         try {
             $nilaiAkademik = NilaiAkademik::GetNilaiAkademik();

             return Helper::responseData($nilaiAkademik);
         } catch (\Throwable $th) {
             return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
             throw $th;
         }
     }


    /**
     * Create or update Nilai Akademik.
     * 
     * @OA\Post(
     *   tags={"Nilai Akademik"},
     *   path="/nilai-akademik/createorupdate",
     *   summary="Create or update nilai Akademik",
     *   description="Create or update nilai Akademik.",
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="uuid",
     *         title="uuid",
     *         description="UUID of nilai akademik (optional for update)",
     *         type="string",
     *         example=""
     *       ),
     *       @OA\Property(
     *         property="semester",
     *         title="Semester",
     *         description="Semester of nilai akademik",
     *         type="string",
     *         example="1"
     *       ),
     *       @OA\Property(
     *         property="nilai_bobot",
     *         title="Nilai Bobot",
     *         description="Nilai Bobot of nilai akademik",
     *         type="number",
     *         format="float",
     *         example="90"
     *       ),
     *       @OA\Property(
     *         property="key_bobot",
     *         title="Key Bobot",
     *         description="UUID dari tabel bobot_akademik yang terhubung dengan tabel nilai_akademik",
     *         type="string",
     *         example="313ce0eb-902d-4dd9-8bd9-1bb9effd974e"
     *       ),
     *     ),
     *   ),
     *  @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="message", type="string", example="OK")
     *             ),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/NilaiAkademik")
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
     *             @OA\Property(property="message", type="string", example="Nilai Akademik Tidak Ditemukan!")
     *         )
     *     ),
     *   security={{ "apiAuth": {} }}
     * )
     */

    public function CreateOrUpdateNilaiAkademik(Request $request)
    {
        try {
           
            \Log::info('Request received', $request->all());
            $required_params = [];
            if (!$request->semester) $required_params[] = 'semester';
            if (!$request->nilai_bobot) $required_params[] = 'nilai_bobot';
            if (!$request->key_bobot) $required_params[] = 'key_bobot';

            if (is_countable($required_params) && count($required_params)) {
                $message = "Parameter berikut harus diisi: " . implode(", ", $required_params);
                return Helper::responseFreeCustom(EC::INSUF_PARAM, $message, array());
            }

            \Log::info('Validation passed');

            $result = NilaiAkademik::CreateOrUpdateNilaiAkademik($request);

            \Log::info('Nilai Akademik saved', ['result' => $result]);

            return Helper::responseData($result);
        } catch (\Throwable $th) {

            \Log::error('Error occurred', ['error' => $th->getMessage()]);
            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
            throw $th;
        }
    }

    /**
     * @OA\Delete(
     *   tags={"Nilai Akademik"},
     *   path="/nilai-akademik/{id}/delete",
     *   summary="Delete nilai akademik",
     *   description="Delete nilai akademik by UUID.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="UUID of nilai akademik to delete",
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
     *             @OA\Property(property="message", type="string", example="Nilai Akademik Tidak Ditemukan!")
     *         )
     *     ),
     *   security={{ "apiAuth": {} }}
     * )
     */

    public function DestroyNilaiAkademik($id)
    {
        try {
            $result = NilaiAkademik::DeleteNilaiAkademik($id);

            return Helper::responseData($result);
        } catch (\Throwable $th) {
            return Helper::responseFreeCustom(EC::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
            throw $th;
        }
    }


}

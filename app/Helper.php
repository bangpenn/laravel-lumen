<?php

namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use App\Exceptions\CustomException;
use App\Models\BobotEdopsModel;
use App\Models\Courses;
use App\Models\SociometriGradeModel;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\LecturersModel;
use App\Models\Student;
use App\Models\KrsModel;
use App\Models\Student_AcademicDetail;
use App\Models\PenilaianEdopsModel;
use App\Models\KrsNewModel;
use App\Models\PromotionModel;
use \DB;
use Ramsey\Uuid\Uuid;

use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Crypt;

use function GuzzleHttp\json_encode;
// use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class Helper
{
    const IS_NOL = 0;

    static function responseData($data = false, $paginate = null)
    {
        if ($paginate == null) {
            $response = [
                "meta" => ['code' => EC::HTTP_OK, 'message' => EM::HTTP_OK],
                "data" => $data
            ];

            if ($data === false) unset($response['data']);
        } else {
            $response = [
                "meta" => ['code' => EC::HTTP_OK, 'message' => EM::HTTP_OK, 'page' => $paginate],
                "data" => $data
            ];
        }

        return response()->json($response, 200);
    }

    static function responseFreeCustom($EC, $EM, $data = false)
    {
        $response = [
            "meta" => ['code' => $EC, 'message' => $EM],
            "data" => $data
        ];

        if ($data === false) unset($response['data']);

        return response()->json($response, 200);
    }

    static function responseDataReport($data = false, $paginate = null, $header = null, $footer = null)
    {
        if ($paginate == null) {
            $response = [
                "meta" => ['error' => EC::NOTHING, 'message' => EM::NONE],
                "data" => $data
            ];

            if ($data === false) unset($response['data']);
        } else {
            $response = [
                "meta" => ['error' => EC::NOTHING, 'message' => EM::NONE, 'page' => $paginate],
                "data" => $data, "header" =>  $header, "footer" =>  $footer
            ];
        }

        return response()->json($response, 200);
    }

    static function createResponse($EC, $EM, $data = false)
    {
        if (!$data && [] !== $data) $data = json_decode("{}");

        $data = [
            "meta" => ['code' => $EC, 'message' => $EM],
            "data" => $data
        ];

        if ($EC > 0 || is_string($EC)) unset($data['data']);
        return response()->json($data, 200);
    }

    static function responseDownload($pathToFile, $filename)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Accept, Authorization, X-Requested-With, Application, Origin, Authorization, APIKey, Timestamp, AccessToken',
            'Content-Disposition' => 'attachment',
            'Pragma' => 'static',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' =>   self::getContentType($pathToFile),
            'Content-Length' => filesize($pathToFile)
        ];

        return response()->download($pathToFile, $filename, $headers);
    }

    static function getContentType($fileName)
    {
        $path_parts = pathinfo($fileName);
        $ext = strtolower($path_parts["extension"]);
        $mime = [
            'doc' => 'application/msword',
            'dot' => 'application/msword',
            'sfdt' => 'application/json',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
            'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
            'xls' => 'application/vnd.ms-excel',
            'xlt' => 'application/vnd.ms-excel',
            'xla' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
            'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
            'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pot' => 'application/vnd.ms-powerpoint',
            'pps' => 'application/vnd.ms-powerpoint',
            'ppa' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'potm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpg'
        ];

        return isset($mime[$ext]) ? $mime[$ext] : 'application/octet-stream';
    }

    static function ChangeDateFormat($tgl)
    {
        $newDate = Carbon::createFromFormat('d-m-Y', $tgl);
        $newDateFormat = $newDate->format('Y-m-d');

        return $newDateFormat;
    }

    static  function cekPermission($field, $nama_permissions)
    {
        $user_id = Auth::user('api')->group_id;
        $cekmenu = Group::where('group.id', $user_id)
            ->join('group_permission', 'group_permission.group_id', 'group.id')
            ->join('permissions', 'permissions.id', 'group_permission.permission_id')
            ->join('menu', 'menu.id', 'permissions.menu_id')
            ->where('menu.url', 'ILIKE', '%' . $nama_permissions . '%')
            ->first();
        // dd($cekmenu);
        if ($cekmenu) {
            if ($cekmenu->$field == true) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    static  function GetUuidSiswa()
    {
        $user_id = Auth::user('api')->id;

        $data = Student::where('id_user', $user_id)
            ->first();
        if (!empty($data)) {
            $key = $data->uuid;
        } else {
            $key = null;
        }

        return $key;
    }

    static  function GetUuidDosen()
    {
        $user_id = Auth::user('api')->id;

        $data = LecturersModel::where('id_user', $user_id)
            ->first();
        if (!empty($data)) {
            $key = $data->uuid;
        } else {
            $key = null;
        }

        return $key;
    }

    static  function GetdetailadminProdi()
    {
        $user_id = Auth::user('api')->group_id;
        // dd($user_id);

        $data = DB::table('group')->select('id_prodi')->where('id', $user_id)
            ->first();



        return $data->id_prodi;
    }

    static  function CheckKRS($student, $generation)
    {
        $user_id = Auth::user('api')->id;

        $data = KrsModel::where('student', $student)->where('generation_id', $generation)->get();
        if ($data->isEmpty()) {
            $response = false;
        } else {
            $response = true;
        }


        return $response;
    }

    static function JawabanEdops($id_soal, $id_bobot,  $dosen, $id_user, $id_dimensi, $peruntukan)
    {
        $data = array(
            'id_pertanyaan'            => $id_soal,
            'id_bobot'           => $id_bobot,
            'id_dosen'          => $dosen,
            'id_user'      => $id_user,
            'id_dimensi'        => $id_dimensi,
            'peruntukan'        => $peruntukan
        );

        DB::table('penilaian_siswa_edops')->insert($data);
    }

    static function GetLecturerNameById($id_dosen)
    {
        $data = LecturersModel::where('uuid', $id_dosen)->first();
        if (!empty($data)) {
            $hasil = $data->lecturer_name;
        } else {
            $hasil = '';
        }
        return $hasil;
    }

    static function GetCourseNameById($id_matkul)
    {
        $data = Courses::where('uuid', $id_matkul)->first();
        if (!empty($data)) {
            $hasil = $data->course_name;
        } else {
            $hasil = '';
        }
        return $hasil;
    }
    static function MeetingTotals($id_matkul, $id_dosen)
    {
        date_default_timezone_set("Asia/Bangkok");
        $datenow = date('Y');
        $data = DB::table('master_jadwal')
            ->select(DB::raw("count(id_matakuliah) as jumlah"))
            ->where('id_dosen_pengajar', $id_dosen)
            ->where('id_matakuliah', $id_matkul)
            ->whereYear('created_at', $datenow)
            ->groupBy('id_matakuliah', 'id_dosen_pengajar')->first();
        // dd($data);
        if (!empty($data)) {
            $hasil = $data->jumlah;
        } else {
            $hasil = '';
        }
        return $hasil;
    }

    static function NilaiTotalEdops($id_matkul, $id_dosen)
    {
        $nilai = DB::table('penilaian_edops_result')->where('id_dosen', $id_dosen)->where('id_matkul', $id_matkul)->first();
        if (!empty($nilai)) {
            if ($nilai->result >= 80.01 && $nilai->result <= 100.00) {
                $predikat = 'A';
            } elseif ($nilai->result >= 60.01 && $nilai->result <= 80.00) {
                $predikat = 'B';
            } elseif ($nilai->result >= 40.01 && $nilai->result <= 60.00) {
                $predikat = 'C';
            } elseif ($nilai->result >= 20.01 && $nilai->result <= 40.00) {
                $predikat = 'D';
            } elseif ($nilai->result >= 0.01 && $nilai->result <= 20.00) {
                $predikat = 'E';
            } else {
                $predikat = 'E';
            }
        } else {
            $predikat = '';
        }
        if (!empty($nilai->result)) {
            $nilai_dosen = $nilai->result;
        } else {
            $nilai_dosen = '';
        }


        $data['predikat'] = $predikat;
        $data['nilai_total'] = $nilai_dosen;
        return $data;
    }

    static function GetNameAspect($id_aspek)
    {
        $data = DB::table('max_weight')->select('aspect')->where('uuid', $id_aspek)->first();
        return $data;
    }

    static function HitungHasilJawaban($id_jadwal, $id_student)
    {
        $dimensi = DB::table('master_dimensi')->where('peruntukan', 1)->orderby('id', 'ASC')->get();
        $temp_total = [];

        foreach ($dimensi as $key => $value) {
            $hasil_kat = Helper::HasilJawabanPerKategori($value->uuid, $id_jadwal, $id_student);
            $hasil_kat_total = 0;
            foreach ($hasil_kat as $val) {
                $bobot = new BobotEdopsModel();
                $hasil_kat_total += ($val->bobot / $bobot->getMaxBobot()) * 100;
            }
            $count_soal_perdimensi = Helper::JumlahSoalPerDimensi($value->uuid);
            $bobot_perkategori = Helper::BobotPerdimensi($value->uuid);

            if ($count_soal_perdimensi > 0) {
                // dd($bobot_perkategori);
                $temp_total[$key] = (($hasil_kat_total / $count_soal_perdimensi * ($bobot_perkategori->nilai / 100)));
                // dd($temp_total[$key]);
            } else {
                $temp_total[$key] = 0;
            }
        }
        return round(array_sum($temp_total), 2);
    }

    static function BobotPerdimensi($id_dimensi)
    {
        $data = DB::table('master_dimensi')->select('nilai')->where('master_dimensi.uuid', $id_dimensi)->first();
        return $data;
    }

    static function JumlahSoalPerDimensi($id_dimensi)
    {
        $data = DB::table('master_pertanyaan_diklat')
            ->join('master_dimensi', 'master_pertanyaan_diklat.id_dimensi', '=', 'master_dimensi.uuid')
            ->where('master_dimensi.uuid', $id_dimensi)
            ->count();
        return $data;
    }

    static function HasilJawabanPerKategori($id_dimensi, $id_jadwal, $id_student)
    {
        $data = DB::table('penilaian_siswa_edops')
            ->select('penilaian_siswa_edops.id_pertanyaan', 'bobot_nilai_edops.uuid as key_bobot', 'bobot_nilai_edops.bobot')
            ->join('bobot_nilai_edops', 'penilaian_siswa_edops.id_bobot', '=', 'bobot_nilai_edops.uuid')
            ->join('master_pertanyaan_diklat', 'penilaian_siswa_edops.id_pertanyaan', '=', 'master_pertanyaan_diklat.uuid')
            ->where('master_pertanyaan_diklat.id_dimensi', $id_dimensi)
            ->where('penilaian_siswa_edops.id_jadwal', $id_jadwal)
            ->where('penilaian_siswa_edops.id_siswa', $id_student)
            ->get();
        // dd($data);
        return $data;
    }

    static function HitungResult($id_matkul, $id_dosen)
    {
        date_default_timezone_set("Asia/Jakarta");
        $uuid = Uuid::uuid4()->toString();
        $temp = [];
        $temp_item = [];
        $temp_result = [];

        $cek_jadwal = DB::table('penilaian_siswa_edops')
            ->select('penilaian_siswa_edops.id_jadwal', 'student.uuid as id_student')
            ->leftjoin('master_jadwal', 'master_jadwal.uuid', '=', 'penilaian_siswa_edops.id_jadwal')
            ->leftjoin('student', 'student.uuid', '=', 'penilaian_siswa_edops.id_siswa')
            ->where('master_jadwal.id_matakuliah', $id_matkul)
            ->where('master_jadwal.id_dosen_pengajar', $id_dosen)
            ->groupBy('penilaian_siswa_edops.id_jadwal', 'student.uuid')
            ->orderBy('penilaian_siswa_edops.id_jadwal', 'ASC')
            ->get();

        foreach ($cek_jadwal as $key => $val) {
            $temp[$key]['nilai'] = Helper::HitungHasilJawaban($val->id_jadwal, $val->id_student);
            $temp[$key]['id_user'] = $val->id_student;
        }
        foreach ($temp as $key => $val) {
            if (!array_key_exists($val['id_user'], $temp_item)) {
                $temp_item[$val['id_user']] = 0;
            }
            $temp_item[$val['id_user']] += $val['nilai'];
        }

        foreach ($temp_item as $key => $val) {
            $temp_result[] = $val / array_count_values(array_column($temp, 'id_user'))[$key];
        }
        //final result
        if (!empty($cek_jadwal)) {
            $final_result = round(array_sum($temp_result) / count($temp_result), 2);

            $cek_result = DB::table('penilaian_edops_result')
                ->where('id_dosen', $id_dosen)
                ->where('id_matkul', $id_matkul)
                ->count();

            if ($cek_result > 0) {
                DB::table('penilaian_edops_result')->where('id_dosen', $id_dosen)->where('id_matkul', $id_matkul)->update([
                    'result' => $final_result,
                    'updated_at'  =>  date('Y-m-d H:i:s')
                ]);
            } else {

                DB::table('penilaian_edops_result')->insert([
                    'uuid' => $uuid,
                    'id_dosen'  =>  $id_dosen,
                    'id_matkul'  =>  $id_matkul,
                    'result'  =>  $final_result,
                    'created_at'  =>  date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    static function nilaiTotalLectureStaff()
    {

        $nama_lecture = DB::table('lecturer as l')
            ->join('dosen_matakuliah as dm', 'l.uuid', '=', 'dm.uuid_lecturer')
            ->join('courses as c', 'c.uuid', '=', 'dm.uuid_course')
            ->select('l.uuid', 'l.lecturer_name', 'l.NIDN', 'c.course_name')
            ->get()->toArray();

        $data = [];
        foreach ($nama_lecture as $key => $lecture) {

            $query = DB::select("
                SELECT SUM(avg)
                FROM (
                select
                    (bne.bobot::float * (bne.bobot::float / count(pse.id_pertanyaan)::float)) as avg
                from
                    penilaian_siswa_edops pse
                join bobot_nilai_edops bne on
                    pse.id_bobot = bne.uuid
                join lecturer l on
                    pse.id_dosen = l.uuid
                join courses c on
                    c.lecturer = l.uuid
                where
                    pse.peruntukan = 3
                    and
                    l.uuid = '$lecture->uuid'
                group by
                    bne.bobot,
                    l.id,
                    c.course_name
                ) AS subquery ;");

            $data[$key]['uuid'] = $lecture->uuid;
            $data[$key]['lecturer_name'] = $lecture->lecturer_name;
            $data[$key]['NIDN'] = $lecture->NIDN;
            $data[$key]['course_name'] = $lecture->course_name;
            $data[$key]['total_nilai'] = $query[0]->sum;
        }

        return $data;
    }

    static function nilaiTotalLectureInstitution()
    {

        $nama_lecture = DB::table('lecturer as l')
            ->join('dosen_matakuliah as dm', 'l.uuid', '=', 'dm.uuid_lecturer')
            ->join('courses as c', 'c.uuid', '=', 'dm.uuid_course')
            ->select('l.uuid', 'l.lecturer_name', 'l.NIDN', 'c.course_name')
            ->get()->toArray();

        $data = [];
        foreach ($nama_lecture as $key => $lecture) {

            $query = DB::select("
                SELECT SUM(avg)
                FROM (
                select
                    (bne.bobot::float * (bne.bobot::float / count(pse.id_pertanyaan)::float)) as avg
                from
                    penilaian_siswa_edops pse
                join bobot_nilai_edops bne on
                    pse.id_bobot = bne.uuid
                join lecturer l on
                    pse.id_dosen = l.uuid
                join courses c on
                    c.lecturer = l.uuid
                where
                    pse.peruntukan = 2
                    and
                    l.uuid = '$lecture->uuid'
                group by
                    bne.bobot,
                    l.id,
                    c.course_name
                ) AS subquery ;");

            $data[$key]['uuid'] = $lecture->uuid;
            $data[$key]['lecturer_name'] = $lecture->lecturer_name;
            $data[$key]['NIDN'] = $lecture->NIDN;
            $data[$key]['course_name'] = $lecture->course_name;
            $data[$key]['total_nilai'] = $query[0]->sum;
        }

        return $data;
    }

    static function nilaiTotalLectureSiswa()
    {
        $nama_lecture = DB::table('lecturer as l')
            ->join('dosen_matakuliah as dm', 'l.uuid', '=', 'dm.uuid_lecturer')
            ->join('courses as c', 'c.uuid', '=', 'dm.uuid_course')
            ->select('l.uuid', 'l.lecturer_name', 'l.NIDN', 'c.course_name')
            ->get()->toArray();
        // dd($nama_lecture);
        $data = [];
        foreach ($nama_lecture as $key => $lecture) {

            $query = DB::select("
            SELECT SUM(avg)
            from
            (
            select
                (bne.bobot::float * (bne.bobot::float / count(pse.id_pertanyaan)::float)) as avg
            from
                penilaian_siswa_edops pse
            join bobot_nilai_edops bne on
                pse.id_bobot = bne.uuid
            join lecturer l on
                pse.id_dosen = l.uuid
            join master_jadwal mj on
                mj.id_dosen_pengajar = l.uuid
            join courses c on
                c.uuid = mj.id_matakuliah
            where
                pse.peruntukan = 1
                and
                l.uuid = '$lecture->uuid'
            group by
                bne.bobot,
                l.id,
                c.course_name
                            ) as subquery;");


            $data[$key]['uuid'] = $lecture->uuid;
            $data[$key]['lecturer_name'] = $lecture->lecturer_name;
            $data[$key]['NIDN'] = $lecture->NIDN;
            $data[$key]['course_name'] = $lecture->course_name;
            $data[$key]['total_nilai'] = $query[0]->sum;
        }

        return $data;
    }

    static function CheckDetailJadwal($id_jadwal)
    {
        $data = DB::table('master_jadwal')->where('uuid', $id_jadwal)->first();
        return $data;
    }


    static function GetNilaiKhsSiswa($course, $student, $prodi, $angkatan)
    {
        $data = DB::table('academic_score')
            ->select(
                'academic_score.uuid as key',
                'academic_score.score',
                \DB::raw('academic_score.score * bobot_penilaian_akademik.nilai_bobot as nilai_total')
            )
            ->join('bobot_penilaian_akademik', 'academic_score.component', '=', 'bobot_penilaian_akademik.uuid')
            ->where('course', $course)
            ->where('student', $student)
            ->where('prodi', $prodi)
            ->where('angkatan', $angkatan)
            ->get();
        $total_nilai = 0;
        if ($data->isNotEmpty()) {
            foreach ($data as  $nilai_akhir) {
                $total_nilai += $nilai_akhir->nilai_total;
                $vals['nilai_asli'] = $total_nilai / 100;


                // dd($total_nilai);
                $predikat = DB::table('grade_level')
                    ->where('min_grade', '<=', $vals['nilai_asli'])
                    ->where('max_grade', '>=', $vals['nilai_asli'])
                    ->where('deleted_at', null)
                    ->where('status', true)
                    ->first();
                if (!empty($predikat)) {
                    $vals['predikat'] = $predikat->quality;
                    $vals['nilai_akhir'] = $predikat->weight;
                } else {
                    $vals['predikat'] = '-';
                    $vals['nilai_akhir'] = '-';
                }
            }
            $bagianDesimal = number_format($vals['nilai_asli'], 2, '.', '');
            $bagianDesimal = explode('.', $bagianDesimal)[1];
            $bagianBulat = intval($vals['nilai_asli']);

            // dd($bagianDesimal, $bagianBulat);
            if ($bagianDesimal > 50) {
                $vals['total_nilai'] = $bagianBulat + 1;
            } else {
                $vals['total_nilai'] = $bagianBulat;
            }
            // dd($vals);
            return $vals;
        } else {
            $data = [
                'nilai_asli' => 0.00,
                'predikat' => '-',
                'nilai_akhir' => 0.00,
                'total_nilai' => 0.00,
            ];
            return $data;
        }
    }

    static function GetNilaiKhsSiswaLatek($course, $student, $prodi, $angkatan)
    {
        $data = DB::table('academic_score_latek')
            ->where('course', $course)
            ->where('student', $student)
            ->where('prodi', $prodi)
            ->where('angkatan', $angkatan)
            ->first();

        if ($data) {
            $vals['nilai_asli'] = $data->score;
            $predikat = DB::table('grade_level')
                ->where('min_grade', '<=', $vals['nilai_asli'])
                ->where('max_grade', '>=', $vals['nilai_asli'])
                ->where('deleted_at', null)
                ->where('status', true)
                ->first();
            if (!empty($predikat)) {
                $vals['predikat'] = $predikat->quality;
                $vals['nilai_akhir'] = $predikat->weight;
            } else {
                $vals['predikat'] = '-';
                $vals['nilai_akhir'] = '-';
            }

            $bagianDesimal = number_format($vals['nilai_asli'], 2, '.', '');
            $bagianDesimal = explode('.', $bagianDesimal)[1];
            $bagianBulat = intval($vals['nilai_asli']);

            // dd($bagianDesimal, $bagianBulat);
            if ($bagianDesimal > 50) {
                $vals['total_nilai'] = $bagianBulat + 1;
            } else {
                $vals['total_nilai'] = $bagianBulat;
            }
            // dd($vals);
            return $vals;
        } else {
            $data = [
                'nilai_asli' => 0.00,
                'predikat' => '-',
                'nilai_akhir' => 0.00,
                'total_nilai' => 0.00,
            ];
            return $data;
        }
    }

    static function getevaluasiDosen()
    {
        $student = DB::table('student')->where('id_user', Auth::user('api')->id)->first()->uuid;
        $generation = DB::table('student_academic_details')->select('generation_id')->where('student_id', $student)->first()->generation_id;
        $data =  DB::table('krs')
            ->select(
                'master_jadwal.id_dosen_pengajar as key_dosen',
                'lecturer.lecturer_name',
                'lecturer.NIDN',
                'courses.course_name',
                'courses.course_code',
                'courses.uuid as key_course'
            )
            ->join('master_jadwal', 'krs.schedule', '=', 'master_jadwal.uuid')
            ->join('lecturer', 'master_jadwal.id_dosen_pengajar', '=', 'lecturer.uuid')
            ->join('courses', 'master_jadwal.id_matakuliah', '=', 'courses.uuid')
            ->where('krs.generation_id', $generation)
            ->where('krs.student', $student)
            ->get();

        $return = [];
        foreach ($data as $key => $value) {
            $return[$key]['key_dosen'] = $value->key_dosen;
            $return[$key]['lecturer_name'] = $value->lecturer_name;
            $return[$key]['NIDN'] = $value->NIDN;
            $return[$key]['key_course'] = $value->key_course;
            $return[$key]['course_name'] = $value->course_name;
            if (PenilaianEdopsModel::where('id_dosen', $value->key_dosen)->where('id_user', $student)->exists()) {

                $return[$key]['status_pengisian'] = 1;
            } else {

                $return[$key]['status_pengisian'] = 0;
            }
        }

        // dd($return);

        return $return;
    }

    static function get_user_moodle_by_email($email)
    {
        $curl = curl_init();

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_user_get_users_by_field&";
        $URL .= "field=email&values[0]=" . $email;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET"

        ));



        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $JSONdata = json_decode($response, true);
        // dd($JSONdata);
        if (isset($JSONdata[0]["id"])) {
            return $JSONdata[0]["id"];
        } else {
            return "";
        }
        //dd($JSONdata);

    }

    static function get_user_moodle_by_nim($nim)
    {
        $curl = curl_init();

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_user_get_users_by_field&";
        $URL .= "field=username&values[0]=" . $nim;
        // dd($URL);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET"

        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $JSONdata = json_decode($response, true);
        if (isset($JSONdata[0]["id"])) {
            return $JSONdata[0]["id"];
        } else {
            return "";
        }
        //dd($JSONdata);

    }

    //moodle start
    static function create_user_moodle($npm, $nama_user, $email)
    {


        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_user_create_users&";
        $URL .= "users[0][username]=" . $npm . "&";
        $URL .= "users[0][email]=" . $email . "&";
        $URL .= "users[0][lastname]=_" . $npm . "&";
        $URL .= "users[0][firstname]=" . $nama_user . "&";
        $URL .= "users[0][password]=Admin1234%&";
        $URL .= "users[0][auth]=manual";
        //   dd($URL);



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $decode = json_decode($response);
        // dd($decode);
        return $decode[0]->id;
    }

    static function delete_user_moodle($id)
    {
        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_user_delete_users&";
        $URL .= "userids[0]=" . $id;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    static function store_member_elibrary($student, $studentPassword)
    {
        $url = URL_ELIBRARY . 'api/member/store';

        $password = $studentPassword->password;

        $member_image = NULL;
        // if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/student_portrait/1697009475_dummy-profile-1.png")) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/student_portrait/$student->portrait_path")) {
            // $member_image = fopen($_SERVER['DOCUMENT_ROOT'] . "/uploads/student_portrait/1697009475_dummy-profile-1.png", 'r');
            $member_image = fopen($_SERVER['DOCUMENT_ROOT'] . "/uploads/student_portrait/$student->portrait_path", 'r');
        }

        $data = [
            'id'             => $student->id,
            'name'           => $student->name,
            'gender'         => $student->gender == 'L' ? 1 : 0,
            'email'          => $student->email,
            'nak'            => $student->nim,
            'birth_date'     => $student->date_of_birth,
            'phone'          => $student->phone,
            'password'       => $password,
        ];

        $response = Http::attach('member_image', $member_image)->post($url, $data);

        if ($response->successful()) {
            $responseData = $response->json();
        } else {
            $responseData = $response->body();
        }
        return response()->json($responseData);
    }

    static function update_prodi_member_elibrary($student_nim, $program_name)
    {
        $url = URL_ELIBRARY . 'api/member/update/prodi';

        $data = [
            'member_nim'    => $student_nim,
            'program_name'  => $program_name,
        ];

        $response = Http::asForm()->post($url, $data);

        if ($response->successful()) {
            $responseData = $response->json();
        } else {
            $responseData = $response->body();
        }

        return response()->json($responseData);
    }

    static function update_user_moodle($id, $nim, $nama, $email)
    {
        // dd($request->all());

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_user_update_users&";
        $URL .= "users[0][id]=" . $id . "&";
        $URL .= "users[0][username]=" . $nim . "&";
        $URL .= "users[0][email]=" . $email . "&";
        $URL .= "users[0][lastname]=_" . $nim . "&";
        $URL .= "users[0][firstname]=" . $nama . "&";
        $URL .= "users[0][password]=Admin1234%";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    static function create_category_moodle($nama)
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_create_categories&";
        $URL .= "categories[0][name]=" . $nama . "&";
        $URL .= "categories[0][description]=AAL";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);
        $decode = json_decode($response);
        return $decode[0]->id;
    }

    static function delete_category_moodle($id)
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_delete_categories&";
        $URL .= "categories[0][id]=" . $id . "&";
        $URL .= "categories[0][newparent]=0&";
        $URL .= "categories[0][recursive]=1";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    static function update_category_moodle($id, $nama)
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_update_categories&";
        $URL .= "categories[0][id]=" . $id . "&";
        $URL .= "categories[0][name]=" . $nama . "&";
        $URL .= "categories[0][descriptionformat]=1";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
    static function create_course_moodle($id_prodi, $course, $start_date)
    {
        $timestamp = strtotime($start_date);

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_create_courses&";
        $URL .= "courses[0][fullname]=" . $course . "&";
        $URL .= "courses[0][shortname]=" . $course . "&";
        $URL .= "courses[0][summary]=" . $course . "&";
        $URL .= "courses[0][categoryid]=" . $id_prodi . "&";
        $URL .= "courses[0][startdate]=" . $timestamp . "&";
        $URL .= "courses[0][visible]=1";
        $curl = curl_init();
        // dd($URL);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        // $response = '[{"id":52,"shortname":"Pengetahuan Bertempur Perorangan"}]';
        // $decode = json_encode($response,true);
        // $id = collect($decode)->pluck('id')->first();
        // dd($response);
        if (is_string($response) && !empty($response)) {
            $data = json_decode($response, true); // true digunakan untuk mengonversi JSON menjadi array asosiatif

            if (is_array($data) && !empty($data)) {
                $id = $data[0]['id'];

                // Menampilkan nilai id
                return $id;
            }
        }
        // return $decode[0]->id;
    }

    static function delete_course_moodle($id)
    {

        // $id_prodi = 31;

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_delete_courses&";
        $URL .= "courseids[0]=" . $id . "";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    static function update_course_moodle($id_prodi, $course, $start_date)
    {
        $timestamp = strtotime($start_date);

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_update_courses&";
        $URL .= "courses[0][id]=" . $id_prodi . "&";
        $URL .= "courses[0][fullname]=" . $course . "&";
        $URL .= "courses[0][shortname]=" . $course . "&";
        $URL .= "courses[0][summary]=" . $course . "&";
        $URL .= "courses[0][startdate]=" . $timestamp . "&";
        $URL .= "courses[0][visible]=1";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    static function get_course_moodle()
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_get_courses&";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $decode = json_decode($response);
        // dd($decode);
        return $decode;
    }

    static function get_course_moodle_by_field($name)
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_get_courses_by_field&";
        $URL .= "wsfunction=core_course_get_courses_by_field&";
        $URL .= "field=shortname&";
        $URL .= "value=" . $name . "&";
        dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $decode = json_decode($response);
        // dd($decode);
        return $decode;
    }

    static function get_categories_moodle_All()
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_get_categories&";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $decode = json_decode($response);
        // dd($decode);
        return $decode;
    }

    static function get_categories_moodle($nama_prodi)
    {

        $URL  = URL_MOODLE;
        $URL .= "wstoken=" . wstoken . "&";
        $URL .= "moodlewsrestformat=json&";
        $URL .= "wsfunction=core_course_get_categories&";
        $URL .= "criteria[0][key]=name&";
        $URL .= "criteria[0][value]=" . $nama_prodi . "&";
        // dd($URL);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $decode = json_decode($response);
        // dd($decode);
        return $decode;
    }

    static function GetProdiName($id)
    {
        $data = DB::table('study_program')->where('uuid', $id)->first();
        if (!empty($data)) {
            return $data->program_name;
        } else {
            return '-';
        }
    }
    static function CountCourses($uuid)
    {
        $data = DB::table('dosen_matakuliah')->where('uuid_lecturer', $uuid)->whereNull('deleted_at')->count();
        return $data;
    }
    static function CountPA($uuid)
    {
        $data = DB::table('student_academic_details')->where('supervisor', $uuid)->whereNull('deleted_at')->count();
        return $data;
    }
    static function CountTA($uuid)
    {
        // $data = DB::table('kenaikan_pangkat_tingkat')->where('uuid_lecturer', $uuid)->whereNull('deleted_at')->count();
        $data = DB::table('student_academic_details')->where('dosen_tugas_akhir', $uuid)->whereNull('deleted_at')->count();
        return $data;
    }

    static function GetTokenDikti($username, $password, $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "act":"GetToken",
                "username":"' . $username . '",
                "password":"' . $password . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);


        curl_close($curl);
        return json_decode($response);
    }

    static function GetProdiDikti($token, $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "act": "GetAllProdi",
            "token": "' . $token . '",
            "filter": "nama_perguruan_tinggi = \'Akademi Angkatan Laut Surabaya\'",
            "order": "",
            "limit": "",
            "offset": ""
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        // dd(json_decode($response)->data);
        return json_decode($response)->data;

        curl_close($curl);
    }

    static function getConfigDikti()
    {
        $response = DB::connection('pgsql2')->table('config')->orderBy('id', 'DESC')->first();
        return $response;
    }

    static function GetDetailStudent($student_id)
    {
        // $data = DB::table('kenaikan_pangkat_tingkat')->where('uuid_lecturer', $uuid)->whereNull('deleted_at')->count();
        // dd($student_id);
        $data =  DB::table('student')
            ->leftjoin('student_academic_details', 'student.uuid', 'student_academic_details.student_id')
            ->leftjoin('student_address', 'student.uuid', 'student_address.student_id')
            ->where('student.uuid', $student_id)
            ->first();
        // dd($data);
        return $data;
    }

    static function GetTA($generation_id)
    {
        $data =  DB::table('generation')
            ->where('uuid', $generation_id)
            ->first();
        if ($data->semester == "Ganjil") {
            $angka = 1;
        } else {
            $angka = 2;
        }
        $hasil = $data->tahun . $angka;

        return $hasil;
    }

    static function PostToNeo($student_id)
    {
        try {
            $getstudent = Helper::GetDetailStudent($student_id);

            $getayah = DB::table('student_guardian')->where('student_id', $student_id)->where('type', 1)->first();
            $getibu = DB::table('student_guardian')->where('student_id', $student_id)->where('type', 2)->first();
            $get_agama = DB::table('religion')->where('uuid', $getstudent->religion)->first();
            $get_prodi = DB::table('study_program')->where('uuid', $getstudent->program_id)->first();
            $tahun_akademik = Helper::GetTA($getstudent->generation_id);
            $nama_mahasiswa = $getstudent->name;
            $jenis_kelamin = $getstudent->gender;
            $tempat_lahir = $getstudent->place_of_birth;
            $tanggal_lahir = $getstudent->date_of_birth;
            $id_agama = $get_agama->id_neo;
            $nik = $getstudent->NIK;
            $nisn = '1112189181';
            $kewarganegaraan = 'ID';
            $jalan = $getstudent->streets;
            $rt = $getstudent->RT;
            $rw = $getstudent->RW;
            $kode_pos = $getstudent->postal_code; //kasi validasi di siak nya cuma 5 angka
            $handphone = $getstudent->phone;
            $email = $getstudent->email;
            $nama_ayah = $getayah->guardian_name;
            $nama_ibu_kandung = $getibu->guardian_name;
            $nama_wali = null;
            $dusun = null;
            $kelurahan = 'indonesia';
            $id_wilayah = '016300'; //wajib
            $id_jenis_tinggal = null;
            $id_alat_transportasi = null;
            $telepon = null;
            $penerima_kps = null;
            $nomor_kps = null;
            $nik_ayah = null;
            $tanggal_lahir_ayah = null;
            $id_pendidikan_ayah = null;
            $id_pekerjaan_ayah = null;
            $id_penghasilan_ayah = null;
            $nik_ibu = null;
            $tanggal_lahir_ibu = null;
            $id_pendidikan_ibu = null;
            $id_pekerjaan_ibu = null;
            $id_penghasilan_ibu = null;
            $npwp = null;
            $tanggal_lahir_wali = null;
            $id_pendidikan_wali = null;
            $id_pekerjaan_wali = null;
            $id_penghasilan_wali = null;
            $id_kebutuhan_khusus_mahasiswa = null;
            $id_kebutuhan_khusus_ayah = null;
            $id_kebutuhan_khusus_ibu = null;
            $nim = $getstudent->nim;



            $config = DB::connection('pgsql2')->table('config')->orderBy('id', 'DESC')->first();
            $token = Helper::GetTokenDikti($config->username, $config->password, $config->url);
            $res = [];
            foreach ($token as $key) {
                $res = $key;
            }
            $token_ = $res->token;

            $data = array(
                "act" => "InsertBiodataMahasiswa",
                "token" => "$token_",
                "record" => array(
                    "nama_mahasiswa" => $nama_mahasiswa,
                    "jenis_kelamin" =>  $jenis_kelamin,
                    "tempat_lahir" => $tempat_lahir,
                    "tanggal_lahir" => $tanggal_lahir,
                    "id_agama" => $id_agama,
                    "nik" => $nik,
                    "nisn" => $nisn,
                    "kewarganegaraan" => $kewarganegaraan,
                    "jalan" => $jalan,
                    "dusun" => $dusun,
                    "rt" => $rt,
                    "rw" => $rw,
                    "kelurahan" => $kelurahan,
                    "kode_pos" => $kode_pos,
                    "id_wilayah" => $id_wilayah,
                    "id_jenis_tinggal" => $id_jenis_tinggal,
                    "id_alat_transportasi" => $id_alat_transportasi,
                    "telepon" => $telepon,
                    "handphone" => $handphone,
                    "email" => $email,
                    "penerima_kps" => $penerima_kps,
                    "nomor_kps" => $nomor_kps,
                    "nik_ayah" => $nik_ayah,
                    "nama_ayah" => $nama_ayah,
                    "tanggal_lahir_ayah" => $tanggal_lahir_ayah,
                    "id_pendidikan_ayah" => $id_pendidikan_ayah,
                    "id_pekerjaan_ayah" => $id_pekerjaan_ayah,
                    "id_penghasilan_ayah" => $id_penghasilan_ayah,
                    "nik_ibu" => $nik_ibu,
                    "nama_ibu_kandung" => $nama_ibu_kandung,
                    "tanggal_lahir_ibu" => $tanggal_lahir_ibu,
                    "id_pendidikan_ibu" => $id_pendidikan_ibu,
                    "id_pekerjaan_ibu" => $id_pekerjaan_ibu,
                    "id_penghasilan_ibu" => $id_penghasilan_ibu,
                    "npwp" => $npwp,
                    "nama_wali" => $nama_wali,
                    "tanggal_lahir_wali" => $tanggal_lahir_wali,
                    "id_pendidikan_wali" => $id_pendidikan_wali,
                    "id_pekerjaan_wali" => $id_pekerjaan_wali,
                    "id_penghasilan_wali" => $id_penghasilan_wali,
                    "id_kebutuhan_khusus_mahasiswa" => $id_kebutuhan_khusus_mahasiswa,
                    "id_kebutuhan_khusus_ayah" => $id_kebutuhan_khusus_ayah,
                    "id_kebutuhan_khusus_ibu" => $id_kebutuhan_khusus_ibu
                )
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $config->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            // dd($response);
            curl_close($curl);
            return json_decode($response)->data->id_mahasiswa;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function PostToNeoTwo($student_id)
    {
        $getstudent = Helper::GetDetailStudent($student_id);
        $getayah = DB::table('student_guardian')->where('student_id', $student_id)->where('type', 1)->first();
        $getibu = DB::table('student_guardian')->where('student_id', $student_id)->where('type', 2)->first();
        $get_agama = DB::table('religion')->where('uuid', $getstudent->religion)->first();
        $get_prodi = DB::table('study_program')->where('uuid', $getstudent->program_id)->first();
        $tahun_akademik = Helper::GetTA($getstudent->generation_id);
        $nim = $getstudent->nim;
        $id_periode_masuk = $tahun_akademik;
        $tanggal_daftar = $getstudent->enrollment_date;
        $id_perguruan_tinggi = '728bf085-d2e7-4547-baea-df17e92857f9';
        $id_prodi =  $get_prodi->neo_id;
        $sks_diakui = null;
        $id_mahasiswa = Helper::PostToNeo($student_id);
        $id_jenis_daftar = null;
        $id_jalur_daftar = null;
        $id_bidang_minat = null;
        $id_perguruan_tinggi_asal = null;
        $nama_perguruan_tinggi_asal = null;
        $id_prodi_asal = null;
        $nama_prodi_asal = null;
        $id_pembiayaan = null;
        $biaya_masuk = null;

        $config = DB::connection('pgsql2')->table('config')->orderBy('id', 'DESC')->first();
        $token = Helper::GetTokenDikti($config->username, $config->password, $config->url);
        $res = [];
        foreach ($token as $key) {
            $res = $key;
        }
        $token_ = $res->token;
        $data = array(
            "act" => "InsertRiwayatPendidikanMahasiswa",
            "token" => "$token_",
            "record" => array(
                "id_mahasiswa" =>  $id_mahasiswa,
                "nim" =>  $nim,
                "id_jenis_daftar" =>  $id_jenis_daftar,
                "id_jalur_daftar" =>  $id_jalur_daftar,
                "id_periode_masuk" =>  $id_periode_masuk,
                "tanggal_daftar" =>  $tanggal_daftar,
                "id_perguruan_tinggi" =>  $id_perguruan_tinggi,
                "id_prodi" =>  $id_prodi,
                "id_bidang_minat" => $id_bidang_minat,
                "sks_diakui" =>  $sks_diakui,
                "id_perguruan_tinggi_asal" =>  $id_perguruan_tinggi_asal,
                "nama_perguruan_tinggi_asal" =>  $nama_perguruan_tinggi_asal,
                "id_prodi_asal" =>  $id_prodi_asal,
                "nama_prodi_asal" =>  $nama_prodi_asal,
                "id_pembiayaan" =>  $id_pembiayaan,
                "biaya_masuk" => $biaya_masuk
            )
        );
        // dd($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $config->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    static function SaveToNeoFinal($student_id)
    {
        // $ws = Helper::PostToNeo($student_id);
        $ws2 = Helper::PostToNeoTwo($student_id);
        return $ws2;
    }

    static function getuuidKrs($program_id, $angkatan, $semester)
    {
        $krs = DB::table('krs_new')
            ->where('prodi', $program_id)
            ->where('angkatan', $angkatan)
            ->where('semester', $semester)
            ->where('deleted_at', null)
            ->first();
        $uuid = (!empty($krs)) ? $krs->uuid : null;
        return $uuid;
    }

    static public function GenerateKodePengaduan($kategori)
    {
        $tanggalskr = date('Y-m-d H:i:s');
        $date = date_format(date_create($tanggalskr), 'Y/m/d');
        $no = 0;
        $key_siswa = Helper::GetUuidSiswa();
        $testing = DB::table('pengaduan')->select('kode_pengaduan')->where('uuid_siswa', $key_siswa)
            ->whereDate('created_at', $tanggalskr)
            ->where('id_jenis_kategori', $kategori)
            ->orderBy('created_at', 'DESC')
            ->first()->kode_pengaduan;
        $rest = (int) substr($testing, -4);
        $kategori = DB::table('master_kategori_pengaduan')->where('uuid', $kategori)->first()->kode;
        $kode_kat = (!empty($kategori)) ? $kategori  : 'a';
        $iduser = Auth::user()->id;
        if ($rest == 0) {
            $no = "$date/$kode_kat-$iduser-0001";
            $autonya = $no;
        } else if ($rest < 9) {
            $no = $rest + 1;

            $autonya = "$date/$kode_kat-$iduser-000$no";
        } else if ($rest < 99) {
            $no = $rest + 1;

            $autonya = "$date/$kode_kat-$iduser-00$no";
        } else if ($rest < 999) {
            $no = $rest + 1;

            $autonya = "$date/$kode_kat-$iduser-0$no";
        } else if ($rest < 9999) {
            $no = $rest + 1;

            $autonya = "$date/$kode_kat-$iduser-$no";
        } else {
            $autonya = "$date/$kode_kat-$iduser-0001";
        }
        return $autonya;
    }

    static public function GetSemesterSaatIni($data)
    {
        // $hasil = DB::table('master_jadwal')->select(
        //     'krs_new.uuid',
        //     DB::raw("MAX(CAST(krs_new.semester AS INT)) AS semester")
        // )
        // ->leftJoin('krs_new', 'krs_new.uuid', 'master_jadwal.krs')
        // ->where('master_jadwal.id_prodi', $data->program_id)
        // ->where('master_jadwal.id_angkatan', $data->angkatan)
        // ->where('master_jadwal.id_kelompok', $data->id_kelompok)
        // ->where('master_jadwal.deleted_at', null)
        // ->groupBy('krs_new.uuid')
        // ->first();

        $hasil = DB::table('krs_new')->select(
            'uuid',
            DB::raw("MAX(CAST(semester AS INT)) AS semester")
        )
            ->where('prodi', $data->program_id)
            ->where('angkatan', $data->angkatan)
            ->where('deleted_at', null)
            ->groupBy('uuid')
            ->first();

        return $hasil;
    }

    static public function GetTASaatIni($data)
    {
        // $hasil = DB::table('master_jadwal')->select(
        //     'generation.uuid',
        //     DB::raw("MAX(CAST(generation.tahun AS INT)) AS tahun")
        // )
        // ->leftJoin('generation', 'generation.uuid', '=', 'master_jadwal.tahun_akademik')
        // ->where('master_jadwal.id_prodi', $data->program_id)
        // ->where('master_jadwal.id_angkatan', $data->angkatan)
        // ->where('master_jadwal.id_kelompok', $data->id_kelompok)
        // ->where('master_jadwal.deleted_at', null)
        // ->groupBy('generation.uuid')
        // ->first();

        $hasil = DB::table('krs_new')->select(
            'generation.uuid',
            DB::raw("MAX(CAST(generation.tahun AS INT)) AS tahun, tahun_akhir, generation.semester")
        )
            ->leftJoin('generation', 'generation.uuid', '=', 'krs_new.generation')
            ->where('krs_new.prodi', $data->program_id)
            ->where('krs_new.angkatan', $data->angkatan)
            ->where('krs_new.deleted_at', null)
            ->groupBy('generation.uuid')
            ->first();

        return $hasil;
    }

    static public function GetKhsSiswa($student, $semester)
    {
        $siswa = DB::table('student')->select(
            'student.uuid as key_student',
            'student_academic_details.program_id',
            'student_academic_details.angkatan',
            'student_academic_details.semester',
            'kelompok_siswa_detail.id_kelompok',
            'student.name',
            'student.nim',
            'master_kelompok.nama_kelompok',
            'study_program.korps',
            DB::raw(
                '
                CASE
                    WHEN (student_academic_details.semester::integer) IN (1, 2) THEN \'I\'
                    WHEN (student_academic_details.semester::integer) IN (3, 4) THEN \'II\'
                    WHEN (student_academic_details.semester::integer) IN (5, 6) THEN \'III\'
                    WHEN (student_academic_details.semester::integer) IN (7, 8) THEN \'IV\'
                    ELSE \'-\'
                END AS tingkat'
            )
        )
            ->leftJoin('student_academic_details', 'student_academic_details.student_id', '=', 'student.uuid')
            ->leftJoin('master_angkatan', 'master_angkatan.uuid', '=', 'student_academic_details.angkatan')
            ->leftJoin('kelompok_siswa_detail', 'kelompok_siswa_detail.id_siswa', '=', 'student.uuid')
            ->leftJoin('master_kelompok', 'master_kelompok.uuid', '=', 'kelompok_siswa_detail.id_kelompok')
            ->leftJoin('study_program', 'student_academic_details.program_id', '=', 'study_program.uuid')
            ->where('student.uuid', $student)
            ->first();

        $uuid_semester = Helper::getuuidKrs($siswa->program_id, $siswa->angkatan, $semester);
        if ($uuid_semester == null) {
            $return = null;
        } else {
            // akademik start
            $krs = KrsNewModel::select(
                'courses.course_name',
                'courses.course_code',
                'courses.credits',
                'krs_new.uuid as key',
                'krs_new.prodi',
                'krs_new.angkatan',
                'courses.semester',
                'detail_krs_new.course as key_course'
            )
                ->leftjoin('detail_krs_new', 'krs_new.uuid', '=', 'detail_krs_new.krs')
                ->leftjoin('courses', 'detail_krs_new.course', '=', 'courses.uuid')

                ->whereNull('krs_new.deleted_at')
                ->where('krs_new.prodi', $siswa->program_id)
                ->where('krs_new.angkatan', $siswa->angkatan)
                ->where('krs_new.semester', $semester)
                ->get();
            // dd($krs);
            $sumxsks = 0;
            $jumlah_sks = 0;
            foreach ($krs as $key => $value) {
                $single_krs[$key]['course_name'] = $value->course_name;
                $single_krs[$key]['credits'] = $value->credits;
                $latek = Helper::GetNilaiKhsSiswaLatek($value->key_course, $student, $value->prodi, $value->angkatan);
                $akademik = Helper::GetNilaiKhsSiswa($value->key_course, $student, $value->prodi, $value->angkatan);
                // dd();
                $single_krs[$key]['nilai'] = ($akademik['nilai_asli'] == 0) ? $latek : $akademik;

                $single_krs[$key]['nilaixsks'] = (int) $value->credits *  (float) $single_krs[$key]['nilai']['nilai_akhir'];
                $sumxsks += $single_krs[$key]['nilaixsks'];
                $jumlah_sks += (int) $value->credits;
            }
            //akademik end

            $return['ip'] = number_format($sumxsks / $jumlah_sks, 2, '.', ',');
        }

        return  $return['ip'];
    }

    static function GetLastSemesterStudent($prodi, $angkatan)
    {
        $latestSemester = KrsNewModel::where('prodi', $prodi)
            ->where('angkatan', $angkatan)
            ->where('deleted_at', null)
            ->max('semester');
        return $latestSemester;
    }
    static function GetLastTingkat($id_student)
    {
        $response = PromotionModel::select(
            'kenaikan_pangkat_tingkat.uuid as promotion_key',
            'student.uuid as student_key',
            'student.name',
            'kenaikan_pangkat_tingkat.status_kenaikan_pangkat',
            'kenaikan_pangkat_tingkat.tanggal_pengesahan_pangkat',
            'kenaikan_pangkat_tingkat.pangkat AS key_pangkat',
            'master_pangkat.nama_pangkat',
            'kenaikan_pangkat_tingkat.alasan_pangkat',
            'kenaikan_pangkat_tingkat.status_kenaikan_tingkat',
            'kenaikan_pangkat_tingkat.tanggal_pengesahan_tingkat',
            'kenaikan_pangkat_tingkat.tingkat',
            'kenaikan_pangkat_tingkat.alasan_tingkat',
        )
            ->join('student', 'kenaikan_pangkat_tingkat.uuid_student', '=', 'student.uuid')
            ->join('master_pangkat', 'kenaikan_pangkat_tingkat.pangkat', '=', 'master_pangkat.uuid')
            ->where('student.uuid', $id_student)
            ->orderBy('kenaikan_pangkat_tingkat.created_at', 'DESC')
            ->first();
        if ($response) {
            $res = $response->tingkat;
        } else {
            $res = "-";
        }
        return $res;
    }
}

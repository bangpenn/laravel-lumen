<?php

namespace App\Constants;


class ErrorMessage
{
    const NONE = 'None';
    const INTERNAL_SERVER_ERROR = 'Terjadi kesalah pada sistem';
    const INSUF_PARAM = 'Kesalahan parameter';
    const INSUF_FILE = 'Tidak ada fail yang dipilih';
    const REQUEST_TIME_OUT = 'Kesalahan parameter';
    const INVALID_LOGIN = 'Kesalahan pada username atau kata sandi';
    const INVALID_ACCESS_TOKEN = 'Sesi habis';
    const ERROR_ACCESS_TOKEN = 'An error while decoding token';
    const URL_UNKNOWN = 'URL tidak dikenali';
    const EXTERNAL_SERVER_ERROR = 'Terjadi kesalahan pada sumber eksternal';
    const DATA_NOT_FOUND = 'Data tidak ditemukan';
    const FORBIDDEN = 'Forbidden';
    const INVALID_VERIFICATION = 'Gagal memverifikasi akun';
    const ACCOUNT_VALID = 'Akun anda sudah terverifikasi';
    const INVALID_RESET_PASSWORD = 'Gagal mengubah password';
    const EMAIL_NOT_REGISTERED = 'Email tidak terdaftar';
    const FAILED_LOGIN = 'Email tidak terdaftar atau Password salah';
    const GLOBAL_MESSAGE = 'Please contact to your system administrator about this error.';
    const INVALID_PASSWORD = 'Password Salah';
    const PROFILE_NOT_COMPLETED = 'Profil anda belum lengkap';
    /*NEW*/
    const HTTP_CONTINUE = 'Continue';
    const HTTP_SWITCHING_PROTOCOLS = 'Switching Protocols';
    const HTTP_PROCESSING = 'Processing';            // RFC2518
    const HTTP_OK = 'OK';
    const HTTP_CREATED = 'Created';
    const HTTP_ACCEPTED = 'Accepted';
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 'Non-Authoritative Information';
    const HTTP_NO_CONTENT = 'No Content';
    const HTTP_RESET_CONTENT = 'Reset Content';
    const HTTP_PARTIAL_CONTENT = 'Partial Content';
    const HTTP_MULTI_STATUS = 'Multi-Status';          // RFC4918
    const HTTP_ALREADY_REPORTED = 'Already Reported';      // RFC5842
    const HTTP_IM_USED = 'IM Used';               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 'Multiple Choices';
    const HTTP_MOVED_PERMANENTLY = 'Moved Permanently';
    const HTTP_FOUND = 'Found';
    const HTTP_SEE_OTHER = 'See Other';
    const HTTP_NOT_MODIFIED = 'Not Modified';
    const HTTP_USE_PROXY = 'Use Proxy';
    const HTTP_RESERVED = '306';
    const HTTP_TEMPORARY_REDIRECT = 'Temporary Redirect';
    const HTTP_PERMANENTLY_REDIRECT = 'Permanent Redirect';  // RFC7238
    const HTTP_BAD_REQUEST = 'Bad Request';
    const HTTP_UNAUTHORIZED = 'Unauthorized';
    const HTTP_PAYMENT_REQUIRED = 'Payment Required';
    const HTTP_FORBIDDEN = 'Forbidden';
    const HTTP_NOT_FOUND = 'Not Found';
    const HTTP_METHOD_NOT_ALLOWED = 'Method Not Allowed';
    const HTTP_NOT_ACCEPTABLE = 'Not Acceptable';
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 'Proxy Authentication Required';
    const HTTP_REQUEST_TIMEOUT = 'Request Timeout';
    const HTTP_CONFLICT = 'Conflict';
    const HTTP_GONE = 'Gone';
    const HTTP_LENGTH_REQUIRED = 'Length Required';
    const HTTP_PRECONDITION_FAILED = 'Precondition Failed';
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 'Payload Too Large';
    const HTTP_REQUEST_URI_TOO_LONG = 'URI Too Long';
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 'Unsupported Media Type';
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 'Range Not Satisfiable';
    const HTTP_EXPECTATION_FAILED = 'Expectation Failed';
    const HTTP_I_AM_A_TEAPOT = 'I\'m a teapot';                                               // RFC2324
    const HTTP_MISDIRECTED_REQUEST = 'Misdirected Request';                                         // RFC7540
    const HTTP_UNPROCESSABLE_ENTITY = 'Unprocessable Entity';                                        // RFC4918
    const HTTP_LOCKED = 'Locked';                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 'Failed Dependency';                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 'Reserved for WebDAV advanced collections expired proposal';   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 'Upgrade Required';                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 'Precondition Required';                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 'Too Many Requests';                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 'Request Header Fields Too Large';                             // RFC6585
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 'Unavailable For Legal Reasons';
    const HTTP_INTERNAL_SERVER_ERROR = 'Internal Server Error';
    const HTTP_NOT_IMPLEMENTED = 'Not Implemented';
    const HTTP_BAD_GATEWAY = 'Bad Gateway';
    const HTTP_SERVICE_UNAVAILABLE = 'Service Unavailable';
    const HTTP_GATEWAY_TIMEOUT = 'Gateway Timeout';
    const HTTP_VERSION_NOT_SUPPORTED = 'HTTP Version Not Supported';
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 'Variant Also Negotiates';                        // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 'Insufficient Storage';                                        // RFC4918
    const HTTP_LOOP_DETECTED = 'Loop Detected';                                               // RFC5842
    const HTTP_NOT_EXTENDED = 'Not Extended';                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 'Network Authentication Required';
}

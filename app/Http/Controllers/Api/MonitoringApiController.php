<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InternshipPlace;
use App\Models\Monitoring;
use App\Models\Report;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class MonitoringApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */

    public function getStats()
    {
        try {

            $monitorings = Monitoring::with([
                'mentor',
                'internshipPlace'
            ])
                ->inRandomOrder()
                ->get();

            $formattedData = $monitorings->map(function ($m) {

                return [
                    'id' => $m->id,

                    'mentor' =>
                    $m->mentor->name
                        ?? 'Tenaga Ahli',

                    'place' =>
                    $m->internshipPlace->name
                        ?? 'Mitra Industri',

                    'place_code' =>
                    $m->internshipPlace->code
                        ?? null,

                    'photo' =>
                    $m->photo
                        ? asset('storage/' . $m->photo)
                        : null,

                    'status' =>
                    $m->status,

                    'notes' =>
                    $m->notes ?? null,

                    'time_ago' =>
                    optional($m->created_at)
                        ->diffForHumans(),

                    'date_full' =>
                    optional($m->created_at)
                        ->format('d M Y H:i'),
                ];
            });


            return response()->json([
                'success' => true,

                'message' =>
                'Berhasil mengambil seluruh data monitoring',

                'total_data' =>
                $formattedData->count(),

                'total_mitra' =>
                $formattedData
                    ->unique('place')
                    ->count(),

                'data' =>
                $formattedData,

            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,

                'message' =>
                'Gagal mengambil data monitoring',

                'error' =>
                $e->getMessage(),

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VIDEO PRAKERIN
    |--------------------------------------------------------------------------
    |
    | GET /api/video
    |
    */

    public function getVideos()
    {
        try {

            $videos = Cache::remember(
                'public_prakerin_videos_v12',
                now()->addMinutes(5),
                function () {

                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL LAPORAN
                    |--------------------------------------------------------------------------
                    */

                    $reports = Report::query()

                        ->with([
                            'student',
                            'student.internshipBatch',
                        ])

                        ->whereNotNull('report_link1')

                        ->whereRaw(
                            "TRIM(report_link1) <> ''"
                        )

                        ->where(
                            'report_status',
                            'Sudah Upload'
                        )

                        ->whereHas(
                            'student.internshipBatch',
                            function ($query) {

                                $query->where(
                                    'status_batch',
                                    'active'
                                );
                            }
                        )

                        ->orderByRaw("
    CAST(
        SUBSTRING_INDEX(
            SUBSTRING_INDEX(report_title, 'Minggu ', -1),
            ':',
            1
        ) AS UNSIGNED
    ) DESC
")

                        ->orderByDesc('id')

                        ->get();


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL SEMUA KODE DUDI
                    |--------------------------------------------------------------------------
                    |
                    | Contoh:
                    |
                    | students.internship_place_code = TK
                    |
                    */

                    $placeCodes = $reports
                        ->pluck(
                            'student.internship_place_code'
                        )
                        ->filter()
                        ->unique()
                        ->values();


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL DATA DUDI SEKALI SAJA
                    |--------------------------------------------------------------------------
                    |
                    | Hindari query satu-satu per video.
                    |
                    */

                    $places = InternshipPlace::query()

                        ->whereIn(
                            'code',
                            $placeCodes
                        )

                        ->get()

                        ->keyBy(
                            'code'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT VIDEO
                    |--------------------------------------------------------------------------
                    */

                    return $reports

                        ->map(function ($report) use ($places) {

                            /*
                            |--------------------------------------------------------------------------
                            | Pastikan student tersedia
                            |--------------------------------------------------------------------------
                            */

                            if (!$report->student) {
                                return null;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | URL VIDEO
                            |--------------------------------------------------------------------------
                            */

                            $originalUrl = trim(
                                $report->report_link1
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | PLATFORM
                            |--------------------------------------------------------------------------
                            */

                            $platform =
                                $this->detectPlatform(
                                    $originalUrl
                                );


                            if (
                                !in_array(
                                    $platform,
                                    [
                                        'youtube',
                                        'tiktok',
                                        'instagram',
                                    ],
                                    true
                                )
                            ) {

                                return null;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | RESOLVE URL
                            |--------------------------------------------------------------------------
                            */

                            $resolvedUrl =
                                $platform === 'tiktok'

                                ? $this->resolveTikTokUrl(
                                    $originalUrl
                                )

                                : $originalUrl;


                            /*
                            |--------------------------------------------------------------------------
                            | EMBED URL
                            |--------------------------------------------------------------------------
                            */

                            $embedUrl =
                                $this->makeEmbedUrl(
                                    $platform,
                                    $resolvedUrl
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | DUDI
                            |--------------------------------------------------------------------------
                            */

                            $placeCode =
                                $report
                                ->student
                                ->internship_place_code;


                            $place =
                                $placeCode

                                ? $places->get(
                                    $placeCode
                                )

                                : null;


                            /*
                            |--------------------------------------------------------------------------
                            | THUMBNAIL / PREVIEW
                            |--------------------------------------------------------------------------
                            */

                            $thumbnailUrl = null;

                            $previewType =
                                'fallback';

                            $previewUrl =
                                null;


                            /*
                            |--------------------------------------------------------------------------
                            | YOUTUBE
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $platform === 'youtube'
                            ) {

                                $thumbnailUrl =
                                    $this->getYoutubeThumbnail(
                                        $resolvedUrl
                                    );


                                if ($thumbnailUrl) {

                                    $previewType =
                                        'image';

                                    $previewUrl =
                                        $thumbnailUrl;
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TIKTOK
                            |--------------------------------------------------------------------------
                            */ elseif (
                                $platform === 'tiktok'
                            ) {

                                $thumbnailUrl =
                                    url(
                                        '/api/video/'
                                            . $report->id
                                            . '/thumbnail'
                                    );


                                $previewType =
                                    'image';


                                $previewUrl =
                                    $thumbnailUrl;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | INSTAGRAM
                            |--------------------------------------------------------------------------
                            |
                            | Instagram menggunakan embed sebagai preview card.
                            |
                            */ elseif (
                                $platform === 'instagram'
                            ) {

                                if ($embedUrl) {

                                    $previewType =
                                        'instagram_embed';

                                    $previewUrl =
                                        $embedUrl;
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | RESPONSE
                            |--------------------------------------------------------------------------
                            */

                            return [

                                /*
                                |--------------------------------------------------------------------------
                                | REPORT
                                |--------------------------------------------------------------------------
                                */

                                'id' =>
                                $report->id,

                                'title' =>
                                $report->report_title
                                    ?? 'Video Prakerin',

                                'description' =>
                                $report->description,

                                'report_date' =>
                                $report->report_date,

                                'report_status' =>
                                $report->report_status,


                                /*
                                |--------------------------------------------------------------------------
                                | SISWA
                                |--------------------------------------------------------------------------
                                */

                                'student_id' =>
                                $report->student_id,

                                'student_name' =>
                                $report->student->name
                                    ?? 'Peserta Didik',

                                'nis' =>
                                $report->student->nis
                                    ?? null,

                                'class_code' =>
                                $report
                                    ->student
                                    ->class_code
                                    ?? null,

                                'department_code' =>
                                $report
                                    ->student
                                    ->department_code
                                    ?? null,


                                /*
                                |--------------------------------------------------------------------------
                                | DUDI
                                |--------------------------------------------------------------------------
                                */

                                'internship_place_code' =>
                                $placeCode,

                                'internship_place_name' =>
                                $place?->name,

                                'internship_place_field' =>
                                $place?->field,

                                'internship_place_address' =>
                                $place?->address,

                                /*
                                |--------------------------------------------------------------------------
                                | Bentuk nested juga disediakan
                                |--------------------------------------------------------------------------
                                */

                                'dudi' => [

                                    'code' =>
                                    $place?->code
                                        ?? $placeCode,

                                    'name' =>
                                    $place?->name,

                                    'field' =>
                                    $place?->field,

                                    'address' =>
                                    $place?->address,
                                ],


                                /*
                                |--------------------------------------------------------------------------
                                | VIDEO
                                |--------------------------------------------------------------------------
                                */

                                'platform' =>
                                $platform,

                                'url' =>
                                $originalUrl,

                                'resolved_url' =>
                                $resolvedUrl,

                                'embed_url' =>
                                $embedUrl,

                                'thumbnail_url' =>
                                $thumbnailUrl,

                                'preview_type' =>
                                $previewType,

                                'preview_url' =>
                                $previewUrl,


                                /*
                                |--------------------------------------------------------------------------
                                | WAKTU
                                |--------------------------------------------------------------------------
                                */

                                'created_at' =>
                                optional(
                                    $report->created_at
                                )
                                    ->toDateTimeString(),

                                'time_ago' =>
                                optional(
                                    $report->created_at
                                )
                                    ->diffForHumans(),
                            ];
                        })

                        ->filter()

                        ->values();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | RESPONSE API
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,

                'message' =>
                'Berhasil mengambil video laporan Prakerin',

                'total_data' =>
                $videos->count(),

                'platforms' => [

                    'tiktok' =>
                    $videos
                        ->where(
                            'platform',
                            'tiktok'
                        )
                        ->count(),

                    'instagram' =>
                    $videos
                        ->where(
                            'platform',
                            'instagram'
                        )
                        ->count(),

                    'youtube' =>
                    $videos
                        ->where(
                            'platform',
                            'youtube'
                        )
                        ->count(),
                ],

                'data' =>
                $videos,

            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,

                'message' =>
                'Gagal mengambil video laporan Prakerin',

                'error' =>
                $e->getMessage(),

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TIKTOK THUMBNAIL
    |--------------------------------------------------------------------------
    |
    | GET /api/video/{report}/thumbnail
    |
    */

    public function thumbnail(Report $report)
    {
        try {

            if (
                empty($report->report_link1)
                ||
                $report->report_status
                !== 'Sudah Upload'
            ) {

                return $this->placeholderThumbnail(
                    'Video'
                );
            }


            $report->loadMissing([
                'student.internshipBatch'
            ]);


            /*
            |--------------------------------------------------------------------------
            | Hanya batch aktif
            |--------------------------------------------------------------------------
            */

            if (
                !$report->student
                ||
                !$report
                    ->student
                    ->internshipBatch
                ||
                $report
                ->student
                ->internshipBatch
                ->status_batch
                !== 'active'
            ) {

                return $this->placeholderThumbnail(
                    'Prakerin'
                );
            }


            $originalUrl = trim(
                $report->report_link1
            );


            $platform =
                $this->detectPlatform(
                    $originalUrl
                );


            /*
            |--------------------------------------------------------------------------
            | Endpoint thumbnail khusus TikTok
            |--------------------------------------------------------------------------
            */

            if (
                $platform !== 'tiktok'
            ) {

                return $this->placeholderThumbnail(
                    ucfirst(
                        $platform
                    )
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Resolve TikTok URL
            |--------------------------------------------------------------------------
            */

            $resolvedUrl =
                $this->resolveTikTokUrl(
                    $originalUrl
                );


            /*
            |--------------------------------------------------------------------------
            | TikTok oEmbed
            |--------------------------------------------------------------------------
            */

            $oembed =
                $this->getTikTokOEmbed(
                    $resolvedUrl
                );


            $thumbnailUrl =
                $oembed['thumbnail_url']
                ?? null;


            if (!$thumbnailUrl) {

                return $this->placeholderThumbnail(
                    'TikTok'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Download thumbnail
            |--------------------------------------------------------------------------
            */

            $response =
                Http::timeout(20)

                ->retry(
                    2,
                    300
                )

                ->withHeaders([

                    'User-Agent' =>
                    $this->browserUserAgent(),

                    'Accept' =>
                    'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',

                    'Referer' =>
                    'https://www.tiktok.com/',
                ])

                ->get(
                    $thumbnailUrl
                );


            if (
                !$response->successful()
            ) {

                return $this->placeholderThumbnail(
                    'TikTok'
                );
            }


            $contentType =
                $response->header(
                    'Content-Type'
                )
                ?: 'image/jpeg';


            if (
                !str_starts_with(
                    strtolower(
                        $contentType
                    ),
                    'image/'
                )
            ) {

                return $this->placeholderThumbnail(
                    'TikTok'
                );
            }


            return response(
                $response->body(),
                200
            )

                ->header(
                    'Content-Type',
                    $contentType
                )

                ->header(
                    'Cache-Control',
                    'public, max-age=900'
                );
        } catch (Throwable $e) {

            return $this->placeholderThumbnail(
                'TikTok'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DETECT PLATFORM
    |--------------------------------------------------------------------------
    */

    private function detectPlatform(
        ?string $url
    ): string {

        if (!$url) {
            return 'unknown';
        }


        $host =
            strtolower(
                parse_url(
                    $url,
                    PHP_URL_HOST
                )
                    ?? ''
            );


        $host =
            preg_replace(
                '/^www\./',
                '',
                $host
            );


        /*
        |--------------------------------------------------------------------------
        | TikTok
        |--------------------------------------------------------------------------
        */

        if (
            $host === 'tiktok.com'
            ||
            str_ends_with(
                $host,
                '.tiktok.com'
            )
        ) {

            return 'tiktok';
        }


        /*
        |--------------------------------------------------------------------------
        | Instagram
        |--------------------------------------------------------------------------
        */

        if (
            $host === 'instagram.com'
            ||
            str_ends_with(
                $host,
                '.instagram.com'
            )
        ) {

            return 'instagram';
        }


        /*
        |--------------------------------------------------------------------------
        | YouTube
        |--------------------------------------------------------------------------
        */

        if (
            $host === 'youtube.com'
            ||
            str_ends_with(
                $host,
                '.youtube.com'
            )
            ||
            $host === 'youtu.be'
        ) {

            return 'youtube';
        }


        return 'unknown';
    }


    /*
    |--------------------------------------------------------------------------
    | RESOLVE TIKTOK SHORT URL
    |--------------------------------------------------------------------------
    */

    private function resolveTikTokUrl(
        string $url
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Sudah canonical
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/\/video\/\d+/',
                $url
            )
        ) {

            return $url;
        }


        $host =
            strtolower(
                parse_url(
                    $url,
                    PHP_URL_HOST
                )
                    ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | Bukan short URL
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $host,
                [
                    'vt.tiktok.com',
                    'vm.tiktok.com'
                ],
                true
            )
        ) {

            return $url;
        }


        return Cache::remember(
            'tiktok_resolved_v12_'
                . md5($url),

            now()->addDays(7),

            function () use ($url) {

                try {

                    $effectiveUrl =
                        $url;


                    Http::timeout(15)

                        ->withHeaders([
                            'User-Agent' =>
                            $this
                                ->browserUserAgent(),
                        ])

                        ->withOptions([

                            'allow_redirects' => [
                                'max' => 10,
                            ],

                            'on_stats' =>
                            function (
                                TransferStats $stats
                            ) use (
                                &$effectiveUrl
                            ) {

                                $effectiveUrl =
                                    (string)
                                    $stats
                                        ->getEffectiveUri();
                            },
                        ])

                        ->get(
                            $url
                        );


                    return
                        $effectiveUrl
                        ?: $url;
                } catch (Throwable $e) {

                    return $url;
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EMBED URL
    |--------------------------------------------------------------------------
    */

    private function makeEmbedUrl(
        string $platform,
        string $url
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | YOUTUBE
        |--------------------------------------------------------------------------
        */

        if (
            $platform === 'youtube'
        ) {

            $videoId =
                $this->getYoutubeId(
                    $url
                );


            if (!$videoId) {
                return null;
            }


            return
                'https://www.youtube.com/embed/'
                . $videoId
                . '?rel=0';
        }


        /*
        |--------------------------------------------------------------------------
        | INSTAGRAM
        |--------------------------------------------------------------------------
        */

        if (
            $platform === 'instagram'
        ) {

            /*
            |--------------------------------------------------------------------------
            | Reel
            |--------------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/instagram\.com\/reel\/([^\/?#]+)/i',
                    $url,
                    $matches
                )
            ) {

                return
                    'https://www.instagram.com/reel/'
                    . $matches[1]
                    . '/embed/';
            }


            /*
            |--------------------------------------------------------------------------
            | Reels
            |--------------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/instagram\.com\/reels\/([^\/?#]+)/i',
                    $url,
                    $matches
                )
            ) {

                return
                    'https://www.instagram.com/reel/'
                    . $matches[1]
                    . '/embed/';
            }


            /*
            |--------------------------------------------------------------------------
            | Post
            |--------------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/instagram\.com\/p\/([^\/?#]+)/i',
                    $url,
                    $matches
                )
            ) {

                return
                    'https://www.instagram.com/p/'
                    . $matches[1]
                    . '/embed/';
            }


            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | TIKTOK
        |--------------------------------------------------------------------------
        */

        if (
            $platform === 'tiktok'
        ) {

            $videoId =
                $this->getTikTokVideoId(
                    $url
                );


            /*
            |--------------------------------------------------------------------------
            | Fallback dari oEmbed
            |--------------------------------------------------------------------------
            */

            if (!$videoId) {

                $oembed =
                    $this->getTikTokOEmbed(
                        $url
                    );


                if (
                    !empty($oembed['html'])
                    &&
                    preg_match(
                        '/data-video-id=["\'](\d+)["\']/i',
                        $oembed['html'],
                        $matches
                    )
                ) {

                    $videoId =
                        $matches[1];
                }
            }


            if (!$videoId) {
                return null;
            }


            return
                'https://www.tiktok.com/player/v1/'
                . $videoId;
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | TIKTOK OEMBED
    |--------------------------------------------------------------------------
    */

    private function getTikTokOEmbed(
        string $url
    ): array {

        return Cache::remember(
            'tiktok_oembed_v12_'
                . md5($url),

            now()->addMinutes(30),

            function () use ($url) {

                try {

                    $response =
                        Http::acceptJson()

                        ->timeout(15)

                        ->retry(
                            2,
                            300
                        )

                        ->withHeaders([
                            'User-Agent' =>
                            $this
                                ->browserUserAgent(),
                        ])

                        ->get(
                            'https://www.tiktok.com/oembed',
                            [
                                'url' =>
                                $url
                            ]
                        );


                    if (
                        !$response->successful()
                    ) {

                        return [];
                    }


                    return
                        $response->json()
                        ?? [];
                } catch (Throwable $e) {

                    return [];
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | YOUTUBE ID
    |--------------------------------------------------------------------------
    */

    private function getYoutubeId(
        string $url
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | Shorts
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/youtube\.com\/shorts\/([^?&\/]+)/i',
                $url,
                $matches
            )
        ) {

            return $matches[1];
        }


        /*
        |--------------------------------------------------------------------------
        | youtu.be
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/youtu\.be\/([^?&\/]+)/i',
                $url,
                $matches
            )
        ) {

            return $matches[1];
        }


        /*
        |--------------------------------------------------------------------------
        | watch?v=
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/[?&]v=([^&]+)/',
                $url,
                $matches
            )
        ) {

            return $matches[1];
        }


        /*
        |--------------------------------------------------------------------------
        | embed
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/youtube\.com\/embed\/([^?&\/]+)/i',
                $url,
                $matches
            )
        ) {

            return $matches[1];
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | YOUTUBE THUMBNAIL
    |--------------------------------------------------------------------------
    */

    private function getYoutubeThumbnail(
        string $url
    ): ?string {

        $videoId =
            $this->getYoutubeId(
                $url
            );


        if (!$videoId) {
            return null;
        }


        return
            'https://i.ytimg.com/vi/'
            . $videoId
            . '/hqdefault.jpg';
    }


    /*
    |--------------------------------------------------------------------------
    | TIKTOK VIDEO ID
    |--------------------------------------------------------------------------
    */

    private function getTikTokVideoId(
        string $url
    ): ?string {

        if (
            preg_match(
                '/\/video\/(\d+)/',
                $url,
                $matches
            )
        ) {

            return $matches[1];
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | USER AGENT
    |--------------------------------------------------------------------------
    */

    private function browserUserAgent(): string
    {
        return
            'Mozilla/5.0 '
            . '(Windows NT 10.0; Win64; x64) '
            . 'AppleWebKit/537.36 '
            . '(KHTML, like Gecko) '
            . 'Chrome/126.0.0.0 '
            . 'Safari/537.36';
    }


    /*
    |--------------------------------------------------------------------------
    | PLACEHOLDER
    |--------------------------------------------------------------------------
    */

    private function placeholderThumbnail(
        string $platform
    ) {

        $platform =
            htmlspecialchars(
                $platform,
                ENT_QUOTES,
                'UTF-8'
            );


        $svg = <<<SVG
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="720"
            height="1280"
            viewBox="0 0 720 1280">

            <defs>

                <linearGradient
                    id="bg"
                    x1="0"
                    y1="0"
                    x2="1"
                    y2="1">

                    <stop
                        offset="0%"
                        stop-color="#14532d"/>

                    <stop
                        offset="100%"
                        stop-color="#020617"/>

                </linearGradient>

            </defs>


            <rect
                width="720"
                height="1280"
                fill="url(#bg)"/>


            <circle
                cx="360"
                cy="570"
                r="90"
                fill="rgba(255,255,255,.12)"/>


            <polygon
                points="335,520 335,620 420,570"
                fill="#ffffff"/>


            <text
                x="360"
                y="720"
                text-anchor="middle"
                fill="#ffffff"
                font-family="Arial, sans-serif"
                font-size="36"
                font-weight="700">

                {$platform}

            </text>


            <text
                x="360"
                y="775"
                text-anchor="middle"
                fill="#ffffff"
                opacity=".60"
                font-family="Arial, sans-serif"
                font-size="22">

                Video Prakerin

            </text>

        </svg>
        SVG;


        return response(
            $svg,
            200
        )

            ->header(
                'Content-Type',
                'image/svg+xml'
            )

            ->header(
                'Cache-Control',
                'public, max-age=300'
            );
    }
}

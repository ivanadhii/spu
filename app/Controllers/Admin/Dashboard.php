<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use Myth\Auth\Models\LoginModel;
use Myth\Auth\Models\UserModel;
use App\Models\LinkModel;

class Dashboard extends Controller
{
   private function getDataLastTwelveMonths($model, $dateField)
    {
        $data = [];
        $endDate = new \DateTime();
        $startDate = (clone $endDate)->modify('-11 months')->modify('first day of this month');

        while ($startDate <= $endDate) {
            $monthKey = $startDate->format('Y-m');
            $monthStart = $startDate->format('Y-m-01');
            $monthEnd = $startDate->format('Y-m-t');

            $monthlyCount = $model->where($dateField . ' >=', $monthStart)
                ->where($dateField . ' <=', $monthEnd)
                ->countAllResults();

            $weeklyData = $this->getWeeklyDataForMonth($model, $dateField, $monthStart, $monthEnd);

            $data[$monthKey] = [
                'total' => $monthlyCount,
                'weeks' => $weeklyData
            ];

            $startDate->modify('+1 month');
        }

        return $data;
    }

    private function getWeeklyDataForMonth($model, $dateField, $monthStart, $monthEnd)
    {
        $weeklyData = [];
        $currentDate = new \DateTime($monthStart);
        $endDate = new \DateTime($monthEnd);

        $weekNumber = 1;
        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->format('Y-m-d');
            $weekEnd = (clone $currentDate)->modify('+6 days');
            if ($weekEnd > $endDate) {
                $weekEnd = $endDate;
            }
            $weekEnd = $weekEnd->format('Y-m-d');

            $weeklyData['Week ' . $weekNumber] = [
                'start' => $weekStart,
                'end' => $weekEnd,
                'data' => $this->getDailyDataForRange($model, $dateField, $weekStart, $weekEnd)
            ];

            $currentDate->modify('+7 days');
            $weekNumber++;
        }

        return $weeklyData;
    }

    private function getDailyDataForRange($model, $dateField, $startDate, $endDate)
    {
        $dailyData = [];
        $currentDate = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        while ($currentDate <= $end) {
            $date = $currentDate->format('Y-m-d');
            $count = $model->where($dateField . ' >=', $date . ' 00:00:00')
                ->where($dateField . ' <', $date . ' 23:59:59')
                ->countAllResults();

            $dailyData[$date] = $count;

            $currentDate->modify('+1 day');
        }

        return $dailyData;
    } 

    public function index()
    {
        // $data['title'] = 'Dashboard';
        $selectedDate = $this->request->getGet('log_date') ?? date('Y-m-d');
        $logFile = WRITEPATH . 'logs/log-' . $selectedDate . '.log';
        $logs = file_exists($logFile) ? file_get_contents($logFile) : 'Tidak ada logs hari ini.';

        $auth_logins = new LoginModel();
        $data['auth_logins'] = $auth_logins->where('success', 1)->countAllResults();

        $model = new UserModel();
        $data['users'] = $model->findAll();

        //  $model = new LinkModel();
        // $data['links'] = $model->findAll();

	$linkModel = new LinkModel();
        $data['total_links'] = $linkModel->getTotalLinks();

        $userModel = new UserModel();
        $users = $userModel->findAll();

	$inactiveUsers = 0;
        foreach ($users as $user) {
            if ($user->active == 0 || !empty($user->activate_hash)) {
                $inactiveUsers++;
            }
        }

        $data['users'] = $users;
        $data['inactiveUsersCount'] = $inactiveUsers;

	// log_message('info', 'Inactive Users: ' . $inactiveUsers);

        $unitOrganisasi = array_column($users, 'unit_organisasi');
        $unitKerja = array_column($users, 'unit_kerja');

        $countUnitOrganisasi = array_count_values($unitOrganisasi);
        $countUnitKerja = array_count_values($unitKerja);

	$userPerMonth = [];
        $urlsPerMonth = [];
        $currentDate = new \DateTime();

        $startDate = (clone $currentDate)->modify('-11 months');

        while ($startDate <= $currentDate) {
            $monthStart = $startDate->format('Y-m-01 00:00:00');
            $monthEnd = $startDate->format('Y-m-t 23:59:59');
            $monthKey = $startDate->format('F Y');

            $userPerMonth[$monthKey] = $userModel->where('created_at >=', $monthStart)
                ->where('created_at <=', $monthEnd)
                ->countAllResults();

            $urlsPerMonth[$monthKey] = $linkModel->where('created_at >=', $monthStart)
                ->where('created_at <=', $monthEnd)
                ->where('deleted_at IS NULL')
                ->countAllResults();

            $startDate->modify('+1 month');
        }

        $logCounts = [
            'emergency' => 0,
            'alert' => 0,
            'critical' => 0,
            'error' => 0,
            'warning' => 0,
            'notice' => 0,
            'info' => 0,
            'debug' => 0,
        ];

        if (file_exists($logFile)) {
            $lines = file($logFile);
            foreach ($lines as $line) {
                foreach ($logCounts as $level => $count) {
                    if (strpos($line, strtoupper($level)) !== false) {
                        $logCounts[$level]++;
                    }
                }
            }
        }

	$userModel = new UserModel();
        $linkModel = new LinkModel();

        $data['userDataMonthly'] = $this->getDataLastTwelveMonths($userModel, 'created_at');
        $data['urlDataMonthly'] = $this->getDataLastTwelveMonths($linkModel, 'created_at');

        $data = array_merge($data, [
            'unitOrganisasi' => $unitOrganisasi,
            'unitKerja' => $unitKerja,
            'countUnitOrganisasi' => $countUnitOrganisasi,
            'countUnitKerja' => $countUnitKerja,
            'userPerMonth' => $userPerMonth,
            'urlsPerMonth' => $urlsPerMonth,
            'logs' => $logs,
            'selectedDate' => $selectedDate,
            'logCounts' => $logCounts,
        ]);

	$unitOrganisasi = [
            "Setjen" => [
                "Biro Perencanaan Anggaran dan Kerja Sama Luar Negeri",
                "Biro Kepegawaian, Organisasi, dan Tata Laksana",
                "Biro Keuangan",
                "Biro Umum",
                "Biro Hukum",
                "Biro Pengelolaan Barang Milik Negara",
                "Biro Komunikasi Publik",
                "Pusat Analisis Pelaksanaan Kebijakan",
                "Pusat Data dan Teknologi Informasi",
                "Pusat Fasilitasi Infrastruktur Daerah"
            ],

            "Itjen" => [
                "Sekretariat Inspektorat Jenderal",
                "Inspektorat I",
                "Inspektorat II",
                "Inspektorat III",
                "Inspektorat IV",
                "Inspektorat V",
                "Inspektorat VI"
            ],

            "Ditjen Sumber Daya Air" => [
                "Sekretariat Direktorat Jenderal",
                "Direktorat Sistem dan Strategi Pengelolaan Sumber Daya Air",
                "Direktorat Sungai dan Pantai",
                "Direktorat Irigasi dan Rawa",
                "Direktorat Bendungan dan Danau",
                "Direktorat Air Tanah dan Air Baku",
                "Direktorat Bina Operasi dan Pemeliharaan",
                "Direktorat Bina Teknik Sumber Daya Air",
                "Direktorat Kepatuhan Intern",
                "Pusat Pengendalian Lumpur Sidoarjo",
                "Balai Besar Wilayah Sungai Sumatera VIII Palembang",
                "Balai Besar Wilayah Sungai Mesuji Sekampung",
                "Balai Besar Wilayah Sungai Cidanau, Ciujung, Cidurian",
                "Balai Besar Wilayah Sungai Ciliwung Cisadane",
                "Balai Besar Wilayah Sungai Citarum",
                "Balai Besar Wilayah Sungai Cimanuk Cisanggarung",
                "Balai Besar Wilayah Sungai Pemali Juana",
                "Balai Besar Wilayah Sungai Serayu Opak",
                "Balai Besar Wilayah Sungai Bengawan Solo",
                "Balai Besar Wilayah Sungai Brantas",
                "Balai Besar Wilayah Sungai Pompengan Jeneberang",
                "Balai Besar Wilayah Sungai Citanduy",
                "Balai Wilayah Sungai Sumatra I Banda Aceh",
                "Balai Wilayah Sungai Sumatra II Medan",
                "Balai Wilayah Sungai Sumatra III Pekanbaru",
                "Balai Wilayah Sungai Sumatra IV Batam",
                "Balai Wilayah Sungai Bangka Belitung",
                "Balai Wilayah Sungai Sumatra V Padang",
                "Balai Wilayah Sungai Sumatra VI Jambi",
                "Balai Wilayah Sungai Sumatra VII Bengkulu",
                "Balai Wilayah Sungai Bali Penida",
                "Balai Wilayah Sungai Nusa Tenggara I Mataram",
                "Balai Wilayah Sungai Nusa Tenggara II Kupang",
                "Balai Wilayah Sungai Kalimantan I Pontianak",
                "Balai Wilayah Sungai Kalimantan II Palangkaraya",
                "Balai Wilayah Sungai Kalimantan III Banjarmasin",
                "Balai Wilayah Sungai Kalimantan IV Samarinda",
                "Balai Wilayah Sungai Kalimantan V Tanjung Selor",
                "Balai Wilayah Sungai Sulawesi I Manado",
                "Balai Wilayah Sungai Sulawesi II Gorontalo",
                "Balai Wilayah Sungai Sulawesi III Palu",
                "Balai Wilayah Sungai Sulawesi IV Kendari",
                "Balai Wilayah Sungai Maluku",
                "Balai Wilayah Sungai Maluku Utara",
                "Balai Wilayah Sungai Papua",
                "Balai Wilayah Sungai Papua Barat",
                "Balai Wilayah Sungai Papua Marauke",
                "Balai Teknik Bendungan",
                "Balai Teknik Pantai",
                "Balai Teknik Sungai",
                "Balai Teknik Rawa",
                "Balai Teknik Irigasi",
                "Balai Teknik Sabo",
                "Balai Teknik Hidrolika dan Geoteknik Keairan",
                "Balai Air Tanah",
                "Balai Hidrologi Dan Lingkungan Keairan"
            ],

            "Ditjen Bina Marga" => [
                "Sekretariat Direktorat Jenderal",
                "Direktorat Sistem dan Strategi Pengelenggaraan Jalan dan Jembatan",
                "Subdirektorat Keterpaduan Sistem Jaringan Jalan dan Jembatan",
                "Direktorat Pembangunan Jalan",
                "Direktorat Pembangunan Jembatan",
                "Direktorat Preservasi Jalan dan Jembatan Wilayah I",
                "Direktorat Preservasi Jalan dan Jembatan Wilayah II",
                "Direktorat Jalan Bebas Hambatan",
                "Direktorat Bina Teknik Jalan dan Jembatan",
                "Direktorat Kepatuhan Intern",
                "Balai Besar Pelaksanaan Jalan Nasional Sumatera Utara",
                "Balai Besar Pelaksanaan Jalan Nasional Sumatera Selatan",
                "Balai Besar Pelaksanaan Jalan Nasional DKI Jakarta - Jawa Barat",
                "Balai Besar Pelaksanaan Jalan Nasional Jawa Tengah - DI Yogyakarta",
                "Balai Besar Pelaksanaan Jalan Nasional Jawa Timur - Bali",
                "Balai Besar Pelaksanaan Jalan Nasional Sulawesi Selatan",
                "Balai Besar Pelaksanaan Jalan Nasional Kalimantan Timur",
                "Balai Pelaksanaan Jalan Nasional Aceh",
                "Balai Pelaksanaan Jalan Nasional Riau",
                "Balai Pelaksanaan Jalan Nasional Kepulauan Riau",
                "Balai Pelaksanaan Jalan Nasional Sumatera Barat",
                "Balai Pelaksanaan Jalan Nasional Jambi",
                "Balai Pelaksanaan Jalan Nasional Bengkulu",
                "Balai Pelaksanaan Jalan Nasional Bangka Belitung",
                "Balai Pelaksanaan Jalan Nasional Lampung",
                "Balai Pelaksanaan Jalan Nasional Banten",
                "Balai Pelaksanaan Jalan Nasional Nusa Tenggara Barat",
                "Balai Pelaksanaan Jalan Nasional Nusa Tenggara Timur",
                "Balai Pelaksanaan Jalan Nasional Kalimantan Barat",
                "Balai Pelaksanaan Jalan Nasional Kalimantan Selatan",
                "Balai Pelaksanaan Jalan Nasional Kalimantan Utara",
                "Balai Pelaksanaan Jalan Nasional Kalimantan Tengah",
                "Balai Pelaksanaan Jalan Nasional Sulawesi Utara",
                "Balai Pelaksanaan Jalan Nasional Gorontalo",
                "Balai Pelaksanaan Jalan Nasional Sulawesi Tengah",
                "Balai Pelaksanaan Jalan Nasional Sulawesi Tenggara",
                "Balai Pelaksanaan Jalan Nasional Sulawesi Barat",
                "Balai Pelaksanaan Jalan Nasional Maluku",
                "Balai Pelaksanaan Jalan Nasional Maluku Utara",
                "Balai Pelaksanaan Jalan Nasional Jayapura",
                "Balai Pelaksanaan Jalan Nasional Merauke",
                "Balai Pelaksanaan Jalan Nasional Papua Barat",
                "Balai Pelaksanaan Jalan Nasional Wamena",
                "Balai Bahan Jalan",
                "Balai Jembatan",
                "Balai Geoteknik dan Terowongan, dan Struktur",
                "Balai Perkerasan dan Lingkungan Jalan"
            ],

            "Ditjen Cipta Karya" => [
                "Sekretariat Direktorat Jenderal",
                "Direktorat Sistem dan Strategi Pengelenggaraan Infrastruktur Permukiman",
                "Direktorat Bina Penataan Bangunan",
                "Direktorat Air Minum",
                "Direktorat Pembangunan Jembatan",
                "Direktorat Pengembangan Kawasan Permukiman",
                "Direktorat Sanitasi",
                "Direktorat Prasarana Strategis",
                "Direktorat Bina Teknik Permukiman dan Perumahan",
                "Direktorat Kepatuhan Intern",
                "Balai Prasarana Permukiman Wilayah Aceh",
                "Balai Prasarana Permukiman Wilayah Sumatera Utara",
                "Balai Prasarana Permukiman Wilayah Riau",
                "Balai Prasarana Permukiman Wilayah Kepulauan Riau",
                "Balai Prasarana Permukiman Wilayah Sumatera Barat",
                "Balai Prasarana Permukiman Wilayah Sumatera Selatan",
                "Balai Prasarana Permukiman Wilayah Lampung",
                "Balai Prasarana Permukiman Wilayah Banten",
                "Balai Prasarana Permukiman Wilayah Jakarta Metropolitan",
                "Balai Prasarana Permukiman Wilayah Jawa Barat",
                "Balai Prasarana Permukiman Wilayah Jawa Tengah",
                "Balai Prasarana Permukiman Wilayah D.I. Yogyakarta",
                "Balai Prasarana Permukiman Wilayah Jawa Timur",
                "Balai Prasarana Permukiman Wilayah Bali",
                "Balai Prasarana Permukiman Wilayah Nusa Tenggara Barat",
                "Balai Prasarana Permukiman Wilayah Nusa Tenggara Timur",
                "Balai Prasarana Permukiman Wilayah Kalimantan Barat",
                "Balai Prasarana Permukiman Wilayah Kalimantan Selatan",
                "Balai Prasarana Permukiman Wilayah Kalimantan Tengah",
                "Balai Prasarana Permukiman Wilayah Kalimantan Timur",
                "Balai Prasarana Permukiman Wilayah Kalimantan Utara",
                "Balai Prasarana Permukiman Wilayah Sulawesi Utara",
                "Balai Prasarana Permukiman Wilayah Sulawesi Tenggara",
                "Balai Prasarana Permukiman Wilayah Sulawesi Tengah",
                "Balai Prasarana Permukiman Wilayah Sulawesi Selatan",
                "Balai Prasarana Permukiman Wilayah Papua",
                "Balai Prasarana Permukiman Wilayah Papua Barat",
                "Balai Prasarana Permukiman Wilayah Bengkulu",
                "Balai Prasarana Permukiman Wilayah Bangka Belitung",
                "Balai Prasarana Permukiman Wilayah Jambi",
                "Balai Prasarana Permukiman Wilayah Gorontalo",
                "Balai Prasarana Permukiman Wilayah Sulawesi Barat",
                "Balai Prasarana Permukiman Wilayah Maluku",
                "Balai Prasarana Permukiman Wilayah Maluku Utara",
                "Balai Teknologi Air Minum",
                "Balai Teknologi Sanitasi",
                "Balai Sains Bangunan",
                "Balai Bahan dan Struktur Bangunan Gedung",
                "Balai Kawasan Permukiman dan Perumahan"
            ],

            "Ditjen Perumahan" => [
                "Sekretariat Direktorat Jenderal",
                "Direktorat Sistem dan Strategi Pengelenggaraan Perumahan",
                "Direktorat Rumah Umum dan Komersial",
                "Direktorat Rumah Swadaya",
                "Direktorat Rumah Susun",
                "Direktorat Rumah Khusus",
                "Direktorat Kepatuhan Intern",
                "Balai Pelaksana Penyediaan Perumahan Sumatera II",
                "Balai Pelaksana Penyediaan Perumahan Sumatera III",
                "Balai Pelaksana Penyediaan Perumahan Sumatera IV",
                "Balai Pelaksana Penyediaan Perumahan Sumatera V",
                "Balai Pelaksana Penyediaan Perumahan Jawa I",
                "Balai Pelaksana Penyediaan Perumahan Jawa II",
                "Balai Pelaksana Penyediaan Perumahan Jawa III",
                "Balai Pelaksana Penyediaan Perumahan Jawa IV",
                "Balai Pelaksana Penyediaan Perumahan Kalimantan I",
                "Balai Pelaksana Penyediaan Perumahan Kalimantan II",
                "Balai Pelaksana Penyediaan Perumahan Sulawesi I",
                "Balai Pelaksana Penyediaan Perumahan Sulawesi I",
                "Balai Pelaksana Penyediaan Perumahan Sulawesi II",
                "Balai Pelaksana Penyediaan Perumahan Sulawesi III",
                "Balai Pelaksana Penyediaan Perumahan Maluku",
                "Balai Pelaksana Penyediaan Perumahan Papua I",
                "Balai Pelaksana Penyediaan Perumahan Sumatera I",
                "Balai Pelaksana Penyediaan Perumahan Nusa Tenggara I",
                "Balai Pelaksana Penyediaan Perumahan Nusa Tenggara II",
                "Balai Pelaksana Penyediaan Perumahan Papua II"
            ],

            "Ditjen Bina Konstruksi" => [
                "Sekretariat Direktorat Jenderal",
                "Direktorat Pengembangan Jasa Konstruksi",
                "Direktorat Kelembagaan dan Sumber Daya Konstruksi",
                "Direktorat Kompentensi dan Produktivitas Konstruksi",
                "Direktorat Pengadaan Jasa Konstruksi",
                "Direktorat Keberlanjutan Konstruksi",
                "Balai Jasa Konstruksi Wilayah I Aceh",
                "Balai Jasa Konstruksi Wilayah II Palembang",
                "Balai Jasa Konstruksi Wilayah III Jakarta",
                "Balai Jasa Konstruksi Wilayah IV Surabaya",
                "Balai Jasa Konstruksi Wilayah V Banjarmasin",
                "Balai Jasa Konstruksi Wilayah VI Makassar",
                "Balai Jasa Konstruksi Wilayah VII Jayapura",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Aceh",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sumatera Utara",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sumatera Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sumatera Selatan",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Jambi",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Lampung",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Banten",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah DKI Jakarta",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Jawa Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah D.I. Yogyakarta",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Jawa Tengah",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Jawa Timur",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Bali",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Nusa Tenggara Timur",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Nusa Tenggara Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Kalimantan Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Kalimantan Selatan",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Kalimantan Tengah",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Kalimantan Timur",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Kalimantan Utara",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sulawesi Utara",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sulawesi Tenggara",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sulawesi Tengah",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sulawesi Selatan",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Papua",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Papua Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Riau",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Kepulauan Papua",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Bengkulu",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Bangka Belitung",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Gorontalo",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sulawesi Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Sulawesi Barat",
                "Balai Pelaksana Pemilihan Jasa Konstruksi Wilayah Maluku Utara"
            ],

            "Ditjen Pembiayaan Infrastruktur Pekerjaan Umum dan Perumahan" => [
                "Sekretariat Direktorat Jenderal",
                "Direktorat Pengembangan Sistem dan Strategi Penyelenggaraan Pembiayaan",
                "Direktorat Pelaksanaan Pembiayaan Infrastruktur Sumber Daya Air",
                "Direktorat Pelaksanaan Pembiayaan Infrastruktur Jalan dan Jembatan",
                "Direktorat Pelaksanaan Pembiayaan Infrastruktur Permukiman",
                "Direktorat Pelaksanaan Pembiayaan Perumahan"
            ],

            "BPIW" => [
                "Sekretariat Badan",
                "Pusat Pengembangan Infrastruktur Wilayah Nasional",
                "Pusat Pengembangan Infrastruktur Pekerjaan Umum dan Perumahan Rakyat Wilayah I",
                "Pusat Pengembangan Infrastruktur Pekerjaan Umum dan Perumahan Rakyat Wilayah II",
                "Pusat Pengembangan Infrastruktur Pekerjaan Umum dan Perumahan Rakyat Wilayah III"
            ],

            "BPSDM" => [
                "Sekretariat Badan",
                "Pusat Pengembangan Talenta",
                "Pusat Pengembangan Kompetensi Sumber Daya Air dan Permukaan",
                "Pusat Pengembangan Kompetensi Jalan, Perumahan, dan Pengembangan Infrastruktur Wilayah",
                "Pusat Pengembangan Kompetensi Manajemen",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah I Medan",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah II Palembang",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah III Jakarta",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah IV Bandung",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah V Yogyakarta",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah VI Surabaya",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah VII Banjarmasin",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah VIII Makassar",
                "Balai Pengembangan Kompetensi Pekerjaan Umum dan Perumahan Rakyat Wilayah IX Jayapura",
                "Balai Penilaian Kompetensi"
            ],

            "BPJT" => ["Sekretariat BPJT", "Bagian Hukum", "Bidang Teknik", "Bagian Investasi", "Bidang Operasi dan Pemeliharaan", "Bidang Pendanaan", "Kelompok Jabatan Fungsional"]
        ];

        $userModel = new UserModel();
        $users = $userModel->findAll();

        $countUnitKerja = array_count_values(array_column($users, 'unit_kerja'));

        $data['countUnitKerja'] = $countUnitKerja;
        $data['unitOrganisasi'] = $unitOrganisasi;

	log_message('debug', 'Data yang dikirim ke view: ' . json_encode($data));

        return view('admin/index', $data);
    }

    public function getInactiveUsers()
    {
        $userModel = new UserModel();
        $inactiveUsers = $userModel->where('active', 0)->orWhere('activate_hash IS NOT NULL', null, false)->findAll();

        $data = [];
        foreach ($inactiveUsers as $index => $user) {
            $data[] = [
                'no' => $index + 1,
                'email' => $user->email,
                'username' => $user->username,
                'fullname' => $user->fullname,
                'unit_organisasi' => $user->unit_organisasi,
                'unit_kerja' => $user->unit_kerja
            ];
        }
        return $this->response->setJSON($data);
    }
}

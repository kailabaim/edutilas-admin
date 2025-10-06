<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataWargaSekolahController extends Controller
{
    /**
     * Display the main data warga sekolah page
     */
    public function index()
    {
        // Get statistics for dashboard cards
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Guru::where('is_active', true)->count(), 
            'total_classes' => Kelas::where('is_active', true)->count()
        ];

        return view('dashboard.data-warga-sekolah', compact('stats'));
    }

    /**
     * Get students data with filters (AJAX)
     */
    public function getStudents(Request $request)
    {
        $search = $request->get('search');
        $filterClass = $request->get('filterClass');
        $filterMajor = $request->get('filterMajor');

        $query = Student::with('kelas')
                         ->where('is_active', true);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%");
            });
        }

        // Apply class filter
        if ($filterClass) {
            $query->whereHas('kelas', function($q) use ($filterClass) {
                $q->where('grade', $filterClass);
            });
        }

        // Apply major filter  
        if ($filterMajor) {
            $query->whereHas('kelas', function($q) use ($filterMajor) {
                $q->where('major', $filterMajor);
            });
        }

        $students = $query->orderBy('full_name')->get();

        // Format data for response
        $formattedStudents = $students->map(function($student, $index) {
            return [
                'no' => $index + 1,
                'nis' => $student->nis,
                'nama_lengkap' => $student->full_name,
                'kelas' => $student->kelas ? $student->kelas->class_name : '-',
                'jurusan' => $student->kelas ? $this->getMajorName($student->kelas->major) : '-',
                'status' => $student->is_active ? 'Aktif' : 'Tidak Aktif',
                'status_class' => $student->is_active ? 'active' : 'inactive'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedStudents
        ]);
    }

    /**
     * Get teachers data with filters (AJAX)
     */
    public function getTeachers(Request $request)
    {
        $search = $request->get('search');

        $query = Guru::where('is_active', true);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_guru', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('nama_guru')->get();

        // Format data for response
        $formattedTeachers = $teachers->map(function($teacher, $index) {
            return [
                'no' => $index + 1,
                'nip' => $teacher->nip,
                'nama_guru' => $teacher->nama_guru,
                'status' => $teacher->is_active ? 'Aktif' : 'Tidak Aktif',
                'status_class' => $teacher->is_active ? 'active' : 'inactive'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedTeachers
        ]);
    }

    /**
     * Get updated statistics (AJAX)
     */
    public function getStats()
    {
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Guru::where('is_active', true)->count(),
            'total_classes' => Kelas::where('is_active', true)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get class options for filter dropdown
     */
    public function getClassOptions()
    {
        $classes = DB::table('kelas')
                    ->select('grade')
                    ->where('is_active', true)
                    ->groupBy('grade')
                    ->orderBy('grade')
                    ->get()
                    ->map(function($item) {
                        return [
                            'value' => $item->grade,
                            'label' => 'Kelas ' . $item->grade
                        ];
                    });

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Get major options for filter dropdown
     */
    public function getMajorOptions()
    {
        $majors = DB::table('kelas')
                   ->select('major')
                   ->where('is_active', true)
                   ->groupBy('major')
                   ->orderBy('major')
                   ->get()
                   ->map(function($item) {
                       return [
                           'value' => $item->major,
                           'label' => $this->getMajorName($item->major)
                       ];
                   });

        return response()->json([
            'success' => true,
            'data' => $majors
        ]);
    }

    /**
     * Export data (CSV/Excel) dengan filter yang diterapkan
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'students');
        $format = $request->get('format', 'csv');
        
        if ($type === 'students') {
            return $this->exportStudents($request);
        } else {
            return $this->exportTeachers($request);
        }
    }

    /**
     * Export students data dengan filter
     */
    private function exportStudents(Request $request)
    {
        // Ambil filter yang sama dengan yang digunakan di getStudents
        $search = $request->get('search');
        $filterClass = $request->get('filterClass');
        $filterMajor = $request->get('filterMajor');

        $query = Student::with('kelas')->where('is_active', true);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%");
            });
        }

        // Apply class filter
        if ($filterClass) {
            $query->whereHas('kelas', function($q) use ($filterClass) {
                $q->where('grade', $filterClass);
            });
        }

        // Apply major filter  
        if ($filterMajor) {
            $query->whereHas('kelas', function($q) use ($filterMajor) {
                $q->where('major', $filterMajor);
            });
        }

        $students = $query->orderBy('full_name')->get();

        $filename = 'data_siswa_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (agar karakter Indonesia tampil dengan benar)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers dengan format yang lebih rapi
            fputcsv($file, ['No', 'NIS', 'Nama Lengkap', 'Kelas', 'Jurusan', 'Status']);
            
            // Data rows
            foreach ($students as $index => $student) {
                $rowData = [
                    $index + 1,
                    $student->nis ?? '-',
                    $student->full_name ?? '-',
                    $student->kelas ? $student->kelas->class_name : '-',
                    $student->kelas ? $this->getMajorName($student->kelas->major) : '-',
                    $student->is_active ? 'Aktif' : 'Tidak Aktif'
                ];
                
                fputcsv($file, $rowData);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export teachers data dengan filter
     */
    private function exportTeachers(Request $request)
    {
        // Ambil filter yang sama dengan yang digunakan di getTeachers
        $search = $request->get('search');

        $query = Guru::where('is_active', true);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_guru', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('nama_guru')->get();

        $filename = 'data_guru_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($teachers) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (agar karakter Indonesia tampil dengan benar)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers dengan format yang lebih rapi
            fputcsv($file, ['No', 'NIP', 'Nama Guru', 'Status']);
            
            // Data rows
            foreach ($teachers as $index => $teacher) {
                $rowData = [
                    $index + 1,
                    $teacher->nip ?? '-',
                    $teacher->nama_guru ?? '-',
                    $teacher->is_active ? 'Aktif' : 'Tidak Aktif'
                ];
                
                fputcsv($file, $rowData);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get major name from code
     */
    private function getMajorName($major)
    {
        $majors = [
            'KA' => 'Kimia Analis',
            'RPL' => 'Rekayasa Perangkat Lunak', 
            'TKJ' => 'Teknik Komputer dan Jaringan'
        ];

        return $majors[$major] ?? $major;
    }

    /**
     * Get detailed student data
     */
    public function getStudentDetail($id)
    {
        $student = Student::with('kelas')->find($id);
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nis' => $student->nis,
                'nama_lengkap' => $student->full_name,
                'kelas' => $student->kelas ? $student->kelas->class_name : '-',
                'jurusan' => $student->kelas ? $this->getMajorName($student->kelas->major) : '-',
                'status' => $student->is_active ? 'Aktif' : 'Tidak Aktif',
                'created_at' => $student->created_at->format('d/m/Y H:i:s'),
                'updated_at' => $student->updated_at->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Get detailed teacher data
     */
    public function getTeacherDetail($id)
    {
        $teacher = Guru::find($id);
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nip' => $teacher->nip,
                'nama_guru' => $teacher->nama_guru,
                'status' => $teacher->is_active ? 'Aktif' : 'Tidak Aktif',
                'created_at' => $teacher->created_at->format('d/m/Y H:i:s'),
                'updated_at' => $teacher->updated_at->format('d/m/Y H:i:s')
            ]
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'isbn' => '978-602-8519-93-9',
                'title' => 'Pemrograman Web dengan Laravel',
                'author' => 'Ahmad Rizki',
                'publisher' => 'Informatika',
                'publication_year' => 2023,
                'category' => 'Teknologi',
                'description' => 'Buku panduan lengkap pemrograman web menggunakan framework Laravel',
                'stock' => 5,
                'available_stock' => 3,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8519-94-6',
                'title' => 'Database Management System',
                'author' => 'Siti Nurhaliza',
                'publisher' => 'Erlangga',
                'publication_year' => 2022,
                'category' => 'Teknologi',
                'description' => 'Konsep dan implementasi sistem manajemen database',
                'stock' => 3,
                'available_stock' => 2,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8519-95-3',
                'title' => 'Algoritma dan Struktur Data',
                'author' => 'Budi Santoso',
                'publisher' => 'Andi Offset',
                'publication_year' => 2021,
                'category' => 'Teknologi',
                'description' => 'Dasar-dasar algoritma dan struktur data dalam pemrograman',
                'stock' => 4,
                'available_stock' => 1,
                'status' => 'borrowed'
            ],
            [
                'isbn' => '978-602-8519-96-0',
                'title' => 'Sejarah Indonesia Modern',
                'author' => 'Prof. Dr. Anwar',
                'publisher' => 'Gramedia',
                'publication_year' => 2020,
                'category' => 'Sejarah',
                'description' => 'Sejarah perkembangan Indonesia dari masa kemerdekaan hingga sekarang',
                'stock' => 6,
                'available_stock' => 4,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8519-97-7',
                'title' => 'Matematika Dasar',
                'author' => 'Dr. Sari Indah',
                'publisher' => 'Erlangga',
                'publication_year' => 2023,
                'category' => 'Pendidikan',
                'description' => 'Buku matematika dasar untuk mahasiswa',
                'stock' => 8,
                'available_stock' => 6,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8519-98-4',
                'title' => 'Fisika Modern',
                'author' => 'Prof. Dr. Joko',
                'publisher' => 'Informatika',
                'publication_year' => 2022,
                'category' => 'Sains',
                'description' => 'Konsep-konsep fisika modern dan aplikasinya',
                'stock' => 3,
                'available_stock' => 0,
                'status' => 'borrowed'
            ],
            [
                'isbn' => '978-602-8519-99-1',
                'title' => 'Novel Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'publication_year' => 2005,
                'category' => 'Fiksi',
                'description' => 'Novel inspiratif tentang perjuangan anak-anak di Belitung',
                'stock' => 10,
                'available_stock' => 7,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8520-00-4',
                'title' => 'Bisnis dan Kewirausahaan',
                'author' => 'Dr. Rina Wijaya',
                'publisher' => 'Salemba Empat',
                'publication_year' => 2023,
                'category' => 'Bisnis',
                'description' => 'Panduan lengkap memulai dan mengembangkan bisnis',
                'stock' => 5,
                'available_stock' => 3,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8520-01-1',
                'title' => 'Psikologi Perkembangan',
                'author' => 'Dr. Maya Sari',
                'publisher' => 'Rajawali Pers',
                'publication_year' => 2021,
                'category' => 'Psikologi',
                'description' => 'Studi tentang perkembangan manusia dari masa kanak-kanak hingga dewasa',
                'stock' => 4,
                'available_stock' => 2,
                'status' => 'available'
            ],
            [
                'isbn' => '978-602-8520-02-8',
                'title' => 'Buku Rusak - Contoh',
                'author' => 'Penulis Contoh',
                'publisher' => 'Penerbit Contoh',
                'publication_year' => 2020,
                'category' => 'Contoh',
                'description' => 'Buku contoh dengan status rusak',
                'stock' => 1,
                'available_stock' => 0,
                'status' => 'damaged'
            ]
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}

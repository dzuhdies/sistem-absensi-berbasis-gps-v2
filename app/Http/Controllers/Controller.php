<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected const ABSENSI_PHOTO_DISK = 'public';
    protected const ABSENSI_PHOTO_DIRECTORY = 'absen';
    protected const ABSENSI_PHOTO_PUBLIC_PREFIX = 'storage';

    /**
     * Satu-satunya tempat untuk menentukan URL publik foto absensi.
     */
    protected function setAbsensiPhotoUrls($absensi): void
    {
        if (!$absensi) {
            return;
        }

        $absensi->foto_masuk_url = $this->absensiPhotoUrl($absensi->foto_masuk);
        $absensi->foto_keluar_url = $this->absensiPhotoUrl($absensi->foto_keluar);
    }

    protected function setStudentAbsensiPhotoUrls(iterable $students): void
    {
        foreach ($students as $student) {
            foreach ($student->absensis as $absensi) {
                $this->setAbsensiPhotoUrls($absensi);
            }
        }
    }

    protected function absensiPhotoUrl(?string $filename): ?string
    {
        return $filename
            ? asset(
                trim(self::ABSENSI_PHOTO_PUBLIC_PREFIX, '/') . '/' .
                $this->absensiPhotoPath($filename)
            )
            : null;
    }

    protected function absensiPhotoPath(string $filename): string
    {
        return trim(self::ABSENSI_PHOTO_DIRECTORY, '/') . '/' . ltrim($filename, '/');
    }
}

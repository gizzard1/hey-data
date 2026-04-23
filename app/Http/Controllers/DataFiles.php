<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataFiles extends Controller
{
    public static function updateOrCreateFile($formData, $model_id)
    {
        try {
            if (!$formData || !is_array($formData)) {
                return response()->json(['msg' => 'file_upload_error'], 400);
            }

            DB::beginTransaction();

            $keptIds = [];
            foreach ($formData as $file) {
                $ext = self::getMimeToExtension($file['mime_type'] ?? '');
                $path = 'public/' . self::getTypeName($file['model_type']) . '/' . ($file['file'] . $ext ?? '');
                $updatedFile = File::updateOrCreate(
                    ['id' => $file['id'] ?? null],
                    [
                        'file' => self::fileExists($path) ? $file['file'] . $ext : uniqid() . '_' . $ext,
                        'model_type' => $file['model_type'],
                        'model_id' => $model_id,
                    ]
                );
                $base64Content = $file['base64'] ?? null;
                
                if ($base64Content) {
                    $imageDecoded = base64_decode($base64Content);
                    Storage::put('public/' . self::getTypeName($updatedFile->model_type) . '/' . $updatedFile->file, $imageDecoded);
                }

                $keptIds[] = $updatedFile->id;
            }
            // Eliminar archivos no incluidos en la solicitud
            File::where('model_id', $model_id)
                ->whereNotIn('id', $keptIds)
                ->each(function ($file) {
                    Storage::delete('public/' . self::getTypeName($file->model_type) . '/' . $file->file);
                    $file->delete();
                });
            DB::commit();
            return response()->json(['msg' => 'Archivo guardado exitosamente'], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollBack();
            return response()->json(['msg' => 'Error al guardar el archivo'], 500);
        }
    }
    private static function getTypeName($model_type)
    {
        $typeMap = [
            'App\\Models\\producto' => 'productos',
            'App\\Models\\servicio' => 'servicios',
        ];

        return $typeMap[$model_type] ?? null;
    }
    private static function fileExists($fileName)
    {
        return Storage::exists($fileName);
    }
    private static function getMimeToExtension($mimeType)
    {
        $mimeMap = [
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
        ];

        return $mimeMap[$mimeType] ?? null;
    }
}

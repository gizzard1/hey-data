<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\DataResourceGrid as DRG;

class DataSalon extends Controller
{
    public static function loadSalon(Request $request)
    {
        try {
            return Salon::select('id', 'name', 'email', 'webPage', 'facebook', 'instagram', 'youtube', 'tiktok', 'phone', 'logoFile', 'rfc', 'start', 'end')
                ->with('file')
                ->find($request->user()->salon_id);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function isUniqueSalonEmail(Request $request)
    {
        try {
            $salonId = $request->user()->salon_id;
            $email = $request->input('email');

            $exists = Salon::where('email', $email)
                ->where('id', '!=', $salonId)
                ->exists();
            return ['is_unique' => !$exists];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function updateSalonData(Request $request)
    {
        try {
            $salonId = $request->user()->salon_id;
            $data = $request->input('business');

            $salon = Salon::find($salonId);
            if ($salon) {
                $salon->updateOrCreate(['id' => $salonId], [
                    'name' => $data['name'],
                    'email' => $data['email'] ?? null,
                    'webPage' => $data['webPage'] ?? null,
                    'facebook' => $data['facebook'] ?? null,
                    'instagram' => $data['instagram'] ?? null,
                    'youtube' => $data['youtube'] ?? null,
                    'tiktok' => $data['tiktok'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'logoFile' => $data['logoFile'] ?? null,
                    'rfc' => $data['rfc'] ?? null,
                    'start' => $data['start'],
                    'end' => $data['end'],
                ]);
                return ['success' => true, 'message' => 'Salon data updated successfully.', 'salon' => $salon];
            } else {
                return ['success' => false, 'message' => 'Salon not found.'];
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating salon data.'];
        }
    }
    public static function loadRewardPoints(Request $request)
    {
        try {
            $item_id = $request->input('item_id');
            $total = $request->input('total', 0);
            $is_service = $request->input('is_service', true);

            return DRG::calculateRewardPoints($item_id, $is_service, $total, $request);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function createDefaultSalon($invitation_code = null)
    {
        try {
            $salon = new Salon;
            $salon->start = "08:00:00";
            $salon->end = "17:00:00";
            $salon->invitation_code = $invitation_code;
            $salon->save();
            return $salon;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}

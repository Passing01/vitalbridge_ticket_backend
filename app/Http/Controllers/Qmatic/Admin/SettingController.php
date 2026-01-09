<?php

namespace App\Http\Controllers\Qmatic\Admin;

use App\Http\Controllers\Controller;
use App\Models\QmaticSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        $settings = [
            'structure_name' => QmaticSetting::get($healthCenterId, 'structure_name', $user->first_name . ' ' . $user->last_name),
            'structure_logo' => QmaticSetting::get($healthCenterId, 'structure_logo'),
            'primary_color' => QmaticSetting::get($healthCenterId, 'primary_color', '#2563eb'),
            'welcome_message' => QmaticSetting::get($healthCenterId, 'welcome_message', 'Bienvenue dans notre établissement'),
            'ticket_footer' => QmaticSetting::get($healthCenterId, 'ticket_footer', 'Merci de votre patience'),
            'display_announcement' => QmaticSetting::get($healthCenterId, 'display_announcement', 'Veuillez vous diriger vers le guichet indiqué'),
            'announcement_language' => QmaticSetting::get($healthCenterId, 'announcement_language', 'fr-FR'),
            'announcement_gender' => QmaticSetting::get($healthCenterId, 'announcement_gender', 'female'),
            'announcement_multi_lang' => QmaticSetting::get($healthCenterId, 'announcement_multi_lang', '0'),
            'announcement_template' => QmaticSetting::get($healthCenterId, 'announcement_template', 'Ticket {ticket}, au guichet {counter}'),
            'display_layout' => QmaticSetting::get($healthCenterId, 'display_layout', 'sidebar_right'),
            'display_bg_color' => QmaticSetting::get($healthCenterId, 'display_bg_color', '#111827'),
            'display_secondary_color' => QmaticSetting::get($healthCenterId, 'display_secondary_color', '#1f2937'),
            'display_text_color' => QmaticSetting::get($healthCenterId, 'display_text_color', '#ffffff'),
            'kiosk_bg_color' => QmaticSetting::get($healthCenterId, 'kiosk_bg_color', '#f3f4f6'),
            'kiosk_card_bg_color' => QmaticSetting::get($healthCenterId, 'kiosk_card_bg_color', '#ffffff'),
            'kiosk_text_color' => QmaticSetting::get($healthCenterId, 'kiosk_text_color', '#111827'),
            'kiosk_layout' => QmaticSetting::get($healthCenterId, 'kiosk_layout', 'grid'),
            'local_languages' => QmaticSetting::get($healthCenterId, 'local_languages', '[]'),
            'template_moore' => QmaticSetting::get($healthCenterId, 'template_moore', 'Ticket {ticket}, guichet {counter} nênga'),
            'template_dioula' => QmaticSetting::get($healthCenterId, 'template_dioula', 'Ticket {ticket}, ka taga guichet {counter} la'),
        ];

        return view('qmatic.admin.settings.index', compact('settings', 'healthCenterId'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;

        $validated = $request->validate([
            'structure_name' => 'required|string|max:255',
            'structure_logo' => 'nullable|image|max:2048',
            'primary_color' => 'required|string|max:7',
            'welcome_message' => 'nullable|string|max:500',
            'ticket_footer' => 'nullable|string|max:255',
            'display_announcement' => 'nullable|string|max:255',
            'announcement_language' => 'required|string|max:10',
            'announcement_gender' => 'required|string|in:male,female',
            'announcement_multi_lang' => 'required|boolean',
            'announcement_template' => 'required|string|max:255',
            'display_layout' => 'required|string|in:sidebar_right,sidebar_left,compact_bottom',
            'display_bg_color' => 'required|string|max:7',
            'display_secondary_color' => 'required|string|max:7',
            'display_text_color' => 'required|string|max:7',
            'kiosk_bg_color' => 'required|string|max:7',
            'kiosk_card_bg_color' => 'required|string|max:7',
            'kiosk_text_color' => 'required|string|max:7',
            'kiosk_layout' => 'required|string|in:grid,list,large_cards',
            'local_languages' => 'nullable|string',
            'template_moore' => 'nullable|string|max:255',
            'template_dioula' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'structure_logo' && $request->hasFile('structure_logo')) {
                $path = $request->file('structure_logo')->store('qmatic/logos', 'public');
                QmaticSetting::set($healthCenterId, $key, Storage::url($path));
                continue;
            }
            QmaticSetting::set($healthCenterId, $key, $value);
        }

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}

@extends('layouts.qmatic')

@section('title', 'Impression Ticket - Qmatic')

@section('content')
@php
    $healthCenterId = $ticket->health_center_id;
    $settings = [
        'name' => \App\Models\QmaticSetting::get($healthCenterId, 'structure_name', 'VitalBridge Qmatic'),
        'logo' => \App\Models\QmaticSetting::get($healthCenterId, 'structure_logo'),
        'color' => \App\Models\QmaticSetting::get($healthCenterId, 'primary_color', '#2563eb'),
        'footer' => \App\Models\QmaticSetting::get($healthCenterId, 'ticket_footer', 'Merci de votre patience'),
    ];
@endphp

<div class="flex flex-col items-center justify-center min-h-[60vh] no-print">
    <div class="qmatic-card p-10 text-center shadow-2xl border-b-8" style="border-color: {{ $settings['color'] }}">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-50 text-blue-600 rounded-full mb-6 animate-pulse">
            <i class="fas fa-ticket-alt text-5xl" style="color: {{ $settings['color'] }}"></i>
        </div>
        <h2 class="text-4xl font-black text-gray-900 mb-4">Ticket Imprimé !</h2>
        <p class="text-xl text-gray-600 mb-2">Votre numéro est le :</p>
        <div class="text-6xl font-black mb-6" style="color: {{ $settings['color'] }}">{{ $ticket->ticket_number }}</div>
        <p class="text-gray-500">Merci de patienter, vous allez être redirigé...</p>
    </div>
</div>

<!-- Le ticket réel (caché à l'écran, visible à l'impression) -->
<div id="printable-ticket" class="print-only">
    <div class="ticket-container">
        @if($settings['logo'])
            <img src="{{ $settings['logo'] }}" alt="Logo" class="ticket-logo">
        @endif
        <h1 class="ticket-structure">{{ $settings['name'] }}</h1>
        
        <div class="ticket-divider"></div>
        
        <div class="ticket-label">TICKET</div>
        <div class="ticket-number">{{ $ticket->ticket_number }}</div>
        <div class="ticket-service">{{ $ticket->service->name }}</div>
        
        <div class="ticket-divider"></div>
        
        <div class="ticket-info">
            <div>Date: {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
            <div>Position: {{ \App\Models\QmaticTicket::where('service_id', $ticket->service_id)->where('status', 'waiting')->where('created_at', '<=', $ticket->created_at)->count() }}</div>
        </div>
        
        <div class="ticket-footer">
            {{ $settings['footer'] }}
        </div>
        
        <div class="ticket-barcode">
            *{{ $ticket->ticket_number }}*
        </div>
    </div>
</div>

<style>
    /* Styles pour l'écran */
    @media screen {
        .print-only { display: none; }
    }

    /* Styles pour l'impression */
    @media print {
        /* Cacher tout ce qui n'est pas le ticket */
        body * { visibility: hidden; margin: 0; padding: 0; }
        nav, footer, .no-print { display: none !important; }
        
        #printable-ticket, #printable-ticket * { visibility: visible; }
        
        #printable-ticket {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm; /* Largeur standard imprimante thermique */
            padding: 5mm;
            background: white;
        }

        .ticket-container {
            text-align: center;
            font-family: 'Courier New', Courier, monospace;
            color: black;
        }

        .ticket-logo {
            max-width: 40mm;
            max-height: 20mm;
            margin-bottom: 5mm;
            filter: grayscale(1); /* Impression noir et blanc */
        }

        .ticket-structure {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        .ticket-divider {
            border-top: 1px dashed black;
            margin: 4mm 0;
        }

        .ticket-label {
            font-size: 10pt;
            letter-spacing: 2mm;
        }

        .ticket-number {
            font-size: 48pt;
            font-weight: 900;
            margin: 2mm 0;
        }

        .ticket-service {
            font-size: 12pt;
            font-weight: bold;
        }

        .ticket-info {
            font-size: 9pt;
            text-align: left;
            margin-bottom: 4mm;
        }

        .ticket-footer {
            font-size: 8pt;
            font-style: italic;
            margin-bottom: 5mm;
        }

        .ticket-barcode {
            font-family: 'Libre Barcode 39', cursive; /* Si disponible, sinon texte simple */
            font-size: 24pt;
        }

        /* Supprimer les marges de page forcées par le navigateur */
        @page {
            margin: 0;
            size: 80mm auto;
        }
    }
</style>

<script>
    window.onload = function() {
        // Lancer l'impression
        window.print();
        
        // Rediriger après un court délai (3 secondes après l'ouverture du dialogue d'impression)
        setTimeout(function() {
            window.location.href = "{{ route('qmatic.kiosk.index') }}";
        }, 3000);
    };
</script>
@endsection

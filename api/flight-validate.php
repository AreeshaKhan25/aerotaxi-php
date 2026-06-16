<?php
/**
 * API: Flight Validation
 * 
 * GET /api/flight/validate?flight_number=BA1234&date=2026-06-20
 * 
 * Validates flight number using AviationStack API with fallback to known airlines
 */

header('Content-Type: application/json');

$flightNumber = strtoupper(trim($_GET['flight_number'] ?? ''));
$date = $_GET['date'] ?? date('Y-m-d');

if (!$flightNumber) {
    echo json_encode(['found' => false]);
    exit;
}

// Parse flight number (e.g., BA1234)
// Extract airline code and flight number (e.g. BA1234 -> BA + 1234)
// Match airline code (2 chars, may include digit like W6, U2) + flight number
preg_match('/^([A-Z\d]{2})(\d{1,5})$/', $flightNumber, $matches);
if (!$matches) {
    preg_match('/^([A-Z]{3})(\d{1,5})$/', $flightNumber, $matches);
}

if (!$matches) {
    echo json_encode(['found' => false]);
    exit;
}

$airlineCode = $matches[1];
$flightNum = $matches[2];

// Try AviationStack API if key is configured
if (!empty(AVIATIONSTACK_KEY)) {
    try {
        $url = "http://api.aviationstack.com/v1/flights?access_key=" . AVIATIONSTACK_KEY . 
               "&flight_iata=" . urlencode($flightNumber) . "&date=" . urlencode($date);
        
        $response = @file_get_contents($url);
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['data']) && is_array($data['data'])) {
                $flight = $data['data'][0];
                $dep = $flight['departure']['airport'] ?? 'Unknown';
                $arr = $flight['arrival']['airport'] ?? 'Unknown';
                $airline = $flight['airline']['name'] ?? $airlineCode;
                $status = $flight['flight_status'] ?? '';
                $depTime = $flight['departure']['scheduled'] ?? '';
                
                $info = "$airline $flightNumber · $dep → $arr";
                if ($depTime) {
                    $info .= " · Departs " . date('H:i', strtotime($depTime));
                }
                if ($status) {
                    $info .= " · Status: " . ucfirst($status);
                }
                
                echo json_encode(['found' => true, 'info' => $info]);
                exit;
            }
        }
    } catch (Exception $e) {
        // Fall through to fallback
    }
}

// Fallback: validate against known UK airline codes
$knownAirlines = [
    'BA' => 'British Airways',
    'EZY' => 'easyJet',
    'U2' => 'easyJet',
    'FR' => 'Ryanair',
    'VS' => 'Virgin Atlantic',
    'LS' => 'Jet2',
    'MT' => 'Thomas Cook',
    'TOM' => 'TUI',
    'BY' => 'TUI Airways',
    'BE' => 'Flybe',
    'LM' => 'Loganair',
    'EI' => 'Aer Lingus',
    'LH' => 'Lufthansa',
    'AF' => 'Air France',
    'KL' => 'KLM',
    'AA' => 'American Airlines',
    'UA' => 'United Airlines',
    'DL' => 'Delta',
    'EK' => 'Emirates',
    'QR' => 'Qatar Airways',
    'TK' => 'Turkish Airlines',
    'SQ' => 'Singapore Airlines',
    'CX' => 'Cathay Pacific',
    'NH' => 'ANA',
    'IB' => 'Iberia',
    'AZ' => 'ITA Airways',
    'SK' => 'SAS',
    'AY' => 'Finnair',
    'TP' => 'TAP Portugal',
    'LX' => 'Swiss',
    'OS' => 'Austrian',
    'SN' => 'Brussels Airlines',
    'W6' => 'Wizz Air',
    'W9' => 'Wizz Air UK',
    'ZT' => 'Titan Airways',
    'RK' => 'Ryanair UK',
];

if (isset($knownAirlines[$airlineCode])) {
    $airline = $knownAirlines[$airlineCode];
    echo json_encode([
        'found' => true,
        'info' => "$airline flight $flightNumber · $date"
    ]);
    exit;
}

// Unknown airline
echo json_encode(['found' => false]);

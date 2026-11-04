<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function show(Reservation $reservation): View
    {
        $reservation->load(['business', 'space.venue', 'package', 'customer.wilaya']);

        return view('contracts.show', compact('reservation'));
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Transport Module
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings for transport
    | offers and requests (spolujízda na závody).
    |
    */

    'page_title' => 'Transport',
    'offers_heading' => 'Transport offers',
    'offer_transport' => 'Offer transport',
    'edit_offer' => 'Edit offer',

    'vehicle' => 'Vehicle',
    'club_vehicle_prefix' => '[Club]',
    'driver' => 'Offered by',
    'departure_place' => 'From',
    'direction' => 'Direction',
    'seats_offered' => 'Seats offered',
    'free_seats' => 'Free seats',
    'distance_km' => 'Distance',
    'distance_km_suffix' => 'km',
    'contribution' => 'Contribution',
    'contribution_suffix' => 'CZK',
    'contribution_helper' => 'Leave empty if you do not want a contribution.',
    'without_contribution' => 'No contribution',
    'active' => 'Active',

    'empty_offers' => 'No transport offers yet',
    'empty_offers_description' => 'Be the first to offer a ride to this race.',

    'request_seat' => 'Reserve a seat',
    'seats' => 'Number of seats',
    'user' => 'User',
    'trip_params' => 'Trip details',
    'request_status' => 'Request status',
    'note' => 'Note',
    'note_placeholder' => 'E.g. how much luggage I have, where I can be picked up…',
    'request_sent' => 'Request sent',
    'request_sent_body' => 'The driver received an e-mail and can approve or reject the request.',
    'requests_for_my_offers' => 'Requests for seats in my offers',
    'my_requests' => 'My carpool requests',
    'approve' => 'Approve',
    'reject' => 'Reject',
    'cancel_request' => 'Cancel request',
    'request_approved' => 'Request approved, the requester received an e-mail.',
    'request_rejected_capacity' => 'Not enough free seats — the request was rejected.',
    'request_rejected_done' => 'Request rejected, the requester received an e-mail.',
    'cancel_request_modal_heading' => 'Cancel the carpool request?',
    'cancel_request_modal_description' => 'The driver will be informed and the seats will be freed for others.',
    'cancel_request_modal_submit' => 'Yes, cancel the request',
    'tab_free_seats' => 'Free seats in all active offers',
    'request_cancelled_done' => 'The request was cancelled.',

    'mail' => [
        'request_created_subject' => 'New carpool request',
        'request_approved_subject' => 'Carpool request approved',
        'request_rejected_subject' => 'Carpool request rejected',
        'request_cancelled_subject' => 'Passenger cancelled the reservation',
        'offer_cancelled_subject' => 'Transport offer was cancelled',

        'request_created_approve_button' => 'Approve request',
        'request_created_reject_button' => 'Reject request',

        'club' => [
            'seats_value' => '{1} :count seat|[2,*] :count seats',
            'driver_label' => 'Driver',
            'passenger_label' => 'Passenger',
            'direction_label' => 'Direction',
            'seats_label' => 'Number of seats',
            'reserved_seats_label' => 'Reserved seats',
            'cancelled_seats_label' => 'Cancelled seats',
            'departure_label' => 'Departure point',
            'departure_from_label' => 'Departing from',
            'vehicle_label' => 'Your car',
        ],

        'request_created_club' => [
            'eyebrow' => 'Carpool · new request',
            'title' => ':passenger wants to ride with you.',
            'lead' => '{1} Asking for :count seat to :event, :date.|[2,*] Asking for :count seats to :event, :date.',
            'note_label' => 'Note with the request',
            'fine' => 'You can handle the request until the race day. You will also find it in DaLin under Transport.',
        ],

        'request_approved_club' => [
            'eyebrow' => 'Carpool · request approved',
            'title' => 'Your seat in the car is confirmed.',
            'lead' => 'Your carpool request to :event, :date, has been approved.',
            'status' => '{1} Approved · :count seat|[2,*] Approved · :count seats',
            'action' => 'Open transport for the race',
            'fine' => 'Arrange the departure details with the driver. You will find the booking in DaLin under Transport.',
        ],

        'request_rejected_club' => [
            'eyebrow' => 'Carpool · request rejected',
            'title' => 'This carpool did not work out.',
            'lead' => 'Your carpool request to :event, :date, has been rejected.',
            'status' => 'Request rejected',
            'action' => 'Browse other transport offers',
            'fine' => 'Look at other transport options for the race or arrange another way to get there.',
        ],

        'request_cancelled_club' => [
            'eyebrow' => 'Carpool · for the driver',
            'title' => ':passenger cancels the booking.',
            'lead' => '{1} :count reserved seat in your car to :event, :date, is free again.|[2,*] :count reserved seats in your car to :event, :date, are free again.',
            'action' => 'Open your transport offer',
            'fine' => 'You will find the current occupancy of your car on the race\'s Transport page.',
        ],

        'offer_cancelled_club' => [
            'eyebrow' => 'Carpool · offer cancelled',
            'title' => 'The driver cancelled the transport offer.',
            'lead' => 'The carpool to :event, :date, is cancelled.',
            'note_label' => 'Plan other transport',
            'note' => 'Your seat request in this offer no longer applies. Have a look at other ways to get there.',
            'action' => 'Browse transport for the race',
            'fine' => 'You will find the current offers in DaLin under Transport.',
        ],
    ],

    'direction_enum' => [
        'there' => 'There only',
        'back' => 'Return only',
        'both' => 'Round trip',
    ],

    'request_status_enum' => [
        'pending' => 'Pending approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ],
];

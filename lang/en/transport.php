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

        'request_cancelled_seats_label' => 'Seats freed up',

        'request_created_approve_button' => 'Approve request',
        'request_created_reject_button' => 'Reject request',

        'request_created_club' => [
            'eyebrow' => 'Car sharing · new request',
            'title' => ':passenger wants to ride with you.',
            'lead' => '{1} Asking for :count seat to :event, :date.|[2,*] Asking for :count seats to :event, :date.',
            'seats_value' => '{1} :count seat|[2,*] :count seats',
            'vehicle_label' => 'Your car',
            'note_label' => 'Note from :passenger',
            'fine' => 'You can handle the request until the race day. You will also find it in the app on the race\'s Transport page.',
        ],

        'request_approved_club' => [
            'eyebrow' => 'Car sharing · request approved',
            'title' => 'You have a seat in the car.',
            'lead' => 'Your car-sharing request to :event, :date has been approved. Arrange the details directly with the driver.',
            'action' => 'Show transport for the race',
            'fine' => 'If you end up not going, cancel the request in the app on the race\'s Transport page to free up the seat.',
        ],

        'request_rejected_club' => [
            'eyebrow' => 'Car sharing · request rejected',
            'title' => 'Not this time.',
            'lead' => 'Your car-sharing request to :event, :date has been rejected. Try another transport offer.',
            'action' => 'Find other transport',
            'fine' => 'You will find all transport offers in the app on the race\'s Transport page.',
        ],

        'request_cancelled_club' => [
            'eyebrow' => 'Car sharing · booking cancelled',
            'title' => 'Seats in your car are free again.',
            'lead' => '{1} :passenger will not ride with you to :event, :date — :count seat is free again.|[2,*] :passenger will not ride with you to :event, :date — :count seats are free again.',
            'action' => 'Show my offer',
            'fine' => 'Freed seats are shown to other members in your offer automatically.',
        ],

        'offer_cancelled_club' => [
            'eyebrow' => 'Car sharing · offer cancelled',
            'title' => 'Your ride to the race is cancelled.',
            'lead' => 'The driver cancelled the transport offer to :event, :date, so your seat request no longer applies. Try another transport offer.',
            'action' => 'Find other transport',
            'fine' => 'You will find all transport offers in the app on the race\'s Transport page.',
        ],

        'request_decided_driver_label' => 'Driver',
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

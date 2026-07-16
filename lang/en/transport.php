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
    'request_cancelled_done' => 'The request was cancelled.',

    'mail' => [
        'request_created_subject' => 'New carpool request',
        'request_approved_subject' => 'Carpool request approved',
        'request_rejected_subject' => 'Carpool request rejected',
        'request_cancelled_subject' => 'Passenger cancelled the reservation',
        'offer_cancelled_subject' => 'Transport offer was cancelled',
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

<?php
    use App\Enums\SportEventType;
?>

<div>
    @if ($getRecord()->event_type->value === SportEventType::Race->value)
        <div>
           <span class="bg-green-100 text-green-800 text-sm font-semibold inline-flex items-center p-1.5 rounded-full dark:bg-green-800 dark:text-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-run" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M13 4m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                <path d="M4 17l5 1l.75 -1.5"></path>
                <path d="M15 21l0 -4l-4 -3l1 -6"></path>
                <path d="M7 12l0 -3l5 -1l3 3l3 1"></path>
            </svg>
            <span class="sr-only">{{__('sport-event.type_enum.' . SportEventType::Race->value)}}</span>
        </span>
       </div>

    @elseif($getRecord()->event_type->value === SportEventType::Training->value)
    <div>
        <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold inline-flex items-center p-1.5 rounded-full dark:bg-yellow-800 dark:text-yellow-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-pin" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
               <path d="M20.971 11.278a9 9 0 1 0 -8.313 9.698"></path>
               <path d="M12 7v5l1.5 1.5"></path>
               <path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z"></path>
               <path d="M19 18v.01"></path>
            </svg>
            <span class="sr-only">{{__('sport-event.type_enum.' . SportEventType::Training->value)}}</span>
        </span>
    </div>
    @elseif($getRecord()->event_type->value === SportEventType::TrainingCamp->value)
        <div>
            <span class="bg-blue-100 text-blue-800 text-sm font-semibold inline-flex items-center p-1.5 rounded-full dark:bg-blue-800 dark:text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trees" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                   <path d="M16 5l3 3l-2 1l4 4l-3 1l4 4h-9"></path>
                   <path d="M15 21l0 -3"></path>
                   <path d="M8 13l-2 -2"></path>
                   <path d="M8 12l2 -2"></path>
                   <path d="M8 21v-13"></path>
                   <path d="M5.824 16a3 3 0 0 1 -2.743 -3.69a3 3 0 0 1 .304 -4.833a3 3 0 0 1 4.615 -3.707a3 3 0 0 1 4.614 3.707a3 3 0 0 1 .305 4.833a3 3 0 0 1 -2.919 3.695h-4z"></path>
                </svg>
                <span class="sr-only">{{__('sport-event.type_enum.' . SportEventType::TrainingCamp->value)}}</span>
            </span>
        </div>

    @elseif($getRecord()->event_type->value === SportEventType::Other->value)
        <div>
            <span class="bg-purple-100 text-purple-800 text-sm font-semibold inline-flex items-center p-1.5 rounded-full dark:bg-purple-800 dark:text-purple-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-hexagon" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                   <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                   <path d="M19.875 6.27c.7 .398 1.13 1.143 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z"></path>
                   <path d="M12 16v.01"></path>
                   <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                </svg>
                <span class="sr-only">{{__('sport-event.type_enum.' . SportEventType::Other->value)}}</span>
            </span>
        </div>

   @endif

</div>

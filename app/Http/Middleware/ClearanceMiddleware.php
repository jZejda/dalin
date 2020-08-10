<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClearanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->hasPermissionTo('Admin RolePerm')) {
            return $next($request);
        }

        if ($request->is('admin/posts/create')) {
            if (! Auth::user()->hasPermissionTo('Create Post')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/posts/*/edit')) {
            if (! Auth::user()->hasPermissionTo('Edit Post')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->isMethod('Delete')) {
            if (! Auth::user()->hasPermissionTo('Delete Post') or ! Auth::user()->hasPermissionTo('Delete Page')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        /*
        * Oevent permission
        */

        if ($request->is('admin/oevents*')) {
            if (! Auth::user()->hasPermissionTo('Show Oevents')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/oevents/create')) {
            if (! Auth::user()->hasPermissionTo('Create Oevents')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/oevents/*/edit')) {
            if (! Auth::user()->hasPermissionTo('Edit Oevents')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        /*
         * Legs Permission
         */

        if ($request->is('admin/legs')) {
            if (! Auth::user()->hasPermissionTo('Show Legs')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/legs/create/*')) {
            if (! Auth::user()->hasPermissionTo('Create Legs')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/legs/*/edit')) {
            if (! Auth::user()->hasPermissionTo('Edit Legs')) {
                abort('401');
            } else {
                return $next($request);
            }
        }


        /*
        * Page permission
        */
        if ($request->is('admin/pages/create')) {
            if (! Auth::user()->hasPermissionTo('Create Page')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/pages/*/edit')) {
            if (! Auth::user()->hasPermissionTo('Edit Page')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        /*
        * Media permission
        */

        if ($request->is('admin/media/store')) {
            if (! Auth::user()->hasPermissionTo('Add File')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('media/*/delete')) {
            if (! Auth::user()->hasPermissionTo('Delete File')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        /*
         * Member Edit
         */
        if ($request->is('admin/member/*/edit')) {
            if (! Auth::user()->hasPermissionTo('Edit member')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/member/*/edit-password')) {
            if (! Auth::user()->hasPermissionTo('Edit member password')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/roles/*')) {
            if (! Auth::user()->hasPermissionTo('Manage roles')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/permissions/*')) {
            if (! Auth::user()->hasPermissionTo('Manage permissions')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        //Manage Race Profiles
        if ($request->is('admin/race-profiles/create')) {
            if (! Auth::user()->hasPermissionTo('Create Race profile')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/race-profiles/*/edit')) {
            if (! Auth::user()->hasPermissionTo('Edit Race profile')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('admin/race-profiles/*/delete')) {
            if (! Auth::user()->hasPermissionTo('Delete Race profile')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        // next return
        return $next($request);
    }
}

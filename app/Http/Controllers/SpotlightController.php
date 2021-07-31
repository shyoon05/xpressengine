<?php

namespace App\Http\Controllers;

use XePresenter;
use Xpressengine\Http\Request;
use Xpressengine\Spotlight\SpotlightItem;
use Xpressengine\Spotlight\SpotlightItemContainer;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;
use Xpressengine\User\Rating;

class SpotlightController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $rating = $request->user()->getRating();

            if (! in_array($rating, [Rating::MANAGER, Rating::SUPER])) {
                throw new AccessDeniedHttpException();
            }

            return $next($request);
        });
    }

    public function index(Request $request, SpotlightItemContainer $container)
    {
        return XePresenter::makeApi($container->all($request->get('keyword')));
    }

    public function show(SpotlightItemContainer $container, $id)
    {
        $spotlightItem = $container->get($id);

        if (! ($spotlightItem instanceof SpotlightItem)) {
            throw new \InvalidArgumentException('This is not a spotlight item.');
        }

        return XePresenter::makeApi($spotlightItem->toArray());
    }
}
<?php

namespace Inertia\Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\NullSessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Inertia\AlwaysProp;
use Inertia\DeferProp;
use Inertia\Inertia;
use Inertia\LazyProp;
use Inertia\MergeProp;
use Inertia\OptionalProp;
use Inertia\ResponseFactory;
use Inertia\Tests\Stubs\ExampleMiddleware;

class ResponseFactoryTest extends TestCase
{
    public function test_can_macro(): void
    {
        $factory = new ResponseFactory;
        $factory->macro('foo', function () {
            return 'bar';
        });

        $this->assertEquals('bar', $factory->foo());
    }

    public function test_location_response_for_inertia_requests(): void
    {
        Request::macro('inertia', function () {
            return true;
        });

        $response = (new ResponseFactory)->location('https://inertiajs.com');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals('https://inertiajs.com', $response->headers->get('X-Inertia-Location'));
    }

    public function test_location_response_for_non_inertia_requests(): void
    {
        Request::macro('inertia', function () {
            return false;
        });

        $response = (new ResponseFactory)->location('https://inertiajs.com');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('https://inertiajs.com', $response->headers->get('location'));
    }

    public function test_location_response_for_inertia_requests_using_redirect_response(): void
    {
        Request::macro('inertia', function () {
            return true;
        });

        $redirect = new RedirectResponse('https://inertiajs.com');
        $response = (new ResponseFactory)->location($redirect);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals('https://inertiajs.com', $response->headers->get('X-Inertia-Location'));
    }

    public function test_location_response_for_non_inertia_requests_using_redirect_response(): void
    {
        $redirect = new RedirectResponse('https://inertiajs.com');
        $response = (new ResponseFactory)->location($redirect);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('https://inertiajs.com', $response->headers->get('location'));
    }

    public function test_location_redirects_are_not_modified(): void
    {
        $response = (new ResponseFactory)->location('/foo');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('/foo', $response->headers->get('location'));
    }

    public function test_location_response_for_non_inertia_requests_using_redirect_response_with_existing_session_and_request_properties(): void
    {
        $redirect = new RedirectResponse('https://inertiajs.com');
        $redirect->setSession($session = new Store('test', new NullSessionHandler));
        $redirect->setRequest($request = new HttpRequest);
        $response = (new ResponseFactory)->location($redirect);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('https://inertiajs.com', $response->headers->get('location'));
        $this->assertSame($session, $response->getSession());
        $this->assertSame($request, $response->getRequest());
        $this->assertSame($response, $redirect);
    }

    public function test_the_version_can_be_a_closure(): void
    {
        Route::middleware([StartSession::class, ExampleMiddleware::class])->get('/', function () {
            $this->assertSame('', Inertia::getVersion());

            Inertia::version(function () {
                return hash('xxh128', 'Inertia');
            });

            return Inertia::render('User/Edit');
        });

        $response = $this->withoutExceptionHandling()->get('/', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => 'f445bd0a2c393a5af14fc677f59980a9',
        ]);

        $response->assertSuccessful();
        $response->assertJson(['component' => 'User/Edit']);
    }

    public function test_shared_data_can_be_shared_from_anywhere(): void
    {
        Route::middleware([StartSession::class, ExampleMiddleware::class])->get('/', function () {
            Inertia::share('foo', 'bar');

            return Inertia::render('User/Edit');
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Edit',
            'props' => [
                'foo' => 'bar',
            ],
        ]);
    }

    public function test_dot_props_are_merged_from_shared(): void
    {
        Route::middleware([StartSession::class, ExampleMiddleware::class])->get('/', function () {
            Inertia::share('auth.user', [
                'name' => 'Jonathan',
            ]);

            return Inertia::render('User/Edit', [
                'auth.user.can.create_group' => false,
            ]);
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Edit',
            'props' => [
                'auth' => [
                    'user' => [
                        'name' => 'Jonathan',
                        'can' => [
                            'create_group' => false,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_shared_data_can_resolve_closure_arguments(): void
    {
        Inertia::share('query', fn (HttpRequest $request) => $request->query());

        Route::middleware([StartSession::class, ExampleMiddleware::class])->get('/', function () {
            return Inertia::render('User/Edit');
        });

        $response = $this->withoutExceptionHandling()->get('/?foo=bar', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Edit',
            'props' => [
                'query' => [
                    'foo' => 'bar',
                ],
            ],
        ]);
    }

    public function test_dot_props_with_callbacks_are_merged_from_shared(): void
    {
        Route::middleware([StartSession::class, ExampleMiddleware::class])->get('/', function () {
            Inertia::share('auth.user', fn () => [
                'name' => 'Jonathan',
            ]);

            return Inertia::render('User/Edit', [
                'auth.user.can.create_group' => false,
            ]);
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);

        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Edit',
            'props' => [
                'auth' => [
                    'user' => [
                        'name' => 'Jonathan',
                        'can' => [
                            'create_group' => false,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_can_flush_shared_data(): void
    {
        Inertia::share('foo', 'bar');
        $this->assertSame(['foo' => 'bar'], Inertia::getShared());
        Inertia::flushShared();
        $this->assertSame([], Inertia::getShared());
    }

    public function test_can_create_lazy_prop(): void
    {
        $factory = new ResponseFactory;
        $lazyProp = $factory->lazy(function () {
            return 'A lazy value';
        });

        $this->assertInstanceOf(LazyProp::class, $lazyProp);
    }

    public function test_can_create_deferred_prop(): void
    {
        $factory = new ResponseFactory;
        $deferredProp = $factory->defer(function () {
            return 'A deferred value';
        });

        $this->assertInstanceOf(DeferProp::class, $deferredProp);
        $this->assertSame($deferredProp->group(), 'default');
    }

    public function test_can_create_deferred_prop_with_custom_group(): void
    {
        $factory = new ResponseFactory;
        $deferredProp = $factory->defer(function () {
            return 'A deferred value';
        }, 'foo');

        $this->assertInstanceOf(DeferProp::class, $deferredProp);
        $this->assertSame($deferredProp->group(), 'foo');
    }

    public function test_can_create_merged_prop(): void
    {
        $factory = new ResponseFactory;
        $mergedProp = $factory->merge(function () {
            return 'A merged value';
        });

        $this->assertInstanceOf(MergeProp::class, $mergedProp);
    }

    public function test_can_create_deep_merged_prop(): void
    {
        $factory = new ResponseFactory;
        $mergedProp = $factory->deepMerge(function () {
            return 'A merged value';
        });

        $this->assertInstanceOf(MergeProp::class, $mergedProp);
    }

    public function test_can_create_deferred_and_merged_prop(): void
    {
        $factory = new ResponseFactory;
        $deferredProp = $factory->defer(function () {
            return 'A deferred + merged value';
        })->merge();

        $this->assertInstanceOf(DeferProp::class, $deferredProp);
    }

    public function test_can_create_deferred_and_deep_merged_prop(): void
    {
        $factory = new ResponseFactory;
        $deferredProp = $factory->defer(function () {
            return 'A deferred + merged value';
        })->deepMerge();

        $this->assertInstanceOf(DeferProp::class, $deferredProp);
    }

    public function test_can_create_optional_prop(): void
    {
        $factory = new ResponseFactory;
        $optionalProp = $factory->optional(function () {
            return 'An optional value';
        });

        $this->assertInstanceOf(OptionalProp::class, $optionalProp);
    }

    public function test_can_create_always_prop(): void
    {
        $factory = new ResponseFactory;
        $alwaysProp = $factory->always(function () {
            return 'An always value';
        });

        $this->assertInstanceOf(AlwaysProp::class, $alwaysProp);
    }

    public function test_will_accept_arrayabe_props()
    {
        Route::middleware([StartSession::class, ExampleMiddleware::class])->get('/', function () {
            Inertia::share('foo', 'bar');

            return Inertia::render('User/Edit', new class implements Arrayable
            {
                public function toArray()
                {
                    return [
                        'foo' => 'bar',
                    ];
                }
            });
        });

        $response = $this->withoutExceptionHandling()->get('/', ['X-Inertia' => 'true']);
        $response->assertSuccessful();
        $response->assertJson([
            'component' => 'User/Edit',
            'props' => [
                'foo' => 'bar',
            ],
        ]);
    }
}

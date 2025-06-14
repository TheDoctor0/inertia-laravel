<?php

namespace Inertia\Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response as BaseResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\View\View;
use Inertia\AlwaysProp;
use Inertia\DeferProp;
use Inertia\LazyProp;
use Inertia\MergeProp;
use Inertia\Response;
use Inertia\Tests\Stubs\FakeResource;
use Mockery;

class ResponseTest extends TestCase
{
    public function test_can_macro(): void
    {
        $response = new Response('User/Edit', []);
        $response->macro('foo', function () {
            return 'bar';
        });

        $this->assertEquals('bar', $response->foo());
    }

    public function test_server_response(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response('User/Edit', ['user' => $user], 'app', '123');
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;}},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false}"></div>', $view->render());
    }

    public function test_server_response_with_deferred_prop(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response(
            'User/Edit',
            [
                'user' => $user,
                'foo' => new DeferProp(function () {
                    return 'bar';
                }, 'default'),
            ],
            'app',
            '123'
        );
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertSame([
            'default' => ['foo'],
        ], $page['deferredProps']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;}},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false,&quot;deferredProps&quot;:{&quot;default&quot;:[&quot;foo&quot;]}}"></div>', $view->render());
    }

    public function test_server_response_with_deferred_prop_and_multiple_groups(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response(
            'User/Edit',
            [
                'user' => $user,
                'foo' => new DeferProp(function () {
                    return 'foo value';
                }, 'default'),
                'bar' => new DeferProp(function () {
                    return 'bar value';
                }, 'default'),
                'baz' => new DeferProp(function () {
                    return 'baz value';
                }, 'custom'),
            ],
            'app',
            '123'
        );
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertSame([
            'default' => ['foo', 'bar'],
            'custom' => ['baz'],
        ], $page['deferredProps']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;}},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false,&quot;deferredProps&quot;:{&quot;default&quot;:[&quot;foo&quot;,&quot;bar&quot;],&quot;custom&quot;:[&quot;baz&quot;]}}"></div>', $view->render());
    }

    public function test_server_response_with_merge_props(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response(
            'User/Edit',
            [
                'user' => $user,
                'foo' => new MergeProp('foo value'),
                'bar' => new MergeProp('bar value'),
            ],
            'app',
            '123'
        );
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertSame([
            'foo',
            'bar',
        ], $page['mergeProps']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;},&quot;foo&quot;:&quot;foo value&quot;,&quot;bar&quot;:&quot;bar value&quot;},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false,&quot;mergeProps&quot;:[&quot;foo&quot;,&quot;bar&quot;]}"></div>', $view->render());
    }

    public function test_server_response_with_deep_merge_props(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response(
            'User/Edit',
            [
                'user' => $user,
                'foo' => (new MergeProp('foo value'))->deepMerge(),
                'bar' => (new MergeProp('bar value'))->deepMerge(),
            ],
            'app',
            '123'
        );
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertSame([
            'foo',
            'bar',
        ], $page['deepMergeProps']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;},&quot;foo&quot;:&quot;foo value&quot;,&quot;bar&quot;:&quot;bar value&quot;},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false,&quot;deepMergeProps&quot;:[&quot;foo&quot;,&quot;bar&quot;]}"></div>', $view->render());
    }

    public function test_server_response_with_defer_and_merge_props(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response(
            'User/Edit',
            [
                'user' => $user,
                'foo' => (new DeferProp(function () {
                    return 'foo value';
                }, 'default'))->merge(),
                'bar' => new MergeProp('bar value'),
            ],
            'app',
            '123'
        );
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertSame([
            'default' => ['foo'],
        ], $page['deferredProps']);
        $this->assertSame([
            'foo',
            'bar',
        ], $page['mergeProps']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;},&quot;bar&quot;:&quot;bar value&quot;},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false,&quot;mergeProps&quot;:[&quot;foo&quot;,&quot;bar&quot;],&quot;deferredProps&quot;:{&quot;default&quot;:[&quot;foo&quot;]}}"></div>', $view->render());
    }

    public function test_server_response_with_defer_and_deep_merge_props(): void
    {
        $request = Request::create('/user/123', 'GET');

        $user = ['name' => 'Jonathan'];
        $response = new Response(
            'User/Edit',
            [
                'user' => $user,
                'foo' => (new DeferProp(function () {
                    return 'foo value';
                }, 'default'))->deepMerge(),
                'bar' => (new MergeProp('bar value'))->deepMerge(),
            ],
            'app',
            '123'
        );
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('User/Edit', $page['component']);
        $this->assertSame('Jonathan', $page['props']['user']['name']);
        $this->assertSame('/user/123', $page['url']);
        $this->assertSame('123', $page['version']);
        $this->assertSame([
            'default' => ['foo'],
        ], $page['deferredProps']);
        $this->assertSame([
            'foo',
            'bar',
        ], $page['deepMergeProps']);
        $this->assertFalse($page['clearHistory']);
        $this->assertFalse($page['encryptHistory']);
        $this->assertSame('<div id="app" data-page="{&quot;component&quot;:&quot;User\/Edit&quot;,&quot;props&quot;:{&quot;user&quot;:{&quot;name&quot;:&quot;Jonathan&quot;},&quot;bar&quot;:&quot;bar value&quot;},&quot;url&quot;:&quot;\/user\/123&quot;,&quot;version&quot;:&quot;123&quot;,&quot;clearHistory&quot;:false,&quot;encryptHistory&quot;:false,&quot;deepMergeProps&quot;:[&quot;foo&quot;,&quot;bar&quot;],&quot;deferredProps&quot;:{&quot;default&quot;:[&quot;foo&quot;]}}"></div>', $view->render());
    }

    public function test_xhr_response(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $user = (object) ['name' => 'Jonathan'];
        $response = new Response('User/Edit', ['user' => $user], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Edit', $page->component);
        $this->assertSame('Jonathan', $page->props->user->name);
        $this->assertSame('/user/123', $page->url);
        $this->assertSame('123', $page->version);
    }

    public function test_resource_response(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $resource = new FakeResource(['name' => 'Jonathan']);

        $response = new Response('User/Edit', ['user' => $resource], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Edit', $page->component);
        $this->assertSame('Jonathan', $page->props->user->name);
        $this->assertSame('/user/123', $page->url);
        $this->assertSame('123', $page->version);
    }

    public function test_lazy_callable_resource_response(): void
    {
        $request = Request::create('/users', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Index', [
            'users' => fn () => [['name' => 'Jonathan']],
            'organizations' => fn () => [['name' => 'Inertia']],
        ], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Index', $page->component);
        $this->assertSame('/users', $page->url);
        $this->assertSame('123', $page->version);
        tap($page->props->users, function ($users) {
            $this->assertSame(json_encode([['name' => 'Jonathan']]), json_encode($users));
        });
        tap($page->props->organizations, function ($organizations) {
            $this->assertSame(json_encode([['name' => 'Inertia']]), json_encode($organizations));
        });
    }

    public function test_lazy_callable_resource_partial_response(): void
    {
        $request = Request::create('/users', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'users']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'User/Index']);

        $response = new Response('User/Index', [
            'users' => fn () => [['name' => 'Jonathan']],
            'organizations' => fn () => [['name' => 'Inertia']],
        ], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Index', $page->component);
        $this->assertSame('/users', $page->url);
        $this->assertSame('123', $page->version);
        $this->assertFalse(property_exists($page->props, 'organizations'));
        tap($page->props->users, function ($users) {
            $this->assertSame(json_encode([['name' => 'Jonathan']]), json_encode($users));
        });
    }

    public function test_lazy_resource_response(): void
    {
        $request = Request::create('/users', 'GET', ['page' => 1]);
        $request->headers->add(['X-Inertia' => 'true']);

        $users = Collection::make([
            new Fluent(['name' => 'Jonathan']),
            new Fluent(['name' => 'Taylor']),
            new Fluent(['name' => 'Jeffrey']),
        ]);

        $callable = static function () use ($users) {
            $page = new LengthAwarePaginator($users->take(2), $users->count(), 2);

            return new class($page, JsonResource::class) extends ResourceCollection {};
        };

        $response = new Response('User/Index', ['users' => $callable], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $expected = [
            'data' => $users->take(2),
            'links' => [
                'first' => '/?page=1',
                'last' => '/?page=2',
                'prev' => null,
                'next' => '/?page=2',
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 2,
                'path' => '/',
                'per_page' => 2,
                'to' => 2,
                'total' => 3,
            ],
        ];

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Index', $page->component);
        $this->assertSame('/users?page=1', $page->url);
        $this->assertSame('123', $page->version);
        tap($page->props->users, function ($users) use ($expected) {
            $this->assertSame(json_encode($expected['data']), json_encode($users->data));
            $this->assertSame(json_encode($expected['links']), json_encode($users->links));
            $this->assertSame('/', $users->meta->path);
        });
    }

    public function test_nested_lazy_resource_response(): void
    {
        $request = Request::create('/users', 'GET', ['page' => 1]);
        $request->headers->add(['X-Inertia' => 'true']);

        $users = Collection::make([
            new Fluent(['name' => 'Jonathan']),
            new Fluent(['name' => 'Taylor']),
            new Fluent(['name' => 'Jeffrey']),
        ]);

        $callable = static function () use ($users) {
            $page = new LengthAwarePaginator($users->take(2), $users->count(), 2);

            // nested array with ResourceCollection to resolve
            return [
                'users' => new class($page, JsonResource::class) extends ResourceCollection {},
            ];
        };

        $response = new Response('User/Index', ['something' => $callable], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $expected = [
            'users' => [
                'data' => $users->take(2),
                'links' => [
                    'first' => '/?page=1',
                    'last' => '/?page=2',
                    'prev' => null,
                    'next' => '/?page=2',
                ],
                'meta' => [
                    'current_page' => 1,
                    'from' => 1,
                    'last_page' => 2,
                    'path' => '/',
                    'per_page' => 2,
                    'to' => 2,
                    'total' => 3,
                ],
            ],
        ];

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Index', $page->component);
        $this->assertSame('/users?page=1', $page->url);
        $this->assertSame('123', $page->version);
        tap($page->props->something->users, function ($users) use ($expected) {
            $this->assertSame(json_encode($expected['users']['data']), json_encode($users->data));
            $this->assertSame(json_encode($expected['users']['links']), json_encode($users->links));
            $this->assertSame('/', $users->meta->path);
        });
    }

    public function test_arrayable_prop_response(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $resource = FakeResource::make(['name' => 'Jonathan']);

        $response = new Response('User/Edit', ['user' => $resource], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Edit', $page->component);
        $this->assertSame('Jonathan', $page->props->user->name);
        $this->assertSame('/user/123', $page->url);
        $this->assertSame('123', $page->version);
    }

    public function test_promise_props_are_resolved(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $user = (object) ['name' => 'Jonathan'];

        $promise = Mockery::mock('GuzzleHttp\Promise\PromiseInterface')
            ->shouldReceive('wait')
            ->andReturn($user)
            ->mock();

        $response = new Response('User/Edit', ['user' => $promise], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Edit', $page->component);
        $this->assertSame('Jonathan', $page->props->user->name);
        $this->assertSame('/user/123', $page->url);
        $this->assertSame('123', $page->version);
    }

    public function test_xhr_partial_response(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'User/Edit']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'partial']);

        $user = (object) ['name' => 'Jonathan'];
        $response = new Response('User/Edit', ['user' => $user, 'partial' => 'partial-data'], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $props = get_object_vars($page->props);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Edit', $page->component);
        $this->assertFalse(isset($props['user']));
        $this->assertCount(1, $props);
        $this->assertSame('partial-data', $page->props->partial);
        $this->assertSame('/user/123', $page->url);
        $this->assertSame('123', $page->version);
    }

    public function test_exclude_props_from_partial_response(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'User/Edit']);
        $request->headers->add(['X-Inertia-Partial-Except' => 'user']);

        $user = (object) ['name' => 'Jonathan'];
        $response = new Response('User/Edit', ['user' => $user, 'partial' => 'partial-data'], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $props = get_object_vars($page->props);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('User/Edit', $page->component);
        $this->assertFalse(isset($props['user']));
        $this->assertCount(1, $props);
        $this->assertSame('partial-data', $page->props->partial);
        $this->assertSame('/user/123', $page->url);
        $this->assertSame('123', $page->version);
    }

    public function test_nested_partial_props(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'User/Edit']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'auth.user,auth.refresh_token']);

        $props = [
            'auth' => [
                'user' => new LazyProp(function () {
                    return [
                        'name' => 'Jonathan Reinink',
                        'email' => 'jonathan@example.com',
                    ];
                }),
                'refresh_token' => 'value',
                'token' => 'value',
            ],
            'shared' => [
                'flash' => 'value',
            ],
        ];

        $response = new Response('User/Edit', $props);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertFalse(isset($page->props->shared));
        $this->assertFalse(isset($page->props->auth->token));
        $this->assertSame('Jonathan Reinink', $page->props->auth->user->name);
        $this->assertSame('jonathan@example.com', $page->props->auth->user->email);
        $this->assertSame('value', $page->props->auth->refresh_token);
    }

    public function test_exclude_nested_props_from_partial_response(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'User/Edit']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'auth']);
        $request->headers->add(['X-Inertia-Partial-Except' => 'auth.user']);

        $props = [
            'auth' => [
                'user' => new LazyProp(function () {
                    return [
                        'name' => 'Jonathan Reinink',
                        'email' => 'jonathan@example.com',
                    ];
                }),
                'refresh_token' => 'value',
            ],
            'shared' => [
                'flash' => 'value',
            ],
        ];

        $response = new Response('User/Edit', $props);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertFalse(isset($page->props->auth->user));
        $this->assertFalse(isset($page->props->shared));
        $this->assertSame('value', $page->props->auth->refresh_token);
    }

    public function test_lazy_props_are_not_included_by_default(): void
    {
        $request = Request::create('/users', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $lazyProp = new LazyProp(function () {
            return 'A lazy value';
        });

        $response = new Response('Users', ['users' => [], 'lazy' => $lazyProp], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame([], $page->props->users);
        $this->assertFalse(property_exists($page->props, 'lazy'));
    }

    public function test_lazy_props_are_included_in_partial_reload(): void
    {
        $request = Request::create('/users', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'Users']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'lazy']);

        $lazyProp = new LazyProp(function () {
            return 'A lazy value';
        });

        $response = new Response('Users', ['users' => [], 'lazy' => $lazyProp], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertFalse(property_exists($page->props, 'users'));
        $this->assertSame('A lazy value', $page->props->lazy);
    }

    public function test_defer_arrayable_props_are_resolved_in_partial_reload(): void
    {
        $request = Request::create('/users', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'Users']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'defer']);

        $deferProp = new DeferProp(function () {
            return new class implements Arrayable
            {
                public function toArray()
                {
                    return ['foo' => 'bar'];
                }
            };
        });

        $response = new Response('Users', ['users' => [], 'defer' => $deferProp], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertFalse(property_exists($page->props, 'users'));
        $this->assertEquals((object) ['foo' => 'bar'], $page->props->defer);
    }

    public function test_always_props_are_included_on_partial_reload(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);
        $request->headers->add(['X-Inertia-Partial-Component' => 'User/Edit']);
        $request->headers->add(['X-Inertia-Partial-Data' => 'data']);

        $props = [
            'user' => new LazyProp(function () {
                return [
                    'name' => 'Jonathan Reinink',
                    'email' => 'jonathan@example.com',
                ];
            }),
            'data' => [
                'name' => 'Taylor Otwell',
            ],
            'errors' => new AlwaysProp(function () {
                return [
                    'name' => 'The email field is required.',
                ];
            }),
        ];

        $response = new Response('User/Edit', $props, 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame('The email field is required.', $page->props->errors->name);
        $this->assertSame('Taylor Otwell', $page->props->data->name);
        $this->assertFalse(isset($page->props->user));
    }

    public function test_top_level_dot_props_get_unpacked(): void
    {
        $props = [
            'auth' => [
                'user' => [
                    'name' => 'Jonathan Reinink',
                ],
            ],
            'auth.user.can' => [
                'do.stuff' => true,
            ],
            'product' => ['name' => 'My example product'],
        ];

        $request = Request::create('/products/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Edit', $props, 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData(true);

        $user = $page['props']['auth']['user'];
        $this->assertSame('Jonathan Reinink', $user['name']);
        $this->assertTrue($user['can']['do.stuff']);
        $this->assertFalse(array_key_exists('auth.user.can', $page['props']));
    }

    public function test_nested_dot_props_do_not_get_unpacked(): void
    {
        $props = [
            'auth' => [
                'user.can' => [
                    'do.stuff' => true,
                ],
                'user' => [
                    'name' => 'Jonathan Reinink',
                ],
            ],
            'product' => ['name' => 'My example product'],
        ];

        $request = Request::create('/products/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Edit', $props, 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData(true);

        $auth = $page['props']['auth'];
        $this->assertSame('Jonathan Reinink', $auth['user']['name']);
        $this->assertTrue($auth['user.can']['do.stuff']);
        $this->assertFalse(array_key_exists('can', $auth));
    }

    public function test_responsable_with_invalid_key(): void
    {
        $request = Request::create('/user/123', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $resource = new FakeResource(["\x00*\x00_invalid_key" => 'for object']);

        $response = new Response('User/Edit', ['resource' => $resource], 'app', '123');
        $response = $response->toResponse($request);
        $page = $response->getData(true);

        $this->assertSame(
            ["\x00*\x00_invalid_key" => 'for object'],
            $page['props']['resource']
        );
    }

    public function test_the_page_url_is_prefixed_with_the_proxy_prefix(): void
    {
        if (version_compare(app()->version(), '7', '<')) {
            $this->markTestSkipped('This test requires Laravel 7 or higher.');
        }

        Request::setTrustedProxies(['1.2.3.4'], Request::HEADER_X_FORWARDED_PREFIX);

        $request = Request::create('/user/123', 'GET');
        $request->server->set('REMOTE_ADDR', '1.2.3.4');
        $request->headers->set('X_FORWARDED_PREFIX', '/sub/directory');

        $user = ['name' => 'Jonathan'];
        $response = new Response('User/Edit', ['user' => $user], 'app', '123');
        $response = $response->toResponse($request);
        $view = $response->getOriginalContent();
        $page = $view->getData()['page'];

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertInstanceOf(View::class, $view);

        $this->assertSame('/sub/directory/user/123', $page['url']);
    }

    public function test_the_page_url_doesnt_double_up(): void
    {
        $request = Request::create('/subpath/product/123', 'GET', [], [], [], [
            'SCRIPT_FILENAME' => '/project/public/index.php',
            'SCRIPT_NAME' => '/subpath/index.php',
        ]);
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('Product/Show', []);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame('/subpath/product/123', $page->url);
    }

    public function test_trailing_slashes_in_a_url_are_preserved(): void
    {
        $request = Request::create('/users/', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Index', []);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame('/users/', $page->url);
    }

    public function test_trailing_slashes_in_a_url_with_query_parameters_are_preserved(): void
    {
        $request = Request::create('/users/?page=1&sort=name', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Index', []);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame('/users/?page=1&sort=name', $page->url);
    }

    public function test_a_url_without_trailing_slash_is_resolved_correctly(): void
    {
        $request = Request::create('/users', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Index', []);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame('/users', $page->url);
    }

    public function test_a_url_without_trailing_slash_and_query_parameters_is_resolved_correctly(): void
    {
        $request = Request::create('/users?page=1&sort=name', 'GET');
        $request->headers->add(['X-Inertia' => 'true']);

        $response = new Response('User/Index', []);
        $response = $response->toResponse($request);
        $page = $response->getData();

        $this->assertSame('/users?page=1&sort=name', $page->url);
    }
}

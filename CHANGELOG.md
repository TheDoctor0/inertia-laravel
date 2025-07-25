# Release Notes

## [Unreleased](https://github.com/inertiajs/inertia-laravel/compare/v2.0.2...2.x)

- Nothing!

## [v2.0.2](https://github.com/inertiajs/inertia-laravel/compare/v2.0.1...v2.0.2) - 2025-04-10

### What's Changed

* [2.x] Supports Laravel 12 by [@crynobone](https://github.com/crynobone) in https://github.com/inertiajs/inertia-laravel/pull/709
* Add Inertia::deepMerge Method for Handling Complex Data Merges in Responses by [@HichemTab-tech](https://github.com/HichemTab-tech) in https://github.com/inertiajs/inertia-laravel/pull/679
* Improve PHPDoc annotations for ResponseFactory class by [@mohammadrasoulasghari](https://github.com/mohammadrasoulasghari) in https://github.com/inertiajs/inertia-laravel/pull/723
* fix props that extends Responsable after closures / lazy props by [@d8vjork](https://github.com/d8vjork) in https://github.com/inertiajs/inertia-laravel/pull/722
* [2.x] Allow environment config for `ssr.enabled`, `ssr.url`, and `history.encrypt` by [@bram-pkg](https://github.com/bram-pkg) in https://github.com/inertiajs/inertia-laravel/pull/714
* Replace `array_merge` with spread operator in `middleware.stub` by [@osbre](https://github.com/osbre) in https://github.com/inertiajs/inertia-laravel/pull/710
* [2.x] Resolve Closure before checking if a prop implements the Arrayable contract by [@rodrigopedra](https://github.com/rodrigopedra) in https://github.com/inertiajs/inertia-laravel/pull/706
* Handle SSR URLs with trailing slashes by [@simon-tma](https://github.com/simon-tma) in https://github.com/inertiajs/inertia-laravel/pull/705
* [2.x] Call `toArray()` on `Arrayable` props resolved from the Container by [@pascalbaljet](https://github.com/pascalbaljet) in https://github.com/inertiajs/inertia-laravel/pull/696
* [2.x] Replace md5 with xxhash by [@RobertBoes](https://github.com/RobertBoes) in https://github.com/inertiajs/inertia-laravel/pull/653

### New Contributors

* [@HichemTab-tech](https://github.com/HichemTab-tech) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/679
* [@mohammadrasoulasghari](https://github.com/mohammadrasoulasghari) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/723
* [@d8vjork](https://github.com/d8vjork) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/722
* [@bram-pkg](https://github.com/bram-pkg) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/714
* [@osbre](https://github.com/osbre) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/710
* [@simon-tma](https://github.com/simon-tma) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/705
* [@pascalbaljet](https://github.com/pascalbaljet) made their first contribution in https://github.com/inertiajs/inertia-laravel/pull/696

**Full Changelog**: https://github.com/inertiajs/inertia-laravel/compare/v2.0.1...v2.0.2

## [v2.0.1](https://github.com/inertiajs/inertia-laravel/compare/v2.0.0...v2.0.1) - 2025-02-18

- Allow Laravel 12.x.

**Full Changelog**: https://github.com/inertiajs/inertia-laravel/compare/v2.0.0...v2.0.1

## [v2.0.0](https://github.com/inertiajs/inertia-laravel/compare/v1.2.0...2.0.0)

- Add support for Inertia.js v2.0.0
- Add `Inertia::defer()` to support deferred props
- Add `Inertia::merge()` to support merging props on client
- Add `Inertia::always()` for props that should always be included ([#627](https://github.com/inertiajs/inertia-laravel/pull/627))
- Add `Inertia::clearHistory()` and `Inertia::encryptHistory()` methods, encryption config, and encryption middleware
- Deprecated `Inertia::lazy()` in favor of `Inertia::optional()`
- Drop support for Laravel 8 and 9 ([#629](https://github.com/inertiajs/inertia-laravel/pull/629))

## [v1.2.0](https://github.com/inertiajs/inertia-laravel/compare/v1.1.0...v1.2.0) - 2024-05-17

- Make commands lazy ([#601](https://github.com/inertiajs/inertia-laravel/pull/601))
- Add persistent properties ([#621](https://github.com/inertiajs/inertia-laravel/pull/621))
- Exclude `except` props from partial reloads ([#622](https://github.com/inertiajs/inertia-laravel/pull/622))

## [v1.1.0](https://github.com/inertiajs/inertia-laravel/compare/v1.0.0...v1.1.0) - 2024-05-16

- Support dot notation in partial requests ([#620](https://github.com/inertiajs/inertia-laravel/pull/620))
- Add `$request->inertia()` IDE helper ([#625](https://github.com/inertiajs/inertia-laravel/pull/625))

## [v1.0.0](https://github.com/inertiajs/inertia-laravel/compare/v0.6.11...v1.0.0) - 2024-03-08

- Add Laravel 11 support ([#560](https://github.com/inertiajs/inertia-laravel/pull/560), [#564](https://github.com/inertiajs/inertia-laravel/pull/564))
- Fix URL generation ([#592](https://github.com/inertiajs/inertia-laravel/pull/592))
- Remove deprecated `Assert` class and Laravel 6 & 7 support. ([#594](https://github.com/inertiajs/inertia-laravel/pull/594))

## [v0.6.11](https://github.com/inertiajs/inertia-laravel/compare/v0.6.10...v0.6.11) - 2023-09-13

- Add option for using the `bun` runtime in SSR ([#552](https://github.com/inertiajs/inertia-laravel/pull/552))

## [v0.6.10](https://github.com/inertiajs/inertia-laravel/compare/v0.6.9...v0.6.10) - 2023-09-13

- Add `inertia_location` helper function ([#491](https://github.com/inertiajs/inertia-laravel/pull/491))
- Add `Route::inertia()` IDE helper ([#413](https://github.com/inertiajs/inertia-laravel/pull/413))
- Automatically update Facade docblocks ([#538](https://github.com/inertiajs/inertia-laravel/pull/538))
- Restore request and session on redirects ([#539](https://github.com/inertiajs/inertia-laravel/pull/539))
- Add PHP 8.3 support ([#540](https://github.com/inertiajs/inertia-laravel/pull/540))

## [v0.6.9](https://github.com/inertiajs/inertia-laravel/compare/v0.6.8...v0.6.9) - 2023-01-17

- Conditionally use `pcntl` extension in `inertia:start-ssr` command ([#492](https://github.com/inertiajs/inertia-laravel/pull/492))

## [v0.6.8](https://github.com/inertiajs/inertia-laravel/compare/v0.6.7...v0.6.8) - 2023-01-14

- Reintroduce `inertia.ssr.enabled` config option ([#488](https://github.com/inertiajs/inertia-laravel/pull/488))
- Fix bug where SSR is dispatched twice when errors exist ([#489](https://github.com/inertiajs/inertia-laravel/pull/489))

## [v0.6.7](https://github.com/inertiajs/inertia-laravel/compare/v0.6.6...v0.6.7) - 2023-01-12

- Report SSR errors ([#486](https://github.com/inertiajs/inertia-laravel/pull/486))
- Auto enable SSR based on existence of SSR bundle ([#487](https://github.com/inertiajs/inertia-laravel/pull/487))

## [v0.6.6](https://github.com/inertiajs/inertia-laravel/compare/v0.6.5...v0.6.6) - 2023-01-11

- Add `inertia:start-ssr` and `inertia:stop-ssr` artisan commands ([#483](https://github.com/inertiajs/inertia-laravel/pull/483))

## [v0.6.5](https://github.com/inertiajs/inertia-laravel/compare/v0.6.4...v0.6.5) - 2023-01-10

- Add Laravel v10 support ([#480](https://github.com/inertiajs/inertia-laravel/pull/480))

## [v0.6.4](https://github.com/inertiajs/inertia-laravel/compare/v0.6.3...v0.6.4) - 2022-11-08

- Add PHP 8.2 support ([#463](https://github.com/inertiajs/inertia-laravel/pull/463))

## [v0.6.3](https://github.com/inertiajs/inertia-laravel/compare/v0.6.2...v0.6.3) - 2022-06-27

- Check Vite manifest path (`build/manifest.json`) when determining the current asset version ([#399](https://github.com/inertiajs/inertia-laravel/pull/399))

## [v0.6.2](https://github.com/inertiajs/inertia-laravel/compare/v0.6.1...v0.6.2) - 2022-05-24

- Switch to using the `Vary: X-Inertia` header ([#404](https://github.com/inertiajs/inertia-laravel/pull/404))
- Fix bug with incompatible `$request->header()` method ([#404](https://github.com/inertiajs/inertia-laravel/pull/404))

## [v0.6.1](https://github.com/inertiajs/inertia-laravel/compare/v0.6.0...v0.6.1) - 2022-05-24

- Set `Vary: Accept` header for all responses ([#398](https://github.com/inertiajs/inertia-laravel/pull/398))
- Only register Blade directives when actually needed ([#395](https://github.com/inertiajs/inertia-laravel/pull/395))

## [v0.6.0](https://github.com/inertiajs/inertia-laravel/compare/v0.5.4...v0.6.0) - 2022-05-10

### Added

- Inertia now redirects back by default when no response is returned from a controller ([#350](https://github.com/inertiajs/inertia-laravel/pull/350))
- The Middleware has an overridable `onEmptyResponse` hook to customize the default 'redirect back' behavior ([#350](https://github.com/inertiajs/inertia-laravel/pull/350))

### Changed

- Internal: Replaced the Middleware's `checkVersion` method with an `onVersionChange` hook ([#350](https://github.com/inertiajs/inertia-laravel/pull/350))

### Fixed

- Fixed namespace issue with `Route::inertia()` method ([#368](https://github.com/inertiajs/inertia-laravel/pull/368))
- Added session check when sharing validation errors ([#380](https://github.com/inertiajs/inertia-laravel/pull/380))
- Fixed docblock on facade render method ([#387](https://github.com/inertiajs/inertia-laravel/pull/387))

## [v0.5.4](https://github.com/inertiajs/inertia-laravel/compare/v0.5.3...v0.5.4) - 2022-01-18

### Added

- `.tsx` extension is now included to the testing paths by default ([#354](https://github.com/inertiajs/inertia-laravel/pull/354))

### Fixed

- Dot-notated props weren't being removed after unpacking ([507b0a](https://github.com/inertiajs/inertia-laravel/commit/507b0a0ad8321028b8651528099f73a88b158359))

## [v0.5.3](https://github.com/inertiajs/inertia-laravel/compare/v0.5.2...v0.5.3) - 2022-01-17

### Fixed

- Incorrect `Arrayable` type-hint ([#353](https://github.com/inertiajs/inertia-laravel/pull/353))
- Pagination with API Resources and other nested props weren't resolving properly ([#342](https://github.com/inertiajs/inertia-laravel/pull/342), [#298](https://github.com/inertiajs/inertia-laravel/pull/298))

## [v0.5.2](https://github.com/inertiajs/inertia-laravel/compare/v0.5.1...v0.5.2) - 2022-01-12

### Added

- Laravel 9 Support ([#347](https://github.com/inertiajs/inertia-laravel/pull/347))

### Fixed

- Respect `X-Forwarded-For` header ([#333](https://github.com/inertiajs/inertia-laravel/pull/333))

## [v0.5.1](https://github.com/inertiajs/inertia-laravel/compare/v0.5.0...v0.5.1) - 2022-01-07

### Fixed

- When the SSR Server crashes, a `null` response will be returned, which wasn't being handled properly ([7d7d89](https://github.com/inertiajs/inertia-laravel/commit/7d7d891d72792f6cab6b616d5bbbb48f0526d65f))

## [v0.5.0](https://github.com/inertiajs/inertia-laravel/compare/v0.4.5...v0.5.0) - 2022-01-07

### Added

- PHP 8.1 Support ([#327](https://github.com/inertiajs/inertia-laravel/pull/327))
- Allow `Inertia::location` to be called with a `RedirectResponse` ([#302](https://github.com/inertiajs/inertia-laravel/pull/302))
- Support Guzzle Promises ([#316](https://github.com/inertiajs/inertia-laravel/pull/316))
- Server-side rendering support (`@inertiaHead` directive) ([#339](https://github.com/inertiajs/inertia-laravel/pull/339))
- Allow custom `@inertia` root element ID (e.g. `@inertia('foo')` -> `<div id="foo" data-page="...`) ([#339](https://github.com/inertiajs/inertia-laravel/pull/339))

### Changed

- We now keep a changelog here on GitHub :tada: For earlier releases, please see [the releases page of inertiajs.com](https://inertiajs.com/releases?all=true#inertia-laravel).
- Add PHP native type declarations ([#301](https://github.com/inertiajs/inertia-laravel/pull/301), [#337](https://github.com/inertiajs/inertia-laravel/pull/337))

### Deprecated

- Deprecate `Assert` library in favor of Laravel's AssertableJson ([#338](https://github.com/inertiajs/inertia-laravel/pull/338))

### Removed

- Laravel 5.4 Support ([#327](https://github.com/inertiajs/inertia-laravel/pull/327))

### Fixed

- Transform Responsable props to arrays instead of objects ([#265](https://github.com/inertiajs/inertia-laravel/pull/265))
- `Inertia::location()`: Fall back to regular redirects when a direct (non-Inertia) visit was made ([#312](https://github.com/inertiajs/inertia-laravel/pull/312))
- Use correct types for Resources ([#214](https://github.com/inertiajs/inertia-laravel/issues/214))

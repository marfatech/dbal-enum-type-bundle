## [Unreleased]
### Added
- Installed `marfatech/dbal-enum-type` with pattern version `^2.0`.
### Changed
- Updated `php` with pattern version `~7.4||~8.0`.
- Updated composer name `wakeapp/dbal-enum-type-bundle` to `marfatech/dbal-enum-type-bundle`.
- [BC BREAK] Refactoring namespace to `MarfaTech`.
- [BC BREAK] Renamed root config `wakeapp_dbal_enum_type` to `marfatech_dbal_enum_type`.
### Removed
- Removed vendor requirement `wakeapp/dbal-enum-type`.

## [1.3.2] - 2021-03-02
### Added
- Support PHP ~8.0.

## [1.3.1] - 2020-09-18
### Changed
- Added support doctrine/doctrine-bundle ^2.1

## [1.3.0] - 2020-09-17
### Added
- Added support Symfony ~5.0

## [1.2.0] - 2019-12-10
### Changed
- Auto-registration for the doctrine dbal enum types now performs in `WakeappDbalEnumTypeExtension::prepend()`
### Removed
- Removed `ConnectionFactoryDecorator`.
- Removed `DbalEnumTypeRegistryCompilerPass`.

## [1.1.0] - 2019-06-04
### Added
- Added auto-registration for the doctrine dbal enum types.
- Added optional configuration parameter `source_directories`.

## [1.0.0] - 2019-05-17
### Added
- First release of this bundle.

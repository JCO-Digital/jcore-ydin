# Changelog

## v5.0.0 (2026-09-23)

#### Features

- core: add opt-in features and documentation (5965594)

#### Refactor

- core: cleanup code and improve documentation (da7937e)

#### Continuous Integration

- github: set prerelease to false in release workflow (14751d5)
- github: update workflow configurations for release and validation (6a96e9f)

#### Maintenance

- settings: remove customizer and ACF options classes (e8fc9ae)
- config: add foonver configuration file (cc017c8)

### v4.2.1 (2026-02-20)

#### Bug Fixes

- Allow OPTIONS for REST user block (3764134)

#### Misc

- Update classes/Security/Hardening.php (dac3dcf)

## v4.2.0 (2026-02-05)

#### Features

- Add security hardening measures (ae22939)

#### Continuous Integration

- Remove Commitsar from CI workflow (a643fee)

#### Format

- Prettier in ContextProvider (e4caefc)

### v4.1.1 (2025-12-11)

#### Features

- Add stub for jcore_global_content if jcore-maailma missing (79022cc)

#### Bug Fixes

- ci: Pin Commitsar action to version 0.20.0 in workflow (50c9da2)

#### Documentation

- Update classes/Timber/ContextProvider.php (8c800f8)

#### Continuous Integration

- downgrade checkout action to v4 and update conventional changelog action to v6 (77c047f)
- bump version of cc action :arrow_up: (b36cea2)

## v4.1.0 (2025-11-19)

#### Features

- Add 'try_fn' function to Twig for safe function calls with error handling ✨ (f07076a)

## v4.0.0 (2025-11-13)

#### Features

- Added Timber and Twig context to Ydin (BREAKING CHANGE) (c4dd08f)

#### Documentation

- Update scope in readme (BREAKING CHANGE) (aab44f5)

#### Misc

- Update version (2e3ce6f)

### v3.8.1 (2026-09-07)

#### Bug Fixes

- blocks: coerce get_fields() false to empty array in populate_context (c35fb61)

## v3.8.0 (2025-05-02)

#### Features

- Added action "environment_changed" that triggers when wp_get_environment_type returns different  value. (5fb0ee1)

### v3.7.2 (2024-09-25)

#### Bug Fixes

- helpers: Added optional argument to specify priority of the timber locations. 🩹 (5d63e9c)

#### Documentation

- helpers: changed helper documentation version 📝 (ad7f9ca)

### v3.7.1 (2024-09-24)

#### Bug Fixes

- acf-blocks: Require the file so class exists. 🐛 (14b5ebe)

## v3.7.0 (2024-09-24)

#### Features

- acf-blocks: Add loading of ACF blocks. ✨ (bad0a77)

## v3.6.0 (2024-09-24)

#### Features

- Added a timber location helper function :sparkles: (b1ed8e8)

### v3.5.2 (2024-09-23)

#### Bug Fixes

- Conditional call to autoloader. (3cbd157)

### v3.5.1 (2024-09-20)

#### Maintenance

- version bump (344d064)
- version bump (e61b0e8)
- Update composer dependencies (cc23cb0)

#### Update

- composer modules (266ef4c)

#### Misc

- File cleanup (5e2ca2c)

## v3.5.0 (2024-01-18)

#### Features

- bootstrap: Added a Bootstrap interface, to have a uniform way to bootstrap all modules. Closes #18 ✨ (63d900e)

#### Documentation

- update a version typo 📝 (bfa84a7)

### v3.4.1 (2024-01-18)

#### Bug Fixes

- Fixed an issue where empty values caused warnings 🐛 (3623466)

#### Refactor

- customizer: Refactored parts of the customizer to add add_section method. ♻️ (0bd3662)

## v3.4.0 (2024-01-18)

#### Features

- make: Added make file (ba8ef67)

#### Misc

- Namespace error (a16d85f)

### v3.3.2 (2024-01-08)

#### Bug Fixes

- Formatting (9908841)

#### Formatting

- Option class (90756fc)

### v3.3.1 (2023-11-28)

#### Bug Fixes

- Initialized $instance, changed $version default from null to '' (8995a95)

#### Refactor

- bootstrap: Refactored the bootstrap function to be called init (f76be9e)

#### Continuous Integration

- Change workflow to use the new branch name (1d381eb)

## v3.3.0 (2023-11-23)

#### Features

- ydin-wordpress: Added posttype and taxonomy classes ✨ (90b54f5)
- ydin-blocks: Added the base block class ✨ (63c9fb3)
- Added some more core classes. ✨ (976a616)

#### Bug Fixes

- use tags as well when pushing the changes. 🐛 (6846775)
- Use push-protected to push the actual changes. 🐛 (bd4c000)
- ydin-blocks: Updated the blocks methods, (also test release action) ⚗️ (1386335)
- composer: Removed version and only use the tagging for versioning. 💚 (d5ec970)

#### Refactor

- ydin-assets: Moved AssetsManager class -> WordPress\Assets ♻️ (c2601ea)

#### Documentation

- Updated some old docs 📝 (649fb7c)

#### Continuous Integration

- Updated labelling workflow to allow for updating pull requests 💚 (410fb6d)
- Attempt to use the master PAT and added a versioning file to correctly track versions. 💚 (6c0c0de)
- skip commit, to allow for push protection. (f539ca3)
- Use the master PAT to push 💚 (0702fd2)
- use docker image instead of build. 💚 (19fbf54)
- Checkout before releasing a version 💚 (2797c9e)
- Remove the use of version-file, and set the starting version (86b848c)
- Fixed an issue with the commit checker 🔧 (9253fc9)
- Fixed an issue with the commit checker 🔧 (95d7e9d)
- Improved pull request flow and release flow 🔧 (a09b56c)
- args -> command.. 💚 (b5d8099)
- Changed runner to use ubuntu-latest 💚 (7b4b633)
- Added CI 👷 (a761480)

#### Maintenance

- Fixed the license. 🔧 (3ea6714)
- Initial commit 🎉 (15084e2)


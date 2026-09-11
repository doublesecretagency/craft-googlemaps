# Unit tests

PHPUnit 10, no Craft bootstrap. Run from inside the DDEV container:

```bash
ddev ssh
cd vendor/doublesecretagency/craft-googlemaps
composer test
```

Or as a one-shot from the sandbox directory on the host:

```bash
ddev exec bash -c "cd /var/www/html/vendor/doublesecretagency/craft-googlemaps && /var/www/html/vendor/bin/phpunit"
```

820 tests in roughly 0.9 seconds. If it gets slow, something is bootstrapping
more than intended.

## Why there is no Craft container

Booting Craft would make the suite slower, order-dependent, and reliant on a
database. Everything here either runs as pure PHP or inspects source text.

That draws a hard line around what can be tested. A method that dereferences
`Craft::$app` gets a structural test instead of a behavioral one, and
`SuiteIntegrityTest` enforces the boundary by failing if any test file
references the container.

## Three categories

Every test fits one of these. Mixing them in one file is fine; mixing them
inside one test method is a smell.

**Pure unit.** Real behavior, called directly. More of this plugin qualifies
than is typical, because the parsing and geometry are container-free:
`GeocodingHelper::restructureComponents()`, `Location` and its distance math,
`AddressField::normalizeRaw()`, `MapHelper`, and both enums.

**Reflection and shape.** For anything coupled to Craft's container. Asserts
inheritance, method existence and signatures, and property defaults read through
`getDefaultProperties()` rather than by constructing the object.

**Source-level.** For cases where the shape of the code is the thing that
matters: the store's binding derivation, the install schema, the event
registrations in `init()`, and the Twig templates.

## No exclusions

`phpunit.xml` excludes nothing, and `SuiteIntegrityTest` asserts that it stays
that way. An exclusion is how a directory of unrunnable tests survives, so the
absence of one is the guard.

## How we know it can fail

A suite is green from the moment it is written, so a passing first run is evidence about nothing.
This one was fired at deliberate defects before being called done: 29 mutations across three rounds,
one at a time, each injected into the plugin source with the suite run against it and the file then
restored.

**29 of 29 genuine defects were caught.** Among them: a default coordinate moved to 0,0, a
UK-specific geocoding branch dropped, a decimal column narrowed, a unique index removed, anonymous
access closed on the public lookup controller, the `[object Object]` guard deleted, an earth-radius
constant changed, a JavaScript method renamed out of parity with its PHP twin, a hidden form input
removed, a Twig loop given an empty subject, and a facade method that stopped delegating.

Two misses were instructive rather than gaps. One was a weak mutation that left the asserted tokens
adjacent, and a genuine deletion of the same code was caught twice. The other crashed the runner
before test collection, which is a louder signal than a failing assertion.

**It also caught two of these tests being wrong**, which no green run could have shown: one asserted
a flat integer enum against a constant that is two-level with string values, and two compared file
modification times, which any `cp` or `git checkout` flips. All three were rewritten.

The harness itself is about twenty lines and the recipe is in
`~/.claude/instructions/plugin-tests.md`. 🛑 It writes deliberately broken code to disk, so do not run
it on a dirty tree: a `git add` landing inside a mutation window stages a defect, which happened on
2026-09-10.

## What this suite catches

- Silent renames of classes, methods, properties and constants
- A subfield, setting, or database column dropped from one side of a contract
- Missing event registrations in the plugin's `init()`
- A hardcoded list replacing a derivation, which is what caused
  [#158](https://github.com/doublesecretagency/craft-googlemaps/issues/158)
- A facade method that stops delegating to its helper
- Schema drift between the migration, the record, and the field
- Drift between the PHP and JavaScript halves of the universal-methods protocol
- Drift between the settings model and the config template
- A class referencing `GoogleMapsPlugin` without importing it

## What it does not catch

- Real geocoding, real Google API responses, real HTTP
- Database query correctness, including the haversine SQL
- Whether a Twig template parses at all. A source-level test reads a template as
  text, and parseability is not a property of text, so a template throwing a
  syntax error on every request satisfies every assertion written about it.
- Anything about the rendered control panel, including whether the Vue hydration
  actually reaches the inputs the bindings name

For all of those, exercise the flow in the sandbox. The store-binding tests in
particular assert that a binding is declared, never that hydration ran, so a
change there still wants a manual check against a real Address field.
